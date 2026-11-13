<?php

declare(strict_types=1);

use App\Core\Aplicacao;
use App\Util\Simulador;

if (! is_file(__DIR__ . '/vendor/autoload.php')) {
    fwrite(STDERR, "Rode 'composer dump-autoload' antes de executar este exercício.\n");
    exit(1);
}

require __DIR__ . '/vendor/autoload.php';

if (! class_exists('App\\Util\\Simulador') || ! class_exists('App\\Core\\Aplicacao')) {
    fwrite(STDERR, "As classes App\\Util\\Simulador e App\\Core\\Aplicacao devem estar disponíveis antes de executar este exercício.\n");
    exit(1);
}

Simulador::iniciar();
$roteador = Aplicacao::criar();

echo "=== Exercício 2: API JSON ===\n\n";

foreach (['/api/produtos', '/api/produtos?categoria=Usinagem'] as $uri) {
    $resposta = Simulador::requisitar($roteador, 'GET', $uri);
    Simulador::relatar('GET', $uri, $resposta);

  
    $json = json_decode($resposta['corpo'], true, 512, JSON_THROW_ON_ERROR);
    echo "      JSON válido! total = {$json['total']}\n\n";
}
