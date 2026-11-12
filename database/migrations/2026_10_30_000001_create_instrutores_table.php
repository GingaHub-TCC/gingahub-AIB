<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instrutores', function (Blueprint $table) {
            $table->id();

            // Cada instrutor pertence a uma academia
            $table->foreignId('pessoa_juridica_id')->constrained('pessoa_juridicas');

            $table->string('nome', 100);
            $table->string('graduacao', 45)->nullable(); // Ex: Mestre, Contramestre, Faixa Preta
            $table->string('contato', 45)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instrutores');
    }
};
