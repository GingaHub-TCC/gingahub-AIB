<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function showLogin() {
        // Quem já está logado vai direto para o seu painel
        if (Auth::check()) {
            if (Auth::user()->pessoaFisica) return redirect()->route('aluno.dashboard');
            if (Auth::user()->pessoaJuridica) return redirect()->route('academia.dashboard');
        }

        return view('login');
    }

    public function login(Request $request) {
        $credentials = [
            'email' => $request->email,
            'password' => $request->senha, // O Laravel espera 'password' internamente
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->ativo == false) {
                // Não pode continuar logado com a conta desativada
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['email' => 'Conta desativada.']);
            }

            // Começa a contar a inatividade a partir do login
            session(['last_activity' => time()]);

            // Lógica de Redirecionamento Inteligente
            if ($user->pessoaFisica) {
                // Se tem ficha física, manda para o painel do aluno
                return redirect()->route('aluno.dashboard');
            }

            if ($user->pessoaJuridica) {
                // Se tem ficha jurídica, manda para o painel da academia
                return redirect()->route('academia.dashboard');
            }

            return redirect('/'); // Caso seja um admin ou algo generico
        }

        return back()->withErrors(['email' => 'Credenciais inválidas.'])->onlyInput('email');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function showEsqueciSenha() {
        return view('esqueci_senha');
    }

    public function enviarToken(Request $request) {
        $request->validate(['email' => 'required|email|exists:pessoas,email']);

        $token = Str::random(60);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        // SIMULAÇÃO: No futuro, enviamos um e-mail. Por hora, vamos apenas avisar:
        return "Token gerado com sucesso! (Simulação): O link seria /redefinir-senha/" . $token;
    }
}