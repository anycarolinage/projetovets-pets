<?php
// Arquivo: agendar_consulta.php
session_start();

// Verifica se o usuário está logado e se é um usuário comum
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["tipo_usuario"] !== 'comum') {
    header("location: login.php");
    exit;
}

require_once 'config.php'; // Inclui o arquivo de configuração

// Certifique-se de que $pdo está definido
if (!isset($pdo)) {
    die("Erro: Conexão com o banco de dados não foi estabelecida.");
}

$nome_paciente = $data_consulta = $hora_consulta = $observacoes = "";
$nome_paciente_err = $data_consulta_err = $hora_consulta_err = "";
$success_message = $error_message = "";
$horario_ja_marcado_err = "";

// Lógica para buscar os veterinários cadastrados na tabela 'veterinarios'
// A consulta agora busca todos os veterinários para a lista de seleção
$veterinarios_disponiveis = [];
$sql_veterinarios = "SELECT id, nome FROM veterinarios";
if ($stmt_veterinarios = $pdo->prepare($sql_veterinarios)) {
    if ($stmt_veterinarios->execute()) {
        $veterinarios_disponiveis = $stmt_veterinarios->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $error_message = "Erro ao carregar a lista de veterinários.";
    }
    unset($stmt_veterinarios);
}

// Processando dados do formulário quando o formulário é enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Valida nome do paciente (campo de texto ou seleção do dropdown de veterinários)
    if (empty(trim($_POST["nome_paciente"]))) {
        // Se o campo de texto estiver vazio, verifica se um veterinário foi selecionado
        if (empty(trim($_POST["veterinario_selecionado"]))) {
            $nome_paciente_err = "Por favor, selecione um veterinário ou digite o nome de um novo paciente.";
        } else {
            // Se um veterinário foi selecionado, usamos o nome dele para o agendamento
            $nome_paciente = trim($_POST["veterinario_selecionado"]);
        }
    } else {
        // Se o campo de texto foi preenchido, usamos o nome digitado
        $nome_paciente = trim($_POST["nome_paciente"]);
    }
    
    // Valida data da consulta
    if (empty(trim($_POST["data_consulta"]))) {
        $data_consulta_err = "Por favor, selecione a data da consulta.";
    } else {
        $data_consulta = trim($_POST["data_consulta"]);
        // **Validação de data no lado do servidor (para segurança)**
        if (strtotime($data_consulta) < strtotime(date('Y-m-d'))) {
            $data_consulta_err = "A data da consulta deve ser igual ou posterior à data de hoje.";
        }
    }

    // Valida hora da consulta
    if (empty(trim($_POST["hora_consulta"]))) {
        $hora_consulta_err = "Por favor, selecione a hora da consulta.";
    } else {
        $hora_consulta = trim($_POST["hora_consulta"]);
    }

    $observacoes = trim($_POST["observacoes"]);

    // Verifica erros antes de inserir no banco de dados
    if (empty($nome_paciente_err) && empty($data_consulta_err) && empty($hora_consulta_err)) {
        
        // Verificação de horário disponível no banco de dados
        $sql_verificar = "SELECT COUNT(*) FROM consultas WHERE data_consulta = :data_consulta AND hora_consulta = :hora_consulta";
        if ($stmt_verificar = $pdo->prepare($sql_verificar)) {
            $stmt_verificar->bindParam(":data_consulta", $param_data_consulta, PDO::PARAM_STR);
            $stmt_verificar->bindParam(":hora_consulta", $param_hora_consulta, PDO::PARAM_STR);

            $param_data_consulta = $data_consulta;
            $param_hora_consulta = $hora_consulta;

            if ($stmt_verificar->execute()) {
                $count = $stmt_verificar->fetchColumn();
                if ($count > 0) {
                    $horario_ja_marcado_err = "Este horário já está ocupado. Por favor, escolha outro.";
                    echo "<script>alert('O horário de consulta já está ocupado. Por favor, escolha outro.');</script>";
                }
            }
            unset($stmt_verificar);
        } else {
            die("Erro: Não foi possível preparar a consulta de verificação.");
        }

        // Se não houver nenhum erro, procede com a inserção
        if (empty($horario_ja_marcado_err)) {
            $sql = "INSERT INTO consultas (usuario_id, nome_paciente, data_consulta, hora_consulta, observacoes) VALUES (:usuario_id, :nome_paciente, :data_consulta, :hora_consulta, :observacoes)";

            if ($stmt = $pdo->prepare($sql)) {
                $stmt->bindParam(":usuario_id", $param_usuario_id, PDO::PARAM_INT);
                $stmt->bindParam(":nome_paciente", $param_nome_paciente, PDO::PARAM_STR);
                $stmt->bindParam(":data_consulta", $param_data_consulta, PDO::PARAM_STR);
                $stmt->bindParam(":hora_consulta", $param_hora_consulta, PDO::PARAM_STR);
                $stmt->bindParam(":observacoes", $param_observacoes, PDO::PARAM_STR);

                $param_usuario_id = $_SESSION["id"];
                $param_nome_paciente = $nome_paciente;
                $param_data_consulta = $data_consulta;
                $param_hora_consulta = $hora_consulta;
                $param_observacoes = $observacoes;

                if ($stmt->execute()) {
                    // Define uma variável de sessão para indicar o sucesso
                    $_SESSION['agendamento_sucesso'] = true;
                    // Redireciona para a página de confirmação
                    header("location: confirmacao_consulta.php");
                    exit;
                } else {
                    $error_message = "Ops! Algo deu errado ao agendar. Por favor, tente novamente mais tarde.";
                }
                unset($stmt);
            }
        }
    }
}

