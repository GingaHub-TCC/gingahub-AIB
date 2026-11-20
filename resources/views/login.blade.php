<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GingaHub</title>
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
                        accent: '#2b2724',
                        'accent-foreground': '#f7f6f4',
                        muted: '#efedea',
                        'muted-foreground': '#57534e',
                        border: '#dcd8d3',
                        input: '#ffffff',
                        ring: '#8a5a00',
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'], serif: ['Fraunces', 'serif'] },
                }
            }
        }
    </script>
    <style>
        /* Placeholder com contraste de 7,5:1 sobre o campo branco */
        body input::placeholder, body textarea::placeholder { color: #57534e; opacity: 1; }
        /* Foco visível para quem navega pelo teclado */
        a:focus-visible, button:focus-visible { outline: 2px solid #8a5a00; outline-offset: 2px; }
        /* Quem pede menos movimento no sistema não vê transições */
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { transition: none !important; animation: none !important; } }
    </style>
</head>
<body class="font-sans antialiased text-foreground bg-background min-h-screen flex flex-col items-center justify-center p-4">

    <a href="{{ url('/') }}" class="flex items-center gap-2 mb-8 group">
        <img src="{{ asset('favicon.png') }}" alt="Logo GingaHub" class="w-10 h-10 rounded-full object-cover object-center" />
        <span class="font-serif text-2xl font-bold text-foreground tracking-[-0.01em]">GingaHub</span>
    </a>

    <div class="bg-card w-full max-w-md rounded-2xl shadow-sm border border-border p-8">

        <div class="text-center mb-8">
            <h1 class="font-serif text-3xl font-bold text-foreground mb-2">Entrar no GingaHub</h1>
            <p class="text-muted-foreground text-sm">Informe seu e-mail e sua senha para acessar o sistema.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-500 p-4 mb-6 rounded-md" role="alert">
                <p class="text-sm text-red-700 font-medium">{{ $errors->first() }}</p>
            </div>
        @elseif(session('error'))
            <!-- Ex: sessão expirada por inatividade -->
            <div class="bg-yellow-50 border border-yellow-500 p-4 mb-6 rounded-md" role="status">
                <p class="text-sm text-yellow-800 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label for="email" class="block text-sm font-medium text-foreground mb-1"><span class="whitespace-nowrap">E-mail</span></label>
                <input type="email" id="email" name="email" placeholder="seu@email.com" required 
                       class="w-full bg-input border border-[#8a847e] rounded-md px-4 py-3 text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="senha" class="block text-sm font-medium text-foreground">Senha</label>
                    <a href="{{ route('password.request') }}" class="text-sm text-secondary hover:text-secondary/80 font-medium transition-colors">Esqueceu a senha?</a>
                </div>
                <input type="password" id="senha" name="senha" placeholder="••••••••" required 
                       class="w-full bg-input border border-[#8a847e] rounded-md px-4 py-3 text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
            </div>

            <button type="submit" class="w-full bg-primary text-primary-foreground font-semibold py-3 rounded-md hover:brightness-105 transition-all shadow-sm mt-2">
                Entrar
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-border text-center">
            <p class="text-muted-foreground text-sm">
                Ainda não tem conta? <br class="sm:hidden">
                <a href="{{ route('aluno.create') }}" class="text-secondary font-semibold hover:underline">Cadastre-se como aluno</a> ou
                <a href="{{ route('academia.create') }}" class="text-secondary font-semibold hover:underline">como academia</a>
            </p>
        </div>
    </div>

</body>
</html>