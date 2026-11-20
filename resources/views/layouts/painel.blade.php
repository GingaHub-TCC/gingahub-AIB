<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Painel') - GingaHub</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=2">

    <!-- Fontes do Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..900;1,9..144,400..900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via CDN (mesmo tema das outras telas) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        /* Tons neutros + um destaque (âmbar). "secondary" é o âmbar escuro, para texto e links com contraste */
                        background: '#f7f6f4',
                        foreground: '#1f1a17',
                        card: '#ffffff',
                        primary: '#f0ad15',
                        'primary-foreground': '#1f1a17',
                        secondary: '#8a5a00',
                        'secondary-foreground': '#ffffff',
                        accent: '#2b2724',
                        'accent-foreground': '#f7f6f4',
                        muted: '#efedea',
                        'muted-foreground': '#57534e',
                        border: '#dcd8d3',
                        input: '#ffffff',
                        ring: '#8a5a00',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Fraunces', 'serif'],
                    },
                    letterSpacing: {
                        tighter: '-0.01em',
                    }
                }
            }
        }
    </script>
    <style>
        /* Classes reaproveitadas nos formulários e botões dos painéis */
        .campo { width: 100%; background: #ffffff; border: 1px solid #8a847e; border-radius: .375rem; padding: .65rem 1rem; color: #1f1a17; }
        .campo:focus { outline: none; box-shadow: 0 0 0 2px #8a5a00; border-color: transparent; }
        .rotulo { display: block; font-size: .875rem; font-weight: 500; margin-bottom: .25rem; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: .4rem; padding: .55rem 1.1rem; border-radius: .375rem; font-weight: 500; font-size: .875rem; transition: all .15s; white-space: nowrap; }
        .btn-primario { background: #f0ad15; color: #1f1a17; }
        .btn-primario:hover { filter: brightness(1.05); }
        .btn-secundario { background: #8a5a00; color: #ffffff; }
        .btn-secundario:hover { filter: brightness(1.1); }
        .btn-contorno { border: 1px solid #8a847e; color: #1f1a17; }
        .btn-contorno:hover { background: #efedea; }
        /* Foco visível para quem navega pelo teclado */
        a:focus-visible, button:focus-visible, select:focus-visible, input[type=checkbox]:focus-visible { outline: 2px solid #8a5a00; outline-offset: 2px; }
        aside a:focus-visible, aside button:focus-visible { outline-color: #f0ad15; }
        .btn-perigo { border: 1px solid #b91c1c; color: #b91c1c; }
        .btn-perigo:hover { background: #b91c1c; color: #fff; }
        .btn-pequeno { padding: .35rem .75rem; font-size: .8rem; }
        .menu-link { display: flex; align-items: center; gap: .6rem; padding: .55rem .9rem; border-radius: .375rem; color: rgba(252,248,241,.8); font-weight: 500; font-size: .9rem; }
        .menu-link:hover { background: rgba(252,248,241,.08); color: #fff; }
        .menu-link.ativo { background: #f0ad15; color: #281606; }
        /* Horários e valores alinhados em coluna, sem fonte monoespaçada */
        .numeros { font-variant-numeric: tabular-nums; }
        /* Escala de títulos dos painéis: página 30/36px, seção 20px, item 18px, texto 16/14px */
        /* Quem pede menos movimento no sistema não vê transições */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { transition: none !important; animation: none !important; scroll-behavior: auto !important; }
        }
    </style>
</head>
<body class="font-sans antialiased text-foreground bg-background min-h-screen">

@php
    $usuario = Auth::user();
    $ehAcademia = (bool) $usuario->pessoaJuridica;
    $menu = $ehAcademia
        ? [
            ['academia.dashboard', 'Visão geral', 'academia.dashboard'],
            ['academia.alunos', 'Alunos', 'academia.alunos'],
            ['academia.planos.index', 'Planos', 'academia.planos.*'],
            ['academia.treinos.index', 'Treinos', 'academia.treinos.*'],
            ['academia.instrutores.index', 'Instrutores', 'academia.instrutores.*'],
            ['academia.locais.index', 'Locais', 'academia.locais.*'],
        ]
        : [
            ['aluno.dashboard', 'Meu painel', 'aluno.dashboard'],
            ['academias.listar', 'Academias', 'academias.listar'],
        ];
    $nomeExibicao = $ehAcademia ? $usuario->pessoaJuridica->nome_fantasia : $usuario->nome;
@endphp

<div class="md:flex min-h-screen">

    <!-- Menu lateral -->
    <aside class="bg-accent text-accent-foreground md:w-64 md:min-h-screen md:sticky md:top-0 md:h-screen flex flex-col">
        <div class="flex items-center justify-between md:block p-4 md:p-6">
            <a href="{{ route($menu[0][0]) }}" class="flex items-center gap-3">
                <img src="{{ asset('favicon.png') }}" alt="Logo GingaHub" class="w-10 h-10 rounded-full object-cover object-center" />
                <span class="font-serif text-2xl font-bold tracking-tighter">GingaHub</span>
            </a>
            <span class="md:hidden text-sm text-accent-foreground/80">{{ $ehAcademia ? 'Academia' : 'Aluno' }}</span>
        </div>

        <nav aria-label="Menu principal" class="px-3 pb-3 md:pb-0 flex md:flex-col gap-1 overflow-x-auto">
            @foreach($menu as [$rota, $rotulo, $padrao])
                <a href="{{ route($rota) }}" class="menu-link {{ request()->routeIs($padrao) ? 'ativo' : '' }}" @if(request()->routeIs($padrao)) aria-current="page" @endif>{{ $rotulo }}</a>
            @endforeach
        </nav>

        <div class="hidden md:block mt-auto p-4 border-t border-white/10">
            <p class="text-sm text-accent-foreground/80 mb-1">Conta de {{ $ehAcademia ? 'academia' : 'aluno' }}</p>
            <p class="font-medium truncate mb-3" title="{{ $nomeExibicao }}">{{ $nomeExibicao }}</p>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full text-left menu-link">Sair</button>
            </form>
        </div>
    </aside>

    <!-- Conteúdo -->
    <main class="flex-1 min-w-0 p-4 md:p-8">
        <div class="max-w-6xl mx-auto">

            <div class="md:hidden flex justify-end mb-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-contorno btn-pequeno">Sair</button>
                </form>
            </div>

            <!-- Mensagens de retorno (success, warning, error) -->
            @foreach(['success' => 'bg-green-50 border-green-600 text-green-800', 'warning' => 'bg-yellow-50 border-yellow-500 text-yellow-800', 'error' => 'bg-red-50 border-red-500 text-red-700'] as $tipo => $classes)
                @if(session($tipo))
                    <div class="border p-4 mb-6 rounded-md {{ $classes }}" role="status">
                        <p class="text-sm font-medium">{{ session($tipo) }}</p>
                    </div>
                @endif
            @endforeach

            @if($errors->any())
                <div class="bg-red-50 border border-red-500 p-4 mb-6 rounded-md" role="alert">
                    <ul class="text-sm text-red-700 font-medium list-disc list-inside space-y-1">
                        @foreach($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('conteudo')
        </div>
    </main>
</div>

<!-- Aviso de sessão: o servidor encerra após 5 min sem atividade (CheckInactivity) -->
<div id="aviso-sessao" class="hidden fixed bottom-4 right-4 left-4 md:left-auto md:w-96 bg-card border border-border shadow-md rounded-xl p-4 z-50" role="alert">
    <p class="font-medium mb-1">Sua sessão vai expirar</p>
    <p class="text-sm text-muted-foreground mb-3">Por segurança, você será desconectado por inatividade em <strong id="contador-sessao">60</strong>s.</p>
    <button type="button" onclick="window.location.reload()" class="btn btn-primario btn-pequeno">Continuar conectado</button>
</div>
<script>
    (function () {
        const limite = 5 * 60;   // mesmo tempo do middleware CheckInactivity
        const aviso = 60;        // mostra o aviso no último minuto
        let restante = limite;
        setInterval(function () {
            restante--;
            if (restante <= aviso) {
                document.getElementById('aviso-sessao').classList.remove('hidden');
                document.getElementById('contador-sessao').textContent = Math.max(restante, 0);
            }
            // Alguns segundos de folga para o servidor já considerar a sessão expirada
            if (restante <= -3) {
                window.location.href = "{{ route('login') }}";
            }
        }, 1000);
    })();
</script>
</body>
</html>
