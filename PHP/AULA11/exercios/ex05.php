<?php

declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';
require_once __DIR__ . '/src/PecaDAO.php';

function prepararTabela(PDO $pdo): void
{
    // Comando fixo, sem nenhum dado externo.
    $pdo->exec('ALTER TABLE pecas_industriais ADD COLUMN IF NOT EXISTS ativo BOOLEAN NOT NULL DEFAULT TRUE');
    echo "Coluna 'ativo' garantida na tabela pecas_industriais.\n";
}

function mostrarLista(string $titulo, array $pecas): void
{
    echo "\n--- {$titulo} ---\n";

    if ($pecas === []) {
        echo "(nenhuma)\n";
    }

    foreach ($pecas as $p) {
        $situacao = $p['ativo'] ? 'ATIVA' : 'INATIVA';
        echo "#{$p['id']} | {$p['codigo_sku']} | {$p['descricao']} | {$situacao}\n";
    }
}


function mostrarAuditoria(object $dao, int $id): void
{
    $peca = $dao->buscarPorId($id);
    $situacao = $peca === null ? 'NÃO ENCONTRADO' : ($peca['ativo'] ? 'ativo' : 'inativo, mas preservado');

    echo "\nAuditoria contábil -> peça #{$id}: {$situacao}\n";
}

function executarDemonstracao(): void
{
    $reflexao = new ReflectionClass(ConexaoBanco::class);
    $conexao = $reflexao->newInstanceWithoutConstructor();
    $constructor = $reflexao->getConstructor();

    if ($constructor === null) {
        throw new RuntimeException('ConexaoBanco não possui constructor.');
    }

    $constructor->setAccessible(true);
    $constructor->invoke($conexao);

    $pdo = $conexao->getConexao();
    $dao = (new ReflectionClass('PecaDAO'))->newInstance($pdo);

    prepararTabela($pdo);
    $id = $dao->inserir('EX05-' . uniqid(), 'Peça para soft delete', 'Mecanica', 3, 19.90);

    mostrarLista('Ativas ANTEStyut da exclusão', $dao->listarTodos());
    $dao->excluir($id);
    mostrarLista('Ativas DEPOIS da exclutsão (peça some da listagem)', $dao->listarTodos());
    mostrarLista('Inativas (continuaum no banco)', $dao->listarInativas());
    mostrarAuditoria($dao, $id);
}

try {
    executarDemonstracao();
} catch (Throwable $erro) {
    error_log($erro->getMessage());
    echo "Erro: não foi puiossível concluir a demonstração.\n";
}