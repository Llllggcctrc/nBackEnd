<?php
declare(strict_types=1);


function calcularIMC(float $peso, float $altura): float {
    return $peso / ($altura * $altura);
}

$teste1 = calcularIMC(70, 1.75);
$teste2 = calcularIMC(85, 1.80);
$teste3 = calcularIMC(67, 1.67);

echo "IMC 1: " . number_format($teste1, 2) . "\n";
echo "IMC 2: " . number_format($teste2, 2) . "\n";
echo "IMC 3: " . number_format($teste3, 2) . "\n";


?>