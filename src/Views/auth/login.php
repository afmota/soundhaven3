<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoundHaven 3 - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/SoundHaven.ico">
    <style>
        :root {
            --bg-color: #0b0f19;
            --card-bg: rgba(18, 24, 38, 0.85);
            --border-color: rgba(255, 255, 255, 0.08);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --accent-start: #8b5cf6;
            --accent-end: #ec4899;
            --accent-hover: #a855f7;
            --card-shadow: rgba(0, 0, 0, 0.5);
            --danger-bg: rgba(239, 68, 68, 0.15);
            --danger-border: rgba(239, 68, 68, 0.4);
            --danger-text: #fca5a5;
            --info-bg: rgba(59, 130, 246, 0.15);
            --info-border: rgba(59, 130, 246, 0.4);
            --info-text: #93c5fd;
            --success-bg: rgba(16, 185, 129, 0.15);
            --success-border: rgba(16, 185, 129, 0.4);
            --success-text: #86efac;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(circle at 15% 20%, rgba(139, 92, 246, 0.18) 0%, transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(236, 72, 153, 0.15) 0%, transparent 45%);
            color: var(--text-primary);
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 420px;
        }

        .auth-card {
            background-color: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 40px 32px;
            box-shadow: 0 16px 40px var(--card-shadow);
            text-align: center;
        }

        .auth-logo {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 24px;
            text-decoration: none;
        }

        .auth-logo img {
            width: 72px;
            height: auto;
            margin-bottom: 12px;
            filter: drop-shadow(0 4px 12px rgba(139, 92, 246, 0.3));
        }

        .auth-logo-fallback {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--accent-start), var(--accent-end));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #fff;
            margin-bottom: 12px;
            box-shadow: 0 8px 20px rgba(139, 92, 246, 0.35);
        }

        .auth-logo-text {
            font-size: 1.85rem;
            font-weight: 800;
            background: linear-gradient(to right, #fff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 0.5px;
        }

        .auth-logo-subtitle {
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 4px;
            font-weight: 600;
        }

        .auth-title {
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 24px;
        }

        .alert {
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 0.88rem;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 12px;
            line-height: 1.4;
        }

        .alert-danger {
            background-color: var(--danger-bg);
            border: 1px solid var(--danger-border);
            color: var(--danger-text);
        }

        .alert-info {
            background-color: var(--info-bg);
            border: 1px solid var(--info-border);
            color: var(--info-text);
        }

        .alert-success {
            background-color: var(--success-bg);
            border: 1px solid var(--success-border);
            color: var(--success-text);
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group i.icon-prefix {
            position: absolute;
            left: 14px;
            color: var(--text-muted);
            font-size: 1rem;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            background-color: rgba(11, 15, 25, 0.7);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            padding: 12px 42px 12px 42px;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--accent-start);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
            background-color: rgba(11, 15, 25, 0.9);
        }

        .btn-toggle-password {
            position: absolute;
            right: 12px;
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 6px;
            font-size: 0.95rem;
            transition: color 0.2s;
        }

        .btn-toggle-password:hover {
            color: var(--text-primary);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--accent-start), var(--accent-end));
            color: #fff;
            border: none;
            padding: 13px 24px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
            margin-top: 10px;
            box-shadow: 0 4px 14px rgba(139, 92, 246, 0.35);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.5);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .auth-footer {
            margin-top: 24px;
            font-size: 0.8rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .auth-footer i {
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <?php if (file_exists(__DIR__ . '/../../../public/assets/images/SoundHaven.png')): ?>
                    <img src="assets/images/SoundHaven.png" alt="Logo SoundHaven">
                <?php else: ?>
                    <div class="auth-logo-fallback">
                        <i class="fas fa-compact-disc fa-spin-hover"></i>
                    </div>
                <?php endif; ?>
                <div class="auth-logo-text">SoundHaven</div>
                <div class="auth-logo-subtitle">Acervo Musical &bull; v3</div>
            </div>

            <h2 class="auth-title">Acessar o Sistema</h2>

            <?php if (!empty($mensagemErro)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div><?= htmlspecialchars($mensagemErro) ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($mensagemInfo)): ?>
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-clock"></i>
                    <div><?= htmlspecialchars($mensagemInfo) ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($mensagemSucesso)): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <div><?= htmlspecialchars($mensagemSucesso) ?></div>
                </div>
            <?php endif; ?>

            <form action="index.php?url=processar_login" method="POST" autocomplete="on">
                <div class="form-group">
                    <label for="usuario">Usuário</label>
                    <div class="input-group">
                        <i class="fas fa-user icon-prefix"></i>
                        <input 
                            type="text" 
                            id="usuario" 
                            name="usuario" 
                            class="form-control" 
                            placeholder="Informe seu usuário" 
                            required 
                            autofocus 
                            autocomplete="username"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <div class="input-group">
                        <i class="fas fa-lock icon-prefix"></i>
                        <input 
                            type="password" 
                            id="senha" 
                            name="senha" 
                            class="form-control" 
                            placeholder="Digite sua senha" 
                            required 
                            autocomplete="current-password"
                        >
                        <button type="button" class="btn-toggle-password" id="togglePassword" aria-label="Mostrar senha">
                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>
            </form>

            <div class="auth-footer">
                <i class="fas fa-shield-alt"></i>
                <span>Sessão protegida com timeout automático de 30 min</span>
            </div>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('senha');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        if (toggleBtn && passwordInput && toggleIcon) {
            toggleBtn.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                toggleIcon.className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
            });
        }
    </script>
</body>
</html>