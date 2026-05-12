<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class Cnpj implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $c = strtoupper(preg_replace('/[^A-Z0-9]/', '', $value));

        if (strlen($c) != 14) {
            $fail('O CNPJ deve ter exatamente 14 caracteres.');
            return;
        }

        $pesos = [
            [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2],
            [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]
        ];

        for ($i = 0; $i < 2; $i++) {
            $soma = 0;
            $t = 12 + $i;
            for ($cc = 0; $cc < $t; $cc++) {
                $soma += (ord($c[$cc]) - 48) * $pesos[$i][$cc];
            }
            $resto = $soma % 11;
            $dv = ($resto < 2) ? 0 : 11 - $resto;
            if ($c[$t] != $dv) {
                $fail('O CNPJ informado é inválido.');
                return;
            }
        }
    }
}