// Lógica para exibir APENAS a última consulta do usuário logado
$ultima_consulta = null;
if (isset($_SESSION["id"])) {
    $sql_select = "SELECT * FROM consultas WHERE usuario_id = :usuario_id ORDER BY id DESC LIMIT 1";
    if ($stmt_select = $pdo->prepare($sql_select)) {
        $stmt_select->bindParam(":usuario_id", $param_usuario_id, PDO::PARAM_INT);
        $param_usuario_id = $_SESSION["id"];
        if ($stmt_select->execute()) {
            $ultima_consulta = $stmt_select->fetch(PDO::FETCH_ASSOC);
        }
        unset($stmt_select);
    } else {
        die("Erro: Não foi possível preparar a consulta SQL.");
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Consulta</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Bem-vindo, <?php echo htmlspecialchars($_SESSION["nome"]); ?>!</h2>
            <a href="logout.php" class="btn btn-danger">Sair</a>
        </div>

        <h1>Agendar Nova Consulta</h1>

        <?php
        if (!empty($error_message)) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($error_message) . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <label for="veterinario_selecionado">Selecione um veterinário:</label>
                <select id="veterinario_selecionado" name="veterinario_selecionado">
                    <option value="" disabled selected>Selecione um veterinário</option>
                    <?php if (!empty($veterinarios_disponiveis)): ?>
                        <?php foreach ($veterinarios_disponiveis as $veterinario): ?>
                            <option value="<?= htmlspecialchars($veterinario['nome']); ?>"><?= htmlspecialchars($veterinario['nome']); ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>Nenhum veterinário encontrado</option>
                    <?php endif; ?>
                </select>
            </div>

            <p style="text-align: center;">--- OU ---</p>

            <div class="form-group">
                <label for="nome_paciente">Nome de um novo paciente:</label>
                <input type="text" id="nome_paciente" name="nome_paciente" value="<?php echo htmlspecialchars($nome_paciente); ?>">
                <span class="invalid-feedback"><?php echo $nome_paciente_err; ?></span>
            </div>
            
            <div class="form-group">
                <label for="data_consulta">Data:</label>
                <input type="date" id="data_consulta" name="data_consulta" value="<?php echo htmlspecialchars($data_consulta); ?>" required min="<?php echo date('Y-m-d'); ?>">
                <span class="invalid-feedback"><?php echo $data_consulta_err; ?></span>
            </div>
            
            <div class="form-group">
                <label for="hora_consulta">Hora:</label>
                <select id="hora_consulta" name="hora_consulta" required>
                    <option value="" disabled selected>Selecione um horário</option>
                    <?php
                    // Gerando as opções de horário de 30 em 30 minutos
                    $hora_atual = strtotime('08:00');
                    $hora_limite = strtotime('18:00');
                    while ($hora_atual <= $hora_limite) {
                        $horario_formatado = date('H:i', $hora_atual);
                        $selected = ($horario_formatado == $hora_consulta) ? 'selected' : '';
                        echo "<option value=\"{$horario_formatado}\" {$selected}>{$horario_formatado}</option>";
                        $hora_atual = strtotime('+30 minutes', $hora_atual);
                    }
                    ?>
                </select>
                <span class="invalid-feedback"><?php echo $hora_consulta_err; ?></span>
            </div>

            <div class="form-group">
                <label for="observacoes">Observações (opcional):</label>
                <textarea id="observacoes" name="observacoes"><?php echo htmlspecialchars($observacoes); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Agendar Consulta</button>
        </form>

        <hr>

        <h2>Última Consulta Agendada</h2>
        <div class="consultas-list">
            <?php if ($ultima_consulta): ?>
                <div class="consulta-item">
                    <p><strong>Paciente:</strong> <?php echo htmlspecialchars($ultima_consulta['nome_paciente']); ?></p>
                    <p><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($ultima_consulta['data_consulta'])); ?></p>
                    <p><strong>Hora:</strong> <?php echo htmlspecialchars($ultima_consulta['hora_consulta']); ?></p>
                    <?php if (!empty($ultima_consulta['observacoes'])): ?>
                        <p><strong>Observações:</strong> <?php echo htmlspecialchars($ultima_consulta['observacoes']); ?></p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p class="no-consultas">Você não tem nenhuma consulta agendada ainda.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectVeterinario = document.getElementById('veterinario_selecionado');
            const inputNovoPaciente = document.getElementById('nome_paciente');

            // Limpa o campo de texto quando uma opção é selecionada no dropdown
            selectVeterinario.addEventListener('change', () => {
                if (selectVeterinario.value !== "") {
                    inputNovoPaciente.value = "";
                }
            });

            // Limpa o dropdown quando o usuário começa a digitar no campo de texto
            inputNovoPaciente.addEventListener('input', () => {
                selectVeterinario.value = "";
            });
        });
    </script>
</body>
</html>