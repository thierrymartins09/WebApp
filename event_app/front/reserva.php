<?php
session_start();
include('../back/conexao.php');

// Usuário logado?
$id_usuario = $_SESSION['id_usuario'] ?? null;
$usuarioLogado = $_SESSION['nome_usuario'] ?? null;

if (!$id_usuario) {
  header("Location: log.php");
  exit;
}

// Buscar reservas
$sql = "SELECT r.id_reserva, r.status, r.data_reserva,
               e.nome, e.local, e.data, e.hora, e.imagem
        FROM reservas r
        JOIN eventos e ON r.id_evento = e.id_evento
        WHERE r.id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Minhas Reservas — Event São José</title>
  
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- FontAwesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

  <!-- CSS do site -->
  <link rel="stylesheet" href="../front/css/reserva.css">

  <script defer src="../front/js/reserva.js"></script>
</head>
<body>

<!-- TOPBAR -->
<header class="topbar">
  <div class="container row between center">
    <div class="logo">
      <span class="logo-text">ESJ</span>
      <h1>Event São José</h1>
    </div>

    <nav class="nav-links">
      <a href="index.php" class="btn-primary">Início</a>
      <a href="eventos.php" class="btn-primary">Eventos</a>
      <a href="reserva.php" class="btn-primary">Reservas</a>

      <?php if ($usuarioLogado): ?>
      <div class="dropdown">
        <button id="userBtn" class="dropbtn">
          <?php echo htmlspecialchars($usuarioLogado); ?> ▼
        </button>
        <div class="dropdown-content" id="dropdownMenu">
          <a href="perfil.php">Meu Perfil</a>
          <a href="#" id="logoutBtn">Sair</a>
        </div>
      </div>
      <?php else: ?>
      <a href="log.php" class="btn-primary">Entrar</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<!-- CONTEÚDO PRINCIPAL -->
<main class="container py-5">
  <h1 class="mb-4">Minhas Reservas</h1>

  <div class="row g-4">
    <?php while ($row = $result->fetch_assoc()): ?>
    <div class="col-md-4">
      <div class="card shadow-sm reserva-card">
        <img src="../<?php echo $row['imagem']; ?>" class="card-img-top" alt="Evento">


        <div class="card-body">
          <h4 class="card-title"><?php echo $row['nome']; ?></h4>

          <p class="card-text">
            <i class="fa-solid fa-location-dot"></i> <?php echo $row['local']; ?><br>
            <i class="fa-solid fa-calendar"></i> 
            <?php echo date('d/m/Y', strtotime($row['data'])); ?> —
            <?php echo $row['hora']; ?>
          </p>

          <span class="badge bg-primary">
            Status: <?php echo $row['status']; ?>
          </span>

          <p class="mt-2 small text-muted">
            Reservado em: <?php echo date('d/m/Y H:i', strtotime($row['data_reserva'])); ?>
          </p>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</main>

<!-- FOOTER -->
<footer>
    <div class="footer-container">
      <div class="footer-col">
        <h4>Event São José</h4>
        <p>Explore, descubra e participe dos melhores eventos culturais da cidade.</p>
      </div>
      <div class="footer-col">
        <h4>Funcionalidades</h4>
        <ul>
          <li>Reservar eventos</li>
          <li>Gerenciar reservas</li>
          <li>Visualizar mapa dos locais</li>
        </ul>
      </div>
      <div class="footer-col social">
        <h4>Redes Sociais</h4>
        <div class="icons">
          <i class="fab fa-instagram"></i>
          <i class="fab fa-facebook-f"></i>
          <i class="fab fa-linkedin-in"></i>
        </div>
      </div>
    </div>
    <p class="footer-bottom">© 2025 Event São José — Todos os direitos reservados.</p>
  </footer>

<!-- Modal de Logout -->
<div id="logoutModal" class="modal">
  <div class="modal-content">
    <h3>Deseja sair da sua conta?</h3>
    <div class="modal-actions">
      <button id="confirmLogout" class="btn-primary">Sim, sair</button>
      <button id="cancelLogout" class="btn-sec">Cancelar</button>
    </div>
  </div>
</div>

</body>
</html>