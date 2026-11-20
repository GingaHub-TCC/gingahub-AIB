<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erro no sistema - GingaHub</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f3f4f6; color: #1f2937; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .caixa { background: #fff; padding: 2rem 2.5rem; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,.15); max-width: 28rem; text-align: center; }
        h1 { margin: 0 0 .5rem; font-size: 1.5rem; }
        p { margin: .5rem 0 1.25rem; }
        a { color: #8a5a00; } a:focus-visible { outline: 2px solid #8a5a00; outline-offset: 2px; }
    </style>
</head>
<body>
    <div class="caixa">
        <h1>Algo deu errado</h1>
        <p>Não foi possível concluir a operação agora. Tente novamente em alguns instantes.</p>
        <a href="{{ url('/') }}">Voltar para o início</a>
    </div>
</body>
</html>
