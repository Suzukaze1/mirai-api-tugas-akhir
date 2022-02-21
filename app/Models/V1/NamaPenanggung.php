<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NamaPenanggung extends Model
{
    use HasFactory;

    protected $table = 'mirai_pasien.dm_nama_penanggung';

    protected $id = 'kode';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama'
    ];
}
