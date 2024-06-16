<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)");
    $stmt->execute(['nome' => $nome, 'email' => $email, 'senha' => $senha]);

    echo "Usuário cadastrado com sucesso!";
    header("Location: login.php");
}
?>

<!-- Formulário de cadastro de usuário (register.php) -->
<form method="POST">
    <input type="text" name="nome" placeholder="Nome" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="senha" placeholder="Senha" required><br>
    <button type="submit">Cadastrar</button>
</form>
