<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoundHaven 3 - Painel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/SoundHaven.ico">
    <style>
        :root {
            --bg-color: #0b0f19;
            --card-bg: rgba(18, 24, 38, 0.85);
            --border-color: rgba(255, 255, 255, 0.08);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --accent-start: #8b5cf6;
            --accent-end: #ec4899;
            --warning-bg: rgba(245, 158, 11, 0.15);
            --warning-border: rgba(245, 158, 11, 0.4);
            --warning-text: #fde68a;
            --success-bg: rgba(16, 185, 129, 0.15);
            --success-border: rgba(16, 185, 129, 0.4);
            --success-text: #86efac;
            --danger-bg: rgba(239, 68, 68, 0.15);
            --danger-border: rgba(239, 68, 68, 0.4);
            --danger-text: #fca5a5;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
            background-color: var(--bg-color);
            color: var(--text-primary);
            display: flex;
            flex-direction: column;
        }

        header {
            background: rgba(18, 24, 38, 0.95);
            border-bottom: 1px solid var(--border-color);
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }

        .brand img {
            width: 38px;
            height: auto;
        }

        .brand-text {
            font-size: 1.3rem;
            font-weight: 800;
            background: linear-gradient(to right, #fff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-badge {
            background: linear-gradient(135deg, var(--accent-start), var(--accent-end));
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 999px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .user-nav {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-start), var(--accent-end));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
        }

        .btn-logout {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.25);
            color: #fff;
        }

        main {
            flex: 1;
            padding: 40px 32px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .alert-banner {
            border-radius: 14px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            line-height: 1.4;
        }

        .alert-warning {
            background-color: var(--warning-bg);
            border: 1px solid var(--warning-border);
            color: var(--warning-text);
        }

        .alert-success {
            background-color: var(--success-bg);
            border: 1px solid var(--success-border);
            color: var(--success-text);
        }

        .alert-danger {
            background-color: var(--danger-bg);
            border: 1px solid var(--danger-border);
            color: var(--danger-text);
        }

        .alert-icon-text {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 0.98rem;
        }

        .alert-icon-text i {
            font-size: 1.4rem;
        }

        .welcome-card {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.15), rgba(236, 72, 153, 0.1));
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 32px;
        }

        .welcome-card h1 {
            font-size: 1.8rem;
            margin-bottom: 8px;
        }

        .welcome-card p {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .card-title {
            font-size: 0.95rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .card-icon {
            font-size: 1.3rem;
            color: var(--accent-start);
        }

        .card-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .card-desc {
            font-size: 0.82rem;
            color: var(--text-secondary);
        }

        .session-timer {
            color: #86efac;
            font-weight: 600;
        }

        /* Seção de Gerenciamento de Usuários Pendentes */
        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .section-header i {
            color: #f59e0b;
        }

        .badge-count {
            background: #f59e0b;
            color: #000;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 999px;
        }

        .pending-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 32px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.92rem;
        }

        th {
            background-color: rgba(255, 255, 255, 0.03);
            color: var(--text-secondary);
            font-weight: 600;
            padding: 14px 20px;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background-color: rgba(255, 255, 255, 0.02);
        }

        .actions-cell {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-action {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
        }

        .btn-approve {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #86efac;
        }

        .btn-approve:hover {
            background: rgba(16, 185, 129, 0.3);
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-reject {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
        }

        .btn-reject:hover {
            background: rgba(239, 68, 68, 0.3);
            color: #fff;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <header>
        <a href="index.php?url=dashboard" class="brand">
            <?php if (file_exists(__DIR__ . '/../../../public/assets/images/SoundHaven.png')): ?>
                <img src="assets/images/SoundHaven.png" alt="Logo">
            <?php else: ?>
                <i class="fas fa-compact-disc" style="color: #8b5cf6; font-size: 24px;"></i>
            <?php endif; ?>
            <span class="brand-text">SoundHaven</span>
            <span class="brand-badge">v3</span>
        </a>

        <div class="user-nav">
            <div class="user-info">
                <div class="user-avatar">
                    <?= strtoupper(substr($usuarioNome, 0, 1)) ?>
                </div>
                <div>
                    <div style="font-weight: 600;"><?= htmlspecialchars($usuarioNome) ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary);">@<?= htmlspecialchars($usuarioLogin) ?></div>
                </div>
            </div>
            <a href="index.php?url=logout" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </header>

    <main>
        <?php if (!empty($mensagemSucesso)): ?>
            <div class="alert-banner alert-success" role="alert">
                <div class="alert-icon-text">
                    <i class="fas fa-check-circle"></i>
                    <div><?= htmlspecialchars($mensagemSucesso) ?></div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($mensagemErro)): ?>
            <div class="alert-banner alert-danger" role="alert">
                <div class="alert-icon-text">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div><?= htmlspecialchars($mensagemErro) ?></div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($isAdmin && !empty($usuariosPendentes)): ?>
            <div class="alert-banner alert-warning" role="alert">
                <div class="alert-icon-text">
                    <i class="fas fa-bell"></i>
                    <div>
                        <strong>Atenção Administrador:</strong> Há <strong><?= count($usuariosPendentes) ?></strong> solicitação(ões) de novo usuário aguardando sua autorização para acesso ao sistema.
                    </div>
                </div>
                <a href="#solicitacoes" style="color: #fde68a; font-size: 0.88rem; font-weight: 600; text-decoration: underline;">
                    Ver solicitações abaixo &darr;
                </a>
            </div>
        <?php endif; ?>

        <div class="welcome-card">
            <h1>Olá, <?= htmlspecialchars($usuarioNome) ?>! 👋</h1>
            <p>Você está autenticado no <strong>Soundhaven 3</strong>. O sistema de controle de acesso, notificações e proteção de rotas está operando normalmente.</p>
        </div>

        <div class="grid">
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Acervo de Álbuns</span>
                    <i class="fas fa-record-vinyl card-icon"></i>
                </div>
                <div class="card-value"><?= $totalAlbuns ?></div>
                <div class="card-desc">Registros na tabela <code>tb_albuns</code></div>
            </div>

            <?php if ($isAdmin): ?>
                <div class="card">
                    <div class="card-header">
                        <span class="card-title">Cadastros Pendentes</span>
                        <i class="fas fa-user-clock card-icon" style="color: #f59e0b;"></i>
                    </div>
                    <div class="card-value" style="color: <?= !empty($usuariosPendentes) ? '#f59e0b' : 'inherit' ?>;">
                        <?= count($usuariosPendentes) ?>
                    </div>
                    <div class="card-desc">Usuários aguardando sua aprovação</div>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">Sessão Atual</span>
                    <i class="fas fa-user-shield card-icon"></i>
                </div>
                <div class="card-value"><span class="session-timer">Ativa</span></div>
                <div class="card-desc">Expira após <strong>30 minutos</strong> de inatividade.</div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">Ambiente</span>
                    <i class="fas fa-server card-icon"></i>
                </div>
                <div class="card-value">Docker</div>
                <div class="card-desc">PHP <?= phpversion() ?> + Nginx + MySQL + Mailpit</div>
            </div>
        </div>

        <?php if ($isAdmin && !empty($usuariosPendentes)): ?>
            <div id="solicitacoes">
                <div class="section-header">
                    <i class="fas fa-user-check"></i>
                    <span>Solicitações de Acesso Pendentes</span>
                    <span class="badge-count"><?= count($usuariosPendentes) ?></span>
                </div>

                <div class="pending-card">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nome Completo</th>
                                    <th>E-mail</th>
                                    <th>Nome de Usuário</th>
                                    <th>Data da Solicitação</th>
                                    <th style="text-align: right;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuariosPendentes as $u): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600;"><?= htmlspecialchars($u['nome']) ?></div>
                                        </td>
                                        <td>
                                            <span style="color: var(--text-secondary); font-size: 0.88rem;">
                                                <i class="fas fa-envelope" style="margin-right: 6px; font-size: 0.8rem; color: var(--accent-start);"></i><?= htmlspecialchars($u['email']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span style="color: var(--accent-start); font-weight: 500;">
                                                @<?= htmlspecialchars($u['usuario']) ?>
                                            </span>
                                        </td>
                                        <td style="color: var(--text-secondary); font-size: 0.85rem;">
                                            <?= date('d/m/Y H:i', strtotime($u['data_cadastro'])) ?>
                                        </td>
                                        <td>
                                            <div class="actions-cell">
                                                <a 
                                                    href="index.php?url=aprovar_usuario&id=<?= (int)$u['id_usuario'] ?>" 
                                                    class="btn-action btn-approve"
                                                    title="Autorizar entrada no sistema e notificar por e-mail"
                                                >
                                                    <i class="fas fa-check"></i> Autorizar
                                                </a>
                                                <a 
                                                    href="index.php?url=rejeitar_usuario&id=<?= (int)$u['id_usuario'] ?>" 
                                                    class="btn-action btn-reject"
                                                    onclick="return confirm('Deseja realmente recusar o acesso de @<?= htmlspecialchars($u['usuario']) ?>?');"
                                                    title="Recusar cadastro"
                                                >
                                                    <i class="fas fa-times"></i> Recusar
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>