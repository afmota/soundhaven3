<?php
header('Content-Type: text/html; charset=utf-8');

$dbHost = getenv('DB_HOST') ?: 'db';
$dbName = getenv('DB_NAME') ?: 'soundhaven3';
$dbUser = getenv('DB_USER') ?: 'sh_user';
$dbPass = getenv('DB_PASS') ?: 'W3azxc*9';
$dbCharset = getenv('DB_CHARSET') ?: 'utf8mb4';

$dbStatus = 'Desconectado';
$tableAlbunsStatus = 'Não verificada';
$tableUsuariosStatus = 'Não verificada';
$adminUserStatus = 'Não verificado';
$error = null;

try {
    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $dbStatus = 'Conectado com sucesso ao banco "' . htmlspecialchars($dbName) . '"';

    // Verificar tb_albuns
    $stmt = $pdo->query("SHOW TABLES LIKE 'tb_albuns'");
    if ($stmt->fetch()) {
        $count = $pdo->query("SELECT COUNT(*) as total FROM tb_albuns")->fetch()['total'] ?? 0;
        $tableAlbunsStatus = "Tabela encontrada ({$count} registros)";
    } else {
        $tableAlbunsStatus = 'Tabela não encontrada';
    }

    // Verificar tb_usuarios
    $stmtUser = $pdo->query("SHOW TABLES LIKE 'tb_usuarios'");
    if ($stmtUser->fetch()) {
        $countUsers = $pdo->query("SELECT COUNT(*) as total FROM tb_usuarios")->fetch()['total'] ?? 0;
        $tableUsuariosStatus = "Tabela encontrada ({$countUsers} registros)";

        // Verificar usuário admin
        $adminStmt = $pdo->prepare("SELECT usuario, nome, senha FROM tb_usuarios WHERE usuario = ?");
        $adminStmt->execute(['admin']);
        $adminRow = $adminStmt->fetch();

        if ($adminRow && password_verify('Gwh28dgcmp#', $adminRow['senha'])) {
            $adminUserStatus = "Usuário \"{$adminRow['usuario']}\" ({$adminRow['nome']}) ativo e senha criptografada válida";
        } elseif ($adminRow) {
            $adminUserStatus = "Usuário \"{$adminRow['usuario']}\" existe, mas senha não confere";
        } else {
            $adminUserStatus = 'Usuário admin não encontrado';
        }
    } else {
        $tableUsuariosStatus = 'Tabela não encontrada';
    }

} catch (PDOException $e) {
    $dbStatus = 'Erro de conexão: ' . htmlspecialchars($e->getMessage());
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
        .card { background: #1e293b; border-radius: 12px; padding: 24px; max-width: 650px; margin: 0 auto; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3); }
        h1 { margin-top: 0; color: #38bdf8; }
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-weight: bold; font-size: 0.85em; }
        .ok { background: #166534; color: #86efac; }
        .err { background: #991b1b; color: #fca5a5; }
        ul { list-style: none; padding: 0; }
        li { margin-bottom: 12px; padding: 12px; background: #334155; border-radius: 6px; display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        li strong { min-width: 150px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Soundhaven 3</h1>
        <p>Ambiente Docker e Banco de Dados inicializados com sucesso.</p>
        <ul>
            <li><strong>PHP:</strong> <span><?= phpversion() ?></span></li>
            <li><strong>Banco de Dados:</strong> <span class="status-badge <?= $error ? 'err' : 'ok' ?>"><?= $dbStatus ?></span></li>
            <li><strong>Tabela tb_albuns:</strong> <span class="status-badge <?= strpos($tableAlbunsStatus, 'encontrada') !== false ? 'ok' : 'err' ?>"><?= $tableAlbunsStatus ?></span></li>
            <li><strong>Tabela tb_usuarios:</strong> <span class="status-badge <?= strpos($tableUsuariosStatus, 'encontrada') !== false ? 'ok' : 'err' ?>"><?= $tableUsuariosStatus ?></span></li>
            <li><strong>Usuário Admin:</strong> <span class="status-badge <?= strpos($adminUserStatus, 'ativo') !== false ? 'ok' : 'err' ?>"><?= $adminUserStatus ?></span></li>
        </ul>
    </div>
</body>
</html>