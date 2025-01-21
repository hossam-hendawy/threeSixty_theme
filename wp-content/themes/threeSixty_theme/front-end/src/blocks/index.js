import {gsap} from 'gsap';
import {ScrollTrigger} from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);




export async function initBlocks(container) {

  const blocks = container.querySelectorAll('[data-section-class]');
  const preLoadedBlocks = document.location.pathname === "/"?6:6;

  async function loadRemainingBlocks() {
    window.removeEventListener('scroll', loadRemainingBlocks);
    for (let i = 0; i < blocks.length - preLoadedBlocks; i++) {
      const block = blocks[i + preLoadedBlocks];
      const {default: blockScript} = await import('./' + block.dataset.sectionClass)
      block.classList.add('js-loaded')
      try {
        await blockScript(block);
      } catch (e) {
        console.log(e);
      }
    }

    // footer
    ScrollTrigger.refresh(false);

    // setTimeout(updateShapes ,1000);

  }

  const footer = container.querySelector('footer');
  if (footer) {
    const {default: blockScript} = await import('./footer_block');
    try {
      await blockScript(footer);
    } catch (e) {
      console.log(e);
    }
    //
  }

  for (let i = 0; i < blocks.length; i++) {
    if (i < preLoadedBlocks && blocks[i].dataset.sectionClass !== 'hero_block') {
      const block = blocks[i];
      const {default: blockScript} = await import('./' + block.dataset.sectionClass)
      block.classList.add('js-loaded')
      try {
        await blockScript(block);
      } catch (e) {
        console.log(e);
      }
    }
  }
  // await loadRemainingBlocks();
  window.scrollY > (document.location.pathname === "/"?1:50) ? await loadRemainingBlocks() : window.addEventListener('scroll', loadRemainingBlocks);
}
