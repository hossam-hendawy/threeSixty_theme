export  function updateSVGInPictures() {
  const pictures = document.querySelectorAll('picture');
  pictures.forEach((picture) => {
    const svgElement = picture.querySelector('svg');
    if (svgElement && !svgElement.hasAttribute('aria-hidden')) {
      svgElement.setAttribute('aria-hidden', 'true');
    }
  });
}
