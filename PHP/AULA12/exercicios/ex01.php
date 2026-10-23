<?php
declare(strict_types=1);

/**
 * Valida a complexidade da senha do usuário.
 */
function validarSenha(string $senha): bool
{
    $padrao = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
    return (bool) preg_match($padrao, $senha);
}

/**
 * Mock para persistência de dados.
 */
class UsuarioDAO
{
    public function __construct()
    {
    }

    public function salvar(string $email, string $hash): void
    {
        // Aqui iria o PDO/PostgreSQL
        echo "Usuário {$email} salvo com sucesso com o hash: {$hash}\n";
    }
}

// Execução CLI
echo "--- Cadastro de Colaborador ---\n";
$email = readline("Digite o e-mail: ");
$senha = readline("Digite a senha (min 8 chars, 1 Maiuscula, 1 Minuscula, 1 Numero, 1 Especial): ");

if (!validarSenha($senha)) {
    echo "Erro: A senha não atende aos requisitos de complexidade.\n";
    exit(1);
}

$hashSeguro = password_hash($senha, PASSWORD_ARGON2ID);
$dao = new UsuarioDAO();
$dao->salvar($email, $hashSeguro);