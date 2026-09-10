<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GingaHub - Toda a roda em um só lugar</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..900;1,9..144,400..900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
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
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=2">
</head>
<body class="font-sans text-foreground bg-background antialiased selection:bg-primary selection:text-primary-foreground">

    <section class="relative h-screen min-h-[600px] flex flex-col">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('fundo-capoeira.png') }}" alt="Roda de Capoeira" class="w-full h-full object-cover object-center" />
        </div>

        <header class="relative z-10 flex items-center justify-between px-6 py-5 md:px-12 lg:px-24">
            <div class="flex items-center gap-3">
                <img src="{{ asset('favicon.png') }}" alt="Logo GingaHub" class="w-10 h-10 rounded-full object-cover object-center" />
                <span class="font-serif text-2xl font-bold text-accent-foreground tracking-tighter">GingaHub</span>
            </div>
            
            <nav class="hidden md:flex items-center gap-6">
                <a href="{{ route('login') }}" class="font-medium text-accent-foreground hover:text-primary transition-colors">Entrar</a>
                <a href="{{ route('aluno.create') }}" class="bg-primary text-primary-foreground px-5 py-2.5 rounded-md font-medium shadow-sm hover:brightness-105 transition-all">Cadastre-se</a>
            </nav>
        </header>

        <div class="relative z-10 flex-1 flex items-center px-6 md:px-12 lg:px-24">
            <div class="max-w-2xl">
                <span class="inline-block px-4 py-1.5 rounded-full bg-accent text-primary text-sm font-medium border border-primary/20 mb-6 shadow-sm">
                    Axé · Comunidade · Tradição
                </span>
                <h1 class="font-serif text-5xl md:text-6xl lg:text-7xl font-bold text-accent-foreground leading-tight tracking-tighter mb-6">
                    Toda a roda em<br>um só lugar.
                </h1>
                <p class="text-lg md:text-xl text-accent-foreground/90 mb-10 max-w-lg font-light leading-relaxed">
                    Fortaleça sua capoeira. Encontre academias, conecte-se com mestres e faça parte da maior comunidade digital da nossa tradição.
                </p>
                <div class="flex flex-wrap items-center gap-4">
                    <a href="{{ route('aluno.create') }}" class="bg-primary text-primary-foreground px-8 py-3.5 rounded-md font-medium text-lg shadow-sm hover:brightness-105 transition-all">
                        Fazer parte da Roda
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
            <div class="text-center mb-16">
                <span class="text-secondary font-semibold tracking-wide uppercase text-sm">O que oferecemos</span>
                <h2 class="font-serif text-4xl font-bold text-foreground mt-3 tracking-tighter">O sistema completo para a sua ginga</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-card rounded-2xl p-8 border border-border shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="w-14 h-14 rounded-2xl bg-primary text-primary-foreground flex items-center justify-center mb-6 relative z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-foreground mb-3 relative z-10">Para Alunos</h3>
                    <p class="text-muted-foreground leading-relaxed relative z-10">Acompanhe sua evolução, encontre rodas e gerencie suas matrículas de forma simples e rápida.</p>
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-muted rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                </div>

                <div class="bg-card rounded-2xl p-8 border border-border shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="w-14 h-14 rounded-2xl bg-primary text-primary-foreground flex items-center justify-center mb-6 relative z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-foreground mb-3 relative z-10">Para Mestres e Academias</h3>
                    <p class="text-muted-foreground leading-relaxed relative z-10">Gestão de turmas, controle financeiro, histórico de graduações e organização de eventos.</p>
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-muted rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                </div>

                <div class="bg-card rounded-2xl p-8 border border-border shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="w-14 h-14 rounded-2xl bg-primary text-primary-foreground flex items-center justify-center mb-6 relative z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-foreground mb-3 relative z-10">Comunidade</h3>
                    <p class="text-muted-foreground leading-relaxed relative z-10">Mantenha viva a tradição. Compartilhe fundamentos, músicas e conecte-se com capoeiristas do mundo todo.</p>
                    <div class="absolute -right-8 -top-8 w-32 h-32 bg-muted rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
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
                    Nossa missão é unir a tecnologia à ancestralidade, facilitando a gestão e fortalecendo as raízes da capoeira.
                </p>
            </div>
            
            <div>
                <h4 class="font-serif text-lg font-bold mb-4 text-primary">Acesso Rápido</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('login') }}" class="text-accent-foreground/80 hover:text-white transition-colors">Entrar no sistema</a></li>
                    <li><a href="{{ route('aluno.create') }}" class="text-accent-foreground/80 hover:text-white transition-colors">Cadastrar Aluno</a></li>
                    <li><a href="{{ route('academia.create') }}" class="text-accent-foreground/80 hover:text-white transition-colors">Cadastrar Academia</a></li>
                    <li><a href="{{ route('password.request') }}" class="text-accent-foreground/80 hover:text-white transition-colors">Recuperar Senha</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-serif text-lg font-bold mb-4 text-primary">Contato</h4>
                <ul class="space-y-3">
                    <li class="text-accent-foreground/80">suporte@capoeiraunion.com.br</li>
                    <li class="text-accent-foreground/80">WhatsApp: (11) 99999-9999</li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto text-center text-accent-foreground/60 text-sm">
            &copy; {{ date('Y') }} CapoeiraUnion. Salve a capoeiragem.
        </div>
    </footer>

</body>
</html>