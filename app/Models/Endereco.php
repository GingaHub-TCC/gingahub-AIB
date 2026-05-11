<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    protected $fillable = [
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
    ];

    /**
     * Relação: Um endereço tem várias Pessoas
     */
    public function pessoas()
    {
        return $this->hasMany(Pessoa::class);
    }
}