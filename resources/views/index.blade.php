<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GingaHub - Seu treino em um só lugar</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..900;1,9..144,400..900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
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
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=2">
    <style>
        /* Foco visível para quem navega pelo teclado */
        a:focus-visible, button:focus-visible { outline: 2px solid #f0ad15; outline-offset: 2px; }
        #sobre a:focus-visible { outline-color: #8a5a00; }
        /* Camadas sobre a foto do topo (a foto fica em tons de cinza, classe grayscale, para todas ganharem o mesmo tom):
           o âmbar da marca multiplica as cores da foto (fica tudo alaranjado)
           e uma sombra marrom-avermelhada escurece sem puxar para o oliva. Pior caso (branco puro na foto):
           fundo #a5600a, contraste de cerca de 4,9:1 com o texto branco */
        .camada-ambar { background-color: #f0ad15; mix-blend-mode: multiply; }
        .camada-escura { background-color: rgba(90, 20, 0, 0.5); }
        /* Quem pede menos movimento no sistema não vê rolagem suave nem transições */
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto !important; }
            *, *::before, *::after { transition: none !important; animation: none !important; }
        }
    </style>
</head>
<body class="font-sans text-foreground bg-background antialiased selection:bg-primary selection:text-primary-foreground">

    @php
        // Fotos de fundo do topo (licença livre; créditos em docs/creditos-imagens.md).
        // Uma é sorteada no servidor a cada carregamento da página.
        $fundos = [
            ['arquivo' => 'fundo-01.jpg', 'alt' => 'Silhueta de praticante de kung fu ao pôr do sol'],
            ['arquivo' => 'fundo-02.jpg', 'alt' => 'Dois praticantes de karatê treinando ao ar livre'],
            ['arquivo' => 'fundo-03.jpg', 'alt' => 'Treino de judô em um ginásio'],
            ['arquivo' => 'fundo-04.jpg', 'alt' => 'Dois lutadores de muay thai no ringue'],
            ['arquivo' => 'fundo-05.jpg', 'alt' => 'Treino de jiu-jitsu no tatame'],
            ['arquivo' => 'fundo-capoeira.png', 'alt' => 'Cena de roda de capoeira com berimbaus'],
        ];
        $fundo = $fundos[array_rand($fundos)];
    @endphp
    <section class="relative h-screen min-h-[600px] flex flex-col">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('img/fundos/' . $fundo['arquivo']) }}" alt="{{ $fundo['alt'] }}" class="w-full h-full object-cover object-center grayscale" />
            <!-- Camada âmbar (cor da marca) tingindo a foto, e uma camada escura para o texto branco ter contraste -->
            <div class="absolute inset-0 camada-ambar"></div>
            <div class="absolute inset-0 camada-escura"></div>
        </div>

        <header class="relative z-10 flex items-center justify-between px-6 py-5 md:px-12 lg:px-24">
            <div class="flex items-center gap-3">
                <img src="{{ asset('favicon.png') }}" alt="Logo GingaHub" class="w-10 h-10 rounded-full object-cover object-center" />
                <span class="font-serif text-2xl font-bold text-accent-foreground tracking-tighter">GingaHub</span>
            </div>
            
            <nav class="flex items-center gap-6">
                @auth
                    <!-- Já logado: atalho para o painel certo -->
                    <a href="{{ Auth::user()->pessoaJuridica ? route('academia.dashboard') : route('aluno.dashboard') }}" class="bg-primary text-primary-foreground px-5 py-2.5 rounded-md font-medium shadow-sm hover:brightness-105 transition-all">Meu painel</a>
                @else
                    <a href="{{ route('login') }}" class="font-medium text-accent-foreground hover:text-primary transition-colors">Entrar</a>
                    {{-- Contorno, para o botão âmbar do topo da página ser o único destaque --}}
                    <a href="{{ route('aluno.create') }}" class="hidden md:inline-block border border-accent-foreground/60 text-accent-foreground px-5 py-2.5 rounded-md font-medium hover:bg-accent-foreground/10 transition-colors">Cadastre-se</a>
                @endauth
            </nav>
        </header>

        @if(session('error'))
            <div class="relative z-10 mx-6 md:mx-12 lg:mx-24 bg-yellow-50 border border-yellow-500 p-4 rounded-md" role="status">
                <p class="text-sm text-yellow-800 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <div class="relative z-10 flex-1 flex items-center px-6 md:px-12 lg:px-24">
            <div class="max-w-2xl">
                <h1 class="font-serif text-5xl md:text-6xl lg:text-7xl font-bold text-accent-foreground leading-tight tracking-tighter mb-6">
                    Seu treino em<br>um só lugar.
                </h1>
                <p class="text-lg md:text-xl text-accent-foreground mb-10 max-w-lg leading-relaxed">
                    Encontre academias de artes marciais, faça sua matrícula, assine planos e veja seus treinos da semana.
                </p>
                <div class="flex flex-wrap items-center gap-4">
                    <a href="{{ route('aluno.create') }}" class="bg-primary text-primary-foreground px-8 py-3.5 rounded-md font-medium text-lg shadow-sm hover:brightness-105 transition-all">
                        Criar minha conta
                    </a>
                    <a href="#sobre" class="bg-transparent border-2 border-accent-foreground/30 text-accent-foreground hover:bg-accent-foreground/10 px-8 py-3.5 rounded-md font-medium text-lg transition-all">
                        Conhecer o projeto
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="sobre" class="py-24 px-6 md:px-12 lg:px-24 bg-background">
        <div class="max-w-7xl mx-auto">
            <div class="mb-12">
                <h2 class="font-serif text-4xl font-bold text-foreground tracking-tighter">O que dá para fazer no GingaHub</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-card rounded-2xl p-8 border border-border">
                    <h3 class="font-serif text-xl font-bold text-foreground mb-3">Para alunos</h3>
                    <ul class="text-muted-foreground leading-relaxed list-disc list-inside space-y-1">
                        <li>Encontrar academias e fazer a matrícula</li>
                        <li>Assinar um ou mais planos de cada academia</li>
                        <li>Ver os treinos da semana liberados pelos planos</li>
                        <li>Cancelar assinaturas e matrículas quando quiser</li>
                    </ul>
                </div>

                <div class="bg-card rounded-2xl p-8 border border-border">
                    <h3 class="font-serif text-xl font-bold text-foreground mb-3">Para academias</h3>
                    <ul class="text-muted-foreground leading-relaxed list-disc list-inside space-y-1">
                        <li>Cadastrar locais, instrutores e treinos da semana</li>
                        <li>Criar planos e escolher quais treinos cada um libera</li>
                        <li>Acompanhar alunos matriculados e assinaturas ativas</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-accent text-accent-foreground py-16 px-6 md:px-12 lg:px-24">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 border-b border-accent-foreground/20 pb-12 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <img src="{{ asset('favicon.png') }}" alt="Logo GingaHub" class="w-10 h-10 rounded-full object-cover object-center" />
                    <span class="font-serif text-2xl font-bold tracking-tighter">GingaHub</span>
                </div>
                <p class="text-accent-foreground/80 leading-relaxed max-w-sm">
                    Sistema que liga alunos de artes marciais às academias: matrícula, planos e grade de treinos em um só lugar.
                </p>
            </div>
            
            <div>
                <h4 class="font-serif text-lg font-bold mb-4">Acesso rápido</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('login') }}" class="text-accent-foreground/80 hover:text-white transition-colors">Entrar no sistema</a></li>
                    <li><a href="{{ route('aluno.create') }}" class="text-accent-foreground/80 hover:text-white transition-colors">Cadastrar aluno</a></li>
                    <li><a href="{{ route('academia.create') }}" class="text-accent-foreground/80 hover:text-white transition-colors">Cadastrar academia</a></li>
                    <li><a href="{{ route('password.request') }}" class="text-accent-foreground/80 hover:text-white transition-colors">Recuperar senha</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-serif text-lg font-bold mb-4">Contato</h4>
                <ul class="space-y-3">
                    <li class="text-accent-foreground/80">suporte@gingahub.com.br</li>
                    <li class="text-accent-foreground/80">WhatsApp: (11) 99999-9999</li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto text-center text-accent-foreground/60 text-sm">
            &copy; {{ date('Y') }} GingaHub. Todos os direitos reservados.
        </div>
        <p class="max-w-7xl mx-auto text-center text-accent-foreground/60 text-xs mt-3">
            Fotos: Alci Alliata, SOON SANTOS, Konstantinos Papadopoulos e Richard Bustos via Unsplash; Gleb Krasnoborov via Pexels; foto de capoeira de origem desconhecida.
        </p>
    </footer>

</body>
</html>