<?php
session_start();
include('../back/conexao.php');

$mensagem = '';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $telefone = trim($_POST['telefone'] ?? '');

    if (!$nome || !$email || !$senha) {
        $mensagem = 'Por favor preencha todos os campos obrigatórios.';
    } else {
        // Verifica se email já existe
        $stmt = $conn->prepare('SELECT id_usuario FROM usuarios WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $mensagem = 'Este e-mail já está cadastrado.';
        } else {
            // Insere usuário com senha hash
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $insert = $conn->prepare('INSERT INTO usuarios (nome, email, senha, telefone) VALUES (?, ?, ?, ?)');
            $insert->bind_param('ssss', $nome, $email, $hash, $telefone);
            if ($insert->execute()) {
                $mensagem = 'Registro realizado com sucesso!';
                $sucesso = true;
            } else {
                $mensagem = 'Erro ao registrar. Tente novamente.';
            }
            $insert->close();
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Registrar — Event São José</title>
  <link rel="stylesheet" href="css/reg.css">
  <script defer src="js/reg.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
  <header class="topbar">
    <div class="container">
      <div class="logo"><span class="logo-text">ESJ</span><h1>Event São José</h1></div>
      <nav class="nav-links">
        <a href="index.php">Início</a>
        <a href="eventos.php">Eventos</a>
      </nav>
    </div>
  </header>

  <main class="auth-page">
    <div class="card auth-card">
      <h2>Crie sua conta</h2>
      <form method="POST" id="regForm" novalidate>
        <label>Nome completo *</label>
        <input type="text" name="nome" required>
        <label>E-mail *</label>
        <input type="email" name="email" required>
        <label>Senha *</label>
        <input type="password" name="senha" required minlength="6">
        <label>Telefone</label>
        <input type="text" name="telefone">
        <button type="submit" class="btn-primary">Registrar</button>
      </form>
      <p class="small">Já tem conta? <a href="log.php">Entrar</a></p>
    </div>
  </main>

  <!-- Modal feedback -->
  <div id="feedbackModal" class="modal">
    <div class="modal-content">
      <h3 id="modalTitle"><?= htmlspecialchars($mensagem) ?></h3>
      <div class="modal-actions">
        <button id="okBtn" class="btn-primary">OK</button>
      </div>
    </div>
  </div>

<?php if ($mensagem): ?>
<script>
  // Pass variables to the frontend script
  window.__FEEDBACK = {
    mensagem: <?= json_encode($mensagem) ?>,
    sucesso: <?= $sucesso ? 'true' : 'false' ?>
  };
</script>
<?php endif; ?>

</body>
</html>
