<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Só deixa passar quem estiver logado E for Pessoa Jurídica (Academia)
 */
class EnsureIsAcademia
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::user() || !Auth::user()->pessoaJuridica) {
            return redirect('/')->with('error', 'Acesso restrito para academias.');
        }

        return $next($request);
    }
}
