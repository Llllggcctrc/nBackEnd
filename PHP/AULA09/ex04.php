<?php
declare(strict_types=1);

function e(string $texto): string {
    return htmlspecialchars($texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function sanitizarTexto(string $dado): string {
    return strip_tags(trim($dado));
}

function validarColaborador(array $dados): array {
    $erros = [];
    if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $erros[] = "E-mail inválido.";
    }
    if (!filter_var($dados['matricula'], FILTER_VALIDATE_INT)) {
        $erros[] = "Matrícula deve ser um número inteiro.";
    }
    if (!filter_var($dados['salario'], FILTER_VALIDATE_FLOAT)) {
        $erros[] = "Salário deve ser um número decimal.";
    }
    return $erros;
}

// Inicialização prévia das variáveis para evitar erros de escopo
$feedback = [];
$dados = [
    'nome'      => '',
    'email'     => '',
    'matricula' => '',
    'salario'   => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'nome'      => sanitizarTexto($_POST['nome'] ?? ''),
        'email'     => trim($_POST['email'] ?? ''),
        'matricula' => trim($_POST['matricula'] ?? ''),
        'salario'   => trim($_POST['salario'] ?? '')
    ];
    
    $feedback = validarColaborador($dados);
    if (empty($feedback) && empty($dados['nome'])) {
        $feedback[] = "Nome é obrigatório.";
    }
}
?>
<!DOCTYPE html>
<html>
<body>
    <h2>Cadastro de Colaborador</h2>
    <form method="POST">
        <input type="text" name="nome" placeholder="Nome"><br>
        <input type="text" name="email" placeholder="E-mail"><br>
        <input type="text" name="matricula" placeholder="Matrícula (Ex: 123)"><br>
        <input type="text" name="salario" placeholder="Salário (Ex: 3500.50)"><br>
        <button type="submit">Cadastrar</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <?php if (!empty($feedback)): ?>
            <ul style="color:red;">
                <?php foreach ($feedback as $erro): ?>
                    <li><?= e($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p style="color:green;">Colaborador <?= e($dados['nome']) ?> salvo com sucesso!</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>