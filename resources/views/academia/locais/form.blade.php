@extends('layouts.painel')

@section('titulo', $local ? 'Editar local' : 'Novo local')

@section('conteudo')
    @php $end = $local->endereco ?? null; @endphp

    <a href="{{ route('academia.locais.index') }}" class="text-sm text-secondary font-medium hover:underline">&larr; Voltar para locais</a>
    <h1 class="font-serif text-3xl font-bold mt-2 mb-6">{{ $local ? 'Editar local' : 'Novo local' }}</h1>

    <form action="{{ $local ? route('academia.locais.update', $local->id) : route('academia.locais.store') }}" method="POST" class="bg-card border border-border rounded-2xl p-6 md:p-8 space-y-5 max-w-3xl">
        @csrf
        @if($local) @method('PUT') @endif

        <div>
            <label for="nome" class="rotulo">Nome do local *</label>
            <input type="text" id="nome" name="nome" value="{{ old('nome', $local->nome ?? '') }}" placeholder="Ex: Sede Centro, Sala 2, Praça da Matriz" required maxlength="100" class="campo">
        </div>

        <div class="grid md:grid-cols-6 gap-5">
            <div class="md:col-span-2">
                <label for="cep" class="rotulo">CEP *</label>
                <input type="text" id="cep" name="cep" value="{{ old('cep', $end->cep ?? '') }}" placeholder="29700-000" required class="campo">
            </div>
            <div class="md:col-span-4">
                <label for="logradouro" class="rotulo">Logradouro *</label>
                <input type="text" id="logradouro" name="logradouro" value="{{ old('logradouro', $end->logradouro ?? '') }}" required maxlength="150" class="campo">
            </div>
            <div class="md:col-span-2">
                <label for="numero" class="rotulo">Número *</label>
                <input type="text" id="numero" name="numero" value="{{ old('numero', $end->numero ?? '') }}" required maxlength="20" class="campo">
            </div>
            <div class="md:col-span-4">
                <label for="complemento" class="rotulo">Complemento</label>
                <input type="text" id="complemento" name="complemento" value="{{ old('complemento', $end->complemento ?? '') }}" maxlength="100" class="campo">
            </div>
            <div class="md:col-span-2">
                <label for="bairro" class="rotulo">Bairro *</label>
                <input type="text" id="bairro" name="bairro" value="{{ old('bairro', $end->bairro ?? '') }}" required maxlength="100" class="campo">
            </div>
            <div class="md:col-span-3">
                <label for="cidade" class="rotulo">Cidade *</label>
                <input type="text" id="cidade" name="cidade" value="{{ old('cidade', $end->cidade ?? '') }}" required maxlength="100" class="campo">
            </div>
            <div class="md:col-span-1">
                <label for="estado" class="rotulo">UF *</label>
                <input type="text" id="estado" name="estado" value="{{ old('estado', $end->estado ?? '') }}" placeholder="ES" required maxlength="2" class="campo uppercase">
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn btn-primario">{{ $local ? 'Salvar alterações' : 'Cadastrar local' }}</button>
            <a href="{{ route('academia.locais.index') }}" class="btn btn-contorno">Cancelar</a>
        </div>
    </form>

    <script>
        // Preenche o endereço automaticamente pelo CEP (ViaCEP)
        document.getElementById('cep').addEventListener('blur', function () {
            const cep = this.value.replace(/\D/g, '');
            if (cep.length !== 8) return;
            fetch('https://viacep.com.br/ws/' + cep + '/json/')
                .then(r => r.json())
                .then(d => {
                    if (d.erro) return;
                    const preencher = (id, valor) => { const el = document.getElementById(id); if (!el.value) el.value = valor || ''; };
                    preencher('logradouro', d.logradouro);
                    preencher('bairro', d.bairro);
                    preencher('cidade', d.localidade);
                    preencher('estado', d.uf);
                })
                .catch(() => {});
        });
    </script>
@endsection
