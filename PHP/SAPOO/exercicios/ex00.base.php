<?php
declare(strict_types=1);

// Exceções de Domínio
class ValorInvalidoException extends \DomainException {
    public function __construct(string $message) {
        parent::__construct($message);
    }
}
class SaldoInsuficienteException extends \DomainException {}
class RegraNegocioException extends \DomainException {}

// Entidades Base
class Titular {
    public function __construct(
        public readonly string $nome, 
        public readonly string $cpf
    ) {}
}

class Transacao {
    public function __construct(
        public readonly string $tipo,
        public readonly float $valor,
        public readonly \DateTimeImmutable $dataHora,
        public readonly bool $sucesso
    ) {}
}

interface NotificadorInterface {
    public function notificar(Titular $titular, string $mensagem): bool;
}

// Classe Abstrata (Regras centralizadas)
abstract class ContaBancaria {
    protected float $saldo = 0.0;
    protected array $notificadores = [];
    protected array $transacoes = [];

    public function __construct(public readonly Titular $titular) {}

    abstract public function sacar(float $valor): void;

    public function depositar(float $valor): void {
        if ($valor <= 0) {
            $this->registrarTransacao('Depósito', $valor, false);
            throw new ValorInvalidoException("O valor do depósito deve ser maior que zero.");
        }
        
        $this->saldo += $valor;
        $this->registrarTransacao('Depósito', $valor, true);
        $this->notificar("Depósito de R$ {$valor} realizado com sucesso.");
    }

    public function getSaldo(): float {
        return $this->saldo;
    }

    public function adicionarNotificador(NotificadorInterface $notificador): void {
        $this->notificadores[] = $notificador;
    }

    protected function notificar(string $mensagem): void {
        foreach ($this->notificadores as $notificador) {
            $notificador->notificar($this->titular, $mensagem);
        }
    }

    protected function registrarTransacao(string $tipo, float $valor, bool $sucesso): void {
        $this->transacoes[] = new Transacao($tipo, $valor, new \DateTimeImmutable(), $sucesso);
    }
    
    public function getExtrato(): array {
        return $this->transacoes;
    }
}