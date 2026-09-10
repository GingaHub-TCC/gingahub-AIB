<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academias Disponíveis - CapoeiraUnion</title>
    
    <!-- Fontes do Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..900;1,9..144,400..900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS via CDN -->
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
</head>
<body class="font-sans antialiased text-foreground bg-background min-h-screen p-4 md:p-8">

    <!-- Header Simples Interno -->
    <header class="max-w-5xl mx-auto mb-10 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
            <img src="{{ asset('favicon.png') }}" alt="Logo GingaHub" class="w-10 h-10 rounded-full object-cover object-center" />
            <span class="font-serif text-2xl font-bold text-accent tracking-tighter">CapoeiraUnion</span>
        </a>
    </header>

    <main class="max-w-5xl mx-auto">
        <!-- Título da Página -->
        <div class="mb-8">
            <h1 class="font-serif text-3xl md:text-4xl font-bold text-foreground mb-2">Academias Parceiras</h1>
            <p class="text-muted-foreground/80 text-lg">Encontre sua roda. Escolha uma academia abaixo para se matricular.</p>
        </div>

        <!-- Container da Tabela -->
        <div class="bg-card rounded-2xl border border-border shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-muted text-muted-foreground font-semibold text-sm uppercase tracking-wider border-b border-border">
                            <th class="px-6 py-4">Nome Fantasia</th>
                            <th class="px-6 py-4">Cidade</th>
                            <th class="px-6 py-4 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        
                        @foreach($academias as $academia)
                        <tr class="hover:bg-input/10 transition-colors group">
                            <td class="px-6 py-4 font-medium text-foreground">
                                {{ $academia->nome_fantasia }}
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                <div class="flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-accent/60"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    {{ $academia->pessoa->endereco->cidade }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @php
                                    $matricula = $minhasMatriculas->get($academia->id);
                                @endphp

                                @if(!$matricula)
                                    <!-- Botão Matricular -->
                                    <form action="{{ route('aluno.matricular', $academia->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="bg-primary text-primary-foreground px-5 py-2 rounded-md font-medium text-sm shadow-sm hover:brightness-105 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:ring-offset-card transition-all">
                                            Matricular-se
                                        </button>
                                    </form>
                                @elseif($matricula->pivot->status === 'ativa')
                                    <!-- Botão Desmatricular -->
                                    <form action="{{ route('aluno.desmatricular', $academia->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="bg-transparent border border-accent text-accent hover:bg-accent hover:text-accent-foreground px-5 py-2 rounded-md font-medium text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-card transition-all">
                                            Desmatricular-se
                                        </button>
                                    </form>
                                @else
                                    <!-- Botão Rematricular -->
                                    <form action="{{ route('aluno.matricular', $academia->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="bg-secondary text-secondary-foreground px-5 py-2 rounded-md font-medium text-sm shadow-sm hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 focus:ring-offset-card transition-all">
                                            Rematricular-se
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            @if(count($academias) == 0)
                <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                    <div class="w-16 h-16 rounded-full bg-muted flex items-center justify-center text-accent mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <h3 class="font-serif text-xl font-semibold text-foreground mb-1">Nenhuma academia encontrada</h3>
                    <p class="text-muted-foreground max-w-sm">Ainda não há academias parceiras cadastradas no sistema. Volte novamente mais tarde!</p>
                </div>
            @endif
        </div>
    </main>

</body>
</html>