<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Uma ficha por pessoa: impede que a mesma Pessoa tenha duas fichas físicas
// ou duas fichas jurídicas. (Ter uma de cada tipo continua sendo barrado só pela aplicação.)
// ATENÇÃO: falha se já existir pessoa_id repetido nas tabelas; confira os dados antes de rodar.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pessoa_fisicas', function (Blueprint $table) {
            $table->unique('pessoa_id');
        });

        Schema::table('pessoa_juridicas', function (Blueprint $table) {
            $table->unique('pessoa_id');
        });
    }

    public function down(): void
    {
        Schema::table('pessoa_fisicas', function (Blueprint $table) {
            $table->dropUnique(['pessoa_id']);
        });

        Schema::table('pessoa_juridicas', function (Blueprint $table) {
            $table->dropUnique(['pessoa_id']);
        });
    }
};
