<?php

namespace Tests\Feature;

use App\Models\Pessoa;
use App\Models\PessoaFisica;
use App\Models\PessoaJuridica;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regras de unicidade e de CNPJ nos cadastros: CPF, e-mail e CNPJ duplicados,
 * CNPJ alfanumérico válido e CNPJ com dígito verificador errado.
 * Não usa o seeder: cada teste cria o que precisa.
 */
class CadastroRegrasTest extends TestCase
{
    use RefreshDatabase;

    private function dadosAluno(array $extra = []): array
    {
        return array_merge([
            'nome' => 'Maria',
            'sobrenome' => 'Silva',
            'cpf' => '529.982.247-25',
            'contato' => '(27) 99999-9999',
            'email' => 'maria.regras@example.com',
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
            'nome' => 'Academia Regras',
            'email' => 'academia.regras@example.com',
            'senha' => 'segredo1',
            'senha_confirmation' => 'segredo1',
            'cnpj' => '11.222.333/0001-81',
            'razao_social' => 'Academia Regras LTDA',
            'nome_fantasia' => 'Academia Regras',
            'contato' => '(27) 3333-4444',
            'cep' => '29000-000',
            'logradouro' => 'Rua das Flores',
            'numero' => '200',
            'bairro' => 'Centro',
            'cidade' => 'Vitória',
            'estado' => 'ES',
        ], $extra);
    }

    // ---------- UNICIDADE ----------

    public function test_cpf_duplicado_e_recusado_no_cadastro_de_aluno() {
        $this->post('/cadastro-aluno', $this->dadosAluno())
            ->assertSessionHasNoErrors();

        // Mesmo CPF, sem máscara, com outro e-mail
        $this->post('/cadastro-aluno', $this->dadosAluno([
            'cpf' => '52998224725',
            'email' => 'outra.maria@example.com',
        ]))->assertSessionHasErrors('cpf');

        $this->assertSame(1, PessoaFisica::count());
        $this->assertSame(1, Pessoa::count());
    }

    public function test_email_duplicado_e_recusado() {
        $this->post('/cadastro-aluno', $this->dadosAluno())
            ->assertSessionHasNoErrors();

        // Mesmo e-mail, com letras maiúsculas, e outro CPF
        $this->post('/cadastro-aluno', $this->dadosAluno([
            'cpf' => '111.444.777-35',
            'email' => 'Maria.Regras@Example.com',
        ]))->assertSessionHasErrors('email');

        // Mesmo e-mail no cadastro de academia
        $this->post('/cadastro-academia', $this->dadosAcademia([
            'email' => 'maria.regras@example.com',
        ]))->assertSessionHasErrors('email');

        $this->assertSame(1, Pessoa::count());
        $this->assertSame(0, PessoaJuridica::count());
    }

    public function test_cnpj_duplicado_e_recusado_no_cadastro_de_academia() {
        $this->post('/cadastro-academia', $this->dadosAcademia())
            ->assertSessionHasNoErrors();

        // Mesmo CNPJ, sem máscara, com outro e-mail e outra razão social
        $this->post('/cadastro-academia', $this->dadosAcademia([
            'cnpj' => '11222333000181',
            'email' => 'outra.academia@example.com',
            'razao_social' => 'Outra Academia LTDA',
        ]))->assertSessionHasErrors('cnpj');

        $this->assertSame(1, PessoaJuridica::count());
    }

    // ---------- CNPJ ----------

    public function test_cnpj_alfanumerico_com_digito_certo_e_aceito() {
        $this->post('/cadastro-academia', $this->dadosAcademia([
            'cnpj' => '12.ABC.345/01DE-35',
        ]))->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $this->assertSame('12ABC34501DE35', PessoaJuridica::first()->cnpj);
    }

    public function test_cnpj_com_digito_verificador_errado_e_recusado() {
        // Alfanumérico com o último dígito trocado
        $this->post('/cadastro-academia', $this->dadosAcademia([
            'cnpj' => '12.ABC.345/01DE-36',
        ]))->assertSessionHasErrors('cnpj');

        // Numérico com o último dígito trocado
        $this->post('/cadastro-academia', $this->dadosAcademia([
            'cnpj' => '11.222.333/0001-82',
        ]))->assertSessionHasErrors('cnpj');

        $this->assertDatabaseCount('pessoas', 0);
        $this->assertSame(0, PessoaJuridica::count());
    }
}
