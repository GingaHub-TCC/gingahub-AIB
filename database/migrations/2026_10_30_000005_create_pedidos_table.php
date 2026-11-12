<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();

            // O pedido é feito dentro de uma matrícula (aluno + academia)
            $table->foreignId('matricula_id')->constrained('matriculas');

            $table->decimal('valor_total', 10, 2);

            // Pagamento simulado: ao concluir, o pedido já nasce como 'pago'
            $table->enum('status', ['pago', 'cancelado'])->default('pago');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
