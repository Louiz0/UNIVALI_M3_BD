<?php
session_start();
require 'db.php';

// Verifica se o usuário está logado, caso contrário, redireciona para a página de login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];
    $usuario_id = $_SESSION['usuario_id'];

    if ($acao == 'criar') {
        $nome = $_POST['nome'];
        // Insere uma nova categoria no banco de dados
        // SQL: INSERT INTO categorias (nome, usuario_id) VALUES ('nome', 'usuario_id');
        // INSERT INTO categorias (nome, usuario_id) VALUES ('valor_do_nome', valor_do_usuario_id);
        $stmt = $pdo->prepare("INSERT INTO categorias (nome, usuario_id) VALUES (:nome, :usuario_id)");
        $stmt->execute(['nome' => $nome, 'usuario_id' => $usuario_id]);
        echo "Categoria criada com sucesso!";
    } elseif ($acao == 'excluir') {
        $categoria_id = $_POST['categoria_id'];

        // Verificar se há tarefas associadas à categoria
        // SQL: SELECT COUNT(*) FROM tarefas WHERE categoria_id = 'categoria_id' AND usuario_id = 'usuario_id';
        // SELECT COUNT(*) FROM tarefas WHERE categoria_id = valor_da_categoria_id AND usuario_id = valor_do_usuario_id;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tarefas WHERE categoria_id = :categoria_id AND usuario_id = :usuario_id");
        $stmt->execute(['categoria_id' => $categoria_id, 'usuario_id' => $usuario_id]);
        $tarefasCount = $stmt->fetchColumn();

        if ($tarefasCount > 0) {
            echo "Não é possível excluir a categoria, pois existem tarefas associadas a ela.";
        } else {
            // Exclui a categoria do banco de dados
            // SQL: DELETE FROM categorias WHERE id = 'id' AND usuario_id = 'usuario_id';
            // DELETE FROM categorias WHERE id = valor_do_id AND usuario_id = valor_do_usuario_id;
            $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = :id AND usuario_id = :usuario_id");
            $stmt->execute(['id' => $categoria_id, 'usuario_id' => $usuario_id]);
            echo "Categoria excluída com sucesso!";
        }
    } elseif ($acao == 'editar') {
        $categoria_id = $_POST['categoria_id'];
        $nome = $_POST['nome'];
        // Atualiza o nome da categoria no banco de dados
        // SQL: UPDATE categorias SET nome = 'nome' WHERE id = 'id' AND usuario_id = 'usuario_id';
        // UPDATE categorias SET nome = 'valor_do_nome' WHERE id = valor_do_id AND usuario_id = valor_do_usuario_id;
        $stmt = $pdo->prepare("UPDATE categorias SET nome = :nome WHERE id = :id AND usuario_id = :usuario_id");
        $stmt->execute(['nome' => $nome, 'id' => $categoria_id, 'usuario_id' => $usuario_id]);
        echo "Categoria editada com sucesso!";
    }
}

// Seleciona todas as categorias do usuário no banco de dados
// SQL: SELECT * FROM categorias WHERE usuario_id = 'usuario_id';
// SELECT * FROM categorias WHERE usuario_id = valor_do_usuario_id;
$stmt = $pdo->prepare("SELECT * FROM categorias WHERE usuario_id = :usuario_id");
$stmt->execute(['usuario_id' => $_SESSION['usuario_id']]);
$categorias = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Categorias</title>
</head>
<body>
    <h2>Gerenciar Categorias</h2>
    <a href="tarefas.php"><button>Voltar para Tarefas</button></a>

    <!-- Formulário de cadastro de categoria -->
    <h2>Cadastrar Categoria</h2>
    <form method="POST">
        <input type="hidden" name="acao" value="criar">
        <input type="text" name="nome" placeholder="Nome da Categoria" required><br>
        <button type="submit">Cadastrar Categoria</button>
    </form>

    <!-- Listagem de categorias -->
    <h2>Minhas Categorias</h2>
    <?php
    foreach ($categorias as $categoria) {
        echo "<p><strong>{$categoria['nome']}</strong></p>";
        echo "<form method='POST' style='display:inline;'>
                <input type='hidden' name='acao' value='excluir'>
                <input type='hidden' name='categoria_id' value='{$categoria['id']}'>
                <button type='submit'>Excluir</button>
              </form>";
        echo "<form method='POST' style='display:inline;'>
                <input type='hidden' name='acao' value='editar'>
                <input type='hidden' name='categoria_id' value='{$categoria['id']}'>
                <input type='text' name='nome' value='{$categoria['nome']}' required>
                <button type='submit'>Editar</button>
              </form>";
    }
    ?>
</body>
</html>
