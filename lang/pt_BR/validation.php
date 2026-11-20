<?php

// Mensagens de validação em português (pt-BR) para os formulários do GingaHub.
// As mensagens personalizadas dos controllers continuam valendo sobre estas.

return [
    'alpha' => 'O campo :attribute deve conter apenas letras.',
    'accepted' => 'O campo :attribute deve ser aceito.',
    'array' => 'O campo :attribute deve ser uma lista.',
    'between' => [
        'numeric' => 'O campo :attribute deve estar entre :min e :max.',
        'string' => 'O campo :attribute deve ter entre :min e :max caracteres.',
    ],
    'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
    'confirmed' => 'A confirmação do campo :attribute não confere.',
    'date' => 'O campo :attribute não é uma data válida.',
    'date_format' => 'O campo :attribute não corresponde ao formato :format.',
    'digits' => 'O campo :attribute deve ter :digits dígitos.',
    'email' => 'O campo :attribute deve ser um e-mail válido.',
    'exists' => 'O valor selecionado em :attribute é inválido.',
    'in' => 'O valor selecionado em :attribute é inválido.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'max' => [
        'numeric' => 'O campo :attribute não pode ser maior que :max.',
        'string' => 'O campo :attribute não pode ter mais de :max caracteres.',
        'array' => 'O campo :attribute não pode ter mais de :max itens.',
    ],
    'min' => [
        'numeric' => 'O campo :attribute deve ser no mínimo :min.',
        'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
        'array' => 'O campo :attribute deve ter pelo menos :min itens.',
    ],
    'nullable' => 'O campo :attribute pode ficar em branco.',
    'numeric' => 'O campo :attribute deve ser um número.',
    'regex' => 'O formato do campo :attribute é inválido.',
    'required' => 'O campo :attribute é obrigatório.',
    'same' => 'Os campos :attribute e :other devem ser iguais.',
    'size' => [
        'numeric' => 'O campo :attribute deve ser :size.',
        'string' => 'O campo :attribute deve ter :size caracteres.',
    ],
    'string' => 'O campo :attribute deve ser um texto.',
    'unique' => 'Este valor de :attribute já está em uso.',
    'after' => 'O campo :attribute deve ser uma data posterior a :date.',
    'after_or_equal' => 'O campo :attribute deve ser uma data igual ou posterior a :date.',
    'gt' => [
        'numeric' => 'O campo :attribute deve ser maior que :value.',
    ],

    'custom' => [],

    // Nomes amigáveis dos campos dos formulários
    'attributes' => [
        'nome' => 'nome',
        'sobrenome' => 'sobrenome',
        'cpf' => 'CPF',
        'cnpj' => 'CNPJ',
        'contato' => 'contato',
        'email' => 'e-mail',
        'senha' => 'senha',
        'senha_confirmation' => 'confirmação da senha',
        'razao_social' => 'razão social',
        'nome_fantasia' => 'nome fantasia',
        'cep' => 'CEP',
        'logradouro' => 'logradouro',
        'numero' => 'número',
        'complemento' => 'complemento',
        'bairro' => 'bairro',
        'cidade' => 'cidade',
        'estado' => 'UF',
        'graduacao' => 'graduação',
        'tema' => 'tema',
        'descricao' => 'descrição',
        'valor' => 'valor',
        'periodicidade' => 'periodicidade',
        'instrutor_id' => 'instrutor',
        'local_id' => 'local',
        'dia_semana' => 'dia da semana',
        'hora_inicio' => 'hora de início',
        'hora_fim' => 'hora de fim',
        'treinos' => 'treinos',
    ],
];
