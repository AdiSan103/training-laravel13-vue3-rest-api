<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AuthController extends Controller
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
     * Login user dan menghasilkan JWT token.
     *
     * Alur:
     * 1. Ambil email & password dari request
     * 2. Coba auth()->attempt() — jika gagal, return 401
     * 3. Jika berhasil, kembalikan token + data user
     *
     * JWT token ini dipakai di header Authorization
     * untuk mengakses route yang dilindungi (auth:api)
     */
    public function login(Request $request)
    {
        // Ambil hanya email dan password dari request
        $credentials = $request->only('email', 'password');

        // attempt() akan cek ke database, jika cocok langsung generate token
        // Jika gagal, kembalikan error 401 Unauthorized
        if (!$token = auth()->attempt($credentials)) {
            return $this->response('Unauthorized', null, 401);
        }

        return $this->response('Login successful', [
            'token' => $token,          // JWT token untuk dipakai di request berikutnya
            'user'  => auth()->user()   // Data user yang sedang login
        ]);
    }

    /**
     * Mengambil data user yang sedang login.
     *
     * Route ini dilindungi middleware auth:api,
     * jadi hanya bisa diakses jika membawa token yang valid
     * di header: Authorization: Bearer {token}
     */
    public function me()
    {
        // auth()->user() otomatis membaca token dari header
        return $this->response('Authenticated user', auth()->user());
    }

    /**
     * Logout user dengan cara menginvalidasi JWT token.
     *
     * Setelah logout, token lama tidak bisa dipakai lagi.
     * Client harus login ulang untuk mendapatkan token baru.
     */
    public function logout()
    {
        // Invalidasi token yang sedang aktif
        auth()->logout();
        return $this->response('Logged out successfully');
    }

    /**
     * Registrasi user baru.
     *
     * Alur:
     * 1. Validasi input dari request
     * 2. Buat user baru di database
     * 3. Password otomatis di-hash sebelum disimpan
     *
     * Note: password_confirmation wajib dikirim karena pakai rule 'confirmed'
     */
    public function register(Request $request)
    {
        // Validasi input — jika gagal, Laravel otomatis return error 422
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email', // email tidak boleh duplikat
            'password' => 'required|min:6|confirmed'           // butuh field password_confirmation
        ]);

        // Buat user baru — password di-hash agar tidak tersimpan plain text
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password) // Hash::make() = bcrypt
        ]);

        // Return 201 Created karena berhasil membuat resource baru
        return $this->response('User registered successfully', $user, 201);
    }

    /**
     * Update profil user yang sedang login.
     *
     * Semua field bersifat opsional (tidak wajib dikirim semua).
     * Hanya field yang dikirim yang akan diupdate.
     *
     * Bisa update: name, password, avatar (foto profil)
     */
    public function updateProfile(Request $request)
    {
        // Ambil user yang sedang login dari token
        $user = auth()->user();

        // 'sometimes' = hanya divalidasi jika field tersebut dikirim
        // 'nullable'  = boleh tidak dikirim / bernilai null
        $request->validate([
            'name'     => 'sometimes|string|max:255',
            'password' => 'nullable|min:6|confirmed',
            'avatar'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048' // max 2MB
        ]);

        // Update name jika dikirim
        if ($request->name) {
            $user->name = $request->name;
        }

        // Update password jika dikirim, langsung di-hash
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        // Upload avatar jika ada file yang dikirim
        if ($request->hasFile('avatar')) {

            // Hapus foto lama jika ada, supaya tidak menumpuk di storage
            if ($user->avatar && File::exists(public_path('uploads/avatars/' . $user->avatar))) {
                File::delete(public_path('uploads/avatars/' . $user->avatar));
            }

            $file = $request->file('avatar');

            // Buat nama file unik: timestamp + random string + ekstensi asli
            // Contoh: 1716888123_aB3xYz.jpg
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            // Pindahkan file ke folder public/uploads/avatars/
            $file->move(public_path('uploads/avatars'), $fileName);

            // Simpan nama file ke database (bukan full path)
            $user->avatar = $fileName;
        }

        // Simpan semua perubahan ke database
        $user->save();

        return $this->response('Profile updated successfully', $user);
    }
}
