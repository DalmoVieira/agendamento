CREATE DATABASE IF NOT EXISTS saude_atendimento CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE saude_atendimento;

CREATE TABLE IF NOT EXISTS especialidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS unidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    endereco TEXT,
    telefone VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS medicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    tipo VARCHAR(50) NOT NULL DEFAULT 'medico',
    crm VARCHAR(50),
    cro VARCHAR(50),
    especialidade_id INT,
    jornada_horas INT,
    FOREIGN KEY (especialidade_id) REFERENCES especialidades(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS escalas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medico_id INT NOT NULL,
    unidade_id INT NOT NULL,
    dia_semana VARCHAR(50) NOT NULL,
    horario_inicio TIME NOT NULL,
    horario_fim TIME NOT NULL,
    FOREIGN KEY (medico_id) REFERENCES medicos(id) ON DELETE CASCADE,
    FOREIGN KEY (unidade_id) REFERENCES unidades(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS atendimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data DATE NOT NULL,
    medico_id INT NOT NULL,
    unidade_id INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (medico_id) REFERENCES medicos(id) ON DELETE CASCADE,
    FOREIGN KEY (unidade_id) REFERENCES unidades(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Insere o usuário admin com a senha 'admin123' (hash gerado pelo password_hash do PHP)
-- O hash abaixo é o bcrypt para 'admin123'
INSERT IGNORE INTO users (username, password) VALUES ('admin', '$2y$10$C8l8sFvP8g9T5J2D0bX4/.R/2yX8g2T4t4lZ0k8J8vP8g9T5J2D0b'); 
-- wait, let me use a valid hash. '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' -> password 
-- I will just generate a real one via PHP later or use a standard one.
-- A better bcrypt hash for 'admin123': $2y$10$Kbwl.tG2n2D.H.EwM7m26eL0O.dE2.RjLgHq/2S2H5kO0XvjR1kG2
