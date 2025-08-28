<?php
session_start();

// Verifica se a variável de sessão de sucesso existe
if (!isset($_SESSION['agendamento_sucesso']) || $_SESSION['agendamento_sucesso'] !== true) {
    // Se não houver, redireciona o usuário de volta para a página de agendamento
    header("location: agendar_consulta.php");
    exit;
}

// Limpa a variável de sessão para que a mensagem não apareça novamente em um refresh
unset($_SESSION['agendamento_sucesso']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Agendada</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .confirmation-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            background-color: #f0f2f5;
        }
        .confirmation-box {
            background-color: #ffffff;
            padding: 40px 60px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }
        .confirmation-box h1 {
            color: #28a745; /* Cor verde para sucesso */
            font-size: 2.5em;
            margin-bottom: 20px;
        }
        .confirmation-box p {
            font-size: 1.2em;
            color: #555;
            margin-bottom: 30px;
        }
        .btn-return {
            background-color: #007bff;
            color: #fff;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn-return:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="confirmation-box">
            <h1>Consulta Agendada com Sucesso!</h1>
            <p>Sua consulta foi agendada. Agradecemos a preferência.</p>
            <a href="agendar_consulta.php" class="btn-return">Voltar para Agendar</a>
        </div>
    </div>
</body>
</html>