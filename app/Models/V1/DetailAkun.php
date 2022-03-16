<?php

namespace App\Models\V1;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailAkun extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_akun',
        'id_pasien',
        'id_pasien_temp',
        'is_lama'
    ];

    protected $table = 'mirai_pasien.detail_akun';

    public function user(){
    	return $this->belongsTo(User::class, 'id_akun');
    }
}
