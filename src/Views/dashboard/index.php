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
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
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
        <div class="welcome-card">
            <h1>Olá, <?= htmlspecialchars($usuarioNome) ?>! 👋</h1>
            <p>Você está autenticado no <strong>Soundhaven 3</strong>. O sistema de sessões ativas e proteção de rotas está operando normalmente.</p>
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

            <div class="card">
                <div class="card-header">
                    <span class="card-title">Sessão Atual</span>
                    <i class="fas fa-user-shield card-icon"></i>
                </div>
                <div class="card-value"><span class="session-timer">Ativa</span></div>
                <div class="card-desc">Expira após <strong>30 minutos</strong> sem novas requisições.</div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">Ambiente</span>
                    <i class="fas fa-server card-icon"></i>
                </div>
                <div class="card-value">Docker</div>
                <div class="card-desc">PHP <?= phpversion() ?> + Nginx + MySQL 8.0</div>
            </div>
        </div>
    </main>
</body>
</html>