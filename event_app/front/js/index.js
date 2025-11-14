document.addEventListener("DOMContentLoaded", () => {
  const userBtn = document.getElementById("userBtn");
  const dropdownMenu = document.getElementById("dropdownMenu");
  const logoutBtn = document.getElementById("logoutBtn");
  const logoutModal = document.getElementById("logoutModal");
  const confirmLogout = document.getElementById("confirmLogout");
  const cancelLogout = document.getElementById("cancelLogout");

  // Alternar dropdown
  userBtn.addEventListener("click", () => {
    dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
  });

  // Fechar dropdown ao clicar fora
  document.addEventListener("click", (e) => {
    if (!userBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
      dropdownMenu.style.display = "none";
    }
  });

  // Abrir modal de logout
  logoutBtn.addEventListener("click", (e) => {
    e.preventDefault();
    logoutModal.style.display = "flex";
  });

  // Confirmar logout
  confirmLogout.addEventListener("click", () => {
    logoutModal.style.display = "none";
    alert("Você saiu da sua conta com sucesso!");
    window.location.href = "../front/log.php";
  });

  // Cancelar logout
  cancelLogout.addEventListener("click", () => {
    logoutModal.style.display = "none";
  });
});
