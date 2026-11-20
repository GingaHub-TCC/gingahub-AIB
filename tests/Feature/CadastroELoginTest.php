<?php

namespace Tests\Feature;

use App\Models\Endereco;
use App\Models\Pessoa;
use App\Models\PessoaFisica;
use App\Models\PessoaJuridica;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cadastros de aluno e academia (endereço, telefone, UF, sobrenome, contato)
 * e limite de tentativas de login. Não usa o seeder: cada teste cria o que precisa.
 */
class CadastroELoginTest extends TestCase
{
    use RefreshDatabase;

    private function dadosAluno(array $extra = []): array
    {
        return array_merge([
            'nome' => 'Maria',
            'sobrenome' => 'Silva',
            'cpf' => '529.982.247-25',
            'contato' => '(27) 99999-9999',
            'email' => 'maria.teste@example.com',
            'senha' => 'segredo1',
            'senha_confirmation' => 'segredo1',
            'cep' => '29000-000',
            'logradouro' => 'Rua das Flores',
            'numero' => '100',
            'bairro' => 'Centro',
            'cidade' => 'Vitória',
            'estado' => 'ES',
        ], $extra);
    }

    private function dadosAcademia(array $extra = []): array
    {
        return array_merge([
            'nome' => 'Academia Teste',
            'email' => 'academia.cadastro@example.com',
            'senha' => 'segredo1',
            'senha_confirmation' => 'segredo1',
            'cnpj' => '11.222.333/0001-81',
            'razao_social' => 'Academia Teste LTDA',
            'nome_fantasia' => 'Academia Teste',
            'contato' => '(27) 3333-4444',
            'cep' => '29000-000',
            'logradouro' => 'Rua das Flores',
            'numero' => '200',
            'bairro' => 'Centro',
            'cidade' => 'Vitória',
            'estado' => 'ES',
        ], $extra);
    }

    // ---------- CADASTRO DE ALUNO ----------

    public function test_cadastro_de_aluno_com_endereco_completo_funciona() {
        $this->post('/cadastro-aluno', $this->dadosAluno())
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $pessoa = Pessoa::where('email', 'maria.teste@example.com')->first();
        $this->assertNotNull($pessoa);
        $this->assertSame('27999999999', $pessoa->contato);
        $this->assertSame('29000000', $pessoa->endereco->cep);
        $this->assertSame('aluno', PessoaFisica::where('pessoa_id', $pessoa->id)->first()->role);
    }

    public function test_cadastro_de_aluno_sem_cep_e_recusado() {
        $this->post('/cadastro-aluno', $this->dadosAluno(['cep' => '']))
            ->assertSessionHasErrors('cep');

        $this->assertDatabaseCount('pessoas', 0);
    }

    public function test_telefone_com_e_sem_mascara_e_aceito() {
        $this->post('/cadastro-aluno', $this->dadosAluno(['contato' => '(27) 99999-9999']))
            ->assertSessionHasNoErrors();

        $this->post('/cadastro-aluno', $this->dadosAluno([
            'email' => 'outra.pessoa@example.com',
            'cpf' => '111.444.777-35',
            'contato' => '27988887777',
        ]))->assertSessionHasNoErrors();

        $this->assertSame(2, Pessoa::count());
        $this->assertSame('27988887777', Pessoa::where('email', 'outra.pessoa@example.com')->value('contato'));
    }

    public function test_telefone_invalido_e_recusado() {
        $this->post('/cadastro-aluno', $this->dadosAluno(['contato' => '12345']))
            ->assertSessionHasErrors('contato');

        $this->post('/cadastro-aluno', $this->dadosAluno(['contato' => 'telefone']))
            ->assertSessionHasErrors('contato');

        $this->assertDatabaseCount('pessoas', 0);
    }

    public function test_sobrenome_vazio_e_recusado() {
        $this->post('/cadastro-aluno', $this->dadosAluno(['sobrenome' => '']))
            ->assertSessionHasErrors('sobrenome');

        $this->assertDatabaseCount('pessoas', 0);
    }

    public function test_sobrenome_acima_de_45_caracteres_e_recusado() {
        $this->post('/cadastro-aluno', $this->dadosAluno(['sobrenome' => str_repeat('a', 46)]))
            ->assertSessionHasErrors('sobrenome');
    }

    public function test_uf_minuscula_do_aluno_e_gravada_em_maiuscula() {
        $this->post('/cadastro-aluno', $this->dadosAluno(['estado' => 'es']))
            ->assertSessionHasNoErrors();

        $this->assertSame('ES', Endereco::first()->estado);
    }

    // ---------- CADASTRO DE ACADEMIA ----------

    public function test_cadastro_de_academia_funciona() {
        $this->post('/cadastro-academia', $this->dadosAcademia())
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $this->assertSame('11222333000181', PessoaJuridica::first()->cnpj);
    }

    public function test_cadastro_de_academia_exige_contato() {
        $this->post('/cadastro-academia', $this->dadosAcademia(['contato' => '']))
            ->assertSessionHasErrors('contato');

        $this->assertDatabaseCount('pessoas', 0);
    }

    public function test_uf_minuscula_da_academia_e_gravada_em_maiuscula() {
        $this->post('/cadastro-academia', $this->dadosAcademia(['estado' => 'rj']))
            ->assertSessionHasNoErrors();

        $this->assertSame('RJ', Endereco::first()->estado);
    }

    // ---------- FORMATO DE CEP E TELEFONE (RF01 e RF02) ----------

    public function test_cep_com_formato_errado_e_recusado() {
        foreach (['2900-0000', '2900000', '290000000', '29.000-000', '29000 000', 'abcde-fgh'] as $cep) {
            $this->post('/cadastro-aluno', $this->dadosAluno(['cep' => $cep]))
                ->assertSessionHasErrors('cep');
            $this->post('/cadastro-academia', $this->dadosAcademia(['cep' => $cep]))
                ->assertSessionHasErrors('cep');
        }

        $this->assertDatabaseCount('pessoas', 0);
    }

    public function test_cep_com_e_sem_hifen_e_aceito() {
        $this->post('/cadastro-aluno', $this->dadosAluno(['cep' => '29000000']))
            ->assertSessionHasNoErrors();
        $this->post('/cadastro-academia', $this->dadosAcademia(['cep' => '29000-000']))
            ->assertSessionHasNoErrors();

        $this->assertSame(2, Pessoa::count());
    }

    public function test_telefone_com_formato_errado_e_recusado() {
        foreach (['(27)99999-9999', '27 99999-9999', '2799999-9999', '(27 99999-9999', '27) 99999-9999', '(27) 999999999', '279999999999', '279999999'] as $telefone) {
            $this->post('/cadastro-aluno', $this->dadosAluno(['contato' => $telefone]))
                ->assertSessionHasErrors('contato');
            $this->post('/cadastro-academia', $this->dadosAcademia(['contato' => $telefone]))
                ->assertSessionHasErrors('contato');
        }

        $this->assertDatabaseCount('pessoas', 0);
    }

    public function test_telefone_nos_formatos_do_documento_e_aceito_na_academia() {
        $this->post('/cadastro-academia', $this->dadosAcademia(['contato' => '2733334444']))
            ->assertSessionHasNoErrors();

        $this->assertSame('2733334444', Pessoa::first()->contato);
    }

    // ---------- LOGIN SEM LIMITE DE TENTATIVAS ----------

    public function test_dez_tentativas_de_login_erradas_seguidas_nao_geram_429() {
        for ($i = 0; $i < 10; $i++) {
            $this->post('/login', ['email' => 'ninguem@example.com', 'senha' => 'errada'])
                ->assertStatus(302) // nunca 429
                ->assertSessionHasErrors(['email' => 'Credenciais inválidas.']); // mensagem genérica mantida
        }
    }

    // ---------- PÁGINAS DE ERRO ----------

    public function test_pagina_404_em_portugues() {
        $this->get('/rota-que-nao-existe')
            ->assertStatus(404)
            ->assertSee('Página não encontrada');
    }
}
