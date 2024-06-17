CREATE SCHEMA to_do;
use to_do;

-- Criação da tabela usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(32) NOT NULL
);

-- Criação da tabela categorias
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    usuario_id INT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Criação da tabela tarefas
CREATE TABLE tarefas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    categoria_id INT,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT,
    status VARCHAR(20) NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- EXEMPLOS DE USO EM SQL PURO
-- Criar usuario
INSERT INTO usuarios (nome, email, senha) VALUES ('Nome do Usuário', 'univali@univali.br', '123456');

-- Editar o usuario
UPDATE usuarios SET nome = 'Novo Nome', senha = 'nova@123456' WHERE id = 1;

-- Excluir usuario
DELETE FROM usuarios WHERE id = 1;

-- Criar uma tarefa
INSERT INTO tarefas (usuario_id, titulo, descricao, status, categoria_id) VALUES (1, 'Tarefa de teste', 'Descricao tarefa', 'pendente', 1);

-- Editar a tarefa
UPDATE tarefas SET titulo = 'Nova tarefa teste', descricao = 'Nova descrição teste', status = 'concluida' WHERE id = 1;

-- Apagar tarefa
DELETE FROM tarefas WHERE usuario_id = 1;

-- Criar categoria
INSERT INTO categorias (nome, usuario_id) VALUES ('Teste catetegoria', 1);

-- Editar categoria
UPDATE categorias SET nome = 'Nova categoria teste' WHERE id = 1;

-- Excluir categoria
DELETE FROM categorias WHERE usuario_id = 1;

-- Lista de usuarios
SELECT * FROM usuarios;

-- Listar tarefas
SELECT tarefas.*, categorias.nome AS categoria_nome FROM tarefas
JOIN categorias ON tarefas.categoria_id = categorias.id
WHERE tarefas.usuario_id = 1;

-- Listar categorias
SELECT * FROM categorias WHERE usuario_id = 1;









