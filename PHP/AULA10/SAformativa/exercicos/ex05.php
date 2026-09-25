<?php

declare(strict_types=1);

require_once __DIR__ . '/ConexaoBanco.php';

const ARQUIVO_CONFIG = __DIR__ . '/config/database.ini';
const TOTAL_ITERACOES = 50;

exibirResultadoBenchmark();

// Abre uma nova conexão PDO a cada iteração, sem reaproveitar nada (cenário ruim)
function benchmarkSemSingleton(int $iteracoes): array
{
    $inicio = microtime(true);
    $memoriaInicial = memory_get_usage();

    $config = parse_ini_file(ARQUIVO_CONFIG);
    $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $config['db_host'], $config['db_port'], $config['db_name']);

    for ($i = 0; $i < $iteracoes; $i++) {
        new PDO($dsn, $config['db_user'], $config['db_pass']);
    }

    return ['tempo' => microtime(true) - $inicio, 'memoria' => memory_get_usage() - $memoriaInicial];
}

// Reaproveita a mesma conexão via Singleton em todas as iterações (cenário bom)
function benchmarkComSingleton(int $iteracoes): array
{
    $inicio = microtime(true);
    $memoriaInicial = memory_get_usage();

    for ($i = 0; $i < $iteracoes; $i++) {
        ConexaoBanco::obterConexao(ARQUIVO_CONFIG);
    }

    return ['tempo' => microtime(true) - $inicio, 'memoria' => memory_get_usage() - $memoriaInicial];
}

// Executa os dois cenários e imprime uma tabela HTML comparando os resultados
function exibirResultadoBenchmark(): void
{
    try {
        $semSingleton = benchmarkSemSingleton(TOTAL_ITERACOES);
        $comSingleton = benchmarkComSingleton(TOTAL_ITERACOES);
    } catch (Throwable $e) {
        echo "Erro ao executar o benchmark: {$e->getMessage()}\n";
        return;
    }

    echo "<table border='1' cellpadding='8' style='border-collapse:collapse'>";
    echo "<tr><th>Cenário</th><th>Tempo (s)</th><th>Memória (bytes)</th></tr>";
    echo "<tr><td>Sem Singleton (50 conexões novas)</td><td>" . number_format($semSingleton['tempo'], 5) . "</td><td>{$semSingleton['memoria']}</td></tr>";
    echo "<tr><td>Com Singleton (1 conexão reaproveitada)</td><td>" . number_format($comSingleton['tempo'], 5) . "</td><td>{$comSingleton['memoria']}</td></tr>";
    echo "</table>";
}