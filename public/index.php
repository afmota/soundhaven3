<?php
header('Content-Type: text/html; charset=utf-8');

$dbHost = getenv('DB_HOST') ?: 'db';
$dbName = getenv('DB_NAME') ?: 'soundhaven3';
$dbUser = getenv('DB_USER') ?: 'sh_user';
$dbPass = getenv('DB_PASS') ?: 'W3azxc*9';
$dbCharset = getenv('DB_CHARSET') ?: 'utf8mb4';

$dbStatus = 'Desconectado';
$tableStatus = 'Nao verificada';
$error = null;

try {
    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $dbStatus = 'Conectado com sucesso ao banco "' . htmlspecialchars($dbName) . '"';

    $stmt = $pdo->query("SHOW TABLES LIKE 'tb_albuns'");
    $tableExists = $stmt->fetch();

    if ($tableExists) {
        $countStmt = $pdo->query("SELECT COUNT(*) as total FROM tb_albuns");
        $total = $countStmt->fetch()['total'] ?? 0;
        $tableStatus = 'Tabela "tb_albuns" encontrada (' . $total . ' registros)';
    } else {
        $tableStatus = 'Tabela "tb_albuns" nao encontrada.';
    }
} catch (PDOException $e) {
    $dbStatus = 'Erro de conexao: ' . htmlspecialchars($e->getMessage());
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Soundhaven 3</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px; }
        .card { background: #1e293b; border-radius: 12px; padding: 24px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3); }
        h1 { margin-top: 0; color: #38bdf8; }
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-weight: bold; font-size: 0.9em; }
        .ok { background: #166534; color: #86efac; }
        .err { background: #991b1b; color: #fca5a5; }
        ul { list-style: none; padding: 0; }
        li { margin-bottom: 12px; padding: 10px; background: #334155; border-radius: 6px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Soundhaven 3</h1>
        <p>Ambiente Docker inicializado com sucesso.</p>
        <ul>
            <li><strong>PHP:</strong> <?= phpversion() ?></li>
            <li><strong>Banco de Dados:</strong> <span class="status-badge <?= $error ? 'err' : 'ok' ?>"><?= $dbStatus ?></span></li>
            <li><strong>Tabela tb_albuns:</strong> <span class="status-badge <?= strpos($tableStatus, 'encontrada') !== false ? 'ok' : 'err' ?>"><?= $tableStatus ?></span></li>
        </ul>
    </div>
</body>
</html>