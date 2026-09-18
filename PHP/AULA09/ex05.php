<?php
declare(strict_types=1);

const ARQUIVO_CHAT = 'chat.json';

function e(string $texto): string {
    return htmlspecialchars($texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function salvarMensagem(string $msg): void {
    if (strlen($msg) > 0 && strlen($msg) <= 250) {
        $mensagens = file_exists(ARQUIVO_CHAT) ? json_decode(file_get_contents(ARQUIVO_CHAT), true) : [];
        $mensagens[] = ['texto' => $msg, 'hora' => date('H:i:s')];
        file_put_contents(ARQUIVO_CHAT, json_encode($mensagens));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    salvarMensagem(trim($_POST['mensagem'] ?? ''));
}

$chatLog = file_exists(ARQUIVO_CHAT) ? json_decode(file_get_contents(ARQUIVO_CHAT), true) : [];
?>
<!DOCTYPE html>
<html>
<body>
    <h2>Chat Industrial</h2>
    <div style="border:1px solid #000; height:300px; overflow-y:scroll; padding:10px; margin-bottom:10px;">
        <?php foreach ($chatLog as $linha): ?>
            <p>
                <small>[<?= e($linha['hora']) ?>]</small><br>
                <!-- 
                DESAFIO - Explicação da Ordem de Execução:
                Fazer e(nl2br($mensagem)) é um erro porque nl2br() insere tags HTML reais (<br />).
                Se executarmos e() DEPOIS, ele transformará a tag <br /> em entidades &lt;br /&gt;
                e a quebra de linha aparecerá como texto impresso na tela do usuário em vez
                de quebrar a linha visualmente. 
                A ordem correta é: higienizar os caracteres do usuário primeiro com e(),
                e em seguida inserir as quebras de linha com nl2br().
                -->
                <?= nl2br(e($linha['texto'])) ?>
            </p>
        <?php endforeach; ?>
    </div>
    <form method="POST">
        <textarea name="mensagem" maxlength="250" placeholder="Mensagem (Máx 250 chars)"></textarea><br>
        <button type="submit">Enviar</button>
    </form>
</body>
</html>