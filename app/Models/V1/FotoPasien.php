<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoPasien extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'foto_swa_pasien',
        'foto_kartu_identitas_pasien',
        'id_pasien',
        'id_pasien_temp'
    ];

    protected $table = 'mirai_pasien.foto_pasien';

    protected $id = 'id';

    
    public static $FOTO_SWA_PASIEN = 'foto_swa_pasien';
    public static $FOTO_KARTU_IDENTITAS_PASIEN = 'foto_kartu_identitas_pasien';
}
