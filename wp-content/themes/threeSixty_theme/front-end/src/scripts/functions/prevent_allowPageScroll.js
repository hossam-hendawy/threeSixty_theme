export function allowPageScroll() {
  document.documentElement.classList.remove('modal-opened');
}

export function preventPageScroll() {
  document.documentElement.classList.add('modal-opened');
}
