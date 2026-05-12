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
            $table->foreignId('pessoa_fisica_id')->constrained('pessoa_fisicas');
            $table->foreignId('pessoa_juridica_id')->constrained('pessoa_juridicas');
            
            // Status: 'ativa', 'inativa'
            $table->enum('status', ['ativa', 'inativa'])->default('ativa');
            
            $table->timestamp('data_matricula')->useCurrent();
            $table->timestamp('data_cancelamento')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('matriculas');
    }
};
