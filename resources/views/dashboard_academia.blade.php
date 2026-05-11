<h1>Painel da Academia: {{ Auth::user()->pessoaJuridica->nome_fantasia }}</h1>
<p>Bem-vindo ao seu painel administrativo.</p>
<form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit">Sair</button>
</form>