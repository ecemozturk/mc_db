<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ceteles', function (Blueprint $table) {
            $table->id();
            $table->boolean('gizli_numara');
            $table->string('arayan_numara');
            $table->dateTime('arama_tarihi');
            $table->boolean('anonim_numara');
            $table->string('arayan_adsoyad');
            $table->string('arayan_sehir');
            $table->string('arayan_ulke');
            $table->string('arayan_kimin_icin');
            $table->string('ne_yapildi');
            $table->string('yonlendirilen_kurumlar');
            $table->string('mc_nereden_duydu');
            $table->text('celete_notlari')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ceteles');
    }
};
