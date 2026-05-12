<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class Cpf implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 1. Limpeza: Mantém apenas números
        $c = preg_replace('/[^0-9]/', '', $value);

        // 2. Validação de tamanho
        if (strlen($c) != 11) {
            $fail('O CPF deve ter exatamente 11 números.');
            return;
        }

        // 3. Bloqueia sequências inválidas conhecidas (111.111.111-11, etc)
        if (preg_match('/(\d)\1{10}/', $c)) {
            $fail('O CPF informado é inválido.');
            return;
        }

        // 4. Cálculo dos Dígitos Verificadores
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($cc = 0; $cc < $t; $cc++) {
                // Peso para o 1º DV: 10, 9, 8...
                // Peso para o 2º DV: 11, 10, 9...
                $d += $c[$cc] * (($t + 1) - $cc);
            }
            
            $d = ((10 * $d) % 11) % 10;

            if ($c[$cc] != $d) {
                $fail('O CPF informado é inválido.');
                return;
            }
        }
    }
}
