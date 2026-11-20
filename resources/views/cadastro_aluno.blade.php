<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de aluno - GingaHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..900;1,9..144,400..900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { background: '#f7f6f4', foreground: '#1f1a17', card: '#ffffff', primary: '#f0ad15', 'primary-foreground': '#1f1a17', secondary: '#8a5a00', accent: '#2b2724', 'accent-foreground': '#f7f6f4', muted: '#efedea', 'muted-foreground': '#57534e', border: '#dcd8d3', input: '#ffffff', ring: '#8a5a00', },
                    fontFamily: { sans: ['Inter', 'sans-serif'], serif: ['Fraunces', 'serif'] },
                }
            }
        }
    </script>
    <style>
        /* Placeholder com contraste de 7,5:1 sobre o campo branco */
        body input::placeholder, body textarea::placeholder { color: #57534e; opacity: 1; }
        /* Foco visível para quem navega pelo teclado */
        a:focus-visible, button:focus-visible, input[type=checkbox]:focus-visible { outline: 2px solid #8a5a00; outline-offset: 2px; }
        /* Quem pede menos movimento no sistema não vê transições */
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { transition: none !important; animation: none !important; } }
    </style>
</head>
<body class="font-sans antialiased text-foreground bg-background min-h-screen flex flex-col items-center justify-center p-4 py-10">

    <a href="{{ url('/') }}" class="flex items-center gap-2 mb-6 group">
        <img src="{{ asset('favicon.png') }}" alt="Logo GingaHub" class="w-10 h-10 rounded-full object-cover object-center" />
        <span class="font-serif text-2xl font-bold text-foreground tracking-[-0.01em]">GingaHub</span>
    </a>

    <div class="bg-card w-full max-w-2xl rounded-2xl shadow-sm border border-border overflow-hidden">
        
        <div class="grid grid-cols-2 border-b border-border bg-muted">
            <div class="text-center py-4 bg-card border-b-2 border-primary font-semibold text-foreground cursor-default" aria-current="page">
                Sou aluno
            </div>
            <a href="{{ route('academia.create') }}" class="text-center py-4 text-muted-foreground hover:bg-input/50 transition-colors font-medium">
                Sou academia
            </a>
        </div>

        <div class="p-8">
            <div class="mb-8">
                <h1 class="font-serif text-3xl font-bold text-foreground mb-2">Cadastro de aluno</h1>
                <p class="text-muted-foreground text-sm">Comece a treinar com o GingaHub. Preencha os dados abaixo para criar seu perfil.</p>
            </div>

            @if (session('success'))
                <div class="bg-green-50 border border-green-600 p-4 mb-6 rounded-md" role="status">
                    <p class="text-sm text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-500 p-4 mb-6 rounded-md" role="alert">
                    <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-500 p-4 mb-6 rounded-md" role="alert">
                    <ul class="list-disc list-inside text-sm text-red-700 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('aluno.store') }}" method="POST" class="space-y-8">
                @csrf 
                
                <div>
                    <h2 class="font-serif text-xl text-foreground font-bold mb-4">
                        Dados pessoais
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="campo-nome" class="block text-sm font-medium text-foreground mb-1">Nome</label>
                            <input type="text" id="campo-nome" name="nome" value="{{ old('nome') }}" required class="w-full bg-input border border-[#8a847e] rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label for="campo-sobrenome" class="block text-sm font-medium text-foreground mb-1">Sobrenome</label>
                            <input type="text" id="campo-sobrenome" name="sobrenome" value="{{ old('sobrenome') }}" required class="w-full bg-input border border-[#8a847e] rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label for="campo-email" class="block text-sm font-medium text-foreground mb-1"><span class="whitespace-nowrap">E-mail</span></label>
                            <input type="email" id="campo-email" name="email" value="{{ old('email') }}" required class="w-full bg-input border border-[#8a847e] rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label for="campo-contato" class="block text-sm font-medium text-foreground mb-1">Contato (WhatsApp)</label>
                            <input type="text" id="campo-contato" name="contato" value="{{ old('contato') }}" required class="w-full bg-input border border-[#8a847e] rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label for="campo-cpf" class="block text-sm font-medium text-foreground mb-1">CPF</label>
                            <input type="text" id="campo-cpf" name="cpf" value="{{ old('cpf') }}" required class="w-full bg-input border border-[#8a847e] rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="font-serif text-xl text-foreground font-bold mb-4">
                        Endereço
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-1">
                            <label for="campo-cep" class="block text-sm font-medium text-foreground mb-1">CEP</label>
                            <input type="text" id="campo-cep" name="cep" value="{{ old('cep') }}" required class="w-full bg-input border border-[#8a847e] rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label for="campo-logradouro" class="block text-sm font-medium text-foreground mb-1">Rua ou logradouro</label>
                            <input type="text" id="campo-logradouro" name="logradouro" value="{{ old('logradouro') }}" required class="w-full bg-input border border-[#8a847e] rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div class="md:col-span-1">
                            <label for="campo-numero" class="block text-sm font-medium text-foreground mb-1">Número</label>
                            <input type="text" id="campo-numero" name="numero" value="{{ old('numero') }}" required class="w-full bg-input border border-[#8a847e] rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label for="campo-bairro" class="block text-sm font-medium text-foreground mb-1">Bairro</label>
                            <input type="text" id="campo-bairro" name="bairro" value="{{ old('bairro') }}" required class="w-full bg-input border border-[#8a847e] rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div class="md:col-span-1">
                            <label for="campo-cidade" class="block text-sm font-medium text-foreground mb-1">Cidade</label>
                            <input type="text" id="campo-cidade" name="cidade" value="{{ old('cidade') }}" required class="w-full bg-input border border-[#8a847e] rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div class="md:col-span-1">
                            <label for="campo-estado" class="block text-sm font-medium text-foreground mb-1">UF</label>
                            <input type="text" id="campo-estado" name="estado" value="{{ old('estado') }}" maxlength="2" placeholder="BA" required class="placeholder-muted-foreground w-full bg-input border border-[#8a847e] rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all uppercase">
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="font-serif text-xl text-foreground font-bold mb-4">
                        Segurança
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="campo-senha" class="block text-sm font-medium text-foreground mb-1">Senha</label>
                            <input type="password" id="campo-senha" name="senha" required class="w-full bg-input border border-[#8a847e] rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label for="campo-senha_confirmation" class="block text-sm font-medium text-foreground mb-1">Confirmar senha</label>
                            <input type="password" id="campo-senha_confirmation" name="senha_confirmation" required class="w-full bg-input border border-[#8a847e] rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-border">
                    <button type="submit" class="w-full bg-primary text-primary-foreground font-semibold py-3 rounded-md hover:brightness-105 transition-all shadow-sm text-lg">
                        Finalizar cadastro
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-muted-foreground text-sm">
                    Já possui conta? <a href="{{ route('login') }}" class="text-secondary font-semibold hover:underline">Faça login aqui</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>