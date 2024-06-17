<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha']; // Senha sem criptografia

    // Preparar uma instrução SQL para inserir um novo usuário na tabela 'usuarios' com os valores fornecidos.
    // SQL: INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)
    // INSERT INTO usuarios (nome, email, senha) VALUES ('valor_do_nome', 'valor_do_email', 'valor_da_senha');

    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)");
    $stmt->execute(['nome' => $nome, 'email' => $email, 'senha' => $senha]);

    echo "Usuário cadastrado com sucesso!";
    header("Location: login.php"); // Redireciona para o login após o cadastro
    exit;
}
?>
<!-- Formulário de cadastro de usuário (register.php) -->
<form method="POST">
    <input type="text" name="nome" placeholder="Nome" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="senha" placeholder="Senha" required><br>
    <button type="submit">Cadastrar</button>
</form>
