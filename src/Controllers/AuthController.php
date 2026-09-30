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
            $stmt = $db->prepare('SELECT id_usuario, usuario, nome, senha FROM tb_usuarios WHERE usuario = :usuario LIMIT 1');
            $stmt->execute([':usuario' => $usuario]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($senha, $user['senha'])) {
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

    public function logout(): void {
        $_SESSION = [];
        session_regenerate_id(true);
        $_SESSION['flash_success'] = 'Você saiu do sistema com sucesso.';
        header('Location: index.php?url=login');
        exit;
    }
}