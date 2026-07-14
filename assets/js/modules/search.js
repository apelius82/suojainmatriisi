/* Zone cascade: populate zone dropdown when site changes */
(function () {
  const form = document.getElementById('sm-search-form');
  const siteSelect = document.getElementById('sm-site-select');
  const zoneStep   = document.getElementById('sm-zone-step');
  const zoneSelect = document.getElementById('sm-zone-select');
  if (!siteSelect || !zoneStep || !zoneSelect || !form) return;

  async function loadZones(siteId) {
    if (!siteId || siteId === '0') {
      zoneStep.classList.add('sm-step-hidden');
      return;
    }
    try {
      const url = new URL('./app/api/zones_by_site.php', window.location.href);
      url.searchParams.set('site_id', siteId);
      const r = await fetch(url.toString(), { credentials: 'same-origin' });
      const data = await r.json();
      const zones = data.zones || [];
      const noZoneText = zoneSelect.querySelector('[value="0"]')?.textContent ?? '-';
      zoneSelect.innerHTML = '';
      const placeholder = document.createElement('option');
      placeholder.value = '0';
      placeholder.textContent = noZoneText;
      zoneSelect.appendChild(placeholder);
      zones.forEach((z) => {
        const opt = document.createElement('option');
        opt.value = z.id;
        opt.textContent = z.name;
        zoneSelect.appendChild(opt);
      });
      if (zones.length > 0) {
        zoneStep.classList.remove('sm-step-hidden');
      } else {
        zoneStep.classList.add('sm-step-hidden');
      }
    } catch (_) {
      zoneStep.classList.add('sm-step-hidden');
    }
  }

  siteSelect.addEventListener('change', () => {
    zoneSelect.value = '0';
    loadZones(siteSelect.value);
    form.requestSubmit();
  });

  /* Re-submit form on env change so server can filter site list */
  const envSelect = document.getElementById('sm-env-select');
  if (envSelect) {
    envSelect.addEventListener('change', () => {
      const s = form.querySelector('[name="site_id"]');
      const z = form.querySelector('[name="zone_id"]');
      if (s) s.value = '0';
      if (z) z.value = '0';
      form.requestSubmit();
    });
  }

  zoneSelect.addEventListener('change', () => form.requestSubmit());
})();

/* Task details modal */
(function () {
  const openers = document.querySelectorAll('[data-modal-open]');
  if (!openers.length) return;

  const closeModal = (dialog) => {
    if (dialog && typeof dialog.close === 'function' && dialog.open) {
      dialog.close();
    }
  };

  openers.forEach((btn) => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-modal-open');
      const dialog = id ? document.getElementById(id) : null;
      if (dialog && typeof dialog.showModal === 'function') {
        dialog.showModal();
      }
    });
  });

  document.querySelectorAll('.sm-modal [data-modal-close]').forEach((btn) => {
    btn.addEventListener('click', () => {
      closeModal(btn.closest('dialog'));
    });
  });

  document.querySelectorAll('.sm-modal').forEach((dialog) => {
    dialog.addEventListener('click', (event) => {
      const rect = dialog.getBoundingClientRect();
      const inside = event.clientX >= rect.left && event.clientX <= rect.right
        && event.clientY >= rect.top && event.clientY <= rect.bottom;
      if (!inside) closeModal(dialog);
    });
  });
})();
