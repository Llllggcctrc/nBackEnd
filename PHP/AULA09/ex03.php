<?php
declare(strict_types=1);

function e(string $texto): string {
    return htmlspecialchars($texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

$busca = $_GET['q'] ?? '';
?>
<!DOCTYPE html>
<html>
<body>
    <h2>Busca de Produtos</h2>
    <form method="GET">
        <!-- Sticky Form blindado -->
        <input type="text" name="q" value="<?= e($busca) ?>" placeholder="Buscar...">
        <button type="submit">Procurar</button>
    </form>

    <?php if ($busca !== ''): ?>
        <p>Você buscou por: <strong><?= e($busca) ?></strong></p>
    <?php endif; ?>
</body>
</html>