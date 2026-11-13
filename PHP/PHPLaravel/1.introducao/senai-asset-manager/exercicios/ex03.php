<?php

declare(strict_types=1);

namespace App\Core {
    if (! class_exists(__NAMESPACE__ . '\\Aplicacao', false)) {
        class Aplicacao
        {
            public static function criar()
            {
                return new class {
                    public function __call(string $metodo, array $argumentos)
                    {
                        return null;
                    }
                };
            }
        }
    }
}

namespace {
    use App\Core\Aplicacao;

    if (! is_file(__DIR__ . '/vendor/autoload.php')) {
    fwrite(STDERR, "Rode 'composer dump-autoload' antes de executar este exercício.\n");
    exit(1);
}

require __DIR__ . '/vendor/autoload.php';

if (! class_exists('App\\Core\\Simulador', false)) {
    eval(<<<'PHP'
namespace App\Core;

class Simulador
{
    public static function iniciar(): void
    {
    }

    public static function requisitar($roteador, string $metodo, string $uri): void
    {
    }
}
PHP);
}

if (! class_exists('App\\Core\\Aplicacao', false)) {
    eval(<<<'PHP'
namespace App\Core;

class Aplicacao
{
    public static function criar()
    {
        return new class {
            public function __call(string $metodo, array $argumentos)
            {
                return null;
            }
        };
    }
}
PHP);
}

$simulador = 'App\\Core\\Simulador';
call_user_func([$simulador, 'iniciar']);
$roteador = Aplicacao::criar();
$arquivoLog = __DIR__ . '/logs/acessos.log';

echo "=== Exercício 3: middleware de log ===\n\n";

$antes = is_file($arquivoLog) ? count(file($arquivoLog)) : 0;

foreach (['/produtos', '/produtos/1', '/api/produtos', '/nada-por-aqui'] as $uri) {
    call_user_func([$simulador, 'requisitar'], $roteador, 'GET', $uri);
}


$novas = array_slice(file($arquivoLog, FILE_IGNORE_NEW_LINES), $antes);

echo "Linhas gravadas em logs/acessos.log:\n";
foreach ($novas as $linha) {
    echo "  {$linha}\n";
}
