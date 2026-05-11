<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PessoaJuridica extends Model
{
    protected $fillable = [
        'nome_fantasia',
        'razao_social',
        'cnpj',
        'pessoa_id',
    ];

    /**
     * Relação: A Pessoa Física pertence a uma Pessoa principal
     */
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function alunos()
    {
        return $this->belongsToMany(PessoaFisica::class, 'matriculas', 'pessoa_juridica_id', 'pessoa_fisica_id');
    }
}
