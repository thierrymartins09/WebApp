<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['id_usuario'])) {
  echo json_encode(['status' => 'error', 'mensagem' => 'Usuário não logado.']);
  exit();
}

$id = $_SESSION['id_usuario'];
$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$senha = $_POST['senha'] ?? '';

// Atualiza os dados
if ($senha !== "") {
  $hash = password_hash($senha, PASSWORD_DEFAULT);
  $stmt = $conn->prepare("UPDATE usuarios SET nome=?, email=?, telefone=?, senha=? WHERE id_usuario=?");
  $stmt->bind_param("ssssi", $nome, $email, $telefone, $hash, $id);
} else {
  $stmt = $conn->prepare("UPDATE usuarios SET nome=?, email=?, telefone=? WHERE id_usuario=?");
  $stmt->bind_param("sssi", $nome, $email, $telefone, $id);
}

if ($stmt->execute()) {
  echo json_encode(['status' => 'success', 'mensagem' => 'Perfil atualizado com sucesso!']);
} else {
  echo json_encode(['status' => 'error', 'mensagem' => 'Erro ao atualizar perfil.']);
}
$stmt->close();
$conn->close();
?>
