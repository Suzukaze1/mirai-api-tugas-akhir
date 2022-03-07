<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    use HasFactory;

    protected $table = 'mirai_pasien.dokter';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'id_poli',
        'gelar',
        'hari',
        'waktu',
        'foto',
        'tentang_dokter'
    ];

    public function poli()
    {
        return $this->belongsTo(Poli::class, 'id_poli', 'id');
    }
}
