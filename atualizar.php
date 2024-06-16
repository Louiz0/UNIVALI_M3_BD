<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];
    $usuario_id = $_SESSION['usuario_id'];

    if ($acao == 'atualizar_nome') {
        $novo_nome = $_POST['nome'];
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome WHERE id = :id");
        $stmt->execute(['nome' => $novo_nome, 'id' => $usuario_id]);

        $_SESSION['usuario_nome'] = $novo_nome;
        echo "Nome atualizado com sucesso!";
    } elseif ($acao == 'atualizar_senha') {
        $senha_atual = $_POST['senha_atual'];
        $nova_senha = $_POST['nova_senha'];

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $usuario_id]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha_atual, $usuario['senha'])) {
            $nova_senha_hash = password_hash($nova_senha, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
            $stmt->execute(['senha' => $nova_senha_hash, 'id' => $usuario_id]);

            echo "Senha atualizada com sucesso!";
            header("Location: login.php");
        } else {
            echo "Senha atual incorreta.";
        }
    }
}
?>

<!-- Formulário de atualização de perfil (perfil.php) -->
<h2>Atualizar Perfil</h2>
<form method="POST">
    <input type="hidden" name="acao" value="atualizar_nome">
    <input type="text" name="nome" placeholder="Novo Nome" required><br>
    <button type="submit">Atualizar Nome</button>
</form>

<form method="POST">
    <input type="hidden" name="acao" value="atualizar_senha">
    <input type="password" name="senha_atual" placeholder="Senha Atual" required><br>
    <input type="password" name="nova_senha" placeholder="Nova Senha" required><br>
    <button type="submit">Atualizar Senha</button>
</form>

<a href="excluir.php"><button>Excluir usuário</button></a>