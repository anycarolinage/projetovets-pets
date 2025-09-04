<?php
// Arquivo: registerclinic.php
session_start();
require_once 'config.php';

$nome_clinica = $email = $senha = $confirm_senha = $telefone = $codigo_registro = $endereco = "";
$nome_clinica_err = $email_err = $senha_err = $confirm_senha_err = $telefone_err = $codigo_registro_err = $endereco_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Valida nome da clínica
    if (empty(trim($_POST["nome_clinica"]))) {
        $nome_clinica_err = "Por favor, digite o nome da clínica.";
    } else {
        $nome_clinica = trim($_POST["nome_clinica"]);
    }

    // Valida email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Por favor, digite um email.";
    } else {
        $sql = "SELECT id FROM clinicas WHERE email = :email";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $param_email = trim($_POST["email"]);

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $email_err = "Este email já está em uso.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }
            unset($stmt);
        }
    }

    // Valida senha
    if (empty(trim($_POST["senha"]))) {
        $senha_err = "Por favor, digite uma senha.";
    } elseif (strlen(trim($_POST["senha"])) < 6) {
        $senha_err = "A senha deve ter pelo menos 6 caracteres.";
    } else {
        $senha = trim($_POST["senha"]);
    }

    // Valida confirmação de senha
    if (empty(trim($_POST["confirm_senha"]))) {
        $confirm_senha_err = "Por favor, confirme a senha.";
    } else {
        $confirm_senha = trim($_POST["confirm_senha"]);
        if (empty($senha_err) && ($senha != $confirm_senha)) {
            $confirm_senha_err = "As senhas não coincidem.";
        }
    }

    // Valida telefone
    if (empty(trim($_POST["telefone"]))) {
        $telefone_err = "Por favor, digite o telefone da clínica.";
    } else {
        $telefone = trim($_POST["telefone"]);
    }

    // Valida código de registro
    if (empty(trim($_POST["codigo_registro"]))) {
        $codigo_registro_err = "Por favor, digite o código de registro.";
    } else {
        $codigo_registro = trim($_POST["codigo_registro"]);
    }

    // Valida endereço
    if (empty(trim($_POST["endereco"]))) {
        $endereco_err = "Por favor, digite o endereço completo da clínica.";
    } else {
        $endereco = trim($_POST["endereco"]);
    }

    // Verifica erros antes de inserir no banco de dados
    if (empty($nome_clinica_err) && empty($email_err) && empty($senha_err) && empty($confirm_senha_err) && empty($telefone_err) && empty($codigo_registro_err) && empty($endereco_err)) {

        $sql = "INSERT INTO clinicas (nome_clinica, email, senha, telefone, codigo_registro, endereco) VALUES (:nome_clinica, :email, :senha, :telefone, :codigo_registro, :endereco)";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":nome_clinica", $param_nome_clinica, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":senha", $param_senha, PDO::PARAM_STR);
            $stmt->bindParam(":telefone", $param_telefone, PDO::PARAM_STR);
            $stmt->bindParam(":codigo_registro", $param_codigo_registro, PDO::PARAM_STR);
            $stmt->bindParam(":endereco", $param_endereco, PDO::PARAM_STR);

            $param_nome_clinica = $nome_clinica;
            $param_email = $email;
            $param_senha = password_hash($senha, PASSWORD_DEFAULT);
            $param_telefone = $telefone;
            $param_codigo_registro = $codigo_registro;
            $param_endereco = $endereco;

            if ($stmt->execute()) {
                header("location: login.php");
            } else {
                echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }
            unset($stmt);
        }
    }

    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro da Clínica - Agenda de Consultas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Registro da Clínica</h2>
        <p>Por favor, preencha este formulário para registrar sua clínica.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Nome da Clínica</label>
                <input type="text" name="nome_clinica" class="form-control <?php echo (!empty($nome_clinica_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nome_clinica; ?>">
                <span class="invalid-feedback"><?php echo $nome_clinica_err; ?></span>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label>Telefone</label>
                <input type="text" name="telefone" class="form-control <?php echo (!empty($telefone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $telefone; ?>">
                <span class="invalid-feedback"><?php echo $telefone_err; ?></span>
            </div>
            <div class="form-group">
                <label>Código de Registro</label>
                <input type="text" name="codigo_registro" class="form-control <?php echo (!empty($codigo_registro_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $codigo_registro; ?>">
                <span class="invalid-feedback"><?php echo $codigo_registro_err; ?></span>
            </div>
            <div class="form-group">
                <label>Endereço Completo</label>
                <input type="text" name="endereco" class="form-control <?php echo (!empty($endereco_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $endereco; ?>">
                <span class="invalid-feedback"><?php echo $endereco_err; ?></span>
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" class="form-control <?php echo (!empty($senha_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $senha; ?>">
                <span class="invalid-feedback"><?php echo $senha_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirme a Senha</label>
                <input type="password" name="confirm_senha" class="form-control <?php echo (!empty($confirm_senha_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $confirm_senha_err; ?></span>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Registrar</button>
            </div>
            <p>Já tem uma conta? <a href="login.php">Faça login aqui</a>.</p>
        </form>
    </div>
</body>
</html>