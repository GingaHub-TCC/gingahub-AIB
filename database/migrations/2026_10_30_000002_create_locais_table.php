<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locais', function (Blueprint $table) {
            $table->id();

            // Cada local (unidade, sala, praça...) pertence a uma academia
            $table->foreignId('pessoa_juridica_id')->constrained('pessoa_juridicas');

            $table->string('nome', 100); // Ex: Sede Centro, Sala 2, Praça da Matriz

            // Reaproveita a tabela de endereços já usada por Pessoa
            $table->foreignId('endereco_id')->constrained('enderecos');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locais');
    }
};
