<?php

use App\Http\Controllers\AlunoController;
use App\Http\Controllers\AcademiaController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\InstrutorController;
use App\Http\Controllers\LocalController;
use App\Http\Controllers\PlanoController;
use App\Http\Controllers\TreinoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;


//INDEX DA PAGINA
Route::get('/', function () {
    return view('index');
});


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
// Sem limite de tentativas nesta versão (decisão do grupo para a apresentação).
// Antes: ->middleware('throttle:5,1'), 5 tentativas por minuto por IP.
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
///////////////////////


// ESQUECI A SENHA
Route::get('/esqueci-senha', [LoginController::class, 'showEsqueciSenha'])->name('password.request');
Route::post('/esqueci-senha', [LoginController::class, 'enviarToken']);
///////////////////////


// ÁREA DO ALUNO
// Rota protegida: Só entra quem estiver logado E for Pessoa Física (Aluno)
// O middleware 'aluno' fica em app/Http/Middleware/EnsureIsAluno.php
Route::middleware(['auth', 'aluno'])->group(function () {

    // Painel do aluno: matrículas, assinaturas e grade de treinos
    Route::get('/dashboard-aluno', [AlunoController::class, 'dashboard'])->name('aluno.dashboard');

    // Rota para o aluno ver as academias
    Route::get('/academias', [AlunoController::class, 'listarAcademias'])->name('academias.listar');

    // Rota para processar a matrícula
    Route::post('/matricular/{academia_id}', [AlunoController::class, 'matricular'])->whereNumber('academia_id')->name('aluno.matricular');

    // Rota para o aluno se desmatricular (inativa a matrícula e as assinaturas)
    Route::post('/academia/{id}/desmatricular', [AlunoController::class, 'desmatricular'])->whereNumber('id')->name('aluno.desmatricular');

    // LOJA DA ACADEMIA (só para quem tem matrícula ativa nela)
    Route::get('/academia/{academia_id}', [CarrinhoController::class, 'show'])->whereNumber('academia_id')->name('aluno.academia');
    Route::post('/academia/{academia_id}/carrinho/{plano_id}', [CarrinhoController::class, 'adicionar'])->whereNumber(['academia_id', 'plano_id'])->name('carrinho.adicionar');
    Route::delete('/academia/{academia_id}/carrinho/{plano_id}', [CarrinhoController::class, 'remover'])->whereNumber(['academia_id', 'plano_id'])->name('carrinho.remover');
    Route::post('/academia/{academia_id}/finalizar', [CarrinhoController::class, 'finalizar'])->whereNumber('academia_id')->name('carrinho.finalizar');

    // Cancelar uma assinatura específica (mantém a matrícula)
    Route::post('/assinatura/{id}/cancelar', [AlunoController::class, 'cancelarAssinatura'])->whereNumber('id')->name('aluno.assinatura.cancelar');
});
///////////////////////


// ÁREA DA ACADEMIA
// Rota protegida: Só entra quem estiver logado E for Pessoa Jurídica (Academia)
// O middleware 'academia' fica em app/Http/Middleware/EnsureIsAcademia.php
Route::middleware(['auth', 'academia'])->group(function () {

    Route::get('/dashboard-academia', [AcademiaController::class, 'dashboard'])->name('academia.dashboard');
    Route::get('/dashboard-academia/alunos', [AcademiaController::class, 'alunos'])->name('academia.alunos');

    Route::prefix('dashboard-academia')->name('academia.')->group(function () {
        // Cadastros da academia (listar, criar, editar, excluir)
        Route::resource('planos', PlanoController::class)->except('show')->parameters(['planos' => 'id']);
        Route::resource('treinos', TreinoController::class)->except('show')->parameters(['treinos' => 'id']);
        Route::resource('instrutores', InstrutorController::class)->except('show')->parameters(['instrutores' => 'id']);
        Route::resource('locais', LocalController::class)->except('show')->parameters(['locais' => 'id']);

        Route::patch('planos/{id}/status', [PlanoController::class, 'alternarStatus'])->name('planos.status');
    });
});

///////////////////////