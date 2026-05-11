<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function showLogin() {
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

            // Lógica de Redirecionamento Inteligente
            if ($user->pessoaFisica) {
                // Se tem ficha física, manda para a vitrine de academias
                return redirect()->route('academias.listar');
            } 
            
            if ($user->pessoaJuridica) {
                // Se tem ficha jurídica, manda para o painel da academia 
                return redirect()->route('academia.dashboard'); // Ainda não feito, coisa para a ETAPA 2
            }

            return redirect('/'); // Caso seja um admin ou algo genérico
        }

        return back()->withErrors(['email' => 'Credenciais inválidas.']);
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