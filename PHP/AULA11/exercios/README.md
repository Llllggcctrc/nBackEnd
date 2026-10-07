# Parte A: Exercícios Teóricos de Fixação

## 1. Definição de CRUD

**CRUD** é o acrônimo para as quatro operações básicas feitas sobre dados persistidos: **C**reate (criar), **R**ead (ler), **U**pdate (atualizar) e **D**elete (excluir).

| Letra | Operação | Instrução SQL (PostgreSQL) |
|-------|----------|----------------------------|
| C | Create | `INSERT INTO` |
| R | Read | `SELECT` |
| U | Update | `UPDATE` |
| D | Delete | `DELETE FROM` |

---

## 2. Anatomia do SQL Injection

Quando o código monta a consulta concatenando diretamente o valor de `$_GET` ou `$_POST` na string SQL, o banco recebe tudo como um único texto e não consegue distinguir o que é **comando** do que é **dado**. O atacante aproveita isso digitando, no campo do formulário ou na URL, trechos que fecham a aspa do valor esperado e acrescentam lógica própria.

Exemplo de código vulnerável:

```php
$sql = "SELECT * FROM usuarios WHERE login = '" . $_POST['login'] . "' AND senha = '" . $_POST['senha'] . "'";
```

Se o atacante digitar `' OR '1'='1` no campo de login, a consulta final vira:

```sql
SELECT * FROM usuarios WHERE login = '' OR '1'='1' AND senha = ''
```

Como `'1'='1'` é sempre verdadeiro, a condição deixa de filtrar e o login é burlado. Com a mesma técnica o atacante pode ler tabelas inteiras (`UNION SELECT`), alterar ou apagar registros e, dependendo das permissões, até comprometer o servidor.

---

## 3. Mecanismo das Prepared Statements

Com prepared statements, a consulta é enviada ao banco em **duas etapas**:

1. **`prepare`**: o banco recebe apenas o *modelo* da consulta, com os marcadores no lugar dos valores. Ele analisa a sintaxe, define a estrutura e monta o plano de execução. Nesse momento a lógica da consulta já está **fechada**.
2. **`execute`**: os valores chegam separadamente e são encaixados nos marcadores como **dados puros**.

Como a estrutura já foi compilada antes de o valor existir, o que o usuário digita nunca é interpretado como SQL. Se alguém digitar `' OR '1'='1`, o banco simplesmente procura um registro cujo valor seja exatamente essa string literal, e não encontra nada. A separação entre código e dados é o que bloqueia a injeção.

---

## 4. Marcadores Nomeados

Marcadores nomeados (`:sku`, `:preco`) trazem vantagens principalmente em consultas complexas:

- **Legibilidade**: o nome indica o que o valor representa, sem precisar contar posições.
- **Menos erros de ordem**: com `?`, trocar a ordem dos parâmetros no array causa bugs silenciosos; com nomes, a ordem não importa.
- **Reuso do mesmo valor**: o mesmo marcador pode ser usado mais de uma vez na consulta (dependendo do driver/configuração), sem repetir o valor.
- **Manutenção mais fácil**: ao adicionar ou remover um campo, não é preciso reordenar todos os parâmetros.

```php
// Posicional: é preciso lembrar a ordem
$stmt = $pdo->prepare("INSERT INTO pecas (sku, descricao, preco) VALUES (?, ?, ?)");

// Nomeado: autoexplicativo
$stmt = $pdo->prepare("INSERT INTO pecas (sku, descricao, preco) VALUES (:sku, :descricao, :preco)");
```

---

## 5. Diferença entre Bindings

A diferença está em **quando** o valor é lido:

- **`bindValue()`** vincula o **valor** no momento da chamada. É uma cópia: se a variável mudar depois, o statement continua com o valor antigo.
- **`bindParam()`** vincula a **referência** da variável. O valor só é lido no momento do `execute()`, então reflete o que a variável contém naquele instante.

```php
$id = 1;
$stmt->bindValue(':id', $id);   // fixa 1
$stmt->bindParam(':id', $id);   // fica "ligado" à variável $id
$id = 2;
$stmt->execute();               // bindValue usa 1; bindParam usa 2
```

O `bindParam()` é útil em laços, quando se executa o mesmo statement várias vezes alterando só a variável. Já o `bindValue()` é mais previsível e aceita valores literais e expressões (o `bindParam()` exige uma variável).

---

## 6. Tipagem no PDO

Por padrão, o PDO trata o valor vinculado como string (`PDO::PARAM_STR`). Em uma cláusula `LIMIT`, que espera um inteiro, omitir `PDO::PARAM_INT` traz riscos:

- Em alguns drivers/configurações (principalmente com *emulated prepares*), o valor é enviado entre aspas (`LIMIT '10'`), o que gera **erro de sintaxe** ou comportamento inconsistente.
- O código passa a depender de conversões implícitas do banco, que variam entre SGBDs e versões, deixando o comportamento imprevisível.
- Se um valor não numérico chegar (por exemplo, vindo da URL), o erro só aparece em tempo de execução, em vez de ser tratado antes.
- Deixa de ficar explícita a intenção do código: "este valor é estritamente um número".

Por isso a boa prática é tipar e, de preferência, converter antes:

```php
$stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
```

---

## 7. Padrão DAO

O **Data Access Object (DAO)** concentra todo o acesso ao banco em uma classe dedicada, separando-o da lógica de negócio e da interface.

Benefícios:

- **Responsabilidade única (SRP, o "S" do SOLID)**: a classe DAO só cuida de persistência (SQL, PDO, binds). Telas e regras de negócio não sabem como os dados são gravados.
- **Manutenibilidade**: se a tabela mudar ou o banco for trocado (por exemplo, de MySQL para PostgreSQL), só o DAO precisa ser alterado.
- **Reuso**: o mesmo método (`listar()`, `inserir()`) é chamado de vários pontos, sem duplicar SQL.
- **Segurança centralizada**: as prepared statements ficam em um só lugar, reduzindo o risco de alguém esquecer de proteger uma consulta.
- **Testabilidade**: fica mais fácil testar ou substituir (mock) a camada de dados.

---

## 8. Operações de Update

Sem `WHERE`, o `UPDATE` é aplicado a **todas as linhas da tabela**.

```sql
UPDATE pecas SET preco = 0;   -- zera o preço de TODAS as peças
```

Em produção isso é gravíssimo porque:

- Corrompe em massa dados reais de clientes, estoque, preços ou financeiro, em um único comando.
- Muitas vezes **não há "desfazer"** simples; só é possível recuperar com backup, o que implica perda de dados recentes e tempo de indisponibilidade.
- Gera prejuízo financeiro, retrabalho, perda de confiança e, dependendo dos dados, problemas legais.
- O mesmo vale para o `DELETE` sem `WHERE`, que apaga a tabela inteira.

Boas práticas: sempre testar o `WHERE` com um `SELECT` antes, usar transações (`BEGIN` / `ROLLBACK` / `COMMIT`), manter backups e restringir permissões de escrita.

---

## 9. Impacto da LGPD

A **Lei nº 13.709/2018 (LGPD)** obriga as organizações a adotar medidas de segurança técnicas e administrativas para proteger dados pessoais (art. 46). Uma falha de SQL Injection que leve ao vazamento de dados de clientes pode ser enquadrada como descumprimento desse dever, gerando consequências como:

**Sanções administrativas (art. 52), aplicadas pela ANPD:**
- Advertência, com prazo para correção;
- Multa simples de até **2% do faturamento** da empresa no Brasil no último exercício, limitada a **R$ 50 milhões por infração**;
- Multa diária;
- Publicização da infração (a empresa torna público o incidente);
- Bloqueio e eliminação dos dados pessoais envolvidos;
- Suspensão parcial do funcionamento do banco de dados e suspensão ou proibição do exercício da atividade de tratamento de dados.

**Outros impactos:**
- **Comunicação obrigatória** do incidente à ANPD e aos titulares afetados (art. 48);
- **Responsabilidade civil**: dever de reparar danos patrimoniais e morais aos titulares (art. 42), com possibilidade de ações individuais e coletivas;
- **Danos à reputação** e perda de confiança de clientes e parceiros;
- Custos de investigação, correção, auditoria e eventual perda de contratos.

Ou seja, uma falha simples de programação, como concatenar `$_POST` em uma consulta, pode custar caro à organização, e é por isso que o uso de prepared statements é uma obrigação prática de quem desenvolve sistemas que tratam dados pessoais.
