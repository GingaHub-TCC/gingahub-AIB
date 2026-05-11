<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enderecos', function (Blueprint $table) {
            $table->id(); // Cria o BIGINT PK chamado 'id'

            // string(nome, tamanho_maximo)
            $table->string('cep', 9); 
            $table->string('logradouro', 150); // Rua, Avenida, etc.
            $table->string('numero', 20);
            $table->string('complemento', 100)->nullable();
            $table->string('bairro', 100);
            $table->string('cidade', 100);
            $table->string('estado', 2); // UF (ex: SP, ES, RJ)

            $table->timestamps(); // created_at e updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enderecos');
    }
};