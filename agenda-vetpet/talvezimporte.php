<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento de Consulta Veterinária</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Agendar Consulta Veterinária</h1>

        <?php
        // Bloco PHP para processar o formulário
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // 1. Coleta de dados do formulário
            $nome_proprietario = htmlspecialchars(trim($_POST['nome_proprietario']));
            $email_proprietario = htmlspecialchars(trim($_POST['email_proprietario']));
            $telefone_proprietario = htmlspecialchars(trim($_POST['telefone_proprietario']));
            $nome_animal = htmlspecialchars(trim($_POST['nome_animal']));
            $especie_animal = htmlspecialchars(trim($_POST['especie_animal']));
            $data_consulta = htmlspecialchars(trim($_POST['data_consulta']));
            $hora_consulta = htmlspecialchars(trim($_POST['hora_consulta']));
            $motivo_consulta = htmlspecialchars(trim($_POST['motivo_consulta']));

            // 2. Validação dos dados (exemplo básico)
            $erros = [];

            if (empty($nome_proprietario)) {
                $erros[] = "O nome do proprietário é obrigatório.";
            }
            if (empty($email_proprietario) || !filter_var($email_proprietario, FILTER_VALIDATE_EMAIL)) {
                $erros[] = "O e-mail do proprietário é inválido ou está vazio.";
            }
            if (empty($telefone_proprietario)) {
                $erros[] = "O telefone do proprietário é obrigatório.";
            }
            if (empty($nome_animal)) {
                $erros[] = "O nome do animal é obrigatório.";
            }
            if (empty($especie_animal)) {
                $erros[] = "A espécie do animal é obrigatória.";
            }
            if (empty($data_consulta)) {
                $erros[] = "A data da consulta é obrigatória.";
            } elseif (strtotime($data_consulta) < strtotime(date('Y-m-d'))) {
                $erros[] = "A data da consulta não pode ser no passado.";
            }
            if (empty($hora_consulta)) {
                $erros[] = "A hora da consulta é obrigatória.";
            }
            if (empty($motivo_consulta)) {
                $erros[] = "O motivo da consulta é obrigatório.";
            }

            // 3. Exibição de erros ou processamento bem-sucedido
            if (count($erros) > 0) {
                echo '<div class="mensagem erro">';
                echo '<h2>Erros no agendamento:</h2>';
                echo '<ul>';
                foreach ($erros as $erro) {
                    echo '<li>' . $erro . '</li>';
                }
                echo '</ul>';
                echo '</div>';
            } else {
                // 4. Se não houver erros, simule o agendamento
                // Em um cenário real, aqui você faria:
                // - Conexão com um banco de dados (MySQL, PostgreSQL, etc.)
                // - Inserção dos dados em uma tabela de agendamentos
                // - Envio de e-mail de confirmação para o proprietário
                // - Verificação de disponibilidade de horários

                // Exemplo de como você poderia salvar em um arquivo (apenas para demonstração, não recomendado para produção)
                $dados_agendamento = "
---
Nome Proprietário: $nome_proprietario
Email Proprietário: $email_proprietario
Telefone Proprietário: $telefone_proprietario
Nome Animal: $nome_animal
Espécie Animal: $especie_animal
Data da Consulta: " . date('d/m/Y', strtotime($data_consulta)) . "
Hora da Consulta: $hora_consulta
Motivo da Consulta: $motivo_consulta
---
";
                // file_put_contents('agendamentos.txt', $dados_agendamento, FILE_APPEND | LOCK_EX); // Descomente para salvar em arquivo

                echo '<div class="mensagem sucesso">';
                echo '<h2>Consulta agendada com sucesso!</h2>';
                echo '<p>Detalhes do agendamento:</p>';
                echo '<ul>';
                echo '<li><strong>Proprietário:</strong> ' . $nome_proprietario . '</li>';
                echo '<li><strong>Animal:</strong> ' . $nome_animal . ' (' . $especie_animal . ')</li>';
                echo '<li><strong>Data:</strong> ' . date('d/m/Y', strtotime($data_consulta)) . '</li>';
                echo '<li><strong>Hora:</strong> ' . $hora_consulta . '</li>';
                echo '<li><strong>Motivo:</strong> ' . $motivo_consulta . '</li>';
                echo '</ul>';
                echo '<p>Você receberá um e-mail de confirmação em breve.</p>';
                echo '</div>';
            }
        }
        ?>

        <form action="index.php" method="POST">
            <fieldset>
                <legend>Dados do Proprietário</legend>
                <div class="form-group">
                    <label for="nome_proprietario">Nome Completo:</label>
                    <input type="text" id="nome_proprietario" name="nome_proprietario" required
                           value="<?php echo isset($nome_proprietario) ? $nome_proprietario : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="email_proprietario">E-mail:</label>
                    <input type="email" id="email_proprietario" name="email_proprietario" required
                           value="<?php echo isset($email_proprietario) ? $email_proprietario : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="telefone_proprietario">Telefone:</label>
                    <input type="tel" id="telefone_proprietario" name="telefone_proprietario" placeholder="(XX) XXXXX-XXXX" pattern="^\(?\d{2}\)?[\s-]?\d{4,5}-?\d{4}$" required
                           value="<?php echo isset($telefone_proprietario) ? $telefone_proprietario : ''; ?>">
                </div>
            </fieldset>

            <fieldset>
                <legend>Dados do Animal</legend>
                <div class="form-group">
                    <label for="nome_animal">Nome do Animal:</label>
                    <input type="text" id="nome_animal" name="nome_animal" required
                           value="<?php echo isset($nome_animal) ? $nome_animal : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="especie_animal">Espécie:</label>
                    <input type="text" id="especie_animal" name="especie_animal" required list="especies"
                           value="<?php echo isset($especie_animal) ? $especie_animal : ''; ?>">
                    <datalist id="especies">
                        <option value="Cachorro">
                        <option value="Gato">
                        <option value="Pássaro">
                        <option value="Roedor">
                        <option value="Outro">
                    </datalist>
                </div>
            </fieldset>

            <fieldset>
                <legend>Detalhes da Consulta</legend>
                <div class="form-group">
                    <label for="data_consulta">Data:</label>
                    <input type="date" id="data_consulta" name="data_consulta" required
                           min="<?php echo date('Y-m-d'); ?>"
                           value="<?php echo isset($data_consulta) ? $data_consulta : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="hora_consulta">Hora:</label>
                    <input type="time" id="hora_consulta" name="hora_consulta" required
                           value="<?php echo isset($hora_consulta) ? $hora_consulta : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="motivo_consulta">Motivo da Consulta:</label>
                    <textarea id="motivo_consulta" name="motivo_consulta" rows="4" required><?php echo isset($motivo_consulta) ? $motivo_consulta : ''; ?></textarea>
                </div>
            </fieldset>

            <button type="submit">Agendar Consulta</button>
        </form>
    </div>
</body>
</html>