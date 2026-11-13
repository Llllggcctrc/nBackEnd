<?php

declare(strict_types=1);


use App\Core\Aplicacao;
use App\Core\Simulador;

if (! is_file(__DIR__ . '/vendor/autoload.php')) {
    fwrite(STDERR, "Rode 'composer dump-autoload' antes de executar este exercício.\n");
    exit(1);
}

require __DIR__ . '/vendor/autoload.php';

Simulador::iniciar();
$roteador = Aplicacao::criar();

echo "=== Exercício 1: rotas com parâmetro dinâmico ===\n\n";

$uris = [
    '/produtos/1',         
    '/produtos/detalhe/2',  
    '/produtos/99999',      
    '/produtos/abc',        
    '/rota/que/nao/existe', 
];

foreach ($uris as $uri) {
    Simulador::relatar('GET', $uri, Simulador::requisitar($roteador, 'GET', $uri));
}
