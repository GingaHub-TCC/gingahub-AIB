<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pessoa_juridica_id')->constrained('pessoa_juridicas');

            $table->string('nome', 100);
            $table->string('descricao', 255)->nullable();
            $table->decimal('valor', 8, 2);
            $table->enum('periodicidade', ['mensal', 'trimestral', 'semestral', 'anual'])->default('mensal');

            // Plano desativado some da vitrine, mas assinaturas antigas continuam no histórico
            $table->boolean('ativo')->default(true);

            $table->timestamps();
        });

        // Quais treinos cada plano permite praticar (N:N)
        Schema::create('plano_treino', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plano_id')->constrained('planos')->cascadeOnDelete();
            $table->foreignId('treino_id')->constrained('treinos')->cascadeOnDelete();
            $table->unique(['plano_id', 'treino_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plano_treino');
        Schema::dropIfExists('planos');
    }
};
