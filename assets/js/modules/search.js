/* Worker autocomplete */
const input = document.getElementById('worker-search');
const list  = document.getElementById('worker-options');
if (input && list) {
  const site = document.querySelector('[name="site_id"]');
  const task = document.querySelector('[name="task_id"]');
  const update = async () => {
    const url = new URL('./app/api/search_workers.php', window.location.href);
    url.searchParams.set('q', input.value || '');
    url.searchParams.set('site_id', site?.value || '0');
    url.searchParams.set('task_id', task?.value || '0');
    const r = await fetch(url.toString(), { credentials: 'same-origin' });
    const data = await r.json();
    list.innerHTML = '';
    (data.workers || []).forEach((w) => {
      const o = document.createElement('option');
      o.value = w.full_name;
      list.appendChild(o);
    });
  };
  input.addEventListener('input', update);
  site?.addEventListener('change', update);
  task?.addEventListener('change', update);
}

/* Zone cascade: populate zone dropdown when site changes */
(function () {
  const siteSelect = document.getElementById('sm-site-select');
  const zoneStep   = document.getElementById('sm-zone-step');
  const zoneSelect = document.getElementById('sm-zone-select');
  if (!siteSelect || !zoneStep || !zoneSelect) return;

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
  });

  /* Re-submit form on env change so server can filter site list */
  const envSelect = document.getElementById('sm-env-select');
  if (envSelect) {
    envSelect.addEventListener('change', () => {
      const form = envSelect.closest('form');
      if (form) {
        const s = form.querySelector('[name="site_id"]');
        const z = form.querySelector('[name="zone_id"]');
        const t = form.querySelector('[name="task_id"]');
        if (s) s.value = '0';
        if (z) z.value = '0';
        if (t) t.value = '0';
        form.submit();
      }
    });
  }
})();

