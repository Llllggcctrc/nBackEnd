<?php
declare(strict_types=1);

require_once 'Ex00_Base.php';

class NotificadorEmail implements NotificadorInterface {
    public function notificar(Titular $titular, string $mensagem): bool {
        // Disparo de E-mail
        echo "[E-mail para {$titular->nome}]: {$mensagem}\n";
        return true;
    }
}

class NotificadorSMS implements NotificadorInterface {
    public function notificar(Titular $titular, string $mensagem): bool {
        // Disparo de SMS
        echo "[SMS para {$titular->nome}]: {$mensagem}\n";
        return true;
    }
}

class NotificadorWhatsApp implements NotificadorInterface {
    public function notificar(Titular $titular, string $mensagem): bool {
        // Disparo de WhatsApp
        echo "[WhatsApp para {$titular->nome}]: {$mensagem}\n";
        return true;
    }
}