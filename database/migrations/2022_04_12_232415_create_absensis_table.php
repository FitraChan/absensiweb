<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_absensi', function (Blueprint $table) {
            $table->id();
            $table->integer('jam_kerja_id')->nullable();
            $table->integer('karyawan_id')->nullable();
            $table->date('tanggal')->nullable();
            $table->timestamp('jam_masuk')->nullable();
            $table->timestamp('jam_pulang')->nullable();
            $table->text('masuk_via')->nullable();
            $table->text('pulang_via')->nullable();
            $table->string('url_masuk')->nullable();
            $table->string('url_keluar')->nullable();
            $table->string('posisi_masuk')->nullable();
            $table->string('posisi_pulang')->nullable();
            $table->double('jarak_masuk')->nullable();
            $table->double('jarak_pulang')->nullable();
            $table->text('keterangan_masuk')->nullable();
            $table->text('keterangan_pulang')->nullable();
            $table->string('status_absensi')->nullable();
            $table->text('keterangan')->nullable();
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
        Schema::dropIfExists('tb_absensi');
    }
}
