<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPoliklinik extends Model
{
    use HasFactory;

    protected $table = 'mirai_pasien.riwayat_poliklinik';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nomor_daftar',
        'nama_pasien',
        'nomor_rekam_medis',
        'id_poliklinik',
        'tanggal_daftar',
        'resume_medis',
        'hasil_penunjang'
    ];
}
