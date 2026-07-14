const input = document.getElementById('worker-search');
const list = document.getElementById('worker-options');
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
