<?php
declare(strict_types=1);

require_once 'Ex00_Base.php';

class ContaSalario extends ContaBancaria {
    private int $saquesNoMes = 0;

    public function __construct(
        Titular $titular,
        public readonly string $cnpjEmpregador
    ) {
        parent::__construct($titular);
    }

    public function depositarSalario(float $valor, string $cnpjOrigem): void {
        if ($cnpjOrigem !== $this->cnpjEmpregador) {
            throw new RegraNegocioException("Apenas o empregador pode depositar nesta conta.");
        }
        parent::depositar($valor);
    }

    public function sacar(float $valor): void {
        $taxa = $this->saquesNoMes >= 2 ? 4.00 : 0.00;
        $valorTotal = $valor + $taxa;

        if ($valorTotal > $this->saldo) {
            $this->registrarTransacao('Saque', $valorTotal, false);
            throw new SaldoInsuficienteException($valorTotal, $this->saldo);
        }

        $this->saldo -= $valorTotal;
        $this->saquesNoMes++;
        $this->registrarTransacao('Saque', $valorTotal, true);
        $this->notificar("Saque de R$ {$valor} realizado.");
    }

    public function transferir(float $valor, ContaBancaria $destino): void {
        if ($destino->titular->cpf !== $this->titular->cpf) {
            throw new RegraNegocioException("Transferência permitida apenas para mesma titularidade.");
        }
        $this->sacar($valor);
        $destino->depositar($valor);
    }
}