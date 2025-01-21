const BiggerThanDesignWidth = 1920;
const designWidth = 1440;
const desktop = 1440;
const tablet = 992;
const mobile = 600;
const sMobile = 375;
export function fixContainer() {
  const resizeHandler = function () {
    if (window.innerWidth >= BiggerThanDesignWidth) {
      document.documentElement.style.fontSize = `${10}px`;
      // document.documentElement.style.fontSize = `${9 * window.innerWidth / desktop}px`;
    } else if (window.innerWidth < BiggerThanDesignWidth && window.innerWidth >= desktop) {
      document.documentElement.style.fontSize = `${10 * window.innerWidth / desktop}px`;
    } else if (window.innerWidth < desktop && window.innerWidth >= tablet) {
      document.documentElement.style.fontSize = `${10 * window.innerWidth / designWidth}px`;
    } else if (window.innerWidth < tablet && window.innerWidth >= mobile) {
      document.documentElement.style.fontSize = `${10 * window.innerWidth / tablet}px`;
    } else if (window.innerWidth < mobile && window.innerWidth >= sMobile) {
      document.documentElement.style.fontSize = `${10}px`;
    } else {
      document.documentElement.style.fontSize = `${10 * window.innerWidth / sMobile}px`;
    }
  };
  resizeHandler();
  window.addEventListener('resize', resizeHandler);
}
