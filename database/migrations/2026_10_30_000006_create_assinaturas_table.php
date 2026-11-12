<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assinaturas', function (Blueprint $table) {
            $table->id();

            // Ligada à matrícula: ao inativar a matrícula, as assinaturas são inativadas junto
            $table->foreignId('matricula_id')->constrained('matriculas');
            $table->foreignId('plano_id')->constrained('planos');
            $table->foreignId('pedido_id')->constrained('pedidos');

            // Valor no momento da compra (o plano pode mudar de preço depois)
            $table->decimal('valor', 8, 2);

            // Status: 'ativa', 'inativa' (mesmo padrão da matrícula)
            $table->enum('status', ['ativa', 'inativa'])->default('ativa');

            $table->timestamp('data_inicio')->useCurrent();
            $table->timestamp('data_cancelamento')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assinaturas');
    }
};
