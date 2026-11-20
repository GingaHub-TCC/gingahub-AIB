<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class Cnpj implements ValidationRule
{
    // Formato aceito: 14 caracteres alfanuméricos (12 letras/números + 2 dígitos verificadores
    // numéricos), com ou sem pontos, barra e traço (AA.AAA.AAA/AAAA-00). Aceita o CNPJ numérico antigo.
    private const FORMATO = '/^[0-9A-Za-z]{2}\.?[0-9A-Za-z]{3}\.?[0-9A-Za-z]{3}\/?[0-9A-Za-z]{4}-?\d{2}$/';

    /**
     * Remove só a máscara (pontos, barras e traços) e deixa as letras em maiúsculas
     * se o valor estiver em um formato aceito; caso contrário devolve o valor original,
     * para a regra rejeitá-lo. Use antes de validar, para que a checagem de unicidade
     * compare com o que está gravado no banco.
     */
    public static function normalizar(mixed $valor): mixed
    {
        if (is_string($valor) && preg_match(self::FORMATO, trim($valor))) {
            return strtoupper(preg_replace('/[.\/-]/', '', trim($valor)));
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
        if (!is_string($value) || !preg_match(self::FORMATO, trim($value))) {
            $fail('O CNPJ deve ter 14 caracteres (letras e números), com ou sem pontos, barra e traço.');
            return;
        }

        $c = strtoupper(preg_replace('/[.\/-]/', '', trim($value)));

        // Bloqueia sequências repetidas (00000000000000, 11111111111111...)
        if (preg_match('/^([0-9A-Z])\1{13}$/', $c)) {
            $fail('O CNPJ informado é inválido.');
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
