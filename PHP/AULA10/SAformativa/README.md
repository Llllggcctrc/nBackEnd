# Projeto de Conexão com Banco de Dados usando PDO, Singleton e PDOException

## Passo 1: validando a extensão do pdo_pgsql e o servidor do postSQL

1. Abra o Terminal e execute o seguinte comando 

```bash
php -m | findstr -i pgsql
```


Saída Esperada: Deve listar `pdo_pgsql` e o `pgsql`

Caso não aparecça:
* Abrir seu `php.ini`
* Localize a linha `;extention=pdo_pgsql`e remova o ponto e vírgula inicial (`;`).
* Salve o Arquivo e valide novamente o comando

2. Validar o serviço do postgreSQL 

instalar uma extensão do VScode -> postgreSQL (Chris Kolkman) E configurar uma conexao

## passo 2: estruturar de diretorios do projeto

```text
SAFormativa/
├── config/
│   └── database.ini        <- Credenciais protegidas
├── logs/
│   └── database.log        <- Arquivo gerado para auditoria de falhas
├── src/
│   └── ConexaoBanco.php    <- Classe Singleton com PDO para PostgreSQL
├── schema.sql              <- Script DDL e DML para o PostgreSQL
├── index.php               <- Painel de diagnóstico e testes operacionais
└── README.md               <- Documentação do projeto
```

## Passo 3: Executar o Script DDL no PostgreSQL (`schema.sql`) 


```sql
-- Cria o banco de dados da biblioteca (caso use o terminal psql)
CREATE DATABASE  WITH ENCODING 'UTF8';

-- Cria a tabela de acervo de livros
CREATE TABLE IF NOT EXISTS livros (
    id SERIAL PRIMARY KEY,
    titulo VARCHAR(120) NOT NULL,
    autor VARCHAR(100) NOT NULL,
    preco NUMERIC(6,2) NOT NULL,
    status VARCHAR(15) NOT NULL DEFAULT 'DISPONIVEL' 
            CHECK (status IN ('DISPONIVEL', 'EMPRESTADO', 'RESERVADO')),
        data_cadastro TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Insere alguns livros iniciais para teste
INSERT INTO livros (titulo, autor, preco, status) 
VALUES 
('O Pequeno Príncipe', 'Antoine de Saint-Exupéry', 39.90, 'DISPONIVEL'),
('Dom Casmurro', 'Machado de Assis', 29.90, 'EMPRESTADO'),
('1984', 'George Orwell', 45.00, 'RESERVADO');
```

## Passo 4: Criando o Arquivo de Configuração (`config/database.ini`)

Crie o arquivo. Ajuste as chaves de acesso ao banco de dado

Colocar o arquivo de configuração dentro do `.gitignore`

## Passo 5: Construindo a Classe Singgleton e Conexão com o BAnco de dados (`src/conexaoBanco.php`)


criação de uma classe segura de conexão usando Singleton PDO e suas Flags de Segurança, e Envelopamento (.ini) de Dados.

## Passo 6: Construindo a Interfacce de Diagnóstico do Banco de Dados (`index.php`)
