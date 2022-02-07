<?php

namespace App\Models;

use App\Models\V1\Pasien;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primatyKey = "id";

    protected $table = 'mirai_pasien.users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'kode',
        'password',
        'kode'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var a   rray<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function pasiens()
    {
        return $this->hasMany(Pasien::class, 'kode', 'id');
    }
}
