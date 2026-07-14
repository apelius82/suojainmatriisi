export function initModalDialogs(selector = '.sm-modal') {
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

  document.querySelectorAll(`${selector} [data-modal-close]`).forEach((btn) => {
    btn.addEventListener('click', () => closeModal(btn.closest('dialog')));
  });

  document.querySelectorAll(selector).forEach((dialog) => {
    dialog.addEventListener('click', (event) => {
      if (event.target === dialog) {
        closeModal(dialog);
      }
    });
  });
}
