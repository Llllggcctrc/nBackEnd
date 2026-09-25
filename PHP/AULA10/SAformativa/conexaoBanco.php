<?php

declare(strict_types=1);

/**
 * Classe Singleton responsável por gerenciar a ÚNICA conexão PDO
 * com o banco de dados PostgreSQL durante a execução do script.
 */
class ConexaoBanco
{
    private static ?PDO $instancia = null;

    // Construtor privado: impede "new ConexaoBanco()" fora da classe
    private function __construct()
    {
    }

    // Bloqueia a clonagem, que criaria uma segunda instância
    private function __clone()
    {
    }

    // Bloqueia a desserialização, outra forma de burlar o Singleton
    public function __wakeup()
    {
        throw new \Exception('Não é permitido desserializar este Singleton.');
    }

    // Ponto único de acesso à conexão: cria na 1ª chamada, reaproveita nas demais
    public static function obterConexao(string $arquivoConfig): PDO
    {
        if (self::$instancia === null) {
            $config = self::carregarConfiguracao($arquivoConfig);
            self::$instancia = self::criarConexao($config);
        }

        return self::$instancia;
    }

    // Lê o arquivo .ini (formato simples, sem seções) e valida sua existência
    private static function carregarConfiguracao(string $arquivo): array
    {
        if (!file_exists($arquivo)) {
            throw new \RuntimeException("Arquivo de configuração não encontrado: {$arquivo}");
        }

        $config = parse_ini_file($arquivo);

        if ($config === false) {
            throw new \RuntimeException('Falha ao interpretar o arquivo de configuração.');
        }

        return $config;
    }

    // Monta o DSN do PostgreSQL e abre a conexão com as opções recomendadas
    private static function criarConexao(array $config): PDO
    {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $config['db_host'],
            $config['db_port'],
            $config['db_name']
        );

        $opcoes = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => false,
        ];

        return new PDO($dsn, $config['db_user'], $config['db_pass'], $opcoes);
    }
}