<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Academias Disponíveis - GingaHub</title>
</head>
<body>
    <h1>Academias Parceiras</h1>
    <p>Escolha uma academia para se matricular:</p>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Nome Fantasia</th>
                <th>Cidade</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($academias as $academia)
            <tr>
                <td>{{ $academia->nome_fantasia }}</td>
                <td>{{ $academia->pessoa->endereco->cidade }}</td>
                <td>
                    @php
                        $matricula = $minhasMatriculas->get($academia->id);
                    @endphp

                    @if(!$matricula)
                        <form action="{{ route('aluno.matricular', $academia->id) }}" method="POST">
                            @csrf
                            <button type="submit">Matricular-se</button>
                        </form>
                    @elseif($matricula->pivot->status === 'ativa')
                        <form action="{{ route('aluno.desmatricular', $academia->id) }}" method="POST">
                            @csrf
                            <button type="submit">Desmatricular-se</button>
                        </form>
                    @else
                        <form action="{{ route('aluno.matricular', $academia->id) }}" method="POST">
                            @csrf
                            <button type="submit" style="background-color: orange;">Rematricular-se</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if(count($academias) == 0)
        <p>Nenhuma academia cadastrada ainda.</p>
    @endif
</body>
</html>