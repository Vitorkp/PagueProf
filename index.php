<?php
declare(strict_types=1);

ini_set('default_charset', 'UTF-8');
header('Content-Type: text/html; charset=UTF-8');

session_set_cookie_params([
    'httponly' => true,
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'samesite' => 'Lax',
]);
session_start();

require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Core/Email.php';
require_once __DIR__ . '/app/Core/NotificacoesEmail.php';
require_once __DIR__ . '/app/Models/Usuario.php';
require_once __DIR__ . '/app/Models/Financeiro.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/AplicacaoController.php';

$auth = new AuthController();
$app = new AplicacaoController();
$rota = (string)($_GET['rota'] ?? 'login');
$metodo = (string)($_SERVER['REQUEST_METHOD'] ?? 'GET');

$rotasAuth = [
    'login' => $metodo === 'POST' ? 'entrar' : 'mostrarLogin',
    'cadastro' => $metodo === 'POST' ? 'cadastrar' : 'mostrarCadastro',
    'sair' => 'sair',
];
$rotasApp = [
    'painel' => 'painel',
    'alunos' => 'alunos',
    'planos' => 'planos',
    'gerar_cobrancas' => 'gerarCobrancas',
    'cobrancas' => 'cobrancas',
    'minhas_cobrancas' => 'minhasCobrancas',
    'enviar_comprovante' => 'enviarComprovante',
    'comprovantes' => 'comprovantes',
    'abrir_comprovante' => 'abrirComprovante',
    'avaliar_comprovante' => 'avaliarComprovante',
    'baixar_comprovante' => 'baixarComprovante',
    'relatorios' => 'relatorios',
    'configuracoes' => 'configuracoes',
];

if (isset($rotasAuth[$rota])) {
    $auth->{$rotasAuth[$rota]}();
} elseif (isset($rotasApp[$rota])) {
    $app->{$rotasApp[$rota]}();
} else {
    http_response_code(404);
    echo 'Página não encontrada.';
}
