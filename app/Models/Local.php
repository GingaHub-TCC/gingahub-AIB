<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    protected $table = 'locais';

    protected $fillable = [
        'nome',
        'endereco_id',
    ];

    public function academia()
    {
        return $this->belongsTo(PessoaJuridica::class, 'pessoa_juridica_id');
    }

    /**
     * Relação: O Local reaproveita a tabela de endereços
     */
    public function endereco()
    {
        return $this->belongsTo(Endereco::class);
    }

    public function treinos()
    {
        return $this->hasMany(Treino::class);
    }
}
