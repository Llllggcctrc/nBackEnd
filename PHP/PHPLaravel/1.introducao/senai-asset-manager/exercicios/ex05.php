<?php

declare(strict_types=1);

use App\Core\Aplicacao;
use App\Core\Simulador;
use App\Models\Produto;

if (! is_file(__DIR__ . '/vendor/autoload.php')) {
    fwrite(STDERR, "Rode 'composer dump-autoload' antes de executar este exercício.\n");
    exit(1);
}

require __DIR__ . '/vendor/autoload.php';

Simulador::iniciar();
$roteador = Aplicacao::criar();

echo "=== Exercício 5: filtro por categoria ===\n\n";

$uris = [
    '/produtos',
    '/produtos?categoria=Usinagem',
    '/produtos?categoria=Estamparia',
    '/produtos?categoria=Inexistente',
   
    '/produtos?categoria=' . rawurlencode("' OR '1'='1"),
];

foreach ($uris as $uri) {
    Simulador::relatar('GET', $uri, Simulador::requisitar($roteador, 'GET', $uri));
}

$total = count(Produto::buscarPorCategoria('Montagem Robótica'));
echo "\nProduto::buscarPorCategoria('Montagem Robótica') devolveu {$total} item(ns)\n";
