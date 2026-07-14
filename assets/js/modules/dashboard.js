(function () {
  const openButtons = document.querySelectorAll('[data-open-admin-modal]');
  if (!openButtons.length) return;

  openButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      const target = btn.getAttribute('data-open-admin-modal');
      const modal = document.getElementById(`sm-admin-modal-${target}`);
      if (modal?.showModal) {
        modal.showModal();
      }
    });
  });

  document.querySelectorAll('[data-close-admin-modal]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const modal = btn.closest('dialog');
      modal?.close();
    });
  });

  document.querySelectorAll('dialog.sm-modal-admin').forEach((modal) => {
    modal.addEventListener('click', (event) => {
      const rect = modal.getBoundingClientRect();
      const inside = (
        event.clientX >= rect.left
        && event.clientX <= rect.right
        && event.clientY >= rect.top
        && event.clientY <= rect.bottom
      );
      if (!inside) modal.close();
    });
  });
})();
