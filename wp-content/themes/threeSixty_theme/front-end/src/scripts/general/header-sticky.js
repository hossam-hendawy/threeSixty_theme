export function headerSticky(container = document) {
  // header sticky
  const header = document.querySelector('header');
  const headerTopPart = document.querySelector('header.threeSixty_theme_header .menu-top-menu-container').clientHeight;
  const headerHeight = document.querySelector('header.threeSixty_theme_header').clientHeight;
  document.documentElement.style.setProperty("--header-height", headerHeight + 'px');
  const top = window.pageYOffset || document.documentElement.scrollTop
  const fireSticky = function () {
    document.documentElement.style.setProperty("--header-top-part-height", (-headerTopPart / 10 + 'rem'));
    if (top >= 10) {
      header.classList.toggle('sticky');
    }
    header.classList.toggle('sticky', window.scrollY >= 10);
  };
  window.addEventListener('scroll', fireSticky);
  if (document.readyState === 'complete') {
    fireSticky();
  }
}
