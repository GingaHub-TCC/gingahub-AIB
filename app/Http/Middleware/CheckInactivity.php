<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- IMPORTANTE: Use a Facade
use Symfony\Component\HttpFoundation\Response;

class CheckInactivity
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = session('last_activity');
            $timeout = 5 * 60; // 5 minutos de inatividade
            $currentTime = time();

            // Se quiser ver os números na tela e parar o código, descomente a linha abaixo:
            // dd(['última' => $lastActivity, 'agora' => $currentTime, 'diferenca' => $currentTime - $lastActivity]);

            if ($lastActivity && ($currentTime - $lastActivity > $timeout)) {
                Auth::logout();

                // Destrói a sessão inteira (inclusive o carrinho) e gera um novo token CSRF
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Sessão expirada por inatividade. Entre novamente.');
            }

            // Atualiza o tempo a cada clique
            session(['last_activity' => $currentTime]);
        }

        return $next($request);
    }
}
