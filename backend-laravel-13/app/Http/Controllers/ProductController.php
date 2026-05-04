<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Helper function untuk format response API agar seragam.
     * Dipakai di semua method supaya struktur JSON selalu sama.
     *
     * @param string $message  Pesan yang dikembalikan ke client
     * @param mixed  $data     Data yang dikembalikan (boleh null)
     * @param int    $status   HTTP status code (default 200)
     */
    private function response($message, $data = null, $status = 200)
    {
        return response()->json([
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ], $status);
    }

    /**
     * Mengambil semua data produk.
     * Diakses via: GET /api/products
     *
     * latest() = urut dari data terbaru (ORDER BY created_at DESC)
     * get()    = eksekusi query dan ambil semua hasilnya sebagai Collection
     */
    public function index()
    {
        $products = Product::latest()->get();
        return $this->response('Products retrieved successfully', $products);
    }

    /**
     * Menyimpan produk baru ke database.
     * Diakses via: POST /api/products
     *
     * Alur:
     * 1. Validasi input
     * 2. Upload gambar jika ada (opsional)
     * 3. Simpan produk ke database
     */
    public function store(Request $request)
    {
        // Validasi input — jika gagal, Laravel otomatis return error 422
        $request->validate([
            'name'  => 'required',
            'price' => 'required|numeric',  // harus angka
            'stock' => 'required|integer',  // harus bilangan bulat
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048' // opsional, max 2MB
        ]);

        // Default null jika tidak ada gambar yang diupload
        $imageName = null;

        // Cek apakah request membawa file gambar
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Buat nama file unik: timestamp + random string + ekstensi asli
            // Contoh: 1716888123_aB3xYz.jpg
            $imageName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            // Pindahkan file ke folder public/uploads/products/
            $file->move(public_path('uploads/products'), $imageName);
        }

        // Simpan produk ke database
        // Spread operator (...) dipakai untuk menggabungkan array field teks + image
        $product = Product::create([
            ...$request->only(['name', 'description', 'price', 'stock']),
            'image' => $imageName
        ]);

        // Return 201 Created karena berhasil membuat resource baru
        return $this->response('Product created successfully', $product, 201);
    }

    /**
     * Mengambil detail satu produk berdasarkan ID.
     * Diakses via: GET /api/products/{id}
     *
     * Laravel otomatis mencari product berdasarkan ID dari URL (Route Model Binding).
     * Jika tidak ditemukan, otomatis return 404.
     */
    public function show(Product $product)
    {
        // $product sudah otomatis diisi oleh Laravel via Route Model Binding
        return $this->response('Product retrieved successfully', $product);
    }

    /**
     * Mengupdate data produk yang sudah ada.
     * Diakses via: PUT/PATCH /api/products/{id}
     *
     * Alur:
     * 1. Jika ada gambar baru → hapus gambar lama, upload gambar baru
     * 2. Update field teks (name, description, price, stock)
     */
    public function update(Request $request, Product $product)
    {
        // Cek apakah ada file gambar baru yang diupload
        if ($request->hasFile('image')) {

            // Hapus gambar lama jika ada, supaya tidak menumpuk di storage
            if ($product->image && File::exists(public_path('uploads/products/' . $product->image))) {
                File::delete(public_path('uploads/products/' . $product->image));
            }

            // Upload gambar baru dengan nama unik
            $file      = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $imageName);

            // Simpan nama gambar baru ke object product (belum ke database)
            $product->image = $imageName;
        }

        // Update field teks — only() memastikan hanya field yang diizinkan yang diupdate
        // Ini mencegah mass assignment dari field yang tidak diinginkan
        $product->update($request->only(['name', 'description', 'price', 'stock']));

        return $this->response('Product updated successfully', $product);
    }

    /**
     * Menghapus produk dari database.
     * Diakses via: DELETE /api/products/{id}
     *
     * Alur:
     * 1. Hapus file gambar dari storage jika ada
     * 2. Hapus data produk dari database
     */
    public function destroy(Product $product)
    {
        // Hapus file gambar dari folder public/uploads/products/ jika ada
        // Penting: selalu hapus file fisik sebelum hapus data di database
        if ($product->image && File::exists(public_path('uploads/products/' . $product->image))) {
            File::delete(public_path('uploads/products/' . $product->image));
        }

        // Hapus data produk dari database
        $product->delete();

        // Tidak ada data yang dikembalikan karena produk sudah dihapus
        return $this->response('Product deleted successfully');
    }
}
