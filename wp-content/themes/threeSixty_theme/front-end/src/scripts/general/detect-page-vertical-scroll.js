export function detectPageVerticalScroll() {
  if (!(window.innerWidth - document.documentElement.clientWidth) > 0) {
    document.documentElement.classList.add('page-has-no-scroll');
  }
}
