<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
    use HasFactory;

    protected $table = 'mirai_pasien.dm_poli';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'foto',
        'detail'
    ];

    public function polis()
    {
        return $this->hasMany(Dokter::class, 'id_poli', 'id');
    }
}
