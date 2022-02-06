<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penanggung extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_penanggung',
        'nomor_kartu',
        'foto_kartu_penanggung',
        'pasien_id'
    ];

    protected $table = 'public.penanggung';

    protected $id = 'id';
}
