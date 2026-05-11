<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Aluno - GingaHub</title>
</head>
<body>
    <h1>Cadastro de Aluno (Básico)</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('aluno.store') }}" method="POST">
        @csrf <h3>Dados Pessoais</h3>
        <input type="text" name="nome" placeholder="Nome" value="{{ old('nome') }}" required><br>
        <input type="text" name="sobrenome" placeholder="Sobrenome" value="{{ old('sobrenome') }}" required><br>
        <input type="email" name="email" placeholder="E-mail" value="{{ old('email') }}" required><br>
        <input type="password" name="senha" placeholder="Senha" required><br>
        <input type="password" name="senha_confirmation" placeholder="Confirme sua Senha" required><br>
        <input type="text" name="cpf" placeholder="CPF" value="{{ old('cpf') }}" required><br>
        <input type="text" name="contato" placeholder="Contato" value="{{ old('contato') }}" required><br>
        
        <h3>Endereço</h3>
        <input type="text" name="cep" placeholder="CEP" value="{{ old('cep') }}" required><br>
        <input type="text" name="logradouro" placeholder="Rua" value="{{ old('logradouro') }}" required><br>
        <input type="text" name="numero" placeholder="Número" value="{{ old('numero') }}" required><br>
        <input type="text" name="bairro" placeholder="Bairro" value="{{ old('bairro') }}" required><br>
        <input type="text" name="cidade" placeholder="Cidade" value="{{ old('cidade') }}" required><br>
        <input type="text" name="estado" placeholder="UF (ex: ES)" maxlength="2" value="{{ old('estado') }}" required><br>

        <button type="submit">Finalizar Cadastro</button>
    </form>
</body>
</html>