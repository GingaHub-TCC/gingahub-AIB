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
                        background: '#fdf4e3',
                        foreground: '#281606',
                        card: '#fffbf3',
                        primary: '#f0ad15',
                        'primary-foreground': '#281606',
                        secondary: '#286f2f',
                        'secondary-foreground': '#fcf8f1',
                        accent: '#733c1a',
                        'accent-foreground': '#fcf8f1',
                        muted: '#f1e6d6',
                        'muted-foreground': '#614731',
                        border: '#decbb1',
                        input: '#e3d5c2',
                        ring: '#f0ad15',
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
        .campo { width: 100%; background: #e3d5c2; border: 1px solid #decbb1; border-radius: .375rem; padding: .65rem 1rem; color: #281606; }
        .campo:focus { outline: none; box-shadow: 0 0 0 2px #f0ad15; border-color: transparent; }
        .rotulo { display: block; font-size: .875rem; font-weight: 500; margin-bottom: .25rem; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: .4rem; padding: .55rem 1.1rem; border-radius: .375rem; font-weight: 500; font-size: .875rem; transition: all .15s; white-space: nowrap; }
        .btn-primario { background: #f0ad15; color: #281606; box-shadow: 0 1px 2px rgba(0,0,0,.05); }
        .btn-primario:hover { filter: brightness(1.05); }
        .btn-secundario { background: #286f2f; color: #fcf8f1; }
        .btn-secundario:hover { filter: brightness(1.1); }
        .btn-contorno { border: 1px solid #733c1a; color: #733c1a; }
        .btn-contorno:hover { background: #733c1a; color: #fcf8f1; }
        .btn-perigo { border: 1px solid #b91c1c; color: #b91c1c; }
        .btn-perigo:hover { background: #b91c1c; color: #fff; }
        .btn-pequeno { padding: .35rem .75rem; font-size: .8rem; }
        .menu-link { display: flex; align-items: center; gap: .6rem; padding: .55rem .9rem; border-radius: .375rem; color: rgba(252,248,241,.8); font-weight: 500; font-size: .9rem; }
        .menu-link:hover { background: rgba(252,248,241,.08); color: #fff; }
        .menu-link.ativo { background: #f0ad15; color: #281606; }
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
            <span class="md:hidden text-xs uppercase tracking-wider bg-white/10 px-2 py-1 rounded">{{ $ehAcademia ? 'Academia' : 'Aluno' }}</span>
        </div>

        <nav class="px-3 pb-3 md:pb-0 flex md:flex-col gap-1 overflow-x-auto">
            @foreach($menu as [$rota, $rotulo, $padrao])
                <a href="{{ route($rota) }}" class="menu-link {{ request()->routeIs($padrao) ? 'ativo' : '' }}">{{ $rotulo }}</a>
            @endforeach
        </nav>

        <div class="hidden md:block mt-auto p-4 border-t border-white/10">
            <p class="text-xs uppercase tracking-wider text-accent-foreground/60 mb-1">{{ $ehAcademia ? 'Academia' : 'Aluno' }}</p>
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
                    <div class="border-l-4 p-4 mb-6 rounded-md {{ $classes }}">
                        <p class="text-sm font-medium">{{ session($tipo) }}</p>
                    </div>
                @endif
            @endforeach

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
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
<div id="aviso-sessao" class="hidden fixed bottom-4 right-4 left-4 md:left-auto md:w-96 bg-card border border-border shadow-xl rounded-xl p-4 z-50">
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
