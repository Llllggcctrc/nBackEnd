<?php
declare(strict_types=1);

require_once 'Ex00_Base.php';

class RelatorioFinanceiro {
    
    // Método para imprimir relatório formatado em HTML (Navegador)
    public static function exibirExtrato(ContaBancaria $conta): void {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<thead><tr><th colspan='4'>EXTRATO BANCÁRIO</th></tr>";
        echo "<tr><th>Data/Hora</th><th>Tipo</th><th>Valor</th><th>Status</th></tr></thead>";
        echo "<tbody>";

        foreach ($conta->getExtrato() as $t) {
            $data = $t->dataHora->format('d/m/Y H:i');
            $status = $t->sucesso ? 'Aprovada' : 'Recusada';
            $valorFmt = "R$ " . number_format($t->valor, 2, ',', '.');
            $corStatus = $t->sucesso ? 'green' : 'red';
            
            echo "<tr>";
            echo "<td>{$data}</td>";
            echo "<td>{$t->tipo}</td>";
            echo "<td>{$valorFmt}</td>";
            echo "<td style='color: {$corStatus}'>{$status}</td>";
            echo "</tr>";
        }
        
        echo "</tbody>";
        echo "<tfoot><tr><th colspan='3'>SALDO CONSOLIDADO:</th>";
        echo "<th>R$ " . number_format($conta->getSaldo(), 2, ',', '.') . "</th></tr></tfoot>";
        echo "</table>";
    }
}