<?php
ob_start();

$sessionTimeout = 30 * 60; // 30 minutos em segundos (1800s)

ini_set('session.gc_maxlifetime', (string)$sessionTimeout);
session_set_cookie_params([
    'lifetime' => $sessionTimeout,
    'path' => '/',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

// Verificação de expiração de sessão por inatividade (30 minutos)
$sessionExpired = isset($_SESSION['usuario_id'])
    && isset($_SESSION['last_activity'])
    && (time() - (int)$_SESSION['last_activity']) >= $sessionTimeout;

if ($sessionExpired) {
    $_SESSION = [];
    session_regenerate_id(true);
    $_SESSION['flash_info'] = 'Sua sessão expirou por inatividade (30 minutos). Por favor, faça login novamente.';
    header('Location: index.php?url=login');
    exit;
}

// Se o usuário estiver autenticado, atualiza o timestamp da última atividade
if (isset($_SESSION['usuario_id'])) {
    $_SESSION['last_activity'] = time();
    setcookie(session_name(), session_id(), [
        'expires' => time() + $sessionTimeout,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

require_once __DIR__ . '/../autoload.php';

use App\Controllers\AuthController;
use App\Controllers\DashboardController;

$route = trim((string)($_GET['url'] ?? ''));

// Lista de rotas públicas permitidas sem autenticação
$publicRoutes = ['login', 'processar_login', 'cadastro', 'processar_cadastro'];

// Guardião de rotas protegidas: se não estiver autenticado e a rota não for pública, redireciona para login
if (!isset($_SESSION['usuario_id']) && !in_array($route, $publicRoutes)) {
    header('Location: index.php?url=login');
    exit;
}

// Se já estiver autenticado e tentar acessar rotas públicas de login/cadastro ou a raiz vazia, vai para o dashboard
if (isset($_SESSION['usuario_id']) && ($route === '' || in_array($route, $publicRoutes))) {
    header('Location: index.php?url=dashboard');
    exit;
}

// Se não houver rota definida para visitante, direciona para login
if ($route === '') {
    $route = 'login';
}

// Roteador
switch ($route) {
    case 'login':
        (new AuthController())->showLoginForm();
        break;

    case 'processar_login':
        (new AuthController())->login();
        break;

    case 'cadastro':
        (new AuthController())->showRegisterForm();
        break;

    case 'processar_cadastro':
        (new AuthController())->register();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    case 'dashboard':
        (new DashboardController())->index();
        break;

    case 'aprovar_usuario':
        (new DashboardController())->approveUser((int)($_GET['id'] ?? 0));
        break;

    case 'rejeitar_usuario':
        (new DashboardController())->rejectUser((int)($_GET['id'] ?? 0));
        break;

    default:
        if (isset($_SESSION['usuario_id'])) {
            (new DashboardController())->index();
        } else {
            header('Location: index.php?url=login');
            exit;
        }
        break;
}