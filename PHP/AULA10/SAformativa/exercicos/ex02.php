<?php

declare(strict_types=1);

require_once __DIR__ . '/ConexaoBanco.php';

const ARQUIVO_CONFIG = __DIR__ . '/config/database.ini';

testarIdentidadeSingleton();

// Verifica se duas chamadas ao Singleton apontam para o mesmo objeto em memória
function testarIdentidadeSingleton(): void
{
    try {
        $conexao1 = ConexaoBanco::obterConexao(ARQUIVO_CONFIG);
        $conexao2 = ConexaoBanco::obterConexao(ARQUIVO_CONFIG);

        $id1 = spl_object_id($conexao1);
        $id2 = spl_object_id($conexao2);

        echo "ID do objeto 1 (SPL Object ID): {$id1}\n";
        echo "ID do objeto 2 (SPL Object ID): {$id2}\n";

        if ($conexao1 === $conexao2) {
            echo "[SUCESSO] As duas variáveis apontam para a MESMA instância. Singleton funcionando.\n";
        } else {
            echo "[FALHA] Foram criadas instâncias diferentes. O Singleton está quebrado.\n";
        }
    } catch (Throwable $e) {
        echo "[ERRO] Não foi possível testar a conexão: {$e->getMessage()}\n";
    }
}