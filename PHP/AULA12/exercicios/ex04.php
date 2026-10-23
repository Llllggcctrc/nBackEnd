<?php
declare(strict_types=1);
session_start();

/**
 * Valida o limite de tentativas de login (Rate Limiting).
 */
function verificarBloqueio(): bool
{
    $limiteFalhas = 3;
    $tempoBloqueio = 60; // segundos

    if (isset($_SESSION['bloqueado_ate'])) {
        $tempoRestante = $_SESSION['bloqueado_ate'] - time();
        if ($tempoRestante > 0) {
            echo "Acesso bloqueado. Tente novamente em {$tempoRestante} segundos.";
            return true;
        }
        // Tempo expirou, libera o acesso
        unset($_SESSION['bloqueado_ate']);
        $_SESSION['tentativas_login'] = 0;
    }
    return false;
}

/**
 * Processa a falha ou sucesso no login.
 */
function processarLogin(bool $sucesso): void
{
    if ($sucesso) {
        $_SESSION['tentativas_login'] = 0;
        echo "Login realizado com sucesso!";
        return;
    }

    $_SESSION['tentativas_login'] = ($_SESSION['tentativas_login'] ?? 0) + 1;
    
    if ($_SESSION['tentativas_login'] >= 3) {
        $_SESSION['bloqueado_ate'] = time() + 60;
        echo "Conta bloqueada por 60 segundos devido a falhas consecutivas.";
    } else {
        echo "Senha incorreta. Tentativa " . $_SESSION['tentativas_login'] . " de 3.";
    }
}

// Teste de execução
if (!verificarBloqueio()) {
    $sucessoNoLogin = false; // Simula erro de senha
    processarLogin($sucessoNoLogin);
}