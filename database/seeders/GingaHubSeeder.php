<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Endereco;
use App\Models\Pessoa;
use App\Models\PessoaFisica;
use App\Models\PessoaJuridica;
use Illuminate\Support\Facades\Hash;

class GingaHubSeeder extends Seeder
{
    public function run(): void
    {
        // 1. CRIAR UMA ACADEMIA DE TESTE
        $endAcademia = Endereco::create([
            'cep' => '29700-000', 'logradouro' => 'Rua das Acacias', 'numero' => '100',
            'bairro' => 'Centro', 'cidade' => 'Colatina', 'estado' => 'ES'
        ]);

        $pessoaAcademia = Pessoa::create([
            'nome' => 'Ricardo Gestor',
            'email' => 'academia@teste.com',
            'senha' => Hash::make('123456'),
            'contato' => '2799999999',
            'endereco_id' => $endAcademia->id
        ]);

        $academia = PessoaJuridica::create([
            'nome_fantasia' => 'Ginga Fit Unidade 1',
            'razao_social' => 'Ginga Atividades Fisicas LTDA',
            'cnpj' => '12345678000100',
            'pessoa_id' => $pessoaAcademia->id
        ]);

        // 2. CRIAR UM ALUNO DE TESTE
        $endAluno = Endereco::create([
            'cep' => '29700-111', 'logradouro' => 'Av Central', 'numero' => '50',
            'bairro' => 'Bela Vista', 'cidade' => 'Colatina', 'estado' => 'ES'
        ]);

        $pessoaAluno = Pessoa::create([
            'nome' => 'Bernardo',
            'email' => 'aluno@teste.com',
            'senha' => Hash::make('123456'),
            'contato' => '2788888888',
            'endereco_id' => $endAluno->id
        ]);

        $aluno = PessoaFisica::create([
            'sobrenome' => 'Schmiedel',
            'cpf' => '12345678901',
            'role' => 'aluno',
            'pessoa_id' => $pessoaAluno->id
        ]);

        // 3. REALIZAR A MATRÍCULA AUTOMÁTICA
        $aluno->academias()->attach($academia->id);
    }
}