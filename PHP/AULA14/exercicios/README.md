## Exercicios Toricos 


### 1. O Colapso do Código Espaguete

Misturar SQL, regras de validação em PHP e marcação HTML no mesmo arquivo cria um alto acoplamento. Qualquer mudança visual no layout pode quebrar a lógica do banco de dados e vice-versa. Isso inviabiliza o trabalho em equipe (front-end e back-end não conseguem atuar simultaneamente no mesmo arquivo), impossibilita a reutilização de código (forçando a duplicação de regras) e impede a criação de testes automatizados, transformando a manutenção em um pesadelo à medida que o sistema cresce.

### 2. A Tríade MVC e Regras de Negócio

Model (Modelo): Responsável por acessar o banco de dados, aplicar transações e realizar as validações de dados. É aqui que devem ficar as regras de negócio.

View (Visão): Cuida exclusivamente da interface e apresentação. Recebe os dados prontos e gera o HTML para o usuário final, sem realizar lógicas complexas.

Controller (Controlador): Atua como o maestro do fluxo. Recebe a requisição HTTP do usuário, aciona o Model para processar os dados e entrega o resultado para a View adequada.

3. O Papel do Composer (json vs. lock) e a pasta vendor/
O composer.json é o manifesto do seu projeto: ele lista quais bibliotecas são necessárias e quais versões são aceitáveis (ex: >= 1.0). O composer.lock é o retrato exato da instalação: ele grava as versões específicas que foram baixadas (ex: 1.0.4), garantindo que a sua equipe e o servidor de produção rodem exatamente o mesmo código. A pasta vendor/ contém todo o código-fonte pesado dessas bibliotecas; ela nunca deve ser versionada para não deixar o repositório lento, gigante e cheio de conflitos. Qualquer desenvolvedor pode recriar a pasta executando composer install.

4. A Especificação PSR-4
A PSR-4 mapeia um prefixo de namespace (como App\) para um diretório base (como src/). Quando você instancia a classe App\Controllers\ProdutoController, o autoloader do Composer substitui o prefixo pelo diretório mapeado e converte o restante do caminho em pastas, procurando automaticamente o arquivo ProdutoController.php dentro da pasta src/Controllers/ sem precisar de require_once.

5. Vantagem de Segurança do Front Controller
Ao apontar o servidor web (Apache/Nginx) exclusivamente para a pasta public/, onde fica o index.php, você isola fisicamente o código-fonte. Arquivos vitais como configurações de banco, logs e classes em src/ ficam fora da raiz pública, impossibilitando que um usuário acesse um arquivo sensível digitando o caminho na URL. Todo acesso é obrigado a passar pelo filtro de segurança e roteamento do index.php.

6. Roteador HTTP e Diferenciação de Métodos
O Roteador analisa variáveis globais do servidor, especificamente $_SERVER['REQUEST_METHOD'] (que informa se a ação é GET, POST, PUT, DELETE) e $_SERVER['REQUEST_URI'] (que informa a rota acessada). Cruzando a rota solicitada com o método HTTP, ele sabe que GET /produtos deve disparar a função de listar o catálogo, enquanto POST /produtos deve disparar a função de cadastrar um novo item no banco.

7. Injeção de Dados na View com extract()
A função extract() pega um array associativo (por exemplo, ['titulo' => 'Estoque', 'saldo' => 50]) e transforma as chaves em variáveis reais dentro do escopo da View. Dessa forma, em vez de imprimir o valor acessando um array desajeitado como $dados['titulo'], o desenvolvedor da interface pode simplesmente utilizar $titulo e $saldo direto no HTML.

8. Segurança contra XSS nas Views
O ataque de Cross-Site Scripting (XSS) ocorre quando um usuário mal-intencionado insere scripts de JavaScript em campos de formulário e o sistema os exibe para outros usuários. A função htmlspecialchars() previne isso convertendo caracteres de marcação HTML (como < e >) em entidades inofensivas (como &lt; e &gt;). Assim, o navegador apenas imprime o texto na tela, em vez de executar o código malicioso.

9. Single Responsibility no Controller
Pelo Princípio da Responsabilidade Única (SRP), o Controller deve apenas orquestrar requisições e respostas HTTP. Se ele possuir queries SQL ou conexões PDO diretas, ele se torna acoplado à infraestrutura de banco de dados. Isso impede que outras partes do sistema reaproveitem aquela consulta, dificulta a manutenção caso o esquema do banco mude e aumenta os riscos de vulnerabilidades, furando completamente a organização da arquitetura MVC.

10. Transição para o Framework Laravel
Os pilares deste MVC artesanal formam exatamente a base do Laravel. Lá, você também encontrará: Arquitetura Model-View-Controller; o Composer gerenciando pacotes e fazendo o Autoload PSR-4 (App\Http\Controllers); um Front Controller em public/index.php; um arquivo dedicado para roteamento definindo verbos GET/POST; Middlewares atuando de forma idêntica aos seus Guards de RBAC; proteção obrigatória de tokens CSRF; e métodos de banco de dados que envelopam transações ACID.