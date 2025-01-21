/**
 * get all elements with selector in container element or array of elements
 * @param container {HTMLElement | HTMLElement[]}
 * @param selector {string}
 * @returns {NodeListOf<Element> | HTMLElement[]}
 */
export function getElementsForAnimation(container, selector) {
  if (Array.isArray(container)) {
    return container.map(el => {
      const a = Array.from(el.querySelectorAll(selector));
      if (el.matches(selector)) a.unshift(el);
      return a;
    }).flat();
  }
  return container.querySelectorAll(selector);
}
