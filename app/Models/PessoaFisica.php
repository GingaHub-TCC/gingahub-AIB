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
        'pessoa_id',
    ];

    /**
     * Relação: A Pessoa Física pertence a uma Pessoa principal
     */
    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function academias() {
        return $this->belongsToMany(PessoaJuridica::class, 'matriculas', 'pessoa_fisica_id', 'pessoa_juridica_id')
                    ->withPivot('id', 'status', 'data_matricula', 'data_cancelamento') // Permite acessar esses campos
                    ->withTimestamps();
    }

    /**
     * Relação: Todas as matrículas do aluno (ativas e inativas)
     */
    public function matriculas()
    {
        return $this->hasMany(Matricula::class, 'pessoa_fisica_id');
    }

    /**
     * Busca a matrícula ATIVA do aluno em uma academia (ou null)
     */
    public function matriculaAtivaEm($academiaId)
    {
        return $this->matriculas()
                    ->where('pessoa_juridica_id', $academiaId)
                    ->where('status', 'ativa')
                    ->first();
    }

    /**
     * Relação: Assinaturas do aluno em todas as academias (via matrícula)
     */
    public function assinaturas()
    {
        return $this->hasManyThrough(Assinatura::class, Matricula::class, 'pessoa_fisica_id', 'matricula_id');
    }
}
