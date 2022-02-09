<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KedudukanKeluarga extends Model
{
    use HasFactory;

    protected $table = 'mirai_pasien.dm_kedudukan_keluarga';

    protected $id = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama'
    ];
}
