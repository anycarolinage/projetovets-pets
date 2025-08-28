<?php
// Arquivo: register.php
session_start();
require_once 'config.php';

$nome = $email = $senha = $confirm_senha = $telefone = $cpf = $rua = $numero = $cidade = $crmv = $clinica_filiado = "";
$nome_err = $email_err = $senha_err = $confirm_senha_err = $telefone_err = $cpf_err = $rua_err = $numero_err = $cidade_err = $crmv_err = $clinica_filiado_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Valida nome
    if (empty(trim($_POST["nome"]))) {
        $nome_err = "Por favor, digite seu nome.";
    } else {
        $nome = trim($_POST["nome"]);
    }

    // Valida email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Por favor, digite um email.";
    } else {
        $sql = "SELECT id FROM usuarios WHERE email = :email";

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
        $telefone_err = "Por favor, digite seu telefone.";
    } else {
        $telefone = trim($_POST["telefone"]);
    }

    // Valida CPF
    if (empty(trim($_POST["cpf"]))) {
        $cpf_err = "Por favor, digite seu CPF.";
    } else {
        $cpf = trim($_POST["cpf"]);
    }

    // Valida rua
    if (empty(trim($_POST["rua"]))) {
        $rua_err = "Por favor, digite sua rua.";
    } else {
        $rua = trim($_POST["rua"]);
    }

    // Valida número
    if (empty(trim($_POST["numero"]))) {
        $numero_err = "Por favor, digite o número da sua residência.";
    } else {
        $numero = trim($_POST["numero"]);
    }

    // Valida cidade
    if (empty(trim($_POST["cidade"]))) {
        $cidade_err = "Por favor, digite sua cidade.";
    } else {
        $cidade = trim($_POST["cidade"]);
    }

    // Valida CRMV
    if (empty(trim($_POST["crmv"]))) {
        $crmv_err = "Por favor, digite seu CRMV.";
    } else {
        $crmv = trim($_POST["crmv"]);
    }

    // Valida clínica filiado
    if (empty(trim($_POST["clinica_filiado"]))) {
        $clinica_filiado_err = "Por favor, digite a clínica à qual você é filiado.";
    } else {
        $clinica_filiado = trim($_POST["clinica_filiado"]);
    }

    // Verifica erros antes de inserir no banco de dados
    if (empty($nome_err) && empty($email_err) && empty($senha_err) && empty($confirm_senha_err) && empty($telefone_err) && empty($cpf_err) && empty($rua_err) && empty($numero_err) && empty($cidade_err) && empty($crmv_err) && empty($clinica_filiado_err)) {

        $sql = "INSERT INTO usuarios (nome, email, senha, telefone, cpf, rua, numero, cidade, crmv, clinica_filiado, tipo_usuario) VALUES (:nome, :email, :senha, :telefone, :cpf, :rua, :numero, :cidade, :crmv, :clinica_filiado, 'comum')";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":nome", $param_nome, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":senha", $param_senha, PDO::PARAM_STR);
            $stmt->bindParam(":telefone", $param_telefone, PDO::PARAM_STR);
            $stmt->bindParam(":cpf", $param_cpf, PDO::PARAM_STR);
            $stmt->bindParam(":rua", $param_rua, PDO::PARAM_STR);
            $stmt->bindParam(":numero", $param_numero, PDO::PARAM_STR);
            $stmt->bindParam(":cidade", $param_cidade, PDO::PARAM_STR);
            $stmt->bindParam(":crmv", $param_crmv, PDO::PARAM_STR);
            $stmt->bindParam(":clinica_filiado", $param_clinica_filiado, PDO::PARAM_STR);

            $param_nome = $nome;
            $param_email = $email;
            $param_senha = password_hash($senha, PASSWORD_DEFAULT); // Hash da senha
            $param_telefone = $telefone;
            $param_cpf = $cpf;
            $param_rua = $rua;
            $param_numero = $numero;
            $param_cidade = $cidade;
            $param_crmv = $crmv;
            $param_clinica_filiado = $clinica_filiado;

            if ($stmt->execute()) {
                header("location: login.php"); // Redireciona para a página de login
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
    <title>Registro - Agenda de Consultas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Registro</h2>
        <p>Por favor, preencha este formulário para criar uma conta.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" class="form-control <?php echo (!empty($nome_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $nome; ?>">
                <span class="invalid-feedback"><?php echo $nome_err; ?></span>
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
                <label>CPF</label>
                <input type="text" name="cpf" class="form-control <?php echo (!empty($cpf_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $cpf; ?>">
                <span class="invalid-feedback"><?php echo $cpf_err; ?></span>
            </div>
            <fieldset>
                <legend>Endereço</legend>
                <div class="form-group">
                    <label>Rua</label>
                    <input type="text" name="rua" class="form-control <?php echo (!empty($rua_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $rua; ?>">
                    <span class="invalid-feedback"><?php echo $rua_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Número</label>
                    <input type="text" name="numero" class="form-control <?php echo (!empty($numero_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $numero; ?>">
                    <span class="invalid-feedback"><?php echo $numero_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Cidade</label>
                    <input type="text" name="cidade" class="form-control <?php echo (!empty($cidade_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $cidade; ?>">
                    <span class="invalid-feedback"><?php echo $cidade_err; ?></span>
                </div>
            </fieldset>
            <div class="form-group">
                <label>CRMV</label>
                <input type="text" name="crmv" class="form-control <?php echo (!empty($crmv_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $crmv; ?>">
                <span class="invalid-feedback"><?php echo $crmv_err; ?></span>
            </div>
            <div class="form-group">
                <label>Clínica Filiada</label>
                <input type="text" name="clinica_filiado" class="form-control <?php echo (!empty($clinica_filiado_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $clinica_filiado; ?>">
                <span class="invalid-feedback"><?php echo $clinica_filiado_err; ?></span>
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