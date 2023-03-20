<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CeteleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'gizli_numara' => 'nullable|boolean',
            'arayan_numara' => 'nullable|string',
            'arama_tarihi' => 'nullable|date',
            'anonim_numara' => 'nullable|boolean',
            'arayan_adsoyad' => 'nullable|string',
            'arayan_sehir' => 'nullable|string',
            'arayan_ulke' => 'nullable|string',
            'arayan_kimin_icin' => 'nullable|string',
            'ne_yapildi' => 'nullable|string',
            'yonlendirilen_kurumlar' => 'nullable|string',
            'mc_nereden_duydu' => 'nullable|string',
            'cetele_notlari' => 'nullable|string',

        ];
    }


}
