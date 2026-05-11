<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>Login - GingaHub</title></head>
<body>
    <h1>Login GingaHub</h1>

    @if($errors->any())
        <p style="color:red">{{ $errors->first() }}</p>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <input type="email" name="email" placeholder="E-mail" required><br>
        <input type="password" name="senha" placeholder="Senha" required><br>
        <button type="submit">Entrar</button>

        <br><br>
        <a href="{{ route('password.request') }}">Esqueci minha senha</a>
    </form>

    <hr>
    <p>Ainda não tem conta? <a href="{{ route('aluno.create') }}">Cadastre-se como Aluno</a> ou <a href="{{ route('academia.create') }}">como Academia</a></p>
</body>
</html>
