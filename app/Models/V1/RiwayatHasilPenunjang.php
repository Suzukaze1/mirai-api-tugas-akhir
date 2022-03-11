<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatHasilPenunjang extends Model
{
    use HasFactory;

    protected $table = 'mirai_pasien.riwayat_hasil_penunjang';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hasil_penunjang_detail',
        'nomor_rekam_medis'
    ];
}
