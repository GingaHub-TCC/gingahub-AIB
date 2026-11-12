<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treinos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pessoa_juridica_id')->constrained('pessoa_juridicas');

            $table->string('tema', 100); // Ex: Capoeira Angola, Jiu-Jitsu Kids, Muay Thai
            $table->string('descricao', 255)->nullable();

            $table->foreignId('instrutor_id')->constrained('instrutores');
            $table->foreignId('local_id')->constrained('locais');

            // 0 = Domingo ... 6 = Sábado (mesmo padrão do Carbon)
            $table->unsignedTinyInteger('dia_semana');
            $table->time('hora_inicio');
            $table->time('hora_fim');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treinos');
    }
};
