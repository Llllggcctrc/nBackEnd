<?php
declare(strict_types=1);

require_once 'Ex00_Base.php';
require_once 'ex01_conta_salario.php';

echo "Iniciando Auditoria de Encapsulamento...\n";

$titular = new Titular('Ana Silva', '123.456.789-00');

// 1. Instanciar classe abstrata
try {
    $reflection = new ReflectionClass(ContaBancaria::class);
    if ($reflection->isAbstract()) {
        throw new Exception("Bloqueado: Não é possível instanciar ContaBancaria (abstract).");
    }
} catch (Exception $e) {
    echo "Teste 1: " . $e->getMessage() . "\n";
}

$conta = new ContaSalario($titular, '99.999.999/0001-99');

// 2. Depósito de valor negativo
try {
    $conta->depositar(-50.0);
} catch (ValorInvalidoException $e) {
    echo "Teste 2: Bloqueado! " . $e->getMessage() . "\n";
}

// 3. Quebra de visibilidade (acesso a atributo protegido)
try {
    // Tenta forçar a leitura do saldo privado no escopo atual
    $propriedade = 'saldo';
    $reflexaoSaldo = function() use ($conta, $propriedade) { return $conta->{$propriedade}; };
    $reflexaoSaldo();
} catch (Error $e) {
    echo "Teste 3: Bloqueado! Acesso direto negado ao saldo.\n";
}