export function initModal() {
  const customModalEl = document.querySelector('#custom-modal');
  const detailingPackageBlock = document.querySelector('.missing-params');

  if (customModalEl && !detailingPackageBlock) {
    const customModalInner = customModalEl.querySelector('.custom-modal-inner');
    const customModalContent = customModalInner.querySelector('.custom-modal-content');

    const closeModal = (e) => {
      document.documentElement.classList.remove("popup-modal-opened");
      customModalEl.classList.remove('modal-active');
      setTimeout(() => {
        customModalContent.innerHTML = '';
        customModalEl.className = 'custom-modal';
      }, 300)

    }

    const keyHandler = (e) => {
      if (e.key === 'Escape') {
        closeModal();
      }
    }
    window.addEventListener('keydown', keyHandler)
    customModalEl?.addEventListener('click', closeModal);
    customModalInner?.addEventListener('click', e => e.stopPropagation());
  }
}
