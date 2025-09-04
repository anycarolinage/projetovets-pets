<?php
// Arquivo: config.php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Usuário padrão do XAMPP
define('DB_PASSWORD', '');     // Senha padrão do XAMPP (vazia)
define('DB_NAME', 'agenda_consultas_db'); // Nome correto do banco de dados

/* Tentativa de conexão com o banco de dados MySQL */
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro: Não foi possível conectar ao banco de dados. " . $e->getMessage());
}
?>