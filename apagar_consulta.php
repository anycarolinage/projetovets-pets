<?php
// Inclui o arquivo de configuração do banco de dados
require_once 'config.php';

// Verifica se o ID foi passado na URL e se é um número válido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Prepara a consulta SQL para excluir a consulta
    $sql = "DELETE FROM consultas WHERE id = :id";

    if ($stmt = $pdo->prepare($sql)) {
        // Vincula o parâmetro ID
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        // Executa a consulta
        if ($stmt->execute()) {
            // Redireciona de volta para o dashboard do administrador após a exclusão
            header("location: admin_dashboard.php");
            exit;
        } else {
            echo "Erro ao tentar apagar a consulta. Por favor, tente novamente.";
        }
    }

    // Libera o recurso do statement
    unset($stmt);
} else {
    // Se o ID não for válido, redireciona para o dashboard
    header("location: admin_dashboard.php");
    exit;
}

// Fecha a conexão com o banco de dados
unset($pdo);
?>