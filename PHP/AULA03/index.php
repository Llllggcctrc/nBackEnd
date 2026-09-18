<?php
// 1.blindagens de operaçoes entres variaveis de tipo diferentes
declare(strict_types=1);

// Criar um cálculo de Holerite em PHP

// 2. Declaração da Constantes

const TAXA_INSS = 0.08;
const DESCONTO_TV = 150.00;

//.3 declarar as variavei//dados do funcionario
$nomeFuncionario = "joao silva";
$salariosBase = 3200.00;
$horasExtras = 10;

//Declara~ção de variaveis usando o lowercamelcase
//primira palavra toda minuscula e depois as demais palavras usa-se maiúsculas na primeira letra 
//exemplo: $hojeEstaUmDiaBonito

//4. cáuculos do salario 
// valor da hora Extra (1.6 da hora normal)
$valorHoraExtra = ($salariosBase/220) * 1.6;
// -> crie uma variavel $totalHoraExtras
 
$totalHoraExtra = $valorHoraExtra * $horasExtras;
// -> Crie uma variável $salarioBruto
$salarioBruto = $salariosBase + $totalHoraExtra;
// -> Criar a variável $descontoInss

$descontoInss = $salarioBruto * TAXA_INSS;
// -> Criar a variável $salarioLiquido
$salarioLiquido = $salarioBruto - $descontoInss - DESCONTO_TV; 
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>holerite <?php echo $nomeFuncionario; ?></title>
    <!-- folha de estilização CCS -->
     <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Demostrativo de pagamento</h2>
    <!-- Saida De Dados misturado HTML e PHP em uma Tabela -->
     <table>
        <tr>
            <th>Colaborador(a)</th>
            <td><?php echo $nomeFuncionario; ?></td>
        </tr>
        <tr>
            <th>Salário Base</th>
            <td><?php echo "R$ " . number_format($salariosBase,2, ",", "."); ?></td>
            <!-- fazer as demais linhas da tabela utilizando as variáveis criadas-->
        </tr>
        <tr>
            <th>Horas Extras</th>
            <td><?php echo $horasExtras; ?> horas</td>
        </tr>
        <tr>
            <th>Valor Hora Extra</th>
            <td>R$ <?php echo number_format($valorHoraExtra, 2, ",", "."); ?></td>
        </tr>
        <tr>
            <th>Total Horas Extras</th>
            <td>R$ <?php echo number_format($totalHoraExtra, 2, ",", "."); ?></td>
        </tr>
        <tr>
            <th>Salário Bruto</th>
            <td>R$ <?php echo number_format($salarioBruto, 2, ",", "."); ?></td>
        </tr>
        <tr>
            <th>Desconto INSS (8%)</th>
            <td>R$ <?php echo TAXA_INSS; ?></td>
        </tr>
        <tr>
            <th>Desconto VT</th>
            <td>R$ <?php echo number_format(DESCONTO_TV, 2, ",", "."); ?></td>
        </tr>
        <tr>
            <th>Salário Líquido</th>
            <td>R$ <?php echo number_format($salarioLiquido, 2, ",", "."); ?></td>
        </tr>
        </td></td>
        </tr>
     </table> 
      
    
</body>
</html>
     
