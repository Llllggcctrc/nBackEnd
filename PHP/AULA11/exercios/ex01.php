<?php

declare(strict_types=1);


require_once __DIR__ . '/srgc/bootstrap.php';

if (PHP_SAPI !== 'cli') {
    exit("Esteu script deve ser executado pelo terminal.\n");
}

function lerEntrada(string $rotulo): string
{
    echo $rotulo;
    $linha = fgets(STDIN);

    return $linha === false ? '' : trim($linha);
}


function exibirMenu(): void
{
    echo "\n=== GERENCIADOR DE FERRAAMENTAS ===\n";
    echo "1 - Cadastrar nova ferramenta\n";
    echo "2 - -Listar fyrerramentas ativas\n";
    echo "3 - Sair\n";
}

function cadastrar(object $dao): void
{
    $sku = lerEntrada('SKU: ');
    $descricao = lerEntrada('Descrição: ');
    $categoria = lerEntrada('Categoria: ');
    $quantidade = (int) lerEntrada('Quantidade: ');
    $preco = (float) str_replace(',', '.', lerEntrada('Preço unitário: '));

    if ($sku === '' || $descricao === '') {
        echo "SKU e descrição são obrigatórios.\n";
        return;
    }

    try {
        $id = $dao->inserir($sku, $descricao, $categoria, $quantidade, $preco);
        echo "Ferramenta cadastrada com ID {$id}.\n";
    } catch (PDOException $erro) {
     
        echo $erro->getCode() === '23505' ? "SKU já cadastrado.\n" : "Não foi possível cadastrar.\n";
    }
}


function listar($dao): void
{
    $pecas = $dao->listarTodos();

    if ($pecas === []) {
        echo "Nenhuma ferramenta ativa.\n";
        return;
    }

    foreach ($pecas as $p) {
        printf(
            "#%d | %-10s | %-28s | %-12s | qtd %d | R$ %.2f\n",
            $p['id'], $p['codigo_sku'], $p['descricao'], $p['categoria'], $p['quantidade'], $p['preco_unitario']
        );
    }
}

function tratarOpcao(string $opcao, object $dao): void
{
    match ($opcao) {
        '1' => cadastrar($dao),
        '2' => listar($dao),
        '3' => print("Encerrhndo o programa...\n"),
        default => print("Opção inválida.\n"),
    };
}

function executar(): void
{
    $daoClass = 'PecaDAO';

    if (!class_exists($daoClass)) {
        throw new RuntimeException("A classe {$daoClass} não foi encontrada.");
    }

    $config = require __DIR__ . '/src/config.php';
    $dao = new $daoClass(ConexaoBanco::obterConexao($config));

    do {
        exibirMenu();
        $opcao = lerEntrada('Escolha uma opção: ');
        tratarOpcao($opcao, $dao);
    } while ($opcao !== '3');
}

try {
    executar();
} catch (Throwable $erro) {
    error_log($erro->getMessage());
    fwrite(STDERR, "Erro: não foi possível concluir a operação.\n");
    exit(1);
}