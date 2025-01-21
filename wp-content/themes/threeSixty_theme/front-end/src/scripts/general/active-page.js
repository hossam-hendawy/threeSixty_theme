export function activePage() {
  let headerLinks = document.querySelectorAll('header a');
  for (let headerLink of headerLinks) {
    headerLink.classList.remove('header-link-active');
    headerLink.classList.remove('header-sublink-active');
    if (headerLink.href === window.location.href) {
      if (headerLink.classList.contains('header-sublink')) {
        headerLink.classList.add('header-sublink-active');
        headerLink.closest('.menu-item')?.querySelector('.header-link')?.classList.add('header-link-active');
      } else {
        headerLink.classList.add('header-link-active');
      }
    }
  }
}
