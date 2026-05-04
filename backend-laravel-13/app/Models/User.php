<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Model User merepresentasikan tabel 'users' di database.
 *
 * Implements JWTSubject wajib ada agar package JWT (Tymon)
 * bisa mengenali model ini sebagai subjek token JWT.
 * Tanpa ini, auth()->attempt() akan error.
 */
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * $fillable = daftar kolom yang boleh diisi via mass assignment.
     * Mass assignment = mengisi banyak field sekaligus, contoh: User::create([...])
     *
     * Kolom yang TIDAK ada di sini tidak bisa diisi via create() atau fill(),
     * ini sebagai proteksi keamanan dari serangan mass assignment.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar', // nama file foto profil user
    ];

    /**
     * $hidden = daftar kolom yang disembunyikan saat model dikonversi ke JSON.
     * Contoh: saat return response()->json($user), field ini tidak akan muncul.
     *
     * password    → tidak boleh terekspos ke client
     * remember_me → token internal Laravel, tidak perlu dikirim ke client
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * $casts = konversi otomatis tipe data kolom saat diambil dari database.
     *
     * email_verified_at → dikonversi ke object Carbon (datetime) bukan string biasa
     * password          → otomatis di-hash saat di-set (tidak perlu Hash::make() manual)
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Mengembalikan identifier unik user yang akan disimpan di dalam JWT token.
     * Biasanya berupa primary key (id) dari tabel users.
     *
     * Isi token JWT (payload) akan menyimpan 'sub' = id user,
     * sehingga saat token dikirim kembali, Laravel tahu user mana yang login.
     *
     * Wajib ada karena implements JWTSubject.
     */
    public function getJWTIdentifier()
    {
        // getKey() mengembalikan nilai primary key model (default: $this->id)
        return $this->getKey();
    }

    /**
     * Mengembalikan custom claims (data tambahan) yang ingin disimpan di JWT token.
     *
     * Contoh penggunaan: menyimpan role user di dalam token
     * return ['role' => $this->role];
     *
     * Untuk saat ini dikosongkan karena tidak ada data tambahan.
     * Wajib ada karena implements JWTSubject.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
