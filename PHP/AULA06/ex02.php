<?php

declare(strict_types=1);

function classificarIMC(float $imc): string
{
    if ($imc < 18.5) {
        return 'Abaixo do peso';
    } elseif ($imc < 24.9) {
        return 'Peso normal';
    } elseif ($imc < 29.9) {
        return 'Sobrepeso';
    } elseif ($imc < 34.9) {
        return 'Obesidade grau I';
    } elseif ($imc < 39.9) {
        return 'Obesidade grau II';
    } else {
        return 'Obesidade grau III';
    }
}

echo "imc1: " 



?>