<?php
session_start();
$usuarioLogado = $_SESSION['nome_usuario'] ?? null;
?>

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Event São José</title>
  <link rel="stylesheet" href="css/index.css">
  <script defer src="js/index.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body>
  <!-- Topbar -->
  <header class="topbar">
  <div class="container row between center">
    <div class="logo">
      <span class="logo-text">ESJ</span>
      <h1>Event São José</h1>
    </div>

    <nav class="nav-links">
      <a href="index.php" class="btn-primary">Inicio</a>
      <a href="eventos.php" class="btn-primary">Eventos</a>
      <a href="reserva.php" class="btn-primary">Reservas</a>
      <?php if ($usuarioLogado): ?>
      <div class="dropdown">
        <button class="dropbtn"><?php echo htmlspecialchars($usuarioLogado); ?> ▼</button>
        <div class="dropdown-content">
          <a href="perfil.php">Meu Perfil</a>
          <a href="logout.php">Sair</a>
        </div>
      </div>
      <?php else: ?>
      <a href="log.php" class="btn-primary">Entrar</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

  <!-- Conteúdo principal -->
  <main>
    <section class="hero">
      <h2>Descubra os melhores eventos culturais em São José dos Campos 🎭</h2>
      <p>Reserve seu ingresso e participe de experiências inesquecíveis!</p>
      <a href="eventos.php" class="btn-primary">Explorar Eventos</a>
    </section>

    <section class="cards-section">
      <h3>Eventos em destaque</h3>
      <div class="card-grid">
        <div class="card">
          <img src="../img/aura.png" alt="Show ao vivo">
          <h4>Show de Música</h4>
          <p>Curta um som ao vivo no centro da cidade!</p>
          <button class="btn-sec">Reservar</button>
        </div>
        <div class="card">
          <img src="../img/linda.png" alt="Exposição de arte">
          <h4>Exposição de Arte</h4>
          <p>Explore a criatividade dos artistas locais.</p>
          <button class="btn-sec">Reservar</button>
        </div>
      </div>
    </section>
  </main>

  <!-- Rodapé -->
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

  <!-- Modal de confirmação -->
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
