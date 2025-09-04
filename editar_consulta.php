<?php
// Inclui o arquivo de configuração do banco de dados
require_once 'config.php';

// Inicializa variáveis
$nome_paciente = $data_consulta = $hora_consulta = $observacoes = "";
$nome_paciente_err = $data_consulta_err = $hora_consulta_err = "";

// Verifica se o ID foi passado na URL e se é válido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Busca os dados da consulta no banco de dados
    $sql = "SELECT * FROM consultas WHERE id = :id";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $nome_paciente = $row['nome_paciente'];
                $data_consulta = $row['data_consulta'];
                $hora_consulta = $row['hora_consulta'];
                $observacoes = $row['observacoes'];
            } else {
                // Redireciona se o ID não for encontrado
                header("location: admin_dashboard.php");
                exit;
            }
        } else {
            echo "Erro ao buscar os dados da consulta.";
        }
    }
    unset($stmt);
} else {
    // Redireciona se o ID não for válido
    header("location: admin_dashboard.php");
    exit;
}

// Processa os dados do formulário quando enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Valida nome do paciente
    if (empty(trim($_POST["nome_paciente"]))) {
        $nome_paciente_err = "Por favor, digite o nome do paciente.";
    } else {
        $nome_paciente = trim($_POST["nome_paciente"]);
    }

    // Valida data da consulta
    if (empty(trim($_POST["data_consulta"]))) {
        $data_consulta_err = "Por favor, selecione a data da consulta.";
    } else {
        $data_consulta = trim($_POST["data_consulta"]);
    }

    // Valida hora da consulta
    if (empty(trim($_POST["hora_consulta"]))) {
        $hora_consulta_err = "Por favor, selecione a hora da consulta.";
    } else {
        $hora_consulta = trim($_POST["hora_consulta"]);
    }

    // Observações (opcional)
    $observacoes = trim($_POST["observacoes"]);

    // Verifica se não há erros antes de atualizar no banco de dados
    if (empty($nome_paciente_err) && empty($data_consulta_err) && empty($hora_consulta_err)) {
        $sql = "UPDATE consultas SET nome_paciente = :nome_paciente, data_consulta = :data_consulta, hora_consulta = :hora_consulta, observacoes = :observacoes WHERE id = :id";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":nome_paciente", $nome_paciente, PDO::PARAM_STR);
            $stmt->bindParam(":data_consulta", $data_consulta, PDO::PARAM_STR);
            $stmt->bindParam(":hora_consulta", $hora_consulta, PDO::PARAM_STR);
            $stmt->bindParam(":observacoes", $observacoes, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Redireciona para o dashboard após a edição
                header("location: admin_dashboard.php");
                exit;
            } else {
                echo "Erro ao atualizar os dados da consulta.";
            }
        }
        unset($stmt);
    }
}

// Fecha a conexão com o banco de dados
unset($pdo);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Consulta</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Editar Consulta</h2>
        <p>Edite os campos abaixo e clique em "Salvar" para atualizar a consulta.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id; ?>" method="post">
            <div class="form-group">
                <label>Nome do Paciente</label>
                <input type="text" name="nome_paciente" class="form-control <?php echo (!empty($nome_paciente_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($nome_paciente); ?>">
                <span class="invalid-feedback"><?php echo $nome_paciente_err; ?></span>
            </div>
            <div class="form-group">
                <label>Data da Consulta</label>
                <input type="date" name="data_consulta" class="form-control <?php echo (!empty($data_consulta_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($data_consulta); ?>">
                <span class="invalid-feedback"><?php echo $data_consulta_err; ?></span>
            </div>
            <div class="form-group">
                <label>Hora da Consulta</label>
                <input type="time" name="hora_consulta" class="form-control <?php echo (!empty($hora_consulta_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($hora_consulta); ?>">
                <span class="invalid-feedback"><?php echo $hora_consulta_err; ?></span>
            </div>
            <div class="form-group">
                <label>Observações</label>
                <textarea name="observacoes" class="form-control"><?php echo htmlspecialchars($observacoes); ?></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="admin_dashboard.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>