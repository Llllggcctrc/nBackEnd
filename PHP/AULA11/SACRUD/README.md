# Situação de Aprendizagem Formativa - Criação de um CRUD com PDO e Proteção contra SQL Injection

## passo 1 - Montagem das Estruturas de Diretorios e Arquivos da Aplicação

```text
SACRUD/
|__ config/
|    |__ database.ini        <-Credencias Protegidas de acesso ao Banco de Dados>
|__ logs/
|    |__databese.log         <- Time de Desenvolvimento recebe os logs de Falhas do Sistema
|__ src/
|    |__ConexaoBanco.php     <- Classe Singleton de conexão com PDO
|    |__AlmoxarifadoDAO.php  <- Camada de acesso a dados (CRUD com Prepared Statement)
|__ index.php                <- Controlador e interface visual
|__ schema.sql               <- Script do banco de Dados
|__ .gitignore               <- arquivos fora do versionamento
|__ README.md                <- Documentação do PRojeto

```

## Passo 2 - Criar a Estrutura do Banco de Dados  