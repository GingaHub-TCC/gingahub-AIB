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
                        background: '#fdf4e3',
                        foreground: '#281606',
                        card: '#fffbf3',
                        primary: '#f0ad15',
                        'primary-foreground': '#281606',
                        secondary: '#286f2f',
                        accent: '#733c1a',
                        'accent-foreground': '#fcf8f1',
                        muted: '#f1e6d6',
                        border: '#decbb1',
                        input: '#e3d5c2',
                        ring: '#f0ad15',
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'], serif: ['Fraunces', 'serif'] },
                }
            }
        }
    </script>
    <style>
        /* Padrão geométrico sutil para o fundo */
        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23f0ad15' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="font-sans antialiased text-foreground bg-accent bg-pattern min-h-screen flex flex-col items-center justify-center p-4">

    <a href="{{ url('/') }}" class="flex items-center gap-2 mb-8 group">
        <img src="{{ asset('favicon.png') }}" alt="Logo GingaHub" class="w-10 h-10 rounded-full object-cover object-center" />
        <span class="font-serif text-3xl font-bold text-accent-foreground tracking-[-0.01em]">GingaHub  </span>
    </a>

    <div class="bg-card w-full max-w-md rounded-2xl shadow-xl border border-border p-8">
        
        <div class="text-center mb-8">
            <h1 class="font-serif text-3xl font-bold text-foreground mb-2">Entrar na Roda</h1>
            <p class="text-muted-foreground/80 text-sm">Bem-vindo de volta! Insira seus dados para acessar o sistema.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
                <p class="text-sm text-red-700 font-medium">{{ $errors->first() }}</p>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label for="email" class="block text-sm font-medium text-foreground mb-1">E-mail</label>
                <input type="email" id="email" name="email" placeholder="mestre@capoeira.com" required 
                       class="w-full bg-input border border-border rounded-md px-4 py-3 text-foreground placeholder-foreground/40 focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="senha" class="block text-sm font-medium text-foreground">Senha</label>
                    <a href="{{ route('password.request') }}" class="text-sm text-secondary hover:text-secondary/80 font-medium transition-colors">Esqueceu a senha?</a>
                </div>
                <input type="password" id="senha" name="senha" placeholder="••••••••" required 
                       class="w-full bg-input border border-border rounded-md px-4 py-3 text-foreground placeholder-foreground/40 focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
            </div>

            <button type="submit" class="w-full bg-primary text-primary-foreground font-semibold py-3 rounded-md hover:brightness-105 transition-all shadow-sm mt-2">
                Entrar
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-border text-center">
            <p class="text-foreground/70 text-sm">
                Ainda não tem conta? <br class="sm:hidden">
                <a href="{{ route('aluno.create') }}" class="text-secondary font-semibold hover:underline">Cadastre-se como Aluno</a> ou 
                <a href="{{ route('academia.create') }}" class="text-secondary font-semibold hover:underline">Academia</a>
            </p>
        </div>
    </div>

</body>
</html>