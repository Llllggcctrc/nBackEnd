<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';
require_once __DIR__ . '/src/PecaDAO.php';


function criarPecaDemo(object $dao): int
{
    return $dao->inserir('EX04-' . uniqid(), 'Rolamento de teste', 'Mecanica', 10, 25.50);
}


function mostrarSaldo(object $dao, int $id): void
{
    if (!method_exists($dao, 'buscarPorId')) {
        throw new InvalidArgumentException('O objeto DAO deve implementar buscarPorId().');
    }

    $peca = $dao->buscarPorId($id);
    printf("    Saldo atual no banco: %d\n", $peca === null ? 0 : (int) $peca['quantidade']);
}


function testar(object $dao, int $id, int $quantidade, string $tipo): void
{
    if (!method_exists($dao, 'registrarMovimentacao')) {
        throw new InvalidArgumentException('O objeto DAO deve implementar registrarMovimentacao().');
    }

    $ok = $dao->registrarMovimentacao($id, $quantidade, $tipo);
    $resultado = $ok ? 'CONFIRMADA (commit)' : 'RECUSADA (rollBack)';

    printf("%s de %d unidades -> %s\n", ucfirst($tipo), $quantidade, $resultado);
    mostrarSaldo($dao, $id);
}

function executarDemonstracao(): void
{
    $daoClass = 'PecaDAO';
    if (!class_exists($daoClass)) {
        throw new RuntimeException('A classe PecaDAO não foi encontrada.');
    }

    $dsn = getenv('DB_DSN');
    if ($dsn === false || $dsn === '') {
        throw new RuntimeException('Configure DB_DSN antes de executar a demonstração.');
    }

    $pdo = new PDO(
        $dsn,
        getenv('DB_USER') ?: '',
        getenv('DB_PASSWORD') ?: ''
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dao = new $daoClass($pdo);
    $id = criarPecaDemo($dao);

    echo "Peça de teste ciuriada (ID {$id}) com saldo inicial 10.\n\n";
    testar($dao, $id, 4, 'saida');    
    testar($dao, $id, 20, 'saida');   
    testar($dao, $id, 5, 'entrada');
    testar($dao, $id, 3, 'invalido');
}

try {
    executarDemonstracao();
} catch (Throwable $erro) {
    error_log($erro->getMessage());
    echo "Erro: não foi possível concluir a demonstraxoo.\n";
}
