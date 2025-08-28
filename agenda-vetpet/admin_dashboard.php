<?php
// Arquivo: admin_dashboard.php
// URGENTE: Certifique-se de que NADA (nem mesmo um espaço ou linha em branco) esteja ANTES desta tag de abertura <?php.
ob_start(); // Inicia o buffer de saída para evitar o erro "headers already sent"
session_start();

// Adicione esta linha para inicializar a variável e evitar o 'Notice'
$data_consulta_err = "";

// Verifica se o usuário está logado e se é um admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["tipo_usuario"] !== 'admin') {
    header("location: login.php");
    exit;
}

require_once 'config.php';

$consultas = [];
$error_message = "";

try {
    // Busca todas as consultas, juntando com informações do usuário que agendou
    $sql = "SELECT c.*, u.nome as nome_usuario, u.email as email_usuario
            FROM consultas c
            JOIN usuarios u ON c.usuario_id = u.id
            ORDER BY c.data_consulta ASC, c.hora_consulta ASC";
    $stmt = $pdo->query($sql);
    $consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Em um ambiente de produção, você registraria o erro (error_log($e->getMessage());)
    // e exibiria uma mensagem genérica ao usuário.
    $error_message = "Erro ao carregar consultas: ". $e->getMessage();
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Administrador - Agenda de Consultas</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1, h2 { text-align: center; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .btn { padding: 8px 15px; border-radius: 5px; text-decoration: none; color: white; background-color: #dc3545; }
        .btn-danger { background-color: #dc3545; }
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 5px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #dc3545; }
        .consultas-list { margin-top: 20px; }
        .consulta-item { background-color: #f9f9f9; border: 1px solid #eee; border-radius: 5px; padding: 15px; margin-bottom: 10px; }
        .consulta-item p { margin: 5px 0; }
        .consulta-item strong { color: #555; }
        .no-consultas { text-align: center; color: #777; }
        .status-pending { color: orange; font-weight: bold; }
        .status-confirmed { color: green; font-weight: bold; }
        .status-cancelled { color: red; font-weight: bold; }
        .status-completed { color: blue; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Bem-vindo, Administrador <?php echo htmlspecialchars($_SESSION["nome"]);?>!</h2>
            <a href="logout.php" class="btn btn-danger">Sair</a>
        </div>

        <h1>Gerenciar Consultas</h1>

        <?php
        if(!empty($error_message)){
            echo '<div class="alert alert-danger">'. htmlspecialchars($error_message). '</div>';
        }
        ?>

        <div class="consultas-list">
            <?php if (empty($consultas)): ?>
                <p class="no-consultas">Nenhuma consulta agendada ainda.</p>
            <?php else: ?>
                <?php foreach ($consultas as $consulta): ?>
                    <div class="consulta-item admin-item">
                        <p><strong>ID da Consulta:</strong> <?php echo htmlspecialchars($consulta['id']); ?></p>
                        <p><strong>Agendado por:</strong> <?php echo htmlspecialchars($consulta['nome_usuario']); ?> (<?php echo htmlspecialchars($consulta['email_usuario']); ?>)</p>
                        <p><strong>Paciente:</strong> <?php echo htmlspecialchars($consulta['nome_paciente']); ?></p>
                        <p><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($consulta['data_consulta'])); ?></p>
                        <p><strong>Hora:</strong> <?php echo htmlspecialchars($consulta['hora_consulta']); ?></p>
                        <?php if (!empty($consulta['observacoes'])): ?>
                            <p><strong>Observações:</strong> <?php echo htmlspecialchars($consulta['observacoes']); ?></p>
                        <?php endif; ?>
                        <p><strong>Status:</strong> <span class="status-<?php echo htmlspecialchars($consulta['status']); ?>"><?php echo ucfirst(htmlspecialchars($consulta['status'])); ?></span></p>
                        
                        <div class="actions">
                            <a href="editar_consulta.php?id=<?php echo $consulta['id']; ?>" class="btn btn-primary">Editar</a>
                            <a href="apagar_consulta.php?id=<?php echo $consulta['id']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja apagar esta consulta?');">Apagar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php
ob_end_flush(); // Libera o buffer de saída no final do script
?>