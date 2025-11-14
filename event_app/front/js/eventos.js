// eventos.js - controla mapa, seleção de lat/lng, reservas via AJAX e interações
document.addEventListener('DOMContentLoaded', () => {
  // Map init will run after Google Maps script loads
  if (typeof google !== 'undefined') {
    initMap();
  } else {
    window.initMap = initMap; // fallback if async
  }

  // Reserve buttons
  document.querySelectorAll('.reserveBtn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const id = btn.dataset.id;
      if (!confirm('Confirmar reserva para este evento?')) return;
      try {
        const form = new FormData();
        form.append('action', 'reserve');
        form.append('id_evento', id);
        const resp = await fetch('eventos.php', { method: 'POST', body: form });
        const json = await resp.json();
        alert(json.mensagem);
        if (json.status === 'success') {
          // opcional: atualizar contadores / UI
        }
      } catch (err) {
        alert('Erro ao reservar. Tente novamente.');
      }
    });
  });

  // Locate on map buttons
  document.querySelectorAll('.locateBtn').forEach(btn => {
    btn.addEventListener('click', () => {
      const lat = parseFloat(btn.dataset.lat) || 0;
      const lng = parseFloat(btn.dataset.lng) || 0;
      if (lat === 0 && lng === 0) {
        alert('Localização não disponível para este evento.');
        return;
      }
      if (window._map && window._markers) {
        // move center and bounce marker
        window._map.setCenter({ lat, lng });
        window._map.setZoom(15);
        // highlight marker if exists
        const found = window._markers.find(m => m._id === `${lat}_${lng}`);
        if (found) {
          found.setAnimation(google.maps.Animation.BOUNCE);
          setTimeout(() => found.setAnimation(null), 800);
        }
      }
    });
  });

  // Map click fills inputs
  const latInput = document.getElementById('latitude');
  const lngInput = document.getElementById('longitude');

  // If form present, intercept submit to show simple client validation
  const createForm = document.getElementById('createEventForm');
  if (createForm) {
    createForm.addEventListener('submit', (e) => {
      // require lat/lng selected
      if (!latInput.value || !lngInput.value) {
        e.preventDefault();
        alert('Selecione a localização do evento clicando no mapa.');
      }
    });
  }
});

// initMap: centered in São José dos Campos
function initMap() {
  const sjc = { lat: -23.1896, lng: -45.8869 };
  const map = new google.maps.Map(document.getElementById('map'), {
    center: sjc,
    zoom: 12,
  });
  window._map = map;
  window._markers = [];

  // Add click listener to set lat/lng inputs
  map.addListener('click', (e) => {
    const lat = e.latLng.lat();
    const lng = e.latLng.lng();
    const latEl = document.getElementById('latitude');
    const lngEl = document.getElementById('longitude');
    if (latEl) latEl.value = lat.toFixed(6);
    if (lngEl) lngEl.value = lng.toFixed(6);

    // place a temporary marker
    if (window._tempMarker) window._tempMarker.setMap(null);
    window._tempMarker = new google.maps.Marker({
      position: { lat, lng },
      map: map,
      title: 'Local do evento (selecionado)',
      animation: google.maps.Animation.DROP
    });
  });

  // Read server-side events embedded in page
  const eventsJson = (() => {
    try {
      // On the PHP page we will output a JS variable `window.__SERVER_EVENTS`
      return window.__SERVER_EVENTS || [];
    } catch (e) { return []; }
  })();

  // Add markers for each event
  eventsJson.forEach(ev => {
    const lat = parseFloat(ev.latitude) || 0;
    const lng = parseFloat(ev.longitude) || 0;
    if (!lat || !lng) return;
    const pos = { lat, lng };
    const marker = new google.maps.Marker({
      position: pos,
      map: map,
      title: ev.nome
    });
    // custom id for easy lookup
    marker._id = `${ev.latitude}_${ev.longitude}`;
    const info = new google.maps.InfoWindow({
      content: `<div style="min-width:180px"><strong>${escapeHtml(ev.nome)}</strong><p style="font-size:13px">${escapeHtml(ev.local)}<br>${ev.data} ${ev.hora}</p></div>`
    });
    marker.addListener('click', () => info.open(map, marker));
    window._markers.push(marker);
  });
}

// small helper to escape
function escapeHtml(s){ return String(s).replace(/[&<>"'`=\/]/g, function(ch){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#47;','`':'&#96;','=':'&#61;'}[ch]; });}
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
