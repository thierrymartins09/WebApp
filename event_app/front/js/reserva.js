document.addEventListener("DOMContentLoaded", () => {

  /* ============================
      DROPDOWN DO USUÁRIO
  ============================ */
  const userBtn = document.getElementById("userBtn");
  const dropdownMenu = document.getElementById("dropdownMenu");

  if (userBtn) {
    userBtn.addEventListener("click", () => {
      dropdownMenu.classList.toggle("show");
    });

    document.addEventListener("click", (e) => {
      if (!userBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
        dropdownMenu.classList.remove("show");
      }
    });
  }

  /* ============================
      ANIMAÇÃO DE CARDS
  ============================ */
  const cards = document.querySelectorAll(".card");

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) entry.target.classList.add("animado");
      });
    },
    { threshold: 0.1 }
  );

  cards.forEach((card) => observer.observe(card));

  /* ============================
      MODAL DE DETALHES DO EVENTO
  ============================ */
  const modal = document.getElementById("eventoModal");
  const modalContent = document.getElementById("modalContent");
  const closeModal = document.getElementById("closeModal");

  document.querySelectorAll(".btnDetalhes").forEach((btn) => {
    btn.addEventListener("click", () => {
      const dados = JSON.parse(btn.dataset.evento);

      modalContent.innerHTML = `
        <h2>${dados.nome}</h2>
        <img src="../uploads/${dados.imagem}" class="modal-img">
        <p><strong>Local:</strong> ${dados.local}</p>
        <p><strong>Data:</strong> ${dados.data} às ${dados.hora}</p>
        <p><strong>Status da Reserva:</strong> ${dados.status}</p>
      `;

      modal.classList.add("show");
    });
  });

  closeModal.addEventListener("click", () => {
    modal.classList.remove("show");
  });

  modal.addEventListener("click", (e) => {
    if (e.target === modal) modal.classList.remove("show");
  });

  /* ============================
      MODAL DE LOGOUT
  ============================ */
  const logoutBtn = document.getElementById("logoutBtn");
  const logoutModal = document.getElementById("logoutModal");
  const confirmLogout = document.getElementById("confirmLogout");
  const cancelLogout = document.getElementById("cancelLogout");

  if (logoutBtn) {
    logoutBtn.addEventListener("click", () => {
      logoutModal.style.display = "flex";
    });
  }

  confirmLogout?.addEventListener("click", () => {
    window.location.href = "../front/log.php";
  });

  cancelLogout?.addEventListener("click", () => {
    logoutModal.style.display = "none";
  });
});

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
