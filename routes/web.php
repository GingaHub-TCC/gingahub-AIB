<?php

use App\Http\Controllers\AlunoController;
use App\Http\Controllers\AcademiaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;


// ALUNO CADASTRO
// Rota para mostrar o formulário (GET)
Route::get('/cadastro-aluno', [AlunoController::class, 'create'])->name('aluno.create');

// Rota para receber os dados e salvar no banco (POST)
Route::post('/cadastro-aluno', [AlunoController::class, 'store'])->name('aluno.store');
///////////////////////


// ACADEMIA CADASTRO
// Rota para mostrar o formulário (GET)
Route::get('/cadastro-academia', [AcademiaController::class, 'create'])->name('academia.create');

// Rota para receber os dados e salvar no banco (POST)
Route::post('/cadastro-academia', [AcademiaController::class, 'store'])->name('academia.store');
///////////////////////


// LOGIN and LOGOUT
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
///////////////////////


// ESQUECI A SENHA
Route::get('/esqueci-senha', [LoginController::class, 'showEsqueciSenha'])->name('password.request');
Route::post('/esqueci-senha', [LoginController::class, 'enviarToken']);
///////////////////////


// MATRICULA
// Rota para o aluno ver as academias
Route::get('/academias', [AlunoController::class, 'listarAcademias'])->name('academias.listar');

// Rota para processar a matrícula
Route::post('/matricular/{academia_id}', [AlunoController::class, 'matricular'])->name('aluno.matricular');
///////////////////////


// DASHBOARD ACADEMIA
// Rota protegida: Só entra quem estiver logado
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard-academia', function () {
        return view('dashboard_academia');
    })->name('academia.dashboard');
});
///////////////////////