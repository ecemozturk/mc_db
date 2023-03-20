<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cetele extends Model
{
    use HasFactory;



 protected $fillable = [
     'gizli_numara', 'arayan_numara', 'arama_tarihi', 'anonim_numara', 'arayan_adsoyad', 'arayan_sehir', 'arayan_ulke', 'arayan_kimin_icin', 'ne_yapildi', 'yonlendirilen_kurumlar', 'mc_nereden_duydu', 'cetele_notlari'
 ];




    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');

    }


}
