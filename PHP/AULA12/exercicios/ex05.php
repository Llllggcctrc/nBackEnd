<?php
declare(strict_types=1);

/**
 * Registra a tentativa de login no log de auditoria.
 */
function registrarAuditoria(string $email, bool $sucesso): void
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconhecido';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido';
    $dataHora = date('Y-m-d H:i:s');
    $status = $sucesso ? 'true' : 'false';

    // Mock de SQL para o PostgreSQL
    $sql = "INSERT INTO auditoria_logins (email, ip, user_agent, sucesso, data_hora) 
            VALUES (:email, :ip, :ua, :sucesso, :data)";
            
    echo "SQL Executado: {$sql}\n";
    echo "Dados Gravados -> Email: {$email} | IP: {$ip} | UA: {$userAgent} | Sucesso: {$status} | Data: {$dataHora}\n";
}

// Simulando execuções
registrarAuditoria('admin@empresa.com', true);
echo "\n";
registrarAuditoria('hacker@evil.com', false);
