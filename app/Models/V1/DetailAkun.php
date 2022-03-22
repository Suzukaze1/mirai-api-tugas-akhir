<?php

namespace App\Models\V1;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'is_lama',
        'is_anggota'
    ];

    protected $table = 'mirai_pasien.detail_akun';

    public function user(){
    	return $this->belongsTo(User::class, 'id_akun');
    }

    public static function getAnggotaInduk($id_pasien, $id_akun)
    {
        $query = DB::raw("
        select * from mirai_pasien.detail_akun md
        where (md.id_pasien != '$id_pasien' or md.id_pasien is null)
        and md.id_akun = '$id_akun'
        ");
        $data = DB::select($query);
        return $data;
    }
}
