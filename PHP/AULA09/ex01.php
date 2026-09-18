<?php
declare(strict_types=1);
session_start();

function e(string $texto): string {
    return htmlspecialchars($texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function processarMensagem(array $post, array &$mural): void {
    $nome = trim($post['nome'] ?? '');
    $msg = trim($post['mensagem'] ?? '');

    if (strlen($nome) >= 3 && strlen($msg) >= 5) {
        $mural[] = ['nome' => $nome, 'mensagem' => $msg];
    } else {
        echo "<p style='color:red;'>Erro: Nome (min 3) e Mensagem (min 5).</p>";
    }
}

if (!isset($_SESSION['mural'])) {
    $_SESSION['mural'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    processarMensagem($_POST, $_SESSION['mural']);
}
?>
<!DOCTYPE html>
<html>
<body>
    <h2>Mural Blindado</h2>
    <form method="POST">
        <input type="text" name="nome" placeholder="Seu Nome" required><br><br>
        <textarea name="mensagem" placeholder="Sua Mensagem" required></textarea><br><br>
        <button type="submit">Enviar</button>
    </form>
    <hr>
    <?php foreach ($_SESSION['mural'] as $recado): ?>
        <div style="border:1px solid #ccc; margin-bottom:10px; padding:10px;">
            <strong><?= e($recado['nome']) ?>:</strong><br>
            <?= nl2br(e($recado['mensagem'])) ?>
        </div>
    <?php endforeach; ?>
</body>
</html>