<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pessoa_fisicas', function (Blueprint $table) {
            $table->id();
            
            $table->string('sobrenome', 45);
            $table->string('cpf', 11)->unique();
            $table->char('sexo', 1)->nullable();
            $table->date('data_nascimento')->nullable();
            
            // ATRIBUTO ADICIONADO: Define o nível de acesso/papel do usuário
            $table->string('role', 45); 

            // CHAVE ESTRANGEIRA
            $table->foreignId('pessoa_id')->constrained('pessoas');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pessoa_fisicas');
    }
};