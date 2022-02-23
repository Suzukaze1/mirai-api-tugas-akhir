<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasienSementara extends Model
{
    use HasFactory;

    protected $table = 'mirai_pasien.pasien_temp';

    protected $primaryKey  = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'agama_kode',
        'pendidikan_kode',
        'pekerjaan_kode',
        'kewarganegaraan_kode',
        'jenis_identitas_kode',
        'suku_kode',
        'no_identitas',
        'nama',
        'ayah_nama',
        'ibu_nama',
        'nama_pasangan',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'jkel',
        'no_telp',
        'alergi',
        'kedudukan_keluarga',
        'golongan_darah',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'umur',
        'anak_ke',
        'jurusan',
        'nama_tempat_bekerja',
        'alamat_tempat_bekerja',
        'no_rekam_medik_ayah',
        'no_rekam_medik_ibu',
        'status_perkawinan',
        'penghasilan',
        'status_validasi',
        'kode_pasien'
    ];
}
