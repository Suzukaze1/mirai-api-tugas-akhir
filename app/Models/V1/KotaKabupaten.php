<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KotaKabupaten extends Model
{
    use HasFactory;

    protected $table = 'mirai_pasien.dm_kabupaten';

    protected $id = 'kode_prov_kabupaten';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'jenis',
        'kode_prov'
    ];

    public function provinsis()
    {
        return $this->belongsTo(Provinsi::class, 'kode_prov', 'kode');
    }
}
