import {gsap} from 'gsap';
import {ScrollTrigger} from 'gsap/ScrollTrigger';
import {getElementsForAnimation} from './getElementsForAnimation';

gsap.registerPlugin(ScrollTrigger);

export function imageLazyLoading(container) {
  const images = getElementsForAnimation(container, '[data-src],[data-srcset]');
  for (const image of images) {
    const _ = {};
    // placeholder image
    const imageParent = image.parentElement;
    const placeholder = image.nextElementSibling;
    if (!imageParent.classList.contains('aspect-ratio') && placeholder) {
      const imageWidth = image.getAttribute('width');
      const imageHeight = image.getAttribute('height');
      placeholder.style.width = imageWidth + 'px';
      placeholder.style.height = imageHeight + 'px';
    } else {
      placeholder?.classList.add('image-placeholder-aspect-ratio');
    }

    image.addEventListener('load', () => {
      // placeholder?.classList.add('image-placeholder-hidden');
      placeholder?.remove();
      ScrollTrigger.refresh(false);
    })
    const {lazyLoadTimeout, lazyLoadOffset, src, srcset} = image.dataset;
    const handler = () => {
      src && image.setAttribute('src', src);
      srcset && image.setAttribute('srcset', srcset);
      clearTimeout(_.timeout);
      _.scrollTrigger?.kill();
    }
    _.scrollTrigger = ScrollTrigger.create({
      trigger: image,
      start: `top ${100 + (+lazyLoadOffset || 100)}%`,
      end: `bottom -${(+lazyLoadOffset || 100)}%`,
      onRefresh({isActive}) {
        isActive && handler();
      },
      onUpdate({isActive}) {
        isActive && handler();
      }
    })
    if (lazyLoadTimeout) {
      _.timeout = setTimeout(handler, +lazyLoadTimeout)
    }
  }
}
