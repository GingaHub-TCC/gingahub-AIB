<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instrutor extends Model
{
    protected $table = 'instrutores';

    protected $fillable = [
        'nome',
        'graduacao',
        'contato',
    ];

    public function academia()
    {
        return $this->belongsTo(PessoaJuridica::class, 'pessoa_juridica_id');
    }

    public function treinos()
    {
        return $this->hasMany(Treino::class);
    }
}
