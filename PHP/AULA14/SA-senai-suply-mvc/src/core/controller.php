<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

abstract class Controller {
    //classe abstrata com elementos de segurança e renderização de layout

    //método para renderizar layout ijetando dados padrão
     protected function renderizar(string $view, array $dados = []): void {
        extract($dados, EXTR_SKIP);
        $arquivoView = dirname(__DIR__) . "/Views/{$view}.php";
        if (!file_exists($arquivoView)) {
            throw new RuntimeException("A view nao foi localizada: {$arquivoView}");
        }
        require_once dirname(__DIR__) . "/Views/layout/header.php";
        require_once $arquivoView;
        require_once dirname(__DIR__) . "/Views/layout/footer.php";
    }
}