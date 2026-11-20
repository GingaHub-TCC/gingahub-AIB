<?php

namespace Tests\Feature;

use App\Models\Pessoa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Login real (sucesso, credenciais inválidas, conta desativada), logout,
 * recuperação de senha simulada e lista de academias.
 * Não usa o seeder: cada teste cria o que precisa pelos formulários de cadastro.
 */
class LoginESenhaTest extends TestCase
{
    use RefreshDatabase;

    private function cadastrarAluno(): Pessoa
    {
        $this->post('/cadastro-aluno', [
            'nome' => 'Maria',
            'sobrenome' => 'Silva',
            'cpf' => '529.982.247-25',
            'contato' => '(27) 99999-9999',
            'email' => 'maria.login@example.com',
            'senha' => 'segredo1',
            'senha_confirmation' => 'segredo1',
            'cep' => '29000-000',
            'logradouro' => 'Rua das Flores',
            'numero' => '100',
            'bairro' => 'Centro',
            'cidade' => 'Vitória',
            'estado' => 'ES',
        ])->assertSessionHasNoErrors();

        return Pessoa::where('email', 'maria.login@example.com')->firstOrFail();
    }

    private function cadastrarAcademia(): Pessoa
    {
        $this->post('/cadastro-academia', [
            'nome' => 'João',
            'email' => 'academia.login@example.com',
            'senha' => 'segredo1',
            'senha_confirmation' => 'segredo1',
            'cnpj' => '11.222.333/0001-81',
            'razao_social' => 'Academia Login LTDA',
            'nome_fantasia' => 'Academia Login',
            'contato' => '(27) 3333-4444',
            'cep' => '29000-000',
            'logradouro' => 'Rua das Flores',
            'numero' => '200',
            'bairro' => 'Centro',
            'cidade' => 'Vitória',
            'estado' => 'ES',
        ])->assertSessionHasNoErrors();

        return Pessoa::where('email', 'academia.login@example.com')->firstOrFail();
    }

    // ---------- LOGIN ----------

    public function test_aluno_faz_login_e_vai_para_o_painel_do_aluno() {
        $aluno = $this->cadastrarAluno();

        $this->post('/login', ['email' => $aluno->email, 'senha' => 'segredo1'])
            ->assertRedirect(route('aluno.dashboard'));

        $this->assertAuthenticatedAs($aluno);
    }

    public function test_academia_faz_login_e_vai_para_o_painel_da_academia() {
        $academia = $this->cadastrarAcademia();

        $this->post('/login', ['email' => $academia->email, 'senha' => 'segredo1'])
            ->assertRedirect(route('academia.dashboard'));

        $this->assertAuthenticatedAs($academia);
    }

    public function test_senha_errada_e_recusada() {
        $aluno = $this->cadastrarAluno();

        $this->post('/login', ['email' => $aluno->email, 'senha' => 'errada'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_conta_desativada_nao_entra() {
        $aluno = $this->cadastrarAluno();
        $aluno->ativo = false;
        $aluno->save();

        $this->post('/login', ['email' => $aluno->email, 'senha' => 'segredo1'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout_encerra_a_sessao() {
        $aluno = $this->cadastrarAluno();
        $this->post('/login', ['email' => $aluno->email, 'senha' => 'segredo1']);
        $this->assertAuthenticated();

        $this->post('/logout')->assertRedirect('/login');

        $this->assertGuest();
        $this->get('/dashboard-aluno')->assertRedirect();
    }

    // ---------- RECUPERAÇÃO DE SENHA (SIMULADA) ----------

    public function test_esqueci_senha_com_email_cadastrado_grava_token_e_mostra_na_tela() {
        $aluno = $this->cadastrarAluno();

        $resposta = $this->post('/esqueci-senha', ['email' => $aluno->email]);

        $resposta->assertOk()->assertSee('Token gerado com sucesso');
        $this->assertDatabaseHas('password_resets', ['email' => $aluno->email]);
    }

    public function test_esqueci_senha_com_email_desconhecido_e_recusado() {
        $this->post('/esqueci-senha', ['email' => 'ninguem@example.com'])
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('password_resets', 0);
    }

    // ---------- LISTA DE ACADEMIAS ----------

    public function test_aluno_logado_ve_a_lista_de_academias() {
        $this->cadastrarAcademia();
        $aluno = $this->cadastrarAluno();

        $this->actingAs($aluno)
            ->withSession(['last_activity' => time()])
            ->get('/academias')
            ->assertOk()
            ->assertSee('Academia Login');
    }

    public function test_lista_de_academias_exige_login() {
        $this->get('/academias')->assertRedirect();
    }
}
