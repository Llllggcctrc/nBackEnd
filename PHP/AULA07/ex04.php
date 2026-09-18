<?php
declare(strict_types=1);

$filmes = [
    ["titulo" => "Matrix", "genero" => "Ficção", "classificacao_idade" => 16],
    ["titulo" => "Shrek", "genero" => "Animação", "classificacao_idade" => 0],
    ["titulo" => "Deadpool", "genero" => "Ação", "classificacao_idade" => 18],
    ["titulo" => "Procurando Nemo", "genero" => "Animação", "classificacao_idade" => 0],
    ["titulo" => "Vingadores", "genero" => "Ação", "classificacao_idade" => 12]
];

function filmeParaCrianca($filme) {
    if ($filme["classificacao_idade"] <= 12) {
        return true;
    } else {
        return false;
    }
}

$filmesInfantis = array_filter($filmes, "filmeParaCrianca");

echo "<h3>Filmes liberados para crianças:</h3>";
foreach ($filmesInfantis as $filme) {
    echo $filme["titulo"] . " (Livre para " . $filme["classificacao_idade"] . " anos)";
    echo "<br>";
}