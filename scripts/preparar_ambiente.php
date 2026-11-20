<?php

/*
 * Prepara o ambiente do GingaHub para o sistema web rodar.
 *
 * - cria o .env a partir do .env.example (se não existir);
 * - ajusta o .env para MySQL/MariaDB (só o que estiver faltando);
 * - gera a APP_KEY (só se estiver vazia);
 * - cria o banco (se não existir) e verifica se ele já tem tabelas.
 *
 * Não apaga nenhum dado. Não grava senhas: quem preenche DB_PASSWORD é você, no .env.
 *
 * Códigos de saída:
 *   0 = banco vazio: pode rodar "php artisan migrate --seed"
 *   3 = banco já tem tabelas: rodar só "php artisan migrate" (não apaga dados)
 *   1 = erro (a mensagem explica o que corrigir)
 */

if (!extension_loaded('pdo_mysql')) {
    fwrite(STDERR, "[ERRO] A extensão pdo_mysql não está ativa. No php.ini, descomente 'extension=pdo_mysql' e rode de novo.\n");
    exit(1);
}

$raiz = dirname(__DIR__);
$caminhoEnv = $raiz . DIRECTORY_SEPARATOR . '.env';
$caminhoExemplo = $raiz . DIRECTORY_SEPARATOR . '.env.example';

// 1. Cria o .env se não existir
if (!file_exists($caminhoEnv)) {
    if (!file_exists($caminhoExemplo) || !copy($caminhoExemplo, $caminhoEnv)) {
        fwrite(STDERR, "[ERRO] Não consegui criar o .env a partir do .env.example.\n");
        exit(1);
    }
    echo "[ok] Arquivo .env criado a partir do .env.example.\n";
}

$conteudo = file_get_contents($caminhoEnv);
$original = $conteudo;

// Lê o valor de uma chave (sem aspas e sem espaços)
$ler = function (string $chave) use (&$conteudo): ?string {
    if (preg_match('/^' . preg_quote($chave, '/') . '=(.*)$/m', $conteudo, $m)) {
        return trim(trim($m[1]), "\"' \r");
    }
    return null;
};

// Define a chave somente se ela não existir ou estiver vazia
$definirSeVazio = function (string $chave, string $valor) use (&$conteudo, $ler): bool {
    $atual = $ler($chave);
    if ($atual !== null && $atual !== '') {
        return false;
    }
    $padrao = '/^' . preg_quote($chave, '/') . '=[^\r\n]*$/m';
    if (preg_match($padrao, $conteudo)) {
        $conteudo = preg_replace_callback($padrao, fn() => $chave . '=' . $valor, $conteudo, 1);
    } else {
        $conteudo = rtrim($conteudo) . PHP_EOL . $chave . '=' . $valor . PHP_EOL;
    }
    return true;
};

// 2. Ajusta o banco para MySQL/MariaDB (só se estiver faltando)
if ($ler('DB_CONNECTION') !== 'mysql') {
    $definirSeVazio('DB_CONNECTION', 'mysql');
    // Se o .env ainda estiver com o valor padrão do SQLite, troca.
    $conteudo = preg_replace('/^DB_CONNECTION=[^\r\n]*$/m', 'DB_CONNECTION=mysql', $conteudo, 1);
}
$definirSeVazio('DB_HOST', '127.0.0.1');
$definirSeVazio('DB_PORT', '3306');
$definirSeVazio('DB_DATABASE', 'gingahub_db');
$definirSeVazio('DB_USERNAME', 'root');
$definirSeVazio('DB_PASSWORD', '');

// O projeto não tem migration da tabela de sessões, então o driver "database"
// (padrão do .env.example) quebra todas as telas. Usa "file", como no ambiente de desenvolvimento.
if ($ler('SESSION_DRIVER') === 'database') {
    $conteudo = preg_replace('/^SESSION_DRIVER=[^\r\n]*$/m', 'SESSION_DRIVER=file', $conteudo, 1);
    echo "[ok] SESSION_DRIVER ajustado para file (o projeto não tem tabela de sessões).\n";
}

// 3. Gera a APP_KEY, se estiver vazia
if (($ler('APP_KEY') ?? '') === '') {
    $definirSeVazio('APP_KEY', 'base64:' . base64_encode(random_bytes(32)));
    echo "[ok] APP_KEY gerada.\n";
}

if ($conteudo !== $original) {
    file_put_contents($caminhoEnv, $conteudo);
    echo "[ok] .env ajustado para MySQL.\n";
}

// 4. Conecta ao servidor e cria o banco, se não existir
$host = $ler('DB_HOST');
$porta = $ler('DB_PORT');
$banco = $ler('DB_DATABASE');
$usuario = $ler('DB_USERNAME');
$senha = $ler('DB_PASSWORD') ?? '';

if (!preg_match('/^[A-Za-z0-9_]+$/', $banco)) {
    fwrite(STDERR, "[ERRO] DB_DATABASE no .env deve conter só letras, números e _. Valor atual: {$banco}\n");
    exit(1);
}

try {
    $pdo = new PDO("mysql:host={$host};port={$porta};charset=utf8mb4", $usuario, $senha, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5,
    ]);
} catch (PDOException $e) {
    fwrite(STDERR, "[ERRO] Não consegui conectar ao MySQL em {$host}:{$porta} com o usuário '{$usuario}'.\n");
    fwrite(STDERR, "       Confira se o MySQL/MariaDB do XAMPP está ligado e preencha DB_USERNAME e DB_PASSWORD no .env.\n");
    fwrite(STDERR, "       Detalhe: " . $e->getMessage() . "\n");
    exit(1);
}

$pdo->exec("CREATE DATABASE IF NOT EXISTS `{$banco}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
echo "[ok] Banco '{$banco}' pronto.\n";

// 5. Verifica se o banco já tem tabelas
$pdo->exec("USE `{$banco}`");
$tabelas = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_NUM);

if (count($tabelas) === 0) {
    echo "[ok] Banco vazio. Próximo passo: criar as tabelas e os dados de teste.\n";
    exit(0);
}

echo "[ok] O banco já tem " . count($tabelas) . " tabelas. Nenhum dado será apagado.\n";
exit(3);
