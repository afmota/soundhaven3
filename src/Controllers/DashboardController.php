<?php
namespace App\Controllers;

use App\Config\Database;
use PDO;

class DashboardController {
    public function index(): void {
        $usuarioNome = $_SESSION['usuario_nome'] ?? 'Usuário';
        $usuarioLogin = $_SESSION['usuario_login'] ?? '';
        $lastActivity = $_SESSION['last_activity'] ?? time();

        $totalAlbuns = 0;
        try {
            $db = Database::getConnection();
            $stmt = $db->query('SELECT COUNT(*) as total FROM tb_albuns');
            $totalAlbuns = (int)($stmt->fetch()['total'] ?? 0);
        } catch (\Throwable $e) {
            $totalAlbuns = 0;
        }

        require __DIR__ . '/../Views/dashboard/index.php';
    }
}