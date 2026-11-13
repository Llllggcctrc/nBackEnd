

### 1. Evolução arquitetural: o que o MVC artesanal da Semana 12 me ensinou?

Construir o MVC "na unha" me mostrou o que o framework esconde. Aprendi que:

- existe um **ponto único de entrada** (front controller) que recebe tods as requisições;
- um **roteador** associa URL + verbo HTTP a um método de controller;
- o **autoload PSR-4** carrega as classes sem `require` manual;
- separar **Model, View e Controller** evita misturar SQL com HTML;
- um **container simples** ajuda a montar as dependências.

Quando abri o Laravel, tudo isso já estava lá, só mais polido. Foi como reencontrar
velhos conhecidos com roupa nova.

### 2. O que mudou na inicialização do Laravel 11?

O esqueleto ficou bem mais enxuto. Sumiram `Http/Kernel.php`, `Console/Kernel.php`,
`Exceptions/andler.php`, a pasta de middlewares e a maioria dos Service Providers
(como o `RouteServiceProvider`). O **novo ponto central é o `bootstrap/app.php`**,
onde, com `Application::configure()`, definimos rotas (`withRouting`),
middlewares (`withMiddleware`) e tratamento de exceções (`withExceptions`).
Providers fica em `bootstrap/providers.php`, comandos em `routes/console.php`,
e arquivos como `api.php` só aparecem quando instalados (`php artisan install:api`).

### 3. Por que não gravar senhas em `config/database.php`?

Porque o arquivo de config vai para o Git. Quem tiver acesso ao repositório (incusive
histórico antigo) teria acesso ao banco. O princípio do **12-Factor App** manda separar
configuração do código: segredos ficam em variáveis de ambiente (`.env`), diferentes
em cada ambiente e for do versionamento. O `config/database.php` apenas **lê** o valor
com `env('DB_PASSWORD')`.

### 4. Qual o risco de trocar a `APP_KEY` em produção?

A `APP_KEY` é usada para criptografar e assinar dados. Trocá-la de repente:

- **derruba todas as sessões** e invalida cookies criptografados (todo mundo é deslogado);
- torna **ilegíveis** os dados gravados com `Crypt` ou casts `encrypted` (erro `DecryptException`);
- invalida **links assinados** e tokens como "lembrar-me";
- as senhas (hash bcrypt) *não* são aetadas.

Se a rotação for necessária, o Laravel 11 permite manter a chave antiga em `APP_PREVIOUS_KEYS`.

### 5. Por que apontar o servidor web só para `public/`?

Fora de `public/` estão o `.env`, o cóigo-fonte, as configurações e o `vendor/`.
Se a raiz fosse exposta, alguém poderia baixar `https://site.com/.env` e ver todas as
credenciais. Apontando para `public/`, só o `index.php` e os assets estáticos ficam
acessíveis; todo o resto fica fora do alcance da web.

### 6. `php arquivo.php` x `php artisan`

Com `php arquivo.php`, o scrpt roda **isolado**: não conhece o framework, o container,
o `.env` nem o banco, e eu teria de carregar tudo manualmente. Com `php artisan`, o
framework é **inicializado por completo** (config, autoload, providers, conexões) e eu
ganho comandos prontos (`make:*`, `migrate`, `route:list`, `tinker`) além de pder criar
os meus, com argumentos, validação e saída colorida.

### 7. Por que manter o `.env.example` atualizado?

O `.env` não vai para o Git, então o `.env.example` é o **manual de configuração** do
projeto: mostra quais variáveis existem (sem valores secretos). Quando alguém adiciona
uma variável nova e esquece e documentar, o colega clona, roda e leva erro sem
entender o motivo. Atualizado, ele permite onboarding em um `cp .env.example .env`.

### 8. `{{ $variavel }}` x `{!! $variavel !!}` e o XSS

- `{{ $variavel }}` **escapa** o conteúdo (equivale a `htmlspecialchars`): um
  `<script>` vira texto inofenivo. É a proteção padrão contra **XSS**.
- `{!! $variavel !!}` imprime o HTML **cru**, sem escape. Se o dado veio do usuário,
  um script malicoso será executado no navegador de outras pessoas.

Regra de ouro: use sepre `{{ }}` e reserve `{!! !!}` para HTML que eu mesmo grei e sei que é seguro.

### 9. Por que controllers curtos (≤ 20 linhas)?

O controller deve apenas **receber a requisição, delegar e devolver a resposta**.
Quando ele acumula SQL cru e regras de negócio, fica difícil de ler, de testa e
impossível de reaproveitar (a mesma regra seria copiada em outras telas e comandos).
Mantê-lo curto forç a levar regras para Services/Models e consultaspara Eloquent,
respeitando a **responsabilidade única** e fcilitando a manutnção.

### 10. Para que serve o Tinker?

O `php artisan tinker` abre um **console interativo (REPL)** com a aplicação inteira
carregada. Ele eonomiza tempo qundo eu qero:

- conferir uma configuração (`config('app.timezone')`);
- criar e testar modes e relacionamenos sem montar rota ou tela;
- consultar ou inserir dados rapidamente no banco e desenvolvimento;
- testar uma ideia de código em egundos antes de colocá-la no projet