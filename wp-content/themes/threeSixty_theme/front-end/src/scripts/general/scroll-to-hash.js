export function scrollToHash(container = document) {
  let anchors = container.querySelectorAll('a[href^="#"]');
  for (let anchor of anchors) {
    try {
      const href = anchor.getAttribute('href');
      const target = href !== '#' && container.querySelector(href);
      if (!target) continue;
      const offset = 100;
      anchor?.addEventListener('click', (e) => {
        console.log(anchor)
        e.preventDefault();
        window.scrollTo({
          top: window.scrollY + target.getBoundingClientRect().top - offset,
          behavior: 'smooth'
        })
      })
    }catch (e){
      console.log(e);
    }
  }
}
