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
        return $this->belongsToMany(PessoaFisica::class, 'matriculas', 'pessoa_juridica_id', 'pessoa_fisica_id')
                    ->withPivot('id', 'status', 'data_matricula', 'data_cancelamento')
                    ->withTimestamps();
    }

    public function matriculas()
    {
        return $this->hasMany(Matricula::class, 'pessoa_juridica_id');
    }

    public function planos()
    {
        return $this->hasMany(Plano::class);
    }

    public function treinos()
    {
        return $this->hasMany(Treino::class);
    }

    public function instrutores()
    {
        return $this->hasMany(Instrutor::class);
    }

    public function locais()
    {
        return $this->hasMany(Local::class);
    }

    /**
     * Relação: Assinaturas feitas nesta academia (via matrícula)
     */
    public function assinaturas()
    {
        return $this->hasManyThrough(Assinatura::class, Matricula::class, 'pessoa_juridica_id', 'matricula_id');
    }
}
