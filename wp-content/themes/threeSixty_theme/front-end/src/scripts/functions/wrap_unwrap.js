export function wrap(el, wrapper) {
  el.parentNode.insertBefore(wrapper, el);
  wrapper.appendChild(el);
}

export function unwrap(el, wrapper) {
  wrapper.parentNode.insertBefore(el, wrapper);
  wrapper.parentNode.removeChild(wrapper);
}