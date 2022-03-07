<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarAntrian extends Model
{
    use HasFactory;

    protected $table = 'mirai_pasien.dm_daftar_antrian';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_daftar_pasien',
        'foto'
    ];
}
