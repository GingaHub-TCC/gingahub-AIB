<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pessoas', function (Blueprint $table) {
            $table->id(); // Cria o BIGINT PK chamado 'id'

            // Campos traduzidos do seu diagrama
            $table->string('nome', 45);
            $table->string('email', 45)->unique();
            $table->string('senha', 255); // 255 para garantir o hash de segurança
            $table->string('contato', 45)->nullable();

            // CHAVE ESTRANGEIRA: Liga a pessoa ao endereço (Endereco_idEndereco do seu print)
            // O Laravel cria o campo INT e já trava a relação com a tabela 'enderecos'
            $table->foreignId('endereco_id')->constrained('enderecos')->onDelete('cascade');

            $table->rememberToken(); // "Lembrar-me" do login

            // Substitui o pessoaCreateDate pelo padrão de auditoria do Laravel
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pessoas');
    }
};