<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Treino extends Model
{
    // Mesmo padrão do Carbon: 0 = Domingo ... 6 = Sábado
    public const DIAS_SEMANA = [
        0 => 'Domingo',
        1 => 'Segunda-feira',
        2 => 'Terça-feira',
        3 => 'Quarta-feira',
        4 => 'Quinta-feira',
        5 => 'Sexta-feira',
        6 => 'Sábado',
    ];

    protected $fillable = [
        'tema',
        'descricao',
        'instrutor_id',
        'local_id',
        'dia_semana',
        'hora_inicio',
        'hora_fim',
    ];

    public function academia()
    {
        return $this->belongsTo(PessoaJuridica::class, 'pessoa_juridica_id');
    }

    public function instrutor()
    {
        return $this->belongsTo(Instrutor::class);
    }

    public function local()
    {
        return $this->belongsTo(Local::class);
    }

    public function planos()
    {
        return $this->belongsToMany(Plano::class, 'plano_treino');
    }

    // Ex: "Segunda-feira"
    public function getDiaSemanaNomeAttribute()
    {
        return self::DIAS_SEMANA[$this->dia_semana] ?? '-';
    }

    // Ex: "19:00 às 20:30"
    public function getHorarioAttribute()
    {
        return substr($this->hora_inicio, 0, 5) . ' às ' . substr($this->hora_fim, 0, 5);
    }
}
