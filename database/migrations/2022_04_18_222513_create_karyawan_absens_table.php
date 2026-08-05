<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKaryawanAbsensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_karyawan_absen', function (Blueprint $table) {
            $table->id();
            $table->integer('karyawan_id');
            $table->date('tanggal_mulai');
            $table->integer('durasi');
            $table->string('status');
            $table->string('jenis_absen');
            $table->string('gambar')->nullable();
            $table->text('keperluan')->nullable();
            $table->date('tgl_persetujuan')->nullable();
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
        Schema::dropIfExists('tb_karyawan_absen');
    }
}
