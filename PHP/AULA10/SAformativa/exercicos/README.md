
# Conexão com Banco de Dados usando PDO

## Parte A — Exercícios Teóricos

### 1. Abstração de Dados

**O que é o PDO e por que ele é preferível ao antigo `pgsql`?**

O **PDO (PHP Data Objects)** é uma camada de abstração de acesso a dados. Ele oferece **uma interface única** para conversar com vários bancos (PostgreSQL, MySQL, SQLite, SQL Server...), mudando apenas o driver.

Já a extensão `pgsql` é **procedural** e presa ao PostgreSQL (`pg_connect()`, `pg_query()`...).

| Característica | PDO | `pgsql` (procedural) |
|---|---|---|
| Orientado a objetos |  Sim |  Não |
| Suporta vários bancos |  Sim |  Só PostgreSQL |
| Prepared statements padronizados |  Sim |  Funções próprias |
| Tratamento de erros com exceções |  Sim |  Manual |
| Facilidade de trocar de SGBD |  Alta |  Reescrever o código |

>  **Em projetos corporativos:** código mais limpo, seguro, fácil de manter e portável.

---

### 2. Ciclo do DSN

**O que é a string DSN e para que serve cada parâmetro?**

**DSN (Data Source Name)** é a string que diz ao PDO *qual driver usar* e *onde está o banco*.

```php
$dsn = "pgsql:host=localhost;port=5432;dbname=meu_banco";
```

| Parâmetro | Finalidade |
|---|---|
| `pgsql:` | Driver que será usado (PostgreSQL) |
| `host` | Endereço do servidor do banco (`localhost`, IP ou domínio) |
| `port` | Porta em que o PostgreSQL está escutando |
| `dbname` | Nome do banco de dados que será acessado |

---

### 3.  Padrão de Portas

**Qual a porta padrão do PostgreSQL e como ela aparece na conexão?**

A porta padrão é a **5432**. Ela é informada no DSN pelo parâmetro `port`:

```php
"pgsql:host=localhost;port=5432;dbname=meu_banco"
```

> Se o `port` for omitido, o driver usa a 5432 automaticamente. Só é preciso informá-la quando o servidor usa uma porta diferente.

---

### 4. Flags de Integridade

**O que acontece com `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`?**

O PDO passa a **lançar uma `PDOException`** sempre que ocorrer um erro (SQL inválido, falha de conexão, tabela inexistente...). Isso permite tratar tudo com `try/catch` e impede que erros passem despercebidos.

```php
try {
    $pdo->query("SELECT * FROM tabela_que_nao_existe");
} catch (PDOException $e) {
    // erro tratado aqui
}
```

**E se não definirmos a flag?**

- **PHP 8.0 ou superior:** o padrão já é `ERRMODE_EXCEPTION`.
- **PHP 7.x e anteriores:** o padrão era `ERRMODE_SILENT`, o erro acontecia **em silêncio**, e só era possível descobrir consultando `errorInfo()` manualmente.

> Boa prática: definir a flag explicitamente, deixando o código claro e independente da versão.

---

### 5. Fetch Mode

**Qual a vantagem de `PDO::FETCH_ASSOC` para a memória RAM?**

O modo padrão do PDO é `FETCH_BOTH`, que devolve cada coluna **duas vezes**: pelo nome e pelo índice numérico.

```php
// FETCH_BOTH (duplicado)
['id' => 1, 0 => 1, 'nome' => 'Ana', 1 => 'Ana']

// FETCH_ASSOC (enxuto)
['id' => 1, 'nome' => 'Ana']
```

Com `FETCH_ASSOC`, cada linha guarda **apenas o array associativo**, consumindo **bem menos memória** (em torno da metade por linha) e deixando o código mais legível, já que acessamos por `$linha['nome']`.

---

### 6.  Padrão Singleton

**Por que abrir `new PDO()` a cada consulta pode esgotar o `max_connections`?**

No PostgreSQL, **cada conexão cria um processo no servidor**, e isso tem custo: handshake TCP, autenticação e memória. O servidor tem um limite de conexões simultâneas (`max_connections`, por padrão **100**).

Se cada consulta abre uma conexão nova, em um sistema com muitos acessos o limite estoura rápido e surge o erro:

```
FATAL: sorry, too many clients already
```

O **Singleton** resolve isso: existe **uma única conexão reaproveitada** por toda a aplicação, economizando recursos e melhorando o desempenho.

---

### 7. Encapsulamento do Singleton

**Por que o construtor da `ConexaoBanco` deve ser `private`? Quais métodos mágicos bloquear?**

Se o construtor fosse público, qualquer parte do código poderia fazer `new ConexaoBanco()` e criar várias instâncias, quebrando o padrão. Sendo **`private`**, só a própria classe consegue se instanciar, através de um método estático (`getInstancia()`).

Para garantir a **unicidade**, também bloqueamos as outras formas de duplicar o objeto:

| Método mágico | Por que bloquear? |
|---|---|
| `__clone()` | Impede criar uma cópia com `clone $obj` (declarar como `private`) |
| `__wakeup()` | Impede recriar a instância via `unserialize()` (lançar uma exceção) |

```php
class ConexaoBanco
{
    private static ?ConexaoBanco $instancia = null;
    private PDO $pdo;

    private function __construct() { /* cria o PDO */ }

    private function __clone() {}

    public function __wakeup()
    {
        throw new Exception("Não é permitido desserializar um Singleton.");
    }

    public static function getInstancia(): self
    {
        return self::$instancia ??= new self();
    }
}
```

---

### 8. Segurança de Credenciais

**Por que não deixar usuário e senha hardcoded nos scripts?**

-  **Vazamento:** o código vai para o Git/GitHub e a senha fica exposta no histórico.
-  **Difícil de trocar:** mudar a senha exigiria editar e republicar o código.
-  **Ambientes diferentes:** desenvolvimento, teste e produção usam credenciais distintas.
-  **Acesso indevido:** qualquer pessoa com acesso ao código passa a ter acesso ao banco.

**Solução:** guardar as credenciais em um arquivo externo (`.ini` ou `.env`), **fora da pasta pública** e **ignorado pelo Git**.

```ini
; config/banco.ini
host     = localhost
port     = 5432
dbname   = meu_banco
user     = meu_usuario
password = minha_senha
```

```gitignore
# .gitignore
config/banco.ini
```

---

### 9.  Tratamento de Exceções e LGPD

**Por que exibir `$e->getMessage()` na tela é uma falha grave (Information Disclosure)?**

A mensagem de uma `PDOException` costuma conter **detalhes internos** do sistema, como:

- endereço do servidor e porta;
- nome do banco de dados e do usuário;
- trechos da consulta SQL;
- nomes de tabelas e colunas.

Um atacante usa essas pistas para **mapear a estrutura do sistema** e planejar ataques, como SQL Injection. Isso é **Information Disclosure** (exposição de informação sensível).

**Ligação com a LGPD:** a lei (Lei 13.709/2018) exige que o controlador adote **medidas de segurança** para proteger dados pessoais. Expor detalhes do banco enfraquece essa proteção e pode facilitar vazamentos.

**Forma correta:** registrar o erro real em um **log interno** e mostrar ao usuário apenas uma **mensagem genérica**.

```php
try {
    $pdo = ConexaoBanco::getInstancia()->getPdo();
} catch (PDOException $e) {
    error_log($e->getMessage());                 // detalhe vai para o log
    echo "Erro ao processar a requisição. Tente novamente mais tarde."; // genérico
}
```

---

##  Resumo Rápido

| # | Tema | Ideia principal |
|---|---|---|
| 1 | PDO | Interface única, orientada a objetos e portável |
| 2 | DSN | String que define driver, host, porta e banco |
| 3 | Porta | 5432, informada em `port=` |
| 4 | ERRMODE | Erros viram exceções tratáveis com `try/catch` |
| 5 | FETCH_ASSOC | Menos memória, sem índices numéricos duplicados |
| 6 | Singleton | Uma conexão reaproveitada, sem estourar `max_connections` |
| 7 | Encapsulamento | Construtor `private` + bloqueio de `__clone` e `__wakeup` |
| 8 | Credenciais | Arquivo externo, fora do Git e da pasta pública |
| 9 | Exceções | Log interno + mensagem genérica ao usuário |
