<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
class KurumController extends Controller
{
    public function index()
    {
        $kurumlar = [
            [
                'id' => 1,
                'name' => 'Aile ve Sosyal Hizmetler Bakanlığı',
                'children' => [
                    ['id' => '1_1', 'name' => 'ŞÖNİM'],
                    ['id' => '1_2', 'name' => 'Sığınak'],
                    ['id' => '1_3', 'name' => 'Sosyal Hizmet Merkezi'],
                    ['id' => '1_4', 'name' => 'Alo 183'],
                    ['id' => '1_5', 'name' => 'Diğer'],
                ],
            ],
            [
                'id' => 2,
                'name' => 'Kolluk',
                'children' => [
                    ['id' => '2_1', 'name' => 'Polis'],
                    ['id' => '2_2', 'name' => 'Jandarma'],
                    ['id' => '2_3', 'name' => 'KADES']
                ]
            ],
            [
                'id' => 3,
                'name' => 'Savcılık'
            ],
            [
                'id' => 4,
                'name' => 'Mahkeme',
                'children' => [
                    ['id' => '4_1', 'name' => 'Aile Mahkemesi'],
                    ['id' => '4_2', 'name' => 'Ceza Mahkemesi'],
                    ['id' => '4_3', 'name' => 'Diğer']
                ]
            ],
            [
                'id' => 5,
                'name' => 'CİMER'
            ],
            [
                'id' => 6,
                'name' => 'Avukat Yardımı',
                'children' => [
                    ['id' => '6_1', 'name' => 'Baro'],
                    ['id' => '6_2', 'name' => 'Ücretli avukat']
                ]
            ],
            [
                'id' => 7,
                'name' => 'Adli Tıp'
            ],
            [
                'id' => 8,
                'name' => 'Sağlık Kurumu',
                'children' => [
                    ['id' => '8_1', 'name' => 'İlk basamak'],
                    ['id' => '8_2', 'name' => 'İkinci basamak']
                ]
            ],
            [
                'id' => 9,
                'name' => 'Belediye'
            ],
            [
                'id' => 10,
                'name' => 'Valilik/Kaymakamlık',
                'children' => [
                    ['id' => '10_1', 'name' => 'Sosyal Yardımlaşma ve Dayanışma Vakfı'],
                    ['id' => '10_2', 'name' => 'Diğer']
                ]
            ],
            [
                'id' => 11,
                'name' => 'Muhtarlık'
            ],
            [
                'id' => 12,
                'name' => 'Eğitim Kurumu'
            ],
            [
                'id' => 13,
                'name' => 'Kadın Örgütü'
            ],
            [
                'id' => 13,
                'name' => 'Kadın Örgütü'
            ],
            [
                'id' => 14,
                'name' => 'Sivil Toplum Kuruluşu'
            ],
            [
                'id' => 15,
                'name' => 'İŞKUR'
            ],
            [
                'id' => 16,
                'name' => 'Diğer'
            ],
        ];

        return response()->json($kurumlar);
    }
}



