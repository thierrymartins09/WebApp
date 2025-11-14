<?php
session_start();

// Quando o usuário confirmar no modal, a URL terá ?confirm=true
if (isset($_GET['confirm']) && $_GET['confirm'] === 'true') {
    session_unset();
    session_destroy();
    header("Location: log.php");
    exit();
}

// Se ainda não confirmou, mostra o modal de confirmação
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Encerrar Sessão — Event São José</title>
  <link rel="stylesheet" href="css/logout.css">
  <style>
    body {
      background: #0d1b2a;
      color: #fff;
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .modal {
      background: white;
      color: #0d1b2a;
      padding: 30px;
      border-radius: 12px;
      text-align: center;
      width: 320px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    .btn-primary {
      background: #415a77;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      margin: 5px;
    }
    .btn-sec {
      background: #ccc;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      margin: 5px;
    }
  </style>
</head>
<body>
  <div class="modal">
    <h2>Deseja realmente sair?</h2>
    <div class="actions">
      <button class="btn-sec" onclick="window.location.href='index.php'">Cancelar</button>
      <button class="btn-primary" onclick="window.location.href='logout.php?confirm=true'">Sair</button>
    </div>
  </div>
</body>
</html>
