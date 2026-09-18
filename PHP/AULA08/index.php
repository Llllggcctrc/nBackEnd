<?php
declare(strict_types=1);
//aplicação de página unica utilizando as variáveis superglobais ($_GET, $_POST, $_SERVER) junto com formulários HTML de 

$produtos = [
    ['nome' => 'Teclado USB', 'categoria' => 'Eletrônicos', 'preco' => 80.00],
    ['nome' => 'Mouse sem fio', 'categoria' => 'Eletrônicos', 'preco' => 65.00],
    ['nome' => 'Caderno', 'categoria' => 'Papelaria', 'preco' => 25.00],
    ['nome' => 'Caneta azul', 'categoria' => 'Papelaria', 'preco' => 3.50],
    ['nome' => 'Monitor 24 polegadas', 'categoria' => 'Eletrônicos', 'preco' => 899.90],

];

//declaeação de variaveis 

$mensagemsucesso = ""; // vai servir para apresentar uma mensagem quando um usuario fo cadastrado 
$erros = []; // array para armazenar erros caso necessários e devolver para o usuários os erros de validação
$cadastros = []; // armazena os usuários cadastrados

$nome = ""; // recebera o valor do campo nome para cadastro de usuarios
$email = ""; // recebera o valor do campo email para cadastro de email

// criando o processo de GET => buscar na lista de produtos e retornar uma lista filtrada 
//busca pelo name e atribuio o valor a superglobal ($_GET)
$buscaproduto = trim((string) ($_GET["produto"] ?? ""));
//verificação/operador de nulidade de uma variável (coalescência nula)
$precoMaximoTexto = trim((string) ($_GET["preco_maximo"] ?? "")); // recebe o valor do input preco_maximo
$categoriaSelecionada = trim((string) ($_GET["categoria"] ?? "")); // recebe o valor do select categoria

// lista de categorias únicas, geradas automaticamente a partir dos produtos (usada para montar o <select>)
$categoriasDisponiveis = array_unique(array_column($produtos, 'categoria'));

$produtosfiltrados = $produtos; //copiando a lista de produtos para produtos filtrados

//criar o mecanismo de filtragem para produtos
if ($buscaproduto !== "" || $precoMaximoTexto !== "" || $categoriaSelecionada !== "") { // se algum dos inputs for diferente de vazio
    $produtosfiltrados = array_filter($produtos, function (array $produto) use ($buscaproduto, $precoMaximoTexto, $categoriaSelecionada): bool {
        $nomeStatus = true;
        $precoStatus = true;
        $categoriaStatus = true;

        if ($buscaproduto !== "") {
            $nomeStatus = str_contains(strtolower($produto["nome"]), strtolower($buscaproduto));
        }

        // verificar o preco de um produto e filtra se o produto for menor que o preço máximo determinado
        if ($precoMaximoTexto !== "") {
            $precoMaximo = filter_var($precoMaximoTexto, FILTER_VALIDATE_FLOAT);
            $precoStatus = $precoMaximo !== false && $produto["preco"] <= $precoMaximo;
        }

        // verificar se a categoria do produto bate com a categoria selecionada
        if ($categoriaSelecionada !== "") {
            $categoriaStatus = strtolower($produto["categoria"]) === strtolower($categoriaSelecionada);
        }

        return $nomeStatus && $precoStatus && $categoriaStatus;
    });
}

//PRocessamento do Método POST => permitir o Cadastro FAke de um Cliente -> exibir os dados na tela 

//verificar o Status da SuperGlobal $_SERVER e prosegue se for um POST
if($_SERVER["REQUEST_METHOD"] === "POST") {
    //REcuperar os dados do Formulário
    $nome = trim((string) ($_POST["nome"] ?? "")); // Recebe o valor do nome do input HTML
    $email = trim((string) ($_POST["email"] ?? "")); // Recebe o valor do email do input HTML

    //Validação dde Dados ao Lado do Servidor
    //enviar uma mensagem de erro se a variável nome for menor que 3 caracteres
    if(strlen($nome)<3){
        $erros["nome"] = "Informe um nome com pelo menos 3 caracteres";
    }
    //Validar email do Usuário 
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $erros["email"] = "informe um email Valido!";
    }

    //Se não exisistir erros, o cadastro será realizado
    if($erros === []){
        $mensagemsucesso = "Cadastro realizado com sucesso!";
        $usuario = ["nome" => $nome, "email" => $email];
        array_push($cadastros, $usuario);
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exemplo de GET e POST no PHP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main>
        <h1>Exemplo prático: GET e POST</h1>

        <section>
            <h2>Utilização de filtro pela URL (GET)</h2>

            <form action="index.php" method="GET">
                    <label for="produto">nome do produto</label>
                    <input type="text" name="produto" id="produto" value="<?= htmlspecialchars($buscaproduto) ?>" placeholder="buscar produto">

                    <label for="preco_maximo">preço máximo</label>
                <input type="number" name="preco_maximo" id="preco_maximo" step="0.01" value="<?= htmlspecialchars($precoMaximoTexto) ?>" placeholder="100">

                    <label for="categoria">Categoria</label>
                <select name="categoria" id="categoria">
                    <option value="">Todas</option>
                    <?php foreach ($categoriasDisponiveis as $categoria): ?>
                        <option value="<?= htmlspecialchars($categoria) ?>" <?= $categoriaSelecionada === $categoria ? "selected" : "" ?>>
                            <?= htmlspecialchars($categoria) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                     <button type="submit">Pesquisar</button>
            </form>

            
            <h2>Lista de Produtos Filtrados</h2>
            <p>Observem que os dados da pesquisa aparecem na URL</p>

            <?php if ($produtosfiltrados === []): ?>
                <p class="vazio">Nenhum produto encontrado.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtosfiltrados as $produto): ?>
                            <tr>
                                <td><?= $produto['nome'] ?></td>
                                <td><?= $produto['categoria'] ?></td>
                                <td>
                                    R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <section>
            <h2>Cadastro de Alunos com POST</h2>
            <p>Os dados serão enviados no corpo(body) da requisição e não aparecem na URL</p>

            <?php if($mensagemsucesso !== ""):?>
                <div class="sucesso">
                   <?=  $nome ?><br>
                    <?= $email ?>
                </div>  
                <?php endif; ?>

            <form action="index.php" method="POST">
                <label for="nome">nome</label>
                <input type="text" name="nome" id="nome" placeholder="digite seu nome">
                <?php if(isset($erros["nome"])):?>
                    <div class="erro">
                        <?= $erros["nome"] ?>
                    </div>
                <?php endif; ?>

                <label for="email">Email</label>
                <input type="text" name="email" id="email" placeholder="Digite seu Email">
                <?php if(isset($erros["email"])): ?>
                    <div class="erro">
                        <?= $erros["email"] ?>
                    </div>
                <?php endif; ?>

                <button type="submit">Cadastrar</button>  

            </form>

        </section>
    </main>
    
</body>
</html>