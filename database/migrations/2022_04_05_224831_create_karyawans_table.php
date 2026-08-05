<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKaryawansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_karyawan', function (Blueprint $table) {
            $table->id();
            $table->integer('agama_id')->nullable();
            $table->integer('jabatan_id')->nullable();
            $table->integer('departement_id')->nullable();
            $table->integer('pendidikan_id')->nullable();
            $table->string('no_karyawan')->nullable();
            $table->string('nama_lengkap')->nullable();
            $table->string('nik')->nullable();
            $table->string('no_kk')->nullable();
            $table->string('npwp')->nullable();
            $table->string('bpjs_kesehatan')->nullable();
            $table->string('bpjs_tenaker')->nullable();
            $table->string('jk')->nullable();
            $table->string('gd')->nullable();
            $table->string('tmp_lahir')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('sts_nikah')->nullable();
            $table->text('alamat_asal')->nullable();
            $table->text('alamat_domisili')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('sts_karyawan')->nullable();
            $table->string('sts_kerja')->nullable();
            $table->date('tgl_masuk')->nullable();
            $table->string('no_rek')->nullable();
            $table->string('bank')->nullable();
            $table->date('tgl_resign')->nullable();
            $table->string('jabatan_skr')->nullable();
            $table->string('hak_akses')->nullable();
            $table->string('foto')->nullable();
            $table->string('email')->unique();
            $table->string('password');
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
        Schema::dropIfExists('tb_karyawan');
    }
}
