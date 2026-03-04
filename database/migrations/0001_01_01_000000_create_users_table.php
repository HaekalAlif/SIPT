<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{

    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nis', 30)->unique()->nullable(); 
            $table->string('email', 150)->unique();
            $table->string('password', 255);
            $table->string('role', 20);
            $table->string('status', 20)->default('active');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('remember_token', 100)->nullable();

            $table->string('nama_santri', 150);
            $table->string('nama_orang_tua', 150)->nullable();
            $table->string('no_telp', 20)->nullable();

            $table->string('tingkatan', 50)->nullable();
            $table->string('kelas', 20)->nullable();

            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin', 2)->nullable();
            $table->text('alamat')->nullable();
            $table->string('tingkatan_ngaji', 50)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}