<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_histories', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();          // ID sesi unik per pengguna
            $table->string('dinas')->default('Dinas Dukcapil'); // Dinas yang dipilih
            $table->text('question');                        // Pertanyaan dari warga
            $table->longText('answer');                      // Jawaban dari Agent AI
            $table->string('ip_address')->nullable();        // IP pengirim (audit)
            $table->timestamps();                            // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_histories');
    }
};
