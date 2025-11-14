document.addEventListener('DOMContentLoaded', ()=>{
  const modal = document.getElementById('feedbackModal');
  const okBtn = document.getElementById('okBtn');

  if (window.__FEEDBACK && window.__FEEDBACK.mensagem) {
    const title = document.getElementById('modalTitle');
    if (title) title.textContent = window.__FEEDBACK.mensagem;
    modal.style.display = 'flex';
    okBtn.addEventListener('click', ()=>{
      modal.style.display = 'none';
      if (window.__FEEDBACK.sucesso) {
        // redirect to homepage
        window.location.href = 'index.php';
      }
    });
  }
});