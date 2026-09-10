<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Academia - GingaHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..900;1,9..144,400..900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { background: '#fdf4e3', foreground: '#281606', card: '#fffbf3', primary: '#f0ad15', 'primary-foreground': '#281606', secondary: '#286f2f', accent: '#733c1a', 'accent-foreground': '#fcf8f1', muted: '#f1e6d6', border: '#decbb1', input: '#e3d5c2', ring: '#f0ad15', },
                    fontFamily: { sans: ['Inter', 'sans-serif'], serif: ['Fraunces', 'serif'] },
                }
            }
        }
    </script>
    <style>
        .bg-pattern { background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23f0ad15' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); }
    </style>
</head>
<body class="font-sans antialiased text-foreground bg-accent bg-pattern min-h-screen flex flex-col items-center justify-center p-4 py-10">

    <a href="{{ url('/') }}" class="flex items-center gap-2 mb-6 group">
        <img src="{{ asset('favicon.png') }}" alt="Logo GingaHub" class="w-10 h-10 rounded-full object-cover object-center" />
        <span class="font-serif text-2xl font-bold text-accent-foreground tracking-[-0.01em]">GingaHub</span>
    </a>

    <div class="bg-card w-full max-w-2xl rounded-2xl shadow-xl border border-border overflow-hidden">
        
        <div class="grid grid-cols-2 border-b border-border bg-muted">
            <a href="{{ route('aluno.create') }}" class="text-center py-4 text-muted-foreground hover:bg-input/50 transition-colors font-medium border-r border-border">
                Sou Aluno
            </a>
            <div class="text-center py-4 bg-card border-b-2 border-primary font-semibold text-primary cursor-default">
                Sou Academia/Mestre
            </div>
        </div>

        <div class="p-8">
            <div class="mb-8">
                <h1 class="font-serif text-3xl font-bold text-foreground mb-2">Cadastro de Academia</h1>
                <p class="text-muted-foreground/80 text-sm">Organize suas turmas e faça a gestão do seu grupo na plataforma.</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
                    <ul class="list-disc list-inside text-sm text-red-700 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('academia.store') }}" method="POST" class="space-y-8">
                @csrf 
                
                <div>
                    <h3 class="font-serif text-xl text-secondary font-bold mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        Dados da Instituição
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">Responsável / Mestre</label>
                            <input type="text" name="nome" value="{{ old('nome') }}" required class="w-full bg-input border border-border rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">Nome Fantasia (Grupo)</label>
                            <input type="text" name="nome_fantasia" value="{{ old('nome_fantasia') }}" required class="w-full bg-input border border-border rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-foreground mb-1">Razão Social</label>
                            <input type="text" name="razao_social" value="{{ old('razao_social') }}" required class="w-full bg-input border border-border rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">CNPJ</label>
                            <input type="text" name="cnpj" value="{{ old('cnpj') }}" required class="w-full bg-input border border-border rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">E-mail Institucional</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-input border border-border rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-foreground mb-1">Contato Telefônico</label>
                            <input type="text" name="contato" value="{{ old('contato') }}" required class="w-full bg-input border border-border rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="font-serif text-xl text-secondary font-bold mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        Sede / Espaço
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-foreground mb-1">CEP</label>
                            <input type="text" name="cep" value="{{ old('cep') }}" required class="w-full bg-input border border-border rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-foreground mb-1">Logradouro</label>
                            <input type="text" name="logradouro" value="{{ old('logradouro') }}" required class="w-full bg-input border border-border rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-foreground mb-1">Número</label>
                            <input type="text" name="numero" value="{{ old('numero') }}" required class="w-full bg-input border border-border rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-foreground mb-1">Bairro</label>
                            <input type="text" name="bairro" value="{{ old('bairro') }}" required class="w-full bg-input border border-border rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-foreground mb-1">Cidade</label>
                            <input type="text" name="cidade" value="{{ old('cidade') }}" required class="w-full bg-input border border-border rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-foreground mb-1">UF</label>
                            <input type="text" name="estado" value="{{ old('estado') }}" maxlength="2" placeholder="Ex: BA" required class="w-full bg-input border border-border rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all uppercase">
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="font-serif text-xl text-secondary font-bold mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Segurança
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">Senha de Acesso</label>
                            <input type="password" name="senha" required class="w-full bg-input border border-border rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">Confirmar Senha</label>
                            <input type="password" name="senha_confirmation" required class="w-full bg-input border border-border rounded-md px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-border">
                    <label class="flex items-start gap-3 mb-6 cursor-pointer">
                        <input type="checkbox" required class="mt-1 accent-primary w-4 h-4 rounded border-border focus:ring-primary">
                        <span class="text-sm text-foreground/80">Declaro que li e concordo com os <a href="#" class="text-secondary font-semibold hover:underline">Termos de Uso</a> para Academias.</span>
                    </label>
                    
                    <button type="submit" class="w-full bg-primary text-primary-foreground font-semibold py-3 rounded-md hover:brightness-105 transition-all shadow-sm text-lg">
                        Finalizar Cadastro Institucional
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-foreground/70 text-sm">
                    Já possui conta? <a href="{{ route('login') }}" class="text-secondary font-semibold hover:underline">Faça login aqui</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>