<?php
namespace App\Controllers;

use App\Config\Database;
use App\Services\Mailer;
use PDO;

class DashboardController {
    public function index(): void {
        $usuarioNome = $_SESSION['usuario_nome'] ?? 'Usuário';
        $usuarioLogin = $_SESSION['usuario_login'] ?? '';
        $lastActivity = $_SESSION['last_activity'] ?? time();
        $isAdmin = ($usuarioLogin === 'admin');

        $mensagemSucesso = $_SESSION['flash_success'] ?? null;
        $mensagemErro = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $totalAlbuns = 0;
        $usuariosPendentes = [];

        try {
            $db = Database::getConnection();

            // Total de álbuns
            $stmt = $db->query('SELECT COUNT(*) as total FROM tb_albuns');
            $totalAlbuns = (int)($stmt->fetch()['total'] ?? 0);

            // Se for admin, carrega lista de usuários pendentes de autorização (incluindo e-mail)
            if ($isAdmin) {
                $stmtPendentes = $db->query("SELECT id_usuario, usuario, nome, email, data_cadastro FROM tb_usuarios WHERE status = 'pendente' ORDER BY data_cadastro DESC");
                $usuariosPendentes = $stmtPendentes->fetchAll(PDO::FETCH_ASSOC);
            }

        } catch (\Throwable $e) {
            // Em caso de erro, continua com dados padrão
        }

        require __DIR__ . '/../Views/dashboard/index.php';
    }

    public function approveUser(int $id): void {
        $usuarioLogin = $_SESSION['usuario_login'] ?? '';
        if ($usuarioLogin !== 'admin') {
            header('Location: index.php?url=dashboard');
            exit;
        }

        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Identificador de usuário inválido.';
            header('Location: index.php?url=dashboard');
            exit;
        }

        try {
            $db = Database::getConnection();

            // Obter dados do usuário para confirmação e envio de e-mail
            $stmtUser = $db->prepare('SELECT id_usuario, usuario, nome, email FROM tb_usuarios WHERE id_usuario = :id AND status = "pendente"');
            $stmtUser->execute([':id' => $id]);
            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $stmt = $db->prepare('UPDATE tb_usuarios SET status = "ativo" WHERE id_usuario = :id');
                $stmt->execute([':id' => $id]);

                // Disparar notificação por e-mail
                $emailSent = false;
                if (!empty($user['email'])) {
                    $emailSent = Mailer::sendApprovalNotification($user['email'], $user['nome'], $user['usuario']);
                }

                if ($emailSent) {
                    $_SESSION['flash_success'] = 'O usuário "' . htmlspecialchars($user['usuario']) . '" (' . htmlspecialchars($user['nome']) . ') foi autorizado com sucesso! Um e-mail de notificação foi enviado para ' . htmlspecialchars($user['email']) . '.';
                } else {
                    $_SESSION['flash_success'] = 'O usuário "' . htmlspecialchars($user['usuario']) . '" foi autorizado com sucesso. (Aviso: não foi possível enviar o e-mail de notificação).';
                }

            } else {
                $_SESSION['flash_error'] = 'Usuário não encontrado ou já processado.';
            }

        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'Erro ao autorizar usuário: ' . $e->getMessage();
        }

        header('Location: index.php?url=dashboard');
        exit;
    }

    public function rejectUser(int $id): void {
        $usuarioLogin = $_SESSION['usuario_login'] ?? '';
        if ($usuarioLogin !== 'admin') {
            header('Location: index.php?url=dashboard');
            exit;
        }

        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Identificador de usuário inválido.';
            header('Location: index.php?url=dashboard');
            exit;
        }

        try {
            $db = Database::getConnection();

            $stmtUser = $db->prepare('SELECT usuario, nome FROM tb_usuarios WHERE id_usuario = :id AND status = "pendente"');
            $stmtUser->execute([':id' => $id]);
            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $stmt = $db->prepare('UPDATE tb_usuarios SET status = "rejeitado" WHERE id_usuario = :id');
                $stmt->execute([':id' => $id]);
                $_SESSION['flash_success'] = 'A solicitação do usuário "' . htmlspecialchars($user['usuario']) . '" foi recusada.';
            } else {
                $_SESSION['flash_error'] = 'Usuário não encontrado ou já processado.';
            }

        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'Erro ao recusar usuário: ' . $e->getMessage();
        }

        header('Location: index.php?url=dashboard');
        exit;
    }
}