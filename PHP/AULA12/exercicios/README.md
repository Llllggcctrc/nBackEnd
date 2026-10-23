1. Protocolo Stateless
O protocolo HTTP é "stateless" (sem estado) porque não retém memória das requisições anteriores; cada ciclo de requisição-resposta é independente. Cookies e sessões são necessários para criar uma persistência de estado, permitindo que a aplicação reconheça um usuário contínuo (ex: manter um login ativo entre diferentes páginas).

2. Cookies vs. Sessões
Cookies armazenam os dados diretamente no navegador do cliente, enquanto as sessões armazenam os dados no servidor e salvam apenas um identificador (Session ID) no navegador do cliente através de um cookie. Sessões oferecem muito mais segurança para informações confidenciais, já que os dados reais não ficam expostos à manipulação ou inspeção no lado do cliente.

3. A Flag HttpOnly
A flag httponly => true instrui o navegador a proibir o acesso ao cookie através de scripts no lado do cliente (como document.cookie em JavaScript). Se houver uma vulnerabilidade de Cross-Site Scripting (XSS), o invasor poderá injetar scripts, mas o script malicioso falhará ao tentar capturar e exfiltrar o Session ID.

4. A Flag SameSite
O atributo SameSite=Lax instrui o navegador a não enviar o cookie em requisições POST originadas de outros domínios, mas permite o envio em navegações seguras de nível superior (como clicar em um link). Ele atua como uma forte defesa contra ataques de Cross-Site Request Forgery (CSRF).

5. Ataque de Fixação de Sessão
Session Fixation ocorre quando um invasor define previamente um Session ID válido no navegador da vítima e a induz a fazer login. Se o sistema não trocar o ID após o login, o invasor passa a ter acesso à conta. O comando session_regenerate_id(true) gera um ID totalmente novo e descarta o antigo no momento exato em que a autenticação ocorre, neutralizando a ameaça.

6. MD5 e Rainbow Tables
MD5 e SHA256 são algoritmos criados para velocidade. Um hardware moderno consegue testar bilhões de hashes por segundo (ataque de força bruta). Rainbow Tables são tabelas gigantes pré-computadas de senhas comuns e seus hashes resultantes. Sem salt, procurar um hash MD5 nessas tabelas leva frações de segundo.

7. O Papel do Salt
O Salt é uma string aleatória gerada e concatenada à senha antes de aplicar o hash. Ele garante que duas senhas idênticas resultem em hashes completamente diferentes, tornando as Rainbow Tables inúteis, pois o atacante precisaria recalcular a tabela inteira para cada salt único.

8. Argon2id vs Bcrypt
Bcrypt é resistente ao tempo (CPU-bound), mas Argon2id introduz resistência à memória (memory-hard). Ele exige uma quantidade configurável de RAM para ser computado. Isso inviabiliza ataques em larga escala usando placas de vídeo (GPUs) ou circuitos dedicados (ASICs), pois esses hardwares possuem muito poder de processamento, mas pouca memória dedicada por núcleo.

9. Timing Attacks
O operador === compara strings byte a byte e retorna false imediatamente no primeiro erro. Um invasor pode medir o tempo exato (em microssegundos) que o servidor leva para responder e deduzir quais letras estão corretas. A função password_verify() (ou hash_equals()) usa uma comparação de tempo constante (constant-time), demorando o mesmo tempo independentemente de quantos caracteres coincidem.