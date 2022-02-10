<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasienTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pasien', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10);
            $table->string('agama_kode', 10);
            $table->string('pendidikan_kode', 10);
            $table->string('pekerjaan_kode', 10);
            $table->string('kewarganegaraan_kode', 10);
            $table->string('jenis_identitas_kode', 10);
            $table->string('suku_kode', 10);
            $table->string('no_identitas', 50);
            $table->string('nama', 150);
            $table->string('ayah_nama', 150);
            $table->string('ibu_nama', 150);
            $table->string('nama_pasangan', 150)->nullable();
            $table->string('tempat_lahir', 255);
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->char('jkel', 1);
            $table->string('no_telp', 50)->nullable();
            $table->text('alergi')->nullable();
            $table->string('status_perkawinan', 10);
            $table->string('kedudukan_keluarga', 50)->nullable();
            $table->string('golongan_darah', 50)->nullable();
            $table->string('provinsi', 10);
            $table->string('kabupaten', 10);
            $table->string('kecamatan', 10);
            $table->string('umur', 10)->nullable();
            $table->string('anak_ke', 10)->nullable();
            $table->string('jurusan', 10)->nullable();
            $table->string('nama_tempat_bekerja', 50);
            $table->string('alamat_tempat_bekerja', 50);
            $table->string('no_rekam_medik_ayah', 10)->nullable();
            $table->string('no_rekam_medik_ibu', 10)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pasien');
    }
}
