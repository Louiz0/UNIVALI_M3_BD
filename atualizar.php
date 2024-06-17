<?php
session_start();
require 'db.php';

// Verifica se o usuário está logado, se não, redireciona para a página de login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];
    $usuario_id = $_SESSION['usuario_id'];

    if ($acao == 'atualizar_nome') {
        $novo_nome = $_POST['nome'];
        
        // Atualiza o nome do usuário no banco de dados
        // SQL: UPDATE usuarios SET nome = :nome WHERE id = :id
        // UPDATE usuarios SET nome = 'valor_do_nome' WHERE id = valor_do_id;

        $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome WHERE id = :id");
        $stmt->execute(['nome' => $novo_nome, 'id' => $usuario_id]);

        // Atualiza o nome do usuário na sessão
        $_SESSION['usuario_nome'] = $novo_nome;
        echo "Nome atualizado com sucesso!";
    } elseif ($acao == 'atualizar_senha') {
        $senha_atual = $_POST['senha_atual'];
        $nova_senha = $_POST['nova_senha'];

        // Seleciona o usuário do banco de dados para verificar a senha atual
        // SQL: SELECT * FROM usuarios WHERE id = :id
        // SELECT * FROM usuarios WHERE id = valor_do_id;

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $usuario_id]);
        $usuario = $stmt->fetch();

        if ($usuario && $senha_atual === $usuario['senha']) { // Comparação direta da senha atual
            
            // Atualiza a senha do usuário no banco de dados
            // SQL: UPDATE usuarios SET senha = :senha WHERE id = :id
            // UPDATE usuarios SET senha = 'valor_da_senha' WHERE id = valor_do_id;
            $stmt = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
            $stmt->execute(['senha' => $nova_senha, 'id' => $usuario_id]);

            echo "Senha atualizada com sucesso!";
            header("Location: login.php");
            exit;
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
