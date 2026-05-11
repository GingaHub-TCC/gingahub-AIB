<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PessoaFisica extends Model
{
    protected $fillable = [
        'sobrenome',
        'cpf',
        'sexo',
        'data_nascimento',
        'role', // <-- Adicionado aqui para permitir o cadastro
        'pessoa_id',
    ];

    /**
     * Relação: A Pessoa Física pertence a uma Pessoa principal
     */
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function academias()
    {
        return $this->belongsToMany(PessoaJuridica::class, 'matriculas', 'pessoa_fisica_id', 'pessoa_juridica_id');
    }
}