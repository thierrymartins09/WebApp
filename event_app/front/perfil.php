<?php
session_start();
include('../back/conexao.php'); // usa $conn (MySQLi)

if (!isset($_SESSION['id_usuario'])) {
  header("Location: log.php");
  exit();
}

$id = $_SESSION['id_usuario'];

// Consulta informações do usuário
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();
?>

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Meu Perfil - Event São José</title>
  <link rel="stylesheet" href="css/perfil.css">
  <script defer src="js/perfil.js"></script>
</head>
<body>
<header class="topbar">
  <div class="container">
    <div class="logo">
      <span class="logo-text">ESJ</span>
      <h1>Event São José</h1>
    </div>
    <div class="user-menu">
      <a href="index.php" class="btn-sec">Voltar</a>
    </div>
  </div>
</header>

<main class="perfil-container">
  <div class="perfil-card">
    <h2>Meu Perfil</h2>
    <p><strong>Nome:</strong> <?php echo htmlspecialchars($usuario['nome']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
    <p><strong>Telefone:</strong> <?php echo htmlspecialchars($usuario['telefone']); ?></p>
    <p><strong>Cadastrado em:</strong> <?php echo date('d/m/Y', strtotime($usuario['data_cadastro'])); ?></p>
    <button id="editarBtn" class="btn-primary">Editar Perfil</button>
  </div>
</main>

<div id="editModal" class="modal">
  <div class="modal-content">
    <h3>Editar Perfil</h3>
    <form id="editForm">
      <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
      <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
      <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
      <input type="text" name="telefone" value="<?php echo htmlspecialchars($usuario['telefone']); ?>">
      <input type="password" name="senha" placeholder="Nova senha (opcional)">
      <button type="submit" class="btn-primary">Salvar</button>
      <button type="button" id="cancelEdit" class="btn-sec">Cancelar</button>
    </form>
  </div>
</div>
</body>
</html>
