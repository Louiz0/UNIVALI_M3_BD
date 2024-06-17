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

    if ($acao == 'criar') {
        $titulo = $_POST['titulo'];
        $descricao = $_POST['descricao'];
        $status = $_POST['status'];
        $categoria_id = $_POST['categoria_id'];
        $usuario_id = $_SESSION['usuario_id'];

        // Inserir uma nova tarefa no banco de dados
        // SQL: INSERT INTO tarefas (usuario_id, titulo, descricao, status, categoria_id, data_criacao) VALUES (:usuario_id, :titulo, :descricao, :status, :categoria_id, NOW())
        // INSERT INTO tarefas (usuario_id, titulo, descricao, status, categoria_id, data_criacao)
        // VALUES (valor_do_usuario_id, 'valor_do_titulo', 'valor_da_descricao', 'valor_do_status', valor_da_categoria_id, NOW());

        $stmt = $pdo->prepare("INSERT INTO tarefas (usuario_id, titulo, descricao, status, categoria_id, data_criacao) VALUES (:usuario_id, :titulo, :descricao, :status, :categoria_id, NOW())");
        $stmt->execute(['usuario_id' => $usuario_id, 'titulo' => $titulo, 'descricao' => $descricao, 'status' => $status, 'categoria_id' => $categoria_id]);

        echo "Tarefa cadastrada com sucesso!";
    } elseif ($acao == 'excluir') {
        $tarefa_id = $_POST['tarefa_id'];

        // Excluir uma tarefa específica do banco de dados
        // SQL: DELETE FROM tarefas WHERE id = :id AND usuario_id = :usuario_id
        // DELETE FROM tarefas WHERE id = valor_do_id AND usuario_id = valor_do_usuario_id;
        $stmt = $pdo->prepare("DELETE FROM tarefas WHERE id = :id AND usuario_id = :usuario_id");
        $stmt->execute(['id' => $tarefa_id, 'usuario_id' => $_SESSION['usuario_id']]);

        echo "Tarefa excluída com sucesso!";
    } elseif ($acao == 'editar') {
        $tarefa_id = $_POST['tarefa_id'];
        $titulo = $_POST['titulo'];
        $descricao = $_POST['descricao'];
        $status = $_POST['status'];
        $categoria_id = $_POST['categoria_id'];

        // Atualizar uma tarefa específica no banco de dados
        // SQL: UPDATE tarefas SET titulo = :titulo, descricao = :descricao, status = :status, categoria_id = :categoria_id WHERE id = :id AND usuario_id = :usuario_id
        // UPDATE tarefas SET titulo = 'valor_do_titulo', descricao = 'valor_da_descricao', status = 'valor_do_status', categoria_id = valor_da_categoria_id 
        // WHERE id = valor_do_id AND usuario_id = valor_do_usuario_id
        $stmt = $pdo->prepare("UPDATE tarefas SET titulo = :titulo, descricao = :descricao, status = :status, categoria_id = :categoria_id WHERE id = :id AND usuario_id = :usuario_id");
        $stmt->execute(['titulo' => $titulo, 'descricao' => $descricao, 'status' => $status, 'categoria_id' => $categoria_id, 'id' => $tarefa_id, 'usuario_id' => $_SESSION['usuario_id']]);

        echo "Tarefa editada com sucesso!";
    } elseif ($acao == 'alterar_status') {
        $tarefa_id = $_POST['tarefa_id'];
        $status = $_POST['status'];

        // Atualizar o status de uma tarefa específica no banco de dados
        // SQL: UPDATE tarefas SET status = :status WHERE id = :id AND usuario_id = :usuario_id
        // UPDATE tarefas SET status = 'valor_do_status' WHERE id = valor_do_id AND usuario_id = valor_do_usuario_id
        $stmt = $pdo->prepare("UPDATE tarefas SET status = :status WHERE id = :id AND usuario_id = :usuario_id");
        $stmt->execute(['status' => $status, 'id' => $tarefa_id, 'usuario_id' => $_SESSION['usuario_id']]);

        echo "Status da tarefa alterado com sucesso!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Tarefas</title>
</head>
<body>
    <h2>Menu Principal</h2>
    <a href="login.php"><button>Retornar ao menu de login</button></a>
    
    <a href="categorias.php"><button>Criar categoria</button></a>

    <a href="login.php"><button>Sair</button></a>

    <!-- Formulário de cadastro de tarefas -->
    <h2>Cadastro de Tarefa</h2>
    <form method="POST">
        <input type="hidden" name="acao" value="criar">
        <input type="text" name="titulo" placeholder="Título" required><br>
        <textarea name="descricao" placeholder="Descrição"></textarea><br>
        <select name="status">
            <option value="pendente">Pendente</option>
            <option value="concluida">Concluída</option>
        </select><br>
        <!-- Campo de seleção de categoria -->
        <select name="categoria_id" required>
            <option value="">Selecione uma Categoria</option>
            <?php
            // Seleciona todas as categorias do usuário no banco de dados
            // SQL: SELECT * FROM categorias WHERE usuario_id = :usuario_id
            // SELECT * FROM categorias WHERE usuario_id = valor_do_usuario_id;
            $stmt = $pdo->prepare("SELECT * FROM categorias WHERE usuario_id = :usuario_id");
            $stmt->execute(['usuario_id' => $_SESSION['usuario_id']]);
            $categorias = $stmt->fetchAll();
            foreach ($categorias as $categoria) {
                echo "<option value='{$categoria['id']}'>{$categoria['nome']}</option>";
            }
            ?>
        </select><br>
        <button type="submit">Cadastrar Tarefa</button>
    </form>

    <!-- Listagem de tarefas do usuário -->
    <h2>Minhas Tarefas</h2>
    <?php
    // Seleciona todas as tarefas do usuário, juntando com as categorias
    // SQL: SELECT tarefas.*, categorias.nome AS categoria_nome FROM tarefas LEFT JOIN categorias ON tarefas.categoria_id = categorias.id WHERE tarefas.usuario_id = :usuario_id
    // SELECT tarefas.*, categorias.nome AS categoria_nome FROM tarefas JOIN categorias ON tarefas.categoria_id = categorias.id
    // WHERE tarefas.usuario_id = valor_do_usuario_id;

    $stmt = $pdo->prepare("SELECT tarefas.*, categorias.nome AS categoria_nome FROM tarefas LEFT JOIN categorias ON tarefas.categoria_id = categorias.id WHERE tarefas.usuario_id = :usuario_id");
    $stmt->execute(['usuario_id' => $_SESSION['usuario_id']]);
    $tarefas = $stmt->fetchAll();

    foreach ($tarefas as $tarefa) {
        echo "<p><strong>{$tarefa['titulo']}</strong> - {$tarefa['descricao']} - Status: {$tarefa['status']} - Categoria: {$tarefa['categoria_nome']} - Criado em: {$tarefa['data_criacao']}</p>";
        echo "<form method='POST' style='display:inline;'>
                <input type='hidden' name='acao' value='excluir'>
                <input type='hidden' name='tarefa_id' value='{$tarefa['id']}'>
                <button type='submit'>Excluir</button>
              </form>";
        echo "<form method='POST' style='display:inline;'>
                <input type='hidden' name='acao' value='editar'>
                <input type='hidden' name='tarefa_id' value='{$tarefa['id']}'>
                <input type='text' name='titulo' value='{$tarefa['titulo']}' required>
                <textarea name='descricao'>{$tarefa['descricao']}</textarea>
                <select name='status'>
                    <option value='pendente' " . ($tarefa['status'] == 'pendente' ? 'selected' : '') . ">Pendente</option>
                    <option value='concluida' " . ($tarefa['status'] == 'concluida' ? 'selected' : '') . ">Concluída</option>
                </select>
                <select name='categoria_id' required>
                    <option value=''>Selecione uma Categoria</option>";
        foreach ($categorias as $categoria) {
            echo "<option value='{$categoria['id']}' " . ($tarefa['categoria_id'] == $categoria['id'] ? 'selected' : '') . ">{$categoria['nome']}</option>";
        }
        echo "</select>
                <button type='submit'>Editar</button>
              </form>";
    }
    ?>
</body>
</html>
