<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfOuCnpj implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 1. Limpa o valor (remove pontos e traços)
        $c = preg_replace('/[^0-9]/', '', $value);

        // 2. Verifica se é CPF (11) ou CNPJ (14)
        if (strlen($c) != 11 && strlen($c) != 14) {
            $fail('O campo :attribute deve ser um CPF ou CNPJ válido.');
            return;
        }

        // 3. Validação matemática simplificada (Exemplo para CPF)
        if (strlen($c) == 11) {
            if (preg_match('/(\d)\1{10}/', $c)) { $fail('CPF inválido.'); return; }
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $cc = 0; $cc < $t; $cc++) {
                    $d += $c[$cc] * (($t + 1) - $cc);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($c[$cc] != $d) { $fail('CPF inválido.'); return; }
            }
        }
        // Nota: A mesma lógica se aplica ao CNPJ com seus pesos específicos.
    }
}