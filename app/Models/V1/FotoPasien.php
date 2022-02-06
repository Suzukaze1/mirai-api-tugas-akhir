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
        'pasien_id'
    ];

    protected $table = 'public.foto_pasien';
}
