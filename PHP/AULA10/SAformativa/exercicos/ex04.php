<?php

declare(strict_types=1);

require_once __DIR__ . '/ConexaoBanco.php';

const ARQUIVO_CONFIG = __DIR__ . '/config/database.ini';
const ARQUIVO_LOG = __DIR__ . '/logs/sistema.log';

testarLogDeConexao();

// Registra uma linha de log no formato [DATA_HORA] [NIVEL] Mensagem
function registrarLog(string $nivel, string $mensagem): void
{
    $pasta = dirname(ARQUIVO_LOG);
    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }

    $dataHora = date('Y-m-d H:i:s');
    $linha = "[{$dataHora}] [{$nivel}] {$mensagem}" . PHP_EOL;

    file_put_contents(ARQUIVO_LOG, $linha, FILE_APPEND);
}

// Tenta conectar ao banco e grava INFO em caso de sucesso ou ERROR em caso de falha
function testarLogDeConexao(): void
{
    try {
        ConexaoBanco::obterConexao(ARQUIVO_CONFIG);
        registrarLog('INFO', 'Conexão com o banco de dados estabelecida com sucesso.');
        echo "Conexão OK. Log gravado em logs/sistema.log\n";
    } catch (Throwable $e) {
        registrarLog('ERROR', 'Falha na conexão com o banco: ' . $e->getMessage());
        echo "Falha na conexão. Log gravado em logs/sistema.log\n";
    }
}