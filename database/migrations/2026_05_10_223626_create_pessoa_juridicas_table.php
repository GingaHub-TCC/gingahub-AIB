<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pessoa_juridicas', function (Blueprint $table) {
            $table->id();
            
            $table->string('nome_fantasia', 100);
            $table->string('razao_social', 100)->unique();
            $table->string('cnpj', 14)->unique();

            // CHAVE ESTRANGEIRA: Liga ao ID da tabela pessoas
            $table->foreignId('pessoa_id')->constrained('pessoas');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pessoa_juridicas');
    }
};
