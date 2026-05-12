<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; // Parou de usar porque alterou o extends de Model para Authenticatable
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pessoa extends Authenticatable
{
    use Notifiable;
    protected $fillable = [
        'nome',
        'email',
        'senha',
        'contato',
        'endereco_id',
    ];

    protected $hidden = [
        'senha', 'remember_token', 'ativo'
    ];

    public function getAuthPassword()
    {
        return $this->senha;
    }

    /**
     * Relação: A Pessoa pertence a um Endereço
     */
    public function endereco()
    {
        return $this->belongsTo(Endereco::class);
    }

    /**
     * Relação: A Pessoa pode ter UMA ficha de Pessoa Física (Aluno/Professor)
     */
    public function pessoaFisica()
    {
        return $this->hasOne(PessoaFisica::class);
    }

    /**
     * Relação: A Pessoa pode ter UMA ficha de Pessoa Jurídica (Academia)
     */
    public function pessoaJuridica()
    {
        return $this->hasOne(PessoaJuridica::class);
    }
}