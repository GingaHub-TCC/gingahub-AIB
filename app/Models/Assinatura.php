<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assinatura extends Model
{
    // Nada é fillable: a assinatura só nasce na finalização do pedido
    protected $guarded = ['*'];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_inicio' => 'datetime',
        'data_cancelamento' => 'datetime',
    ];

    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    public function plano()
    {
        return $this->belongsTo(Plano::class);
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
