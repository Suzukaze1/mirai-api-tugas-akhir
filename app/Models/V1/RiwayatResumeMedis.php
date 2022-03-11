<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatResumeMedis extends Model
{
    use HasFactory;

    protected $table = 'mirai_pasien.riwayat_resume_medis';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'resume_medis_detail',
        'nomor_rekam_medis'
    ];
}
