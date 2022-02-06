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
        'id_pasien'
    ];

    protected $table = 'public.foto_pasien';

    protected $id = 'id';
}
