<?php
declare(strict_types=1);

$carrinho = [
    ["produto" => "Notebook", "preco" => 4000.00],
    ["produto" => "Mouse", "preco" => 150.00],
    ["produto" => "Teclado", "preco" => 300.00]
];

function aplicarDesconto(array $item): array {
    $item["preco"] = $item["preco"] * 0.80;
    return $item;
}

$carrinhoBlackFriday = array_map("aplicarDesconto", $carrinho);

echo "<h3>Preços da Black Friday:</h3>";
foreach ($carrinhoBlackFriday as $item) {
    echo $item["produto"] . ": R$ " . number_format($item["preco"], 2, ',', '.');
    echo "<br>";
}