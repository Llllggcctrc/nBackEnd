<?php
declare(strict_types=1);

session_start();

/**
 * Verifica se o usuário atual possui o perfil mínimo exigido.
 */
function verificarPermissao(string $perfilMinimo): void
{
    $niveis = [
        'USUARIO' => 1,
        'OPERADOR' => 2,
        'ADMIN' => 3
    ];

    $perfilUsuario = $_SESSION['perfil'] ?? 'USUARIO';

    $nivelUsuario = $niveis[$perfilUsuario] ?? 0;
    $nivelExigido = $niveis[$perfilMinimo] ?? 99;

    if ($nivelUsuario < $nivelExigido) {
        http_response_code(403);
        echo "<h1>403 Forbidden</h1>";
        echo "<p>Acesso negado. Você não tem permissão para acessar esta página.</p>";
        exit;
    }
}

// Simulando acesso a relatório gerencial
$_SESSION['perfil'] = 'OPERADOR'; 
verificarPermissao('ADMIN'); 

echo "Página de relatórios gerenciais.";