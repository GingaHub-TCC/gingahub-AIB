<h1>Recuperar Senha</h1>
@if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
<form action="{{ route('password.request') }}" method="POST">
    @csrf
    <input type="email" name="email" placeholder="Digite seu e-mail cadastrado" required>
    <button type="submit">Enviar link de recuperação</button>
</form>