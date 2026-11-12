<?php

namespace Tests\Feature;

use App\Models\Assinatura;
use App\Models\Matricula;
use App\Models\Pessoa;
use App\Models\Plano;
use App\Models\PessoaJuridica;
use Database\Seeders\GingaHubSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testa os fluxos principais usando os dados do GingaHubSeeder:
 * - academia@teste.com (Ginga Fit) / dojo@teste.com (Dojo Sol Nascente)
 * - aluno@teste.com (matriculado na Ginga Fit, assina "Regional 2x") / aluno2@teste.com (sem matrícula)
 */
class GingaHubFluxoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(GingaHubSeeder::class);
    }

    private function usuario($email) {
        return Pessoa::where('email', $email)->first();
    }

    private function ginga() {
        return PessoaJuridica::where('nome_fantasia', 'Ginga Fit Unidade 1')->first();
    }

    private function dojo() {
        return PessoaJuridica::where('nome_fantasia', 'Dojo Sol Nascente')->first();
    }

    // ---------- SEGURANÇA ----------

    public function test_rotas_protegidas_exigem_login() {
        $this->get('/dashboard-aluno')->assertRedirect('/login');
        $this->get('/dashboard-academia')->assertRedirect('/login');
        $this->get('/academia/1')->assertRedirect('/login');
    }

    public function test_aluno_nao_entra_na_area_da_academia_e_vice_versa() {
        $this->actingAs($this->usuario('aluno@teste.com'))
            ->get('/dashboard-academia/planos')->assertRedirect('/');

        $this->actingAs($this->usuario('academia@teste.com'))
            ->get('/dashboard-aluno')->assertRedirect('/');
    }

    public function test_academia_nao_edita_plano_de_outra_academia() {
        $planoDoDojo = $this->dojo()->planos()->first();

        $this->actingAs($this->usuario('academia@teste.com'))
            ->get("/dashboard-academia/planos/{$planoDoDojo->id}/edit")->assertNotFound();

        $this->actingAs($this->usuario('academia@teste.com'))
            ->delete("/dashboard-academia/planos/{$planoDoDojo->id}")->assertNotFound();
    }

    public function test_academia_nao_usa_treino_de_outra_academia_no_plano() {
        $treinoDoDojo = $this->dojo()->treinos()->first();

        $this->actingAs($this->usuario('academia@teste.com'))
            ->post('/dashboard-academia/planos', [
                'nome' => 'Invasor', 'valor' => '10', 'periodicidade' => 'mensal', 'treinos' => [$treinoDoDojo->id],
            ])->assertSessionHasErrors('treinos.0');
    }

    public function test_sessao_expira_por_inatividade() {
        $this->actingAs($this->usuario('aluno@teste.com'))
            ->withSession(['last_activity' => time() - 6 * 60])
            ->get('/dashboard-aluno')
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    // ---------- ACADEMIA ----------

    public function test_academia_cadastra_instrutor_local_treino_e_plano() {
        $this->actingAs($this->usuario('dojo@teste.com'));

        $this->post('/dashboard-academia/instrutores', ['nome' => 'Prof. Lia', 'graduacao' => 'Faixa Marrom', 'contato' => '(27) 99999-1111'])
            ->assertRedirect('/dashboard-academia/instrutores');
        $this->post('/dashboard-academia/locais', [
            'nome' => 'Sala 2', 'cep' => '29700-300', 'logradouro' => 'Rua A', 'numero' => '10',
            'bairro' => 'Centro', 'cidade' => 'Colatina', 'estado' => 'es',
        ])->assertRedirect('/dashboard-academia/locais');

        $instrutor = $this->dojo()->instrutores()->where('nome', 'Prof. Lia')->first();
        $local = $this->dojo()->locais()->where('nome', 'Sala 2')->first();
        $this->assertEquals('ES', $local->endereco->estado);

        $this->post('/dashboard-academia/treinos', [
            'tema' => 'Muay Thai', 'instrutor_id' => $instrutor->id, 'local_id' => $local->id,
            'dia_semana' => 1, 'hora_inicio' => '07:00', 'hora_fim' => '08:00',
        ])->assertRedirect('/dashboard-academia/treinos');

        $treino = $this->dojo()->treinos()->where('tema', 'Muay Thai')->first();

        $this->post('/dashboard-academia/planos', [
            'nome' => 'Muay Thai Manhã', 'valor' => '1.099,90', 'periodicidade' => 'trimestral', 'treinos' => [$treino->id],
        ])->assertRedirect('/dashboard-academia/planos');

        $plano = $this->dojo()->planos()->where('nome', 'Muay Thai Manhã')->first();
        $this->assertEquals('1099.90', $plano->valor);
        $this->assertEquals([$treino->id], $plano->treinos->pluck('id')->all());

        // Todas as telas da academia abrem sem erro
        foreach (['', '/alunos', '/planos', '/planos/create', "/planos/{$plano->id}/edit", '/treinos', '/treinos/create',
                  "/treinos/{$treino->id}/edit", '/instrutores', '/instrutores/create', '/locais', '/locais/create'] as $url) {
            $this->get('/dashboard-academia' . $url)->assertOk();
        }
    }

    public function test_treino_com_horario_invertido_e_recusado() {
        $academia = $this->ginga();
        $this->actingAs($this->usuario('academia@teste.com'))
            ->post('/dashboard-academia/treinos', [
                'tema' => 'X', 'instrutor_id' => $academia->instrutores()->first()->id, 'local_id' => $academia->locais()->first()->id,
                'dia_semana' => 1, 'hora_inicio' => '20:00', 'hora_fim' => '19:00',
            ])->assertSessionHasErrors('hora_fim');
    }

    public function test_plano_com_assinatura_nao_pode_ser_excluido_so_desativado() {
        $plano = $this->ginga()->planos()->where('nome', 'Regional 2x')->first();
        $this->actingAs($this->usuario('academia@teste.com'));

        $this->delete("/dashboard-academia/planos/{$plano->id}")->assertSessionHas('error');
        $this->assertDatabaseHas('planos', ['id' => $plano->id]);

        $this->patch("/dashboard-academia/planos/{$plano->id}/status");
        $this->assertFalse($plano->fresh()->ativo);
    }

    // ---------- ALUNO ----------

    public function test_fluxo_completo_matricula_carrinho_pedido() {
        $aluno = $this->usuario('aluno2@teste.com');
        $dojo = $this->dojo();
        $jiu = $dojo->planos()->where('nome', 'Jiu-Jitsu Mensal')->first();
        $judo = $dojo->planos()->where('nome', 'Judô Mensal')->first();

        $this->actingAs($aluno);

        // Sem matrícula não abre a academia
        $this->get("/academia/{$dojo->id}")->assertRedirect('/academias');

        $this->post("/matricular/{$dojo->id}")->assertSessionHas('success');
        $this->get("/academia/{$dojo->id}")->assertOk()->assertSee('Jiu-Jitsu Mensal');

        // Carrinho com dois planos
        $this->post("/academia/{$dojo->id}/carrinho/{$jiu->id}");
        $this->post("/academia/{$dojo->id}/carrinho/{$judo->id}");
        $this->post("/academia/{$dojo->id}/carrinho/{$jiu->id}")->assertSessionHas('warning'); // repetido
        $this->assertEquals([$jiu->id, $judo->id], session("carrinho.{$dojo->id}"));

        $this->get("/academia/{$dojo->id}")->assertSee('R$ 260,00');

        // Finaliza: pedido pago + 2 assinaturas ativas, carrinho vazio
        $this->post("/academia/{$dojo->id}/finalizar")->assertSessionHas('success');
        $matricula = $aluno->pessoaFisica->matriculaAtivaEm($dojo->id);
        $this->assertEquals(2, $matricula->assinaturasAtivas()->count());
        $this->assertDatabaseHas('pedidos', ['matricula_id' => $matricula->id, 'valor_total' => 260, 'status' => 'pago']);
        $this->assertEmpty(session("carrinho.{$dojo->id}", []));

        // Plano já assinado não volta para o carrinho
        $this->post("/academia/{$dojo->id}/carrinho/{$jiu->id}")->assertSessionHas('warning');

        $this->get('/dashboard-aluno')->assertOk()->assertSee('Dojo Sol Nascente')->assertSee('Jiu-Jitsu');
    }

    public function test_aluno_em_varias_academias_com_varios_planos() {
        $aluno = $this->usuario('aluno@teste.com');
        $dojo = $this->dojo();
        $combo = $dojo->planos()->where('nome', 'Combo Anual')->first();

        $this->actingAs($aluno);
        $this->post("/matricular/{$dojo->id}");
        $this->post("/academia/{$dojo->id}/carrinho/{$combo->id}");
        $this->post("/academia/{$dojo->id}/finalizar");

        $this->assertEquals(2, $aluno->pessoaFisica->matriculas()->where('status', 'ativa')->count());
        $this->assertEquals(2, $aluno->pessoaFisica->assinaturas()->where('assinaturas.status', 'ativa')->count());
    }

    public function test_aluno_nao_compra_plano_de_outra_academia_nem_plano_desativado() {
        $aluno = $this->usuario('aluno@teste.com');
        $ginga = $this->ginga();
        $planoDoDojo = $this->dojo()->planos()->first();

        $this->actingAs($aluno)->post("/academia/{$ginga->id}/carrinho/{$planoDoDojo->id}")->assertNotFound();

        $kids = $ginga->planos()->where('nome', 'Kids')->first();
        $kids->ativo = false;
        $kids->save();
        $this->actingAs($aluno)->post("/academia/{$ginga->id}/carrinho/{$kids->id}")->assertNotFound();
    }

    public function test_desmatricular_inativa_matricula_e_assinaturas() {
        $aluno = $this->usuario('aluno@teste.com');
        $ginga = $this->ginga();
        $matricula = $aluno->pessoaFisica->matriculaAtivaEm($ginga->id);
        $this->assertEquals(1, $matricula->assinaturasAtivas()->count());

        $this->actingAs($aluno)->post("/academia/{$ginga->id}/desmatricular")->assertSessionHas('success');

        // A matrícula continua existindo, só que inativa
        $matricula->refresh();
        $this->assertEquals('inativa', $matricula->status);
        $this->assertNotNull($matricula->data_cancelamento);
        $this->assertEquals(0, $matricula->assinaturasAtivas()->count());
        $this->assertEquals(1, $matricula->assinaturas()->where('status', 'inativa')->count());

        // Sem matrícula ativa, não abre mais a academia
        $this->get("/academia/{$ginga->id}")->assertRedirect('/academias');

        // Rematrícula reaproveita o mesmo registro e não reativa as assinaturas antigas
        $this->post("/matricular/{$ginga->id}")->assertSessionHas('success');
        $this->assertEquals(1, Matricula::where('pessoa_fisica_id', $aluno->pessoaFisica->id)->where('pessoa_juridica_id', $ginga->id)->count());
        $this->assertEquals('ativa', $matricula->fresh()->status);
        $this->assertEquals(0, $matricula->assinaturasAtivas()->count());
    }

    public function test_aluno_nao_cancela_assinatura_de_outro_aluno() {
        $assinatura = Assinatura::first(); // pertence ao aluno@teste.com

        $this->actingAs($this->usuario('aluno2@teste.com'))
            ->post("/assinatura/{$assinatura->id}/cancelar")->assertNotFound();

        $this->actingAs($this->usuario('aluno@teste.com'))
            ->post("/assinatura/{$assinatura->id}/cancelar")->assertSessionHas('success');
        $this->assertEquals('inativa', $assinatura->fresh()->status);
    }
}
