<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    protected $fillable = [
        'pessoa_fisica_id',
        'pessoa_juridica_id',
        'status',
        'data_cancelamento',
    ];

    protected $casts = [
        'data_matricula' => 'datetime',
        'data_cancelamento' => 'datetime',
    ];

    /**
     * Relação: A Matrícula pertence a um Aluno (Pessoa Física)
     */
    public function aluno()
    {
        return $this->belongsTo(PessoaFisica::class, 'pessoa_fisica_id');
    }

    /**
     * Relação: A Matrícula pertence a uma Academia (Pessoa Jurídica)
     */
    public function academia()
    {
        return $this->belongsTo(PessoaJuridica::class, 'pessoa_juridica_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function assinaturas()
    {
        return $this->hasMany(Assinatura::class);
    }

    public function assinaturasAtivas()
    {
        return $this->assinaturas()->where('status', 'ativa');
    }
}
