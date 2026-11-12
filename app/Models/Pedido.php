<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    // Nada é fillable: o pedido é montado só no servidor, a partir do carrinho
    protected $guarded = ['*'];

    protected $casts = [
        'valor_total' => 'decimal:2',
    ];

    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    public function assinaturas()
    {
        return $this->hasMany(Assinatura::class);
    }
}
