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
            $table->string('nis', 30)->unique();
            $table->string('email', 150)->unique();
            $table->string('password', 255);
            $table->string('role', 20); // enum: admin, bendahara, santri
            $table->string('status', 20)->default('active'); // enum: active, inactive
            $table->timestamp('email_verified_at')->nullable();
            $table->string('remember_token', 100)->nullable();

            $table->string('nama_santri', 150);
            $table->string('nama_orang_tua', 150);
            $table->string('no_telp', 20);

            $table->string('tingkatan', 50);
            $table->string('kelas', 20);

            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->string('jenis_kelamin', 2); // enum: L, P
            $table->text('alamat');
            $table->string('tingkatan_ngaji', 50);

            $table->timestamps();
            $table->softDeletes(); // deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}