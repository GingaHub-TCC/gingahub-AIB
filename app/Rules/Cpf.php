<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class Cpf implements ValidationRule
{
    // Formato aceito: 11 dígitos, com ou sem pontos e traço (000.000.000-00)
    private const FORMATO = '/^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$/';

    /**
     * Devolve só os dígitos se o valor estiver em um formato aceito;
     * caso contrário devolve o valor original, para a regra rejeitá-lo.
     * Use antes de validar, para que a checagem de unicidade compare só dígitos.
     */
    public static function normalizar(mixed $valor): mixed
    {
        if (is_string($valor) && preg_match(self::FORMATO, trim($valor))) {
            return preg_replace('/\D/', '', $valor);
        }
        return $valor;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 1. Formato: só números, com ou sem pontos e traço
        if (!is_string($value) || !preg_match(self::FORMATO, trim($value))) {
            $fail('O CPF deve ter 11 números, com ou sem pontos e traço.');
            return;
        }

        // 2. Limpeza: mantém apenas números
        $c = preg_replace('/\D/', '', $value);

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
