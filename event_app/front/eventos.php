<?php
session_start();
include('../back/conexao.php'); // espera $conn (MySQLi)

$usuarioLogado = $_SESSION['nome_usuario'] ?? null; // <-- ADICIONE ESTA LINHA

// Requer usuário logado para criar evento ou reservar
$id_usuario = $_SESSION['id_usuario'] ?? null;
$mensagem = '';
$erro = '';

// Processa criação de evento (form multipart)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_event') {
    if (!$id_usuario) {
        $erro = 'Você precisa estar logado para criar eventos.';
    } else {
        // Recebe campos
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $local = trim($_POST['local'] ?? '');
        $data = trim($_POST['data'] ?? '');
        $hora = trim($_POST['hora'] ?? '');
        $capacidade = intval($_POST['capacidade'] ?? 0);
        $latitude = trim($_POST['latitude'] ?? '');
        $longitude = trim($_POST['longitude'] ?? '');

        // Validação mínima
        if (!$nome || !$descricao || !$local || !$data || !$hora || $capacidade <= 0) {
            $erro = 'Preencha todos os campos obrigatórios corretamente.';
        } else {
            // Tratamento de imagem
            $imagem_path = null;
            if (!empty($_FILES['imagem']['name'])) {
                $img = $_FILES['imagem'];
                if ($img['error'] === 0) {
                    $ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg','jpeg','png','webp'];
                    if (!in_array($ext, $allowed)) {
                        $erro = 'Formato de imagem não permitido. Use jpg/png/webp.';
                    } else {
                        $nomeArquivo = uniqid('evt_') . '.' . $ext;
                        $dest = __DIR__ . '/../img/eventos/' . $nomeArquivo;

                        // Garante pasta
                        if (!is_dir(__DIR__ . '/../img/eventos/')) mkdir(__DIR__ . '/../img/eventos/', 0755, true);
                        if (move_uploaded_file($img['tmp_name'], $dest)) {
                            $imagem_path = 'img/eventos/' . $nomeArquivo;


                        } else {
                            $erro = 'Falha ao salvar imagem.';
                        }
                    }
                } else {
                    $erro = 'Erro no upload da imagem.';
                }
            }
        }

        // Inserção
        if (!$erro) {
            $stmt = $conn->prepare("INSERT INTO eventos (nome, descricao, local, data, hora, capacidade, imagem, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sssssiiss', $nome, $descricao, $local, $data, $hora, $capacidade, $imagem_path, $latitude, $longitude);
            if ($stmt->execute()) {
                $mensagem = 'Evento criado com sucesso!';
            } else {
                $erro = 'Erro ao salvar evento: ' . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Processa reserva por AJAX (fetch) -> retorna JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reserve') {
    header('Content-Type: application/json; charset=utf-8');
    if (!$id_usuario) {
        echo json_encode(['status' => 'error', 'mensagem' => 'Você precisa estar logado para reservar.']);
        exit();
    }
    $id_evento = intval($_POST['id_evento'] ?? 0);
    if ($id_evento <= 0) {
        echo json_encode(['status' => 'error', 'mensagem' => 'Evento inválido.']);
        exit();
    }
    // Verifica lotação / existência
    $q = $conn->prepare("SELECT capacidade, (SELECT COUNT(*) FROM reservas r WHERE r.id_evento = e.id_evento AND r.status = 'A') AS ocupadas FROM eventos e WHERE id_evento = ?");
    $q->bind_param('i', $id_evento);
    $q->execute();
    $res = $q->get_result();
    if ($row = $res->fetch_assoc()) {
        if ($row['ocupadas'] >= $row['capacidade']) {
            echo json_encode(['status' => 'error', 'mensagem' => 'Evento lotado.']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'mensagem' => 'Evento não encontrado.']);
        exit();
    }
    $q->close();

    // Insere reserva
    $status = 'A';
    $stmt = $conn->prepare("INSERT INTO reservas (id_usuario, id_evento, status) VALUES (?, ?, ?)");
    $stmt->bind_param('iis', $id_usuario, $id_evento, $status);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'mensagem' => 'Reserva realizada com sucesso!']);
    } else {
        echo json_encode(['status' => 'error', 'mensagem' => 'Erro ao reservar: ' . $stmt->error]);
    }
    $stmt->close();
    exit();
}

// Busca eventos para listar e mapar
$events = [];
$rs = $conn->query("SELECT * FROM eventos ORDER BY data ASC, hora ASC");
if ($rs) {
    while ($r = $rs->fetch_assoc()) $events[] = $r;
    $rs->close();
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Eventos — Event São José</title>
  <link rel="stylesheet" href="../front/css/eventos.css">
  <script defer src="../front/js/eventos.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <!-- Google Maps: substitua YOUR_GOOGLE_MAPS_API_KEY pelo seu key -->
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD1ymgJSOFD9yCS4hoC7hNeU8Km40bbQi0&libraries=places" defer></script>
</head>
<body>
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

  <main class="page">
    <section class="create-event">
      <h2>Criar novo evento</h2>
      <p>Preencha os dados do evento. Clique no mapa para selecionar latitude/longitude automaticamente.</p>

      <form id="createEventForm" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create_event">
        <label>Nome</label>
        <input type="text" name="nome" required>

        <label>Descrição</label>
        <textarea name="descricao" rows="3" required></textarea>

        <label>Local (endereço)</label>
        <input type="text" name="local" required>

        <div class="row">
          <div>
            <label>Data</label>
            <input type="date" name="data" required>
          </div>
          <div>
            <label>Hora</label>
            <input type="time" name="hora" required>
          </div>
        </div>

        <label>Capacidade</label>
        <input type="number" name="capacidade" min="1" required>

        <label>Imagem (jpg/png/webp)</label>
        <input type="file" name="imagem" accept="image/*">

        <div class="row">
          <div>
            <label>Latitude</label>
            <input type="text" name="latitude" id="latitude" placeholder="Clique no mapa" readonly>
          </div>
          <div>
            <label>Longitude</label>
            <input type="text" name="longitude" id="longitude" placeholder="Clique no mapa" readonly>
          </div>
        </div>

        <button type="submit" class="btn-primary">Criar Evento</button>
        <?php if ($mensagem): ?><p class="success"><?= htmlspecialchars($mensagem) ?></p><?php endif; ?>
        <?php if ($erro): ?><p class="error"><?= htmlspecialchars($erro) ?></p><?php endif; ?>
      </form>
    </section>

    <section class="map-section">
      <h2>Mapa de eventos</h2>
      <div id="map"></div>
    </section>

    <section class="list-section">
      <h2>Eventos</h2>
      <div class="grid">
        <?php if (count($events) === 0): ?>
          <p>Nenhum evento cadastrado.</p>
        <?php else: foreach ($events as $e): ?>
          <article class="card">
            <div class="thumb">
              <?php if (!empty($e['imagem'])): ?>
                <img src="../<?= htmlspecialchars($e['imagem']) ?>" alt="<?= htmlspecialchars($e['nome']) ?>">

              <?php else: ?>
                <div class="noimg">Sem imagem</div>
              <?php endif; ?>
            </div>
            <div class="card-body">
              <h3><?= htmlspecialchars($e['nome']) ?></h3>
              <p class="meta"><?= htmlspecialchars($e['local']) ?> — <?= date('d/m/Y', strtotime($e['data'])) ?> <?= htmlspecialchars($e['hora']) ?></p>
              <p><?= nl2br(htmlspecialchars($e['descricao'])) ?></p>
              <p>Capacidade: <?= intval($e['capacidade']) ?></p>
              <div class="card-actions">
                <?php if ($id_usuario): ?>
                  <button class="btn-primary reserveBtn" data-id="<?= intval($e['id_evento']) ?>">Reservar</button>
                <?php else: ?>
                  <a class="btn-primary" href="log.php">Entrar para reservar</a>
                <?php endif; ?>
                <button class="btn-sec locateBtn" data-lat="<?= htmlspecialchars($e['latitude']) ?>" data-lng="<?= htmlspecialchars($e['longitude']) ?>">Localizar no mapa</button>
              </div>
            </div>
          </article>
        <?php endforeach; endif; ?>
      </div>
    </section>
  </main>

  <footer class="site-footer">
    <div class="container">
      <small>© 2025 Event São José — Todos os direitos reservados.</small>
    </div>
  </footer>
</body>
</html>
