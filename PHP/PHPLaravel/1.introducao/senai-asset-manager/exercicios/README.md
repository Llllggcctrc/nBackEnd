### **1. O Colapso do Código Espaguete**
Misturar SQL, regras de negócio/validação e HTML no mesmo arquivo viola o princípio da Separação de Preocupações (SoC) e gera forte acoplamento:

Manutenção inviável: Alterar uma regra de imposto ou o nome de uma coluna no banco exige vasculhar dezenas de arquivos misturados com layout, aumentando o risco de quebrar a interface ao mexer no banco (e vice-versa).

Impossibilidade de reuso: Se a empresa precisar criar um aplicativo mobile ou uma API REST, toda a lógica de validação e acesso ao banco precisará ser reescrita, pois está presa à renderização HTML.

Escalabilidade de equipe: Designers/front-ends e desenvolvedores back-end não conseguem trabalhar paralelamente nos mesmos arquivos sem gerar constantes conflitos de merge.


### **2. A Tríade MVC**
Model (`Modelo`): Representa os dados, o estado da aplicação e a comunicação com a camada de persistência (banco de dados).

View (`Visão`): Responsável exclusivamente pela camada de apresentação (HTML, CSS, templates), exibindo os dados recebidos sem processar lógica complexa.

Controller (`Controlador`): Atua como intermediário. Recebe a requisição HTTP, valida/extrai os dados de entrada, aciona o Model adequado e decide qual View será renderizada (ou qual resposta HTTP será enviada).

Onde ficam as regras de negócio? Devem residir na camada de Model (ou em classes de Serviço/Domínio dentro dessa camada), mantendo os Controllers enxutos (Skinny Controllers, Fat Models).

### 3. O Papel do Composer
composer.json: É o manifesto declarativo do projeto. Define metadados, regras de autoload (como PSR-4) e as dependências desejadas com faixas de versão flexíveis (ex.: ^2.0).

composer.lock: É o retrato exato (snapshot) do ambiente. Registra as versões exatas, hashes e subdependências que foram efetivamente instaladas. Garante que todos os desenvolvedores e o servidor de produção rodem exatamente o mesmo código ao executar composer install.

Por que ignorar vendor/ no Git? A pasta contém milhares de arquivos de terceiros gerenciáveis automaticamente. Versioná-la infla o tamanho do repositório, torna os commits e clones lentos e polui o histórico de alterações (diffs) com código que não pertence ao projeto.


### 4. A Especificação PSR-4
A PSR-4 estabelece um mapeamento direto entre namespaces e a estrutura de diretórios do sistema de arquivos:

No composer.json, define-se o prefixo base: "App\\": "src/".

Quando a classe App\Controllers\ProdutoController é instanciada, o autoloader substitui o prefixo App\ pelo diretório base src/.

Os sub-namespaces subsequentes (Controllers\) são convertidos em subdiretórios (Controllers/), substituindo a barra invertida (\) pelo separador de diretório do sistema operacional (/).

O nome da classe (ProdutoController) recebe a extensão .php, resultando no caminho exato: src/Controllers/ProdutoController.php.

### 5. Front Controller
A principal vantagem de segurança é o isolamento da superfície de ataque:

Ao configurar o servidor web (Apache/Nginx) para apontar o Document Root exclusivamente para a pasta public/, apenas o index.php e os assets públicos (CSS, JS, imagens) ficam acessíveis via URL.

Arquivos sensíveis — como o .env (senhas de banco e chaves de API), composer.json, código-fonte em src/ e logs — ficam fora da raiz pública, impedindo que um atacante os baixe ou execute diretamente pelo navegador, mesmo em caso de falha de configuração no servidor.

### 6. Roteador HTTP
O Roteador cruza duas informações distintas da superglobal $_SERVER a cada requisição:

O caminho (URI): Extraído de $_SERVER['REQUEST_URI'] (geralmente limpo com parse_url(..., PHP_URL_PATH) para ignorar query strings).

O verbo HTTP: Extraído de $_SERVER['REQUEST_METHOD'].

Assim, mesmo que a URI seja idêntica (/produtos), o roteador consulta tabelas/arrays separados por método HTTP: direciona GET para a listagem (ex.: ProdutoController@index) e POST para a criação de um novo registro (ex.: ProdutoController@store).

### 7. Injeção de Dados na View
O método renderizar($view, $dados = []) recebe um array associativo onde as chaves são os nomes desejados para as variáveis e os valores são os dados vindos do Controller (ex.: ['produtos' => $lista, 'titulo' => 'Catálogo']).

Ao executar extract($dados) dentro do escopo do método antes do require do arquivo da View, o PHP converte cada chave do array em uma variável local na tabela de símbolos atual ($produtos e $titulo).

Como o arquivo .php da View é incluído logo em seguida dentro desse mesmo escopo, ele passa a ter acesso direto a essas variáveis de forma limpa.

### 8. Segurança contra XSS nas Views
O ataque de Cross-Site Scripting (XSS) ocorre quando um usuário mal-intencionado insere código JavaScript em um campo de formulário (ex.: <script>document.location='...'</script>), que é salvo no banco e posteriormente exibido na tela de outros usuários.

A função htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') converte caracteres especiais do HTML (<, >, ", ', &) em suas respectivas entidades HTML seguras (&lt;, &gt;, &quot;, etc.).

Dessa forma, o navegador renderiza o conteúdo apenas como texto plano inofensivo, impedindo que o navegador interprete e execute o script malicioso.

### 9. Single Responsibility no Controller
Pelo Princípio da Responsabilidade Única (SRP), o papel do Controller é apenas orquestrar o fluxo HTTP (receber entrada e devolver resposta), não saber como os dados são armazenados:

Conexões diretas (new PDO()): Criam alto acoplamento com um banco específico, dificultam testes unitários (sem mocking) e geram duplicação de credenciais e conexões abertas desnecessariamente.

SQL com strings concatenadas: Além de violar a separação de camadas (misturando sintaxe de banco com fluxo HTTP), abre brechas críticas de SQL Injection. Toda manipulação de banco deve ser delegada aos Models/Repositories utilizando Prepared Statements.

### 10. Transição para o Framework Laravel
Quase toda a fundação de um MVC artesanal bem estruturado é a base arquitetural do Laravel 11:

Entry Point (public/index.php): O Laravel utiliza exatamente o padrão Front Controller com a pasta public/ isolada do restante do projeto (app/, routes/, config/).

Autoloading PSR-4 e Composer: A pasta app/ do Laravel é mapeada via PSR-4 para o namespace App\ no composer.json.

Roteamento baseado em Verbo + URI: O arquivo routes/web.php funciona sob a mesma lógica (Route::get('/produtos', ...) vs Route::post('/produtos', ...)).

Separação MVC: Os Controllers ficam em app/Http/Controllers, os Models em app/Models e as Views em resources/views.

Injeção de Dados e Proteção XSS nas Views: O helper view('produtos', ['produtos' => $lista]) realiza a mesma injeção de variáveis na View, enquanto a sintaxe {{ $variavel }} do motor Blade executa htmlspecialchars() automaticamente por baixo dos panos.