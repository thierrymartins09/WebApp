document.addEventListener("DOMContentLoaded", () => {
  const editBtn = document.getElementById("editarBtn");
  const editModal = document.getElementById("editModal");
  const cancelEdit = document.getElementById("cancelEdit");
  const form = document.getElementById("editForm");

  editBtn.addEventListener("click", () => editModal.style.display = "flex");
  cancelEdit.addEventListener("click", () => editModal.style.display = "none");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    try {
      const response = await fetch("../back/update_perfil.php", {
        method: "POST",
        body: formData
      });
      const result = await response.json();
      alert(result.mensagem);
      if (result.status === "success") {
        window.location.reload();
      }
    } catch (error) {
      alert("Erro de conexão com o servidor.");
    }
  });
});
