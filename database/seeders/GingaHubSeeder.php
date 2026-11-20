<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assinatura;
use App\Models\Endereco;
use App\Models\Matricula;
use App\Models\Pedido;
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
            'cep' => '29700000', 'logradouro' => 'Rua das Acacias', 'numero' => '100',
            'bairro' => 'Centro', 'cidade' => 'Colatina', 'estado' => 'ES'
        ]);

        $pessoaAcademia = Pessoa::create([
            'nome' => 'Ricardo Gestor',
            'email' => 'academia@teste.com',
            'senha' => Hash::make('123456'),
            'contato' => '2799999999',
            'endereco_id' => $endAcademia->id,
        ]);

        $academia = PessoaJuridica::create([
            'nome_fantasia' => 'Ginga Fit Unidade 1',
            'razao_social' => 'Ginga Atividades Fisicas LTDA',
            'cnpj' => '12345678000100',
            'pessoa_id' => $pessoaAcademia->id
        ]);

        // 2. CRIAR UM ALUNO DE TESTE
        $endAluno = Endereco::create([
            'cep' => '29700111', 'logradouro' => 'Av Central', 'numero' => '50',
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

        // 4. ESTRUTURA DA ACADEMIA: locais, instrutores, treinos e planos
        $sede = $academia->locais()->make(['nome' => 'Sede Centro']);
        $sede->endereco_id = $endAcademia->id;
        $sede->save();

        $praca = $academia->locais()->make(['nome' => 'Praça Municipal']);
        $praca->endereco_id = Endereco::create([
            'cep' => '29700010', 'logradouro' => 'Praça Municipal', 'numero' => 's/n',
            'bairro' => 'Centro', 'cidade' => 'Colatina', 'estado' => 'ES'
        ])->id;
        $praca->save();

        $professor = $academia->instrutores()->create(['nome' => 'Professor Carlos', 'graduacao' => 'Faixa Preta', 'contato' => '27991112222']);
        $prof = $academia->instrutores()->create(['nome' => 'Professora Ana', 'graduacao' => 'Faixa Marrom', 'contato' => '27993334444']);

        $treinos = [];
        foreach ([
            ['Muay Thai', $professor, $sede, 1, '19:00', '20:30'],
            ['Muay Thai', $professor, $sede, 3, '19:00', '20:30'],
            ['Kickboxing', $prof, $sede, 2, '18:30', '20:00'],
            ['Karatê Kids', $prof, $sede, 4, '17:00', '18:00'],
            ['Treino de demonstração', $professor, $praca, 6, '09:00', '11:00'],
        ] as [$tema, $instrutor, $local, $dia, $inicio, $fim]) {
            $treinos[] = $academia->treinos()->create([
                'tema' => $tema, 'instrutor_id' => $instrutor->id, 'local_id' => $local->id,
                'dia_semana' => $dia, 'hora_inicio' => $inicio, 'hora_fim' => $fim,
            ]);
        }

        $muayThai = $academia->planos()->create(['nome' => 'Muay Thai 2x', 'descricao' => 'Duas aulas de Muay Thai por semana', 'valor' => 120, 'periodicidade' => 'mensal']);
        $muayThai->treinos()->sync([$treinos[0]->id, $treinos[1]->id]);

        $completo = $academia->planos()->create(['nome' => 'Completo', 'descricao' => 'Todas as aulas adultas e a turma de sábado', 'valor' => 189.90, 'periodicidade' => 'mensal']);
        $completo->treinos()->sync([$treinos[0]->id, $treinos[1]->id, $treinos[2]->id, $treinos[4]->id]);

        $kids = $academia->planos()->create(['nome' => 'Kids', 'descricao' => 'Para crianças de 6 a 12 anos', 'valor' => 90, 'periodicidade' => 'mensal']);
        $kids->treinos()->sync([$treinos[3]->id]);

        $sabado = $academia->planos()->create(['nome' => 'Turma de sábado', 'valor' => 240, 'periodicidade' => 'semestral']);
        $sabado->treinos()->sync([$treinos[4]->id]);

        // 5. PEDIDO JÁ CONCLUÍDO: o aluno de teste assina o plano Muay Thai 2x
        $matricula = Matricula::where('pessoa_fisica_id', $aluno->id)->where('pessoa_juridica_id', $academia->id)->first();
        $pedido = Pedido::create(['matricula_id' => $matricula->id, 'valor_total' => $muayThai->valor, 'status' => 'pago']);
        Assinatura::create(['matricula_id' => $matricula->id, 'plano_id' => $muayThai->id, 'pedido_id' => $pedido->id, 'valor' => $muayThai->valor, 'status' => 'ativa']);

        // 6. SEGUNDA ACADEMIA (outra modalidade) para testar várias matrículas
        $endDojo = Endereco::create([
            'cep' => '29700200', 'logradouro' => 'Rua Sete de Setembro', 'numero' => '321',
            'bairro' => 'Esplanada', 'cidade' => 'Colatina', 'estado' => 'ES'
        ]);

        $dojo = PessoaJuridica::create([
            'nome_fantasia' => 'Dojo Sol Nascente',
            'razao_social' => 'Sol Nascente Artes Marciais LTDA',
            'cnpj' => '98765432000199',
            'pessoa_id' => Pessoa::create([
                'nome' => 'Kenji Gestor', 'email' => 'dojo@teste.com', 'senha' => Hash::make('123456'),
                'contato' => '2733334444', 'endereco_id' => $endDojo->id,
            ])->id,
        ]);

        $tatame = $dojo->locais()->make(['nome' => 'Tatame Principal']);
        $tatame->endereco_id = $endDojo->id;
        $tatame->save();

        $sensei = $dojo->instrutores()->create(['nome' => 'Sensei Kenji', 'graduacao' => 'Faixa Preta 3º Dan']);

        $jiu1 = $dojo->treinos()->create(['tema' => 'Jiu-Jitsu', 'instrutor_id' => $sensei->id, 'local_id' => $tatame->id, 'dia_semana' => 2, 'hora_inicio' => '20:00', 'hora_fim' => '21:30']);
        $jiu2 = $dojo->treinos()->create(['tema' => 'Jiu-Jitsu', 'instrutor_id' => $sensei->id, 'local_id' => $tatame->id, 'dia_semana' => 4, 'hora_inicio' => '20:00', 'hora_fim' => '21:30']);
        $judo = $dojo->treinos()->create(['tema' => 'Judô', 'instrutor_id' => $sensei->id, 'local_id' => $tatame->id, 'dia_semana' => 5, 'hora_inicio' => '19:00', 'hora_fim' => '20:00']);

        $dojo->planos()->create(['nome' => 'Jiu-Jitsu Mensal', 'valor' => 150, 'periodicidade' => 'mensal'])->treinos()->sync([$jiu1->id, $jiu2->id]);
        $dojo->planos()->create(['nome' => 'Judô Mensal', 'valor' => 110, 'periodicidade' => 'mensal'])->treinos()->sync([$judo->id]);
        $dojo->planos()->create(['nome' => 'Combo Anual', 'descricao' => 'Jiu-Jitsu + Judô com desconto', 'valor' => 2400, 'periodicidade' => 'anual'])->treinos()->sync([$jiu1->id, $jiu2->id, $judo->id]);

        // 7. ALUNO NOVO (sem matrícula), para testar o fluxo do zero
        $pessoaNovo = Pessoa::create([
            'nome' => 'Isabela', 'email' => 'aluno2@teste.com', 'senha' => Hash::make('123456'),
            'contato' => '27977776666', 'endereco_id' => $endAluno->id,
        ]);
        PessoaFisica::create(['sobrenome' => 'Garcia', 'cpf' => '98765432100', 'role' => 'aluno', 'pessoa_id' => $pessoaNovo->id]);
    }
}
