<?php
declare(strict_types=1);

function e(string $texto): string {
    return htmlspecialchars($texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function validarLinkPortfolio(string $url): ?string {
    $url = trim($url);
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return null;
    }
    if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
        return null; // Bloqueia javascript:, data:, etc.
    }
    return $url;
}

$linkValido = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $linkValido = validarLinkPortfolio($_POST['link'] ?? '');
}
?>
<!DOCTYPE html>
<html>
<body>
    <h2>Cadastro de Portfólio</h2>
    <form method="POST">
        <input type="text" name="link" placeholder="Link do GitHub/LinkedIn" required>
        <button type="submit">Validar Link</button>
    </form>
    
    <?php if ($linkValido): ?>
        <p>Sucesso: <a href="<?= e($linkValido) ?>" target="_blank">Visitar Portfólio</a></p>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <p style="color:red;">Link inválido ou perigoso!</p>
    <?php endif; ?>
</body>
</html>