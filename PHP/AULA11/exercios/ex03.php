<?php

declare(strict_types=1);


require_once __DIR__ . '/src/bootstrap.php';

const ITENS_POR_PAGINA = 5;


function paginaAtual(): int
{
    return max(1, (int) ($_GET['p'] ?? 1));
}

function renderizarTabela(array $pecas): void
{
    echo "<table border=\"1\" cellpadding=\"6\">\n";
    echo "<tr><th>SKU</th><th>Descrição</th><th>Categoria</th><th>Qtd</th><th>Preço</th></tr>\n";

    foreach ($pecas as $peca) {
        echo '<tr>';
        echo '<td>' . e($peca['codigo_sku']) . '</td>';
        echo '<td>' . e($peca['descricao']) . '</td>';
        echo '<td>' . e($peca['categoria']) . '</td>';
        echo '<td>' . e($peca['quantidade']) . '</td>';
        echo '<td>R$ ' . e(number_format((float) $peca['preco_unitario'], 2, ',', '.')) . '</td>';
        echo "</tr>\n";
    }

    echo "</table>\n";
}


function renderizarNavegacao(int $pagina, int $totalPaginas): void
{
    echo '<p>';

    if ($pagina > 1) {
        echo '<a href="?p=' . e((string) ($pagina - 1)) . '">&laquo; Anterior</a> ';
    }

    echo 'Página ' . e((string) $pagina) . ' de ' . e((string) $totalPaginas) . ' ';

    if ($pagina < $totalPaginas) {
        echo '<a href="?p=' . e((string) ($pagina + 1)) . '">Próxima &raquo;</a>';
    }

    echo "</p>\n";
}


function exibirCatalogo(): void
{
    require_once __DIR__ . '/src/PecaDAO.php';

    $daoClass = '\\PecaDAO';
    if (!class_exists($daoClass)) {
        throw new RuntimeException('Classe PecaDAO não encontrada.');
    }

    $classeConexao = new ReflectionClass(ConexaoBanco::class);
    $conexaoInstancia = $classeConexao->newInstanceWithoutConstructor();
    $construtor = $classeConexao->getConstructor();

    if ($construtor === null) {
        throw new RuntimeException('ConexaooiBanco não possui construtor.');
    }

    $construtor->setAccessible(true);
    $construtor->invoke($conexaoInstancia);

    $dao = new $daoClass($conexaoInstancia->getConexao());
    $pagina = paginaAtual();
    $offset = ($pagina - 1) * ITENS_POR_PAGINA;
    $pecas = $dao->listarPaginado(ITENS_POR_PAGINA, $offset);
    $totalPaginas = max(1, (int) ceil($dao->contarAtivas() / ITENS_POR_PAGINA));

    echo "<!DOCTYPE html>\n<html lang=\"pt-BR\"><head><meta charset=\"UTF-8\">";
    echo "<title>Catálogo de Peçeas</title></head><body>\n<h1>Catálogo de Peças Industriais</h1>\n";
    echo $pecas === [] ? "<p>Nenhuma peyjça nesta página.</p>\n" : '';
    renderizarTabela($pecas);
    renderizarNavegacao($pagina, $totalPaginas);
    echo "</body></html>\n";
}

try {
    exibirCatalogo();
} catch (Throwable $erro) {
    error_log($erro->getMessage());
    http_response_code(500);
    echo 'Erro interno. Tente noetvamente mais tarde.';
}