<?php
namespace App\Controllers;

use App\Config\Database;
use PDO;

class AuthController {
    public function showLoginForm(): void {
        if (isset($_SESSION['usuario_id'])) {
            header('Location: index.php?url=dashboard');
            exit;
        }

        $mensagemErro = $_SESSION['flash_error'] ?? null;
        $mensagemInfo = $_SESSION['flash_info'] ?? null;
        $mensagemSucesso = $_SESSION['flash_success'] ?? null;

        unset($_SESSION['flash_error'], $_SESSION['flash_info'], $_SESSION['flash_success']);

        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=login');
            exit;
        }

        $usuario = trim($_POST['usuario'] ?? '');
        $senha = trim($_POST['senha'] ?? '');

        if (empty($usuario) || empty($senha)) {
            $_SESSION['flash_error'] = 'Por favor, informe o usuário e a senha.';
            header('Location: index.php?url=login');
            exit;
        }

        try {
            $db = Database::getConnection();
            $stmt = $db->prepare('SELECT id_usuario, usuario, nome, senha, status FROM tb_usuarios WHERE usuario = :usuario LIMIT 1');
            $stmt->execute([':usuario' => $usuario]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($senha, $user['senha'])) {
                // Verificar status de autorização
                $status = strtolower(trim((string)$user['status']));

                if ($status === 'pendente') {
                    $_SESSION['flash_info'] = 'Seu cadastro está aguardando a autorização do administrador. Por favor, aguarde a liberação do seu acesso.';
                    header('Location: index.php?url=login');
                    exit;
                }

                if ($status === 'rejeitado') {
                    $_SESSION['flash_error'] = 'Seu cadastro foi recusado pelo administrador. Acesso não permitido.';
                    header('Location: index.php?url=login');
                    exit;
                }

                if ($status !== 'ativo') {
                    $_SESSION['flash_error'] = 'Sua conta está inativa ou bloqueada. Contate o administrador.';
                    header('Location: index.php?url=login');
                    exit;
                }

                // Usuário ativo e autorizado
                session_regenerate_id(true);

                $_SESSION['usuario_id'] = (int)$user['id_usuario'];
                $_SESSION['usuario_login'] = $user['usuario'];
                $_SESSION['usuario_nome'] = $user['nome'];
                $_SESSION['last_activity'] = time();

                header('Location: index.php?url=dashboard');
                exit;
            }

            $_SESSION['flash_error'] = 'Usuário ou senha inválidos.';
            header('Location: index.php?url=login');
            exit;

        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'Erro ao processar autenticação. Tente novamente.';
            header('Location: index.php?url=login');
            exit;
        }
    }

    public function showRegisterForm(): void {
        if (isset($_SESSION['usuario_id'])) {
            header('Location: index.php?url=dashboard');
            exit;
        }

        $mensagemErro = $_SESSION['flash_error'] ?? null;
        $mensagemInfo = $_SESSION['flash_info'] ?? null;
        $mensagemSucesso = $_SESSION['flash_success'] ?? null;

        $oldNome = $_SESSION['old_nome'] ?? '';
        $oldUsuario = $_SESSION['old_usuario'] ?? '';

        unset(
            $_SESSION['flash_error'],
            $_SESSION['flash_info'],
            $_SESSION['flash_success'],
            $_SESSION['old_nome'],
            $_SESSION['old_usuario']
        );

        require __DIR__ . '/../Views/auth/register.php';
    }

    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?url=cadastro');
            exit;
        }

        $nome = trim($_POST['nome'] ?? '');
        $usuario = trim($_POST['usuario'] ?? '');
        $senha = trim($_POST['senha'] ?? '');
        $confirmarSenha = trim($_POST['confirmar_senha'] ?? '');

        $_SESSION['old_nome'] = $nome;
        $_SESSION['old_usuario'] = $usuario;

        if (empty($nome) || empty($usuario) || empty($senha) || empty($confirmarSenha)) {
            $_SESSION['flash_error'] = 'Por favor, preencha todos os campos do formulário.';
            header('Location: index.php?url=cadastro');
            exit;
        }

        if (mb_strlen($nome) < 3) {
            $_SESSION['flash_error'] = 'O nome completo deve conter pelo menos 3 caracteres.';
            header('Location: index.php?url=cadastro');
            exit;
        }

        if (!preg_match('/^[a-zA-Z0-9_.-]{3,64}$/', $usuario)) {
            $_SESSION['flash_error'] = 'O usuário deve ter de 3 a 64 caracteres e conter apenas letras, números, ponto, hífen ou underline.';
            header('Location: index.php?url=cadastro');
            exit;
        }

        if (strlen($senha) < 6) {
            $_SESSION['flash_error'] = 'A senha deve ter no mínimo 6 caracteres.';
            header('Location: index.php?url=cadastro');
            exit;
        }

        if ($senha !== $confirmarSenha) {
            $_SESSION['flash_error'] = 'A confirmação de senha não confere com a senha digitada.';
            header('Location: index.php?url=cadastro');
            exit;
        }

        try {
            $db = Database::getConnection();

            // Verificar se o nome de usuário já existe
            $stmtCheck = $db->prepare('SELECT COUNT(*) as total FROM tb_usuarios WHERE usuario = :usuario');
            $stmtCheck->execute([':usuario' => $usuario]);
            if ((int)($stmtCheck->fetch()['total'] ?? 0) > 0) {
                $_SESSION['flash_error'] = 'Este nome de usuário já está cadastrado. Por favor, escolha outro.';
                header('Location: index.php?url=cadastro');
                exit;
            }

            // Criptografar a senha e inserir com status pendente
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmtInsert = $db->prepare('INSERT INTO tb_usuarios (usuario, nome, senha, status) VALUES (:usuario, :nome, :senha, "pendente")');
            $stmtInsert->execute([
                ':usuario' => $usuario,
                ':nome' => $nome,
                ':senha' => $hash,
            ]);

            unset($_SESSION['old_nome'], $_SESSION['old_usuario']);

            $_SESSION['flash_info'] = 'Cadastro realizado com sucesso! Sua conta está aguardando a autorização do administrador para que você possa acessar o sistema.';
            header('Location: index.php?url=login');
            exit;

        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'Erro ao processar o cadastro: ' . $e->getMessage();
            header('Location: index.php?url=cadastro');
            exit;
        }
    }

    public function logout(): void {
        $_SESSION = [];
        session_regenerate_id(true);
        $_SESSION['flash_success'] = 'Você saiu do sistema com sucesso.';
        header('Location: index.php?url=login');
        exit;
    }
}