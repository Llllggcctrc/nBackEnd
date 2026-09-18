<?php
declare(strict_types=1);

$usuario = [
    "nome" => "Carlos Eduardo",
    "idade" => 28,
    "cidade" => "Americana",
    "estado" => "SP",
    "premium" => true
];

$localizacao = $usuario["cidade"] . " - " . $usuario["estado"];

if ($usuario["premium"] == true) {
    $nomeExibido = $usuario["nome"] . " ⭐";
} else {
    $nomeExibido = $usuario["nome"];
}
?>

<div style="border: 1px solid #ccc; padding: 15px; width: 250px;">
    <h2><?php echo $nomeExibido; ?></h2>
    <p>Idade: <?php echo $usuario["idade"]; ?> anos</p>
    <p>Local: <?php echo $localizacao; ?></p>
</div>