<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Product merepresentasikan tabel 'products' di database.
 *
 * Kolom yang tersedia:
 * - id          → primary key, auto increment
 * - name        → nama produk
 * - description → deskripsi produk (opsional)
 * - stock       → jumlah stok (integer)
 * - price       → harga produk (decimal, 10 digit, 2 angka di belakang koma)
 * - image       → nama file gambar produk (opsional)
 * - created_at  → waktu dibuat (otomatis oleh Laravel)
 * - updated_at  → waktu diupdate (otomatis oleh Laravel)
 */
class Product extends Model
{
    /**
     * $fillable = daftar kolom yang boleh diisi via mass assignment.
     * Contoh: Product::create([...]) atau $product->fill([...])
     *
     * Kolom yang tidak ada di sini tidak bisa diisi secara massal,
     * ini melindungi dari serangan mass assignment.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'stock',
        'price',
        'image',
    ];

    /**
     * $casts = konversi otomatis tipe data kolom saat diambil dari database.
     *
     * price → dikonversi ke float agar tidak dikembalikan sebagai string
     * stock → dikonversi ke integer
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
    ];
}
