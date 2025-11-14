document.addEventListener('DOMContentLoaded', ()=>{
  const modal = document.getElementById('feedbackModal');
  const okBtn = document.getElementById('okBtn');

  // Show modal if backend provided feedback
  if (window.__FEEDBACK && window.__FEEDBACK.mensagem) {
    const title = document.getElementById('modalTitle');
    if (title) title.textContent = window.__FEEDBACK.mensagem;
    modal.style.display = 'flex';
    // if success, after OK redirect to login
    okBtn.addEventListener('click', ()=>{
      modal.style.display = 'none';
      if (window.__FEEDBACK.sucesso) {
        window.location.href = 'log.php';
      }
    });
  }

  // Basic client-side validation example
  const form = document.getElementById('regForm');
  if(form){
    form.addEventListener('submit', (e)=>{
      const senha = form.querySelector('input[name=senha]').value;
      if (senha.length < 6) {
        e.preventDefault();
        const title = document.getElementById('modalTitle');
        if (title) title.textContent = 'A senha deve ter ao menos 6 caracteres.';
        modal.style.display = 'flex';
        okBtn.onclick = ()=> modal.style.display = 'none';
      }
    });
  }
});