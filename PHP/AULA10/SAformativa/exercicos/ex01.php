<?php

declare(strict_types=1);

require_once __DIR__ . '/ConexaoBanco.php';

const ARQUIVO_CONFIG = __DIR__ . '/config/database.ini';

verificarConexao();

// Tenta conectar ao PostgreSQL e imprime no terminal o resultado do diagnóstico
function verificarConexao(): void
{
    echo "Iniciando diagnóstico de conexão (porta 5432)...\n";

    try {
        $pdo = ConexaoBanco::obterConexao(ARQUIVO_CONFIG);
        $versao = $pdo->query('SELECT version()')->fetchColumn();

        echo "[OK] Conexão estabelecida com sucesso.\n";
        echo "[OK] Versão do PostgreSQL: {$versao}\n";
    } catch (PDOException $e) {
        echo "[ERRO] Não foi possível conectar ao banco de dados.\n";
        echo "[ERRO] Verifique host, porta 5432 e credenciais em database.ini.\n";
    } catch (RuntimeException $e) {
        echo "[ERRO] {$e->getMessage()}\n";
    }
}