<?php
declare(strict_types=1);

$extrato = [
    ["data" => "2026-09-01", "descricao" => "Salário", "tipo" => "Entrada", "valor" => 4000.00],
    ["data" => "2026-09-02", "descricao" => "Supermercado", "tipo" => "Saida", "valor" => 450.50],
    ["data" => "2026-09-05", "descricao" => "Pix João", "tipo" => "Entrada", "valor" => 200.00],
    ["data" => "2026-09-10", "descricao" => "Conta de Luz", "tipo" => "Saida", "valor" => 120.00],
    ["data" => "2026-09-12", "descricao" => "Cinema", "tipo" => "Saida", "valor" => 65.00]
];

$totalEntradas = 0;
$totalSaidas = 0;

foreach ($extrato as $transacao) {
    if ($transacao["tipo"] == "Entrada") {
        $totalEntradas = $totalEntradas + $transacao["valor"];
    } else {
        $totalSaidas = $totalSaidas + $transacao["valor"];
    }
}

$saldoAtual = $totalEntradas - $totalSaidas;

if ($saldoAtual >= 0) {
    $corSaldo = "green";
} else {
    $corSaldo = "red";
}
?>

<div style="display: flex; gap: 15px; margin-bottom: 20px;">
    <div style="border: 1px solid #ccc; padding: 15px;">
        <h4>Entradas</h4>
        <p style="color: green;">R$ <?php echo number_format($totalEntradas, 2, ',', '.'); ?></p>
    </div>
    <div style="border: 1px solid #ccc; padding: 15px;">
        <h4>Saídas</h4>
        <p style="color: red;">R$ <?php echo number_format($totalSaidas, 2, ',', '.'); ?></p>
    </div>
    <div style="border: 1px solid #ccc; padding: 15px;">
        <h4>Saldo Atual</h4>
        <p style="color: <?php echo $corSaldo; ?>;">R$ <?php echo number_format($saldoAtual, 2, ',', '.'); ?></p>
    </div>
</div>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Data</th>
        <th>Descrição</th>
        <th>Tipo</th>
        <th>Valor</th>
    </tr>

    <?php foreach ($extrato as $transacao) { ?>
        <tr>
            <td><?php echo $transacao["data"]; ?></td>
            <td><?php echo $transacao["descricao"]; ?></td>
            <td><?php echo $transacao["tipo"]; ?></td>
            <td>R$ <?php echo number_format($transacao["valor"], 2, ',', '.'); ?></td>
        </tr>
    <?php } ?>
</table>

<?php

function gastoAlto($transacao) {
    if ($transacao["tipo"] == "Saida" && $transacao["valor"] > 100.00) {
        return true;
    } else {
        return false;
    }
}

$gastosAltos = array_filter($extrato, "gastoAlto");
?>

<h3>Atenção: Gastos Altos do Mês</h3>
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Data</th>
        <th>Descrição</th>
        <th>Valor</th>
    </tr>

    <?php foreach ($gastosAltos as $gasto) { ?>
        <tr>
            <td><?php echo $gasto["data"]; ?></td>
            <td><?php echo $gasto["descricao"]; ?></td>
            <td>R$ <?php echo number_format($gasto["valor"], 2, ',', '.'); ?></td>
        </tr>
    <?php } ?>
</table>