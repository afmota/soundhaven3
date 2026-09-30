<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoundHaven 3 - Solicitar Cadastro</title>
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
            padding: 24px 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
        }

        .auth-card {
            background-color: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 36px 32px;
            box-shadow: 0 16px 40px var(--card-shadow);
            text-align: center;
        }

        .auth-logo {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
            text-decoration: none;
        }

        .auth-logo img {
            width: 64px;
            height: auto;
            margin-bottom: 10px;
            filter: drop-shadow(0 4px 12px rgba(139, 92, 246, 0.3));
        }

        .auth-logo-fallback {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--accent-start), var(--accent-end));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #fff;
            margin-bottom: 10px;
            box-shadow: 0 8px 20px rgba(139, 92, 246, 0.35);
        }

        .auth-logo-text {
            font-size: 1.7rem;
            font-weight: 800;
            background: linear-gradient(to right, #fff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 0.5px;
        }

        .auth-logo-subtitle {
            font-size: 0.72rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 3px;
            font-weight: 600;
        }

        .auth-title {
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .auth-note {
            font-size: 0.82rem;
            color: var(--text-secondary);
            margin-bottom: 20px;
            line-height: 1.4;
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

        .form-group {
            margin-bottom: 16px;
            text-align: left;
        }

        .form-group label {
            display: block;
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-bottom: 6px;
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
            font-size: 0.95rem;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            background-color: rgba(11, 15, 25, 0.7);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            padding: 11px 40px 11px 40px;
            font-size: 0.92rem;
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
            font-size: 0.9rem;
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
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 12px;
            box-shadow: 0 4px 14px rgba(139, 92, 246, 0.35);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.5);
        }

        .auth-login-link {
            margin-top: 20px;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .auth-login-link a {
            color: var(--accent-start);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .auth-login-link a:hover {
            color: var(--accent-end);
            text-decoration: underline;
        }

        .auth-footer {
            margin-top: 20px;
            padding-top: 14px;
            border-top: 1px solid var(--border-color);
            font-size: 0.78rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
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
                        <i class="fas fa-compact-disc"></i>
                    </div>
                <?php endif; ?>
                <div class="auth-logo-text">SoundHaven</div>
                <div class="auth-logo-subtitle">Acervo Musical &bull; v3</div>
            </div>

            <h2 class="auth-title">Solicitar Cadastro</h2>
            <p class="auth-note">Após o envio, o administrador deverá autorizar a sua entrada para liberação do acesso.</p>

            <?php if (!empty($mensagemErro)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div><?= htmlspecialchars($mensagemErro) ?></div>
                </div>
            <?php endif; ?>

            <form action="index.php?url=processar_cadastro" method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <div class="input-group">
                        <i class="fas fa-id-card icon-prefix"></i>
                        <input 
                            type="text" 
                            id="nome" 
                            name="nome" 
                            class="form-control" 
                            placeholder="Seu nome completo" 
                            value="<?= htmlspecialchars($oldNome ?? '') ?>"
                            required 
                            autofocus
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="usuario">Nome de Usuário</label>
                    <div class="input-group">
                        <i class="fas fa-user icon-prefix"></i>
                        <input 
                            type="text" 
                            id="usuario" 
                            name="usuario" 
                            class="form-control" 
                            placeholder="ex: joaosilva" 
                            value="<?= htmlspecialchars($oldUsuario ?? '') ?>"
                            required 
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
                            placeholder="Mínimo de 6 caracteres" 
                            required 
                            autocomplete="new-password"
                        >
                        <button type="button" class="btn-toggle-password" data-target="senha" aria-label="Mostrar senha">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirmar_senha">Confirmar Senha</label>
                    <div class="input-group">
                        <i class="fas fa-shield-alt icon-prefix"></i>
                        <input 
                            type="password" 
                            id="confirmar_senha" 
                            name="confirmar_senha" 
                            class="form-control" 
                            placeholder="Repita a senha" 
                            required 
                            autocomplete="new-password"
                        >
                        <button type="button" class="btn-toggle-password" data-target="confirmar_senha" aria-label="Mostrar confirmação de senha">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus"></i> Enviar Solicitação
                </button>
            </form>

            <div class="auth-login-link">
                Já possui uma conta? <a href="index.php?url=login">Fazer Login</a>
            </div>

            <div class="auth-footer">
                <i class="fas fa-user-shield"></i>
                <span>Liberação sob aprovação do administrador</span>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.btn-toggle-password').forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = btn.querySelector('i');
                if (input && icon) {
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    icon.className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
                }
            });
        });
    </script>
</body>
</html>