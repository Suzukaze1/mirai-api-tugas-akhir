<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranPoliklinik extends Model
{
    use HasFactory;

    protected $table = 'mirai_pasien.pendaftaran_poliklinik';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nomor_rekam_medis',
        'kunjungan',
        'nomor_debitur',
        'id_poliklinik',
        'status_pendaftaran',
        'nomor_antrian',
        'id_user',
        'nomor_rujukan'
    ];
}
