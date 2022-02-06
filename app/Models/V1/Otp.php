<?php

namespace App\Models\V1;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Otp extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'kode_otp',
        'expired_time'
    ];

    protected $table = 'public.otp';

    protected $primaryKey = 'id';

    public static function cariKodeOtpEmail($email)
    {
        $query = DB::raw("
        select u.email , o.kode_otp 
        from users u 
        join otp o on u.email = o.email 
        ");
        $data = DB::select($query);
        return $data;
    }

    protected $id = 'id';
}
