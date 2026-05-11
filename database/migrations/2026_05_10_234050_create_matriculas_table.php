<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matriculas', function (Blueprint $table) {
            $table->id();
            
            // FK para o Aluno (Pessoa Física)
            $table->foreignId('pessoa_fisica_id')->constrained('pessoa_fisicas')->onDelete('cascade');
            
            // FK para a Academia (Pessoa Jurídica)
            $table->foreignId('pessoa_juridica_id')->constrained('pessoa_juridicas')->onDelete('cascade');
            
            // Status da matrícula (Ativa, Trancada, Cancelada)
            $table->string('status', 20)->default('ativa');

            // Impede que o aluno se matricule 2x na mesma academia:
            $table->unique(['pessoa_fisica_id', 'pessoa_juridica_id']);

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('matriculas');
    }
};
