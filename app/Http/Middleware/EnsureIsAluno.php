<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Só deixa passar quem estiver logado E for Pessoa Física (Aluno)
 */
class EnsureIsAluno
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::user() || !Auth::user()->pessoaFisica) {
            return redirect('/')->with('error', 'Acesso restrito para alunos.');
        }

        return $next($request);
    }
}
