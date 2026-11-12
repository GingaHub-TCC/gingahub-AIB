<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plano extends Model
{
    public const PERIODICIDADES = [
        'mensal' => 'Mensal',
        'trimestral' => 'Trimestral',
        'semestral' => 'Semestral',
        'anual' => 'Anual',
    ];

    // 'ativo' NÃO é fillable: só muda pela ação de ativar/desativar
    protected $fillable = [
        'nome',
        'descricao',
        'valor',
        'periodicidade',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    public function academia()
    {
        return $this->belongsTo(PessoaJuridica::class, 'pessoa_juridica_id');
    }

    /**
     * Relação: Quais treinos este plano permite praticar
     */
    public function treinos()
    {
        return $this->belongsToMany(Treino::class, 'plano_treino');
    }

    public function assinaturas()
    {
        return $this->hasMany(Assinatura::class);
    }

    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }
}
