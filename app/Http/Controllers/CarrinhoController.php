<?php

namespace App\Http\Controllers;

use App\Models\Assinatura;
use App\Models\Pedido;
use App\Models\PessoaJuridica;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Loja de planos de UMA academia, com carrinho guardado na sessão.
 * O carrinho é separado por academia: session('carrinho.{academia_id}') = [ids dos planos]
 */
class CarrinhoController extends Controller
{
    // Abre a academia: planos, treinos de cada plano e o carrinho
    public function show($academia_id) {
        [$academia, $matricula] = $this->academiaComMatriculaAtiva($academia_id);

        if (!$matricula) {
            return redirect()->route('academias.listar')->with('warning', 'Matricule-se na academia para ver os planos dela.');
        }

        $planos = $academia->planos()
            ->where('ativo', true)
            ->with(['treinos' => fn ($q) => $q->with('instrutor', 'local.endereco')->orderBy('dia_semana')->orderBy('hora_inicio')])
            ->orderBy('valor')
            ->get();

        // Planos que o aluno já assina nesta academia (não podem ir para o carrinho de novo)
        $assinaturasAtivas = $matricula->assinaturasAtivas()->with('plano')->get();
        $planosAssinados = $assinaturasAtivas->pluck('plano_id')->all();

        $carrinho = $this->planosDoCarrinho($academia);
        $total = $carrinho->sum('valor');

        return view('aluno.academia', compact('academia', 'matricula', 'planos', 'assinaturasAtivas', 'planosAssinados', 'carrinho', 'total'));
    }

    public function adicionar($academia_id, $plano_id) {
        [$academia, $matricula] = $this->academiaComMatriculaAtiva($academia_id);

        if (!$matricula) {
            return redirect()->route('academias.listar')->with('warning', 'Você precisa estar matriculado nesta academia.');
        }

        // Só aceita plano ATIVO que pertença a ESTA academia
        $plano = $academia->planos()->where('ativo', true)->findOrFail($plano_id);

        if ($matricula->assinaturasAtivas()->where('plano_id', $plano->id)->exists()) {
            return redirect()->back()->with('warning', 'Você já assina o plano "' . $plano->nome . '".');
        }

        $chave = 'carrinho.' . $academia->id;
        $itens = session($chave, []);

        if (in_array($plano->id, $itens)) {
            return redirect()->back()->with('warning', 'Este plano já está no carrinho.');
        }

        $itens[] = $plano->id;
        session([$chave => $itens]);

        return redirect()->back()->with('success', 'Plano "' . $plano->nome . '" adicionado ao carrinho.');
    }

    public function remover($academia_id, $plano_id) {
        $chave = 'carrinho.' . (int) $academia_id;
        $itens = array_values(array_diff(session($chave, []), [(int) $plano_id]));
        session([$chave => $itens]);

        return redirect()->back()->with('success', 'Plano removido do carrinho.');
    }

    // Conclui o pedido. Pagamento simulado: considera aprovado na hora.
    public function finalizar($academia_id) {
        [$academia, $matricula] = $this->academiaComMatriculaAtiva($academia_id);

        if (!$matricula) {
            return redirect()->route('academias.listar')->with('warning', 'Você precisa estar matriculado nesta academia.');
        }

        // Revalida tudo no servidor: plano ativo, da academia, e ainda não assinado
        $jaAssinados = $matricula->assinaturasAtivas()->pluck('plano_id')->all();
        $planos = $this->planosDoCarrinho($academia)->whereNotIn('id', $jaAssinados);

        if ($planos->isEmpty()) {
            return redirect()->back()->with('warning', 'Seu carrinho está vazio.');
        }

        try {
            DB::beginTransaction();

            $pedido = new Pedido();
            $pedido->matricula_id = $matricula->id;
            $pedido->valor_total = $planos->sum('valor');
            $pedido->status = 'pago';
            $pedido->save();

            foreach ($planos as $plano) {
                $assinatura = new Assinatura();
                $assinatura->matricula_id = $matricula->id;
                $assinatura->plano_id = $plano->id;
                $assinatura->pedido_id = $pedido->id;
                $assinatura->valor = $plano->valor; // Preço congelado no momento da compra
                $assinatura->status = 'ativa';
                $assinatura->data_inicio = now();
                $assinatura->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Falha ao finalizar o pedido: ' . $e->getMessage());
        }

        session()->forget('carrinho.' . $academia->id);

        return redirect()->route('aluno.academia', $academia->id)
            ->with('success', 'Pedido #' . $pedido->id . ' concluído! Pagamento aprovado e ' . $planos->count() . ' plano(s) assinado(s).');
    }

    /**
     * Retorna [academia, matrícula ativa do aluno logado nela (ou null)]
     */
    private function academiaComMatriculaAtiva($academia_id) {
        $academia = PessoaJuridica::with('pessoa.endereco')->findOrFail($academia_id);
        $matricula = Auth::user()->pessoaFisica->matriculaAtivaEm($academia->id);

        return [$academia, $matricula];
    }

    /**
     * Busca no banco os planos do carrinho, descartando os que não são mais válidos
     */
    private function planosDoCarrinho(PessoaJuridica $academia) {
        $ids = session('carrinho.' . $academia->id, []);

        return $academia->planos()->where('ativo', true)->whereIn('id', $ids)->get();
    }
}
