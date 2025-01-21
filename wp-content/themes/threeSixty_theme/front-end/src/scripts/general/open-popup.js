export function openPopup(container = document) {
  const customModal = document.querySelector("#custom-modal");
  const customModalContent = customModal?.querySelector(".custom-modal-content");

  // open video modal
  const buttonsOpenModal = container?.querySelectorAll(".open-popup");

  for (let openPopup of buttonsOpenModal) {
    openPopup.setAttribute('aria-haspopup', 'dialog');
    openPopup.setAttribute('aria-controls', 'custom-modal');
    openPopup.setAttribute('aria-expanded', 'false');
    openPopup?.addEventListener("click", function () {
      openPopup.scrollIntoView({ behavior: "smooth", block: "start" });
      document.documentElement.classList.add("popup-modal-opened");
      const modalTemplate = openPopup.nextElementSibling;
      if (modalTemplate) {
        let clone = modalTemplate.content.cloneNode(true);
        customModalContent.innerHTML = '';
        customModalContent.appendChild(clone);
        customModal.classList.add("modal-active");
        customModal.setAttribute('aria-hidden', 'false');
      }
    });
  }
}
