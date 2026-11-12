<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    /**
     * Academia (Pessoa Jurídica) do usuário logado.
     * Toda consulta da academia parte daqui, assim ela só enxerga os próprios dados.
     */
    protected function academiaLogada()
    {
        return Auth::user()->pessoaJuridica;
    }
}
