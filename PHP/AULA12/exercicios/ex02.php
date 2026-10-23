<?php
declare(strict_types=1);

$senha = "SenhaForte@123";
$hash = password_hash($senha, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,
    'time_cost' => 4,
    'threads' => 1
]);

echo "Senha Original: {$senha}\n";
echo "Hash Completo: {$hash}\n\n";

// Decomposição do Argon2id: $argon2id$v=19$m=65536,t=4,p=1$saltBase64$hashBase64
$partes = explode('$', $hash);

if ($partes[1] === 'argon2id') {
    echo "1. Algoritmo: {$partes[1]}\n";
    echo "2. Versão: {$partes[2]}\n";
    echo "3. Configurações de Custo: {$partes[3]}\n";
    echo "4. Salt (Base64): {$partes[4]}\n";
    echo "5. Hash Resultante (Base64): {$partes[5]}\n";
} else {
    echo "Algoritmo diferente de Argon2id utilizado.\n";
}