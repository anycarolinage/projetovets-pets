<?php
// Arquivo: login.php
session_start(); // Inicia a sessão
require_once 'config.php'; // Inclui o arquivo de configuração do banco de dados

// Certifique-se de que $pdo está definido e inicializado corretamente
if (!isset($pdo)) {
    die("Erro: Conexão com o banco de dados não foi estabelecida.");
}

$email = $senha = "";
$email_err = $senha_err = $login_err = "";

// Processando dados do formulário quando o formulário é enviado
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Verifica se o email está vazio
    if(empty(trim($_POST["email"]))){
        $email_err = "Por favor, digite seu email.";
    } else{
        $email = trim($_POST["email"]);
    }

    // Verifica se a senha está vazia
    if(empty(trim($_POST["senha"]))){
        $senha_err = "Por favor, digite sua senha.";
    } else{
        $senha = trim($_POST["senha"]);
    }

    // Valida as credenciais
    if(empty($email_err) && empty($senha_err)){
        $sql = "SELECT id, nome, email, senha, tipo_usuario FROM usuarios WHERE email = :email";

        if($stmt = $pdo->prepare($sql)){
            // Vincular variáveis para a instrução preparada como parâmetros
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);

            // Definir parâmetros
            $param_email = $email;

            // Tenta executar a instrução preparada
            if($stmt->execute()){
                // Verifica se o email existe, se sim, verifica a senha
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $nome = $row["nome"];
                        $hashed_senha = $row["senha"];
                        $tipo_usuario = $row["tipo_usuario"];

                        // Senha não será verificada, o usuário sempre será autenticado
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["nome"] = $nome;
                        $_SESSION["email"] = $email;
                        $_SESSION["tipo_usuario"] = $tipo_usuario;

                        // Redireciona o usuário para a página apropriada
                        if($tipo_usuario == 'admin'){
                            header("location: admin_dashboard.php");
                        } else {
                            header("location: agendar_consulta.php");
                        }
                        exit();
                    }
                } else{
                    // Email não existe
                    $login_err = "Email ou senha inválidos.";
                }
            } else{
                echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }

            // Fechar declaração
            unset($stmt);
        }
    }

    // Fechar conexão
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Agenda de Consultas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <p>Por favor, preencha suas credenciais para fazer login.</p>

        <?php
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" class="form-control <?php echo (!empty($senha_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $senha_err; ?></span>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
            <p>Não tem uma conta? <a href="register.php">Cadastre-se agora</a>.</p>
        </form>
    </div>
</body>
</html>