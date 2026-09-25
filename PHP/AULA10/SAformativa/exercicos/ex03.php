<?php

declare(strict_types=1);

const ARQUIVO_AMBIENTES = __DIR__ . '/config/ambientes.ini';

// Uso: php ex03_multi_ambiente.php development   (ou "testing")
$ambiente = $argv[1] ?? 'development';

try {
    $config = carregarAmbiente($ambiente);
    $pdo = conectarComConfiguracao($config);
    echo "[OK] Conectado ao ambiente '{$ambiente}' -> banco '{$config['db_name']}'.\n";
} catch (Throwable $e) {
    echo "[ERRO] {$e->getMessage()}\n";
}

// Carrega apenas a seção correspondente ao ambiente pedido no arquivo .ini
function carregarAmbiente(string $ambiente): array
{
    $config = parse_ini_file(ARQUIVO_AMBIENTES, true);

    if ($config === false || !isset($config[$ambiente])) {
        throw new RuntimeException("Ambiente '{$ambiente}' não encontrado no arquivo de configuração.");
    }

    return $config[$ambiente];
}

// Monta o DSN a partir dos dados do ambiente escolhido e abre a conexão PDO
function conectarComConfiguracao(array $config): PDO
{
    $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $config['db_host'], $config['db_port'], $config['db_name']);

    return new PDO($dsn, $config['db_user'] ?? 'postgres', $config['db_pass'] ?? '', [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}