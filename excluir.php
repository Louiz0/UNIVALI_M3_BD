<?php
session_start();
require 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
    if ($_POST['confirmar'] === 'sim') {
        $usuario_id = $_SESSION['usuario_id'];

        // Excluir tarefas do usuário
        $stmt = $pdo->prepare("DELETE FROM tarefas WHERE usuario_id = :usuario_id");
        $stmt->execute(['usuario_id' => $usuario_id]);

        // Excluir usuário
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $usuario_id]);

        // Destruir sessão e redirecionar
        session_destroy();
        header("Location: login.php");
        exit;
    } else {
        header("Location: tarefas.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Conta</title>
</head>
<body>
    <h2>Excluir Conta</h2>
    <p>Tem certeza de que deseja excluir sua conta? Esta ação não pode ser desfeita.</p>
    <form method="POST">
        <button type="submit" name="confirmar" value="sim">Sim, excluir minha conta</button>
        <button type="submit" name="confirmar" value="nao">Não, manter minha conta</button>
    </form>
</body>
</html>
