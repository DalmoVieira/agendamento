<?php
// config/database.php

$host = '127.0.0.1';
$port = '3306'; // Porta alterada para 3306 conforme seu ambiente
$db   = 'saude_atendimento';
$user = 'root'; // Usuário padrão do MAMP
$pass = 'root'; // Senha padrão do MAMP (tente em branco se der erro)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
