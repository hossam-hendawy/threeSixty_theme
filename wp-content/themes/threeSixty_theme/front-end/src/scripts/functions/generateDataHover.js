export function generateDataHover(container = document) {
  const hoverElements = container.querySelectorAll('[data-hover]');
  for (let hoverElement of hoverElements) {
    hoverElement.dataset.hover = hoverElement.textContent;
  }
}