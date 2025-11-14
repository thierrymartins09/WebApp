<?php
session_start();
include('../back/conexao.php');

$mensagem = '';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!$email || !$senha) {
        $mensagem = 'Preencha e-mail e senha.';
    } else {
        $stmt = $conn->prepare('SELECT id_usuario, nome, senha FROM usuarios WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $nome, $hash);
            $stmt->fetch();

            if (password_verify($senha, $hash)) {
                // sucesso: cria sessão e redireciona
                $_SESSION['id_usuario'] = $id;
                $_SESSION['nome_usuario'] = $nome; // agora correto
                $mensagem = 'Login realizado com sucesso!';
                $sucesso = true;

                header("Location: index.php");
                exit();
            } else {
                $mensagem = 'E-mail ou senha incorretos.';
            }
        } else {
            $mensagem = 'E-mail ou senha incorretos.';
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
  <title>Entrar — Event São José</title>
  <link rel="stylesheet" href="css/log.css">
  <script defer src="js/log.js"></script>
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
      <h2>Entrar</h2>
      <form method="POST" id="logForm" novalidate>
        <label>E-mail</label>
        <input type="email" name="email" required>
        <label>Senha</label>
        <input type="password" name="senha" required>
        <button type="submit" class="btn-primary">Entrar</button>
      </form>
      <p class="small">Ainda não tem conta? <a href="reg.php">Registrar-se</a></p>
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
  window.__FEEDBACK = {
    mensagem: <?= json_encode($mensagem) ?>,
    sucesso: <?= $sucesso ? 'true' : 'false' ?>
  };
</script>
<?php endif; ?>

</body>
</html>
