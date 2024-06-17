<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Preparar uma instrução SQL para selecionar todos os dados da tabela 'usuarios' onde o email corresponde ao fornecido.
    // SQL: SELECT * FROM usuarios WHERE email = :email
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch(); // Executa a consulta e busca os resultados.

    // Verificar se o usuário existe e se a senha fornecida corresponde à senha armazenada no banco de dados.
    if ($usuario && $senha === $usuario['senha']) {
        // Se a senha for válida, armazena informações do usuário na sessão.
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email']; // Armazena o email na sessão
        header("Location: tarefas.php");
        exit;
    } else {
        echo "Email ou senha incorretos.";
    }
}
?>
<!-- Formulário de login (login.php) -->
<form method="POST">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="senha" placeholder="Senha" required><br>
    <button type="submit">Entrar</button>
</form>
<a href="register.php"><button>Registrar</button></a>
<a href="atualizar.php"><button>Atualizar Dados</button></a>
