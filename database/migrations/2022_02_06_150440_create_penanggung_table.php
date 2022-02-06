<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenanggungTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penanggung', function (Blueprint $table) {
            $table->id();
            $table->string('nama_penanggung', 10);
            $table->string('nomor_kartu', 30);
            $table->integer('pasien_id', false, false);
            $table->text('foto_kartu_penanggung');
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
        Schema::dropIfExists('penanggung');
    }
}
