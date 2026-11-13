<?php

declare(strict_types=1);

use App\Core\Aplicacao;
use App\Models\Produto;

if (! is_file(__DIR__ . '/vendor/autoload.php')) {
    fwrite(STDERR, "Rode 'composer dump-autoload' antes de executar este exercício.\n");
    exit(1);
}

require __DIR__ . '/vendor/autoload.php';

if (! class_exists('App\\Core\\Simulador')) {
    class_alias('App\\Helpers\\Simulador', 'App\\Core\\Simulador');
}

$simulador = 'App\\Core\\Simulador';

if (method_exists($simulador, 'iniciar')) {
    $simulador::iniciar();
} elseif (method_exists($simulador, 'inicializar')) {
    $simulador::inicializar();
}

$roteador = Aplicacao::criar();

$csrf = null;
foreach (['App\\Helpers\\Csrf', 'App\\Core\\Csrf', 'App\\Security\\Csrf'] as $classeCsrf) {
    if (class_exists($classeCsrf)) {
        $csrf = $classeCsrf;
        break;
    }
}

if ($csrf === null) {
    fwrite(STDERR, "Classe de proteção CSRF não encontrada.\n");
    exit(1);
}

echo "=== Exercício 4: exclusão segura ===\n\n";


$lista = $simulador::requisitar($roteador, 'GET', '/produtos');
$temConfirm = str_contains($lista['corpo'], 'confirm(') ? 'sim' : 'não';
echo "Botão Excluir com confirm() na tabela: {$temConfirm}\n\n";


$id = Produto::criar('Insumo temporário (teste)', 'Usinagem', 1.99, 1);
echo "Produto temporário criado com id {$id}\n\n";


 $simulador::relatar('POST', '/produtos/excluir (sem token)', $simulador::requisitar(
    $roteador, 'POST', '/produtos/excluir', ['id' => (string) $id]
));


$simulador::relatar('POST', '/produtos/excluir (id=abc)', $simulador::requisitar(
    $roteador, 'POST', '/produtos/excluir', ['id' => 'abc', 'csrf_token' => $csrf::token()]
));


$simulador::relatar('POST', '/produtos/excluir (válido)', $simulador::requisitar(
    $roteador, 'POST', '/produtos/excluir', ['id' => (string) $id, 'csrf_token' => $csrf::token()]
));

$sobrou = Produto::buscarPorId($id) === null ? 'removido' : 'AINDA EXISTE';
echo "\nSituação do produto #{$id} no banco: {$sobrou}\n";

$repetiu = Produto::excluir($id) ? 'true' : 'false';
echo "Produto::excluir($id) uma segunda vez devolve: {$repetiu}\n";
