import {gsap} from 'gsap';
import {ScrollTrigger} from 'gsap/ScrollTrigger';
import {getElementsForAnimation} from '../../functions/getElementsForAnimation';

gsap.registerPlugin(ScrollTrigger);


export function inViewAnimations(container = document) {
  const elementsHorizontal = getElementsForAnimation(container, '.iv-st-from-left, .iv-st-from-right');
  ScrollTrigger.batch(elementsHorizontal, {
    onEnter: batch => {
      gsap.fromTo(batch, {
        autoAlpha: 0,
        xPercent: (_, target) => 100 * (target.classList.contains('iv-st-from-right') ? 1 : -1),
      }, {
        duration: .7,
        autoAlpha: 1,
        xPercent: 0,
        ease: 'power1.out',
        stagger: .1,
        clearProps: 'transform',
      });
    },
    onEnterBack: batch => {
      gsap.fromTo(batch, {
        autoAlpha: 0,
        xPercent: (_, target) => 100 * (target.classList.contains('iv-st-from-right') ? 1 : -1),
      }, {
        duration: .7,
        autoAlpha: 1,
        xPercent: 0,
        ease: 'power1.out',
        stagger: .1,
        clearProps: 'transform',
      });
    },
    start: 'top 90%',
    once: true,
  });
  const elementsVertical = getElementsForAnimation(container, '.iv-st-from-top, .iv-st-from-bottom');
  ScrollTrigger.batch(elementsVertical, {
    onEnter: batch => {
      gsap.fromTo(batch, {
        autoAlpha: 0,
        y: (_, target) => 80 * (target.classList.contains('iv-st-from-bottom') ? 1 : -1),
      }, {
        duration: .7,
        autoAlpha: 1,
        y: 0,
        ease: 'power1.out',
        stagger: .1,
        clearProps: 'transform',
      });
    },
    onEnterBack: batch => {
      gsap.fromTo(batch, {
        autoAlpha: 0,
        y: (_, target) => 80 * (target.classList.contains('iv-st-from-bottom') ? 1 : -1),
      }, {
        duration: .7,
        autoAlpha: 1,
        yPercent: 0,
        ease: 'power1.out',
        stagger: .1,
        clearProps: 'transform',
      });
    },
    start: 'top 90%',
    once: true,
  });
  const elementsHorizontalFast = getElementsForAnimation(container, '.iv-st-from-left-f, .iv-st-from-right-f');
  ScrollTrigger.batch(elementsHorizontalFast, {
    onEnter: batch => {
      gsap.fromTo(batch, {
        autoAlpha: 0,
        xPercent: (_, target) => 100 * (target.classList.contains('iv-st-from-right-f') ? 1 : -1),
      }, {
        duration: .4,
        autoAlpha: 1,
        xPercent: 0,
        ease: 'power2.out',
        stagger: .1,
        clearProps: 'transform',
      });
    },
    onEnterBack: batch => {
      gsap.fromTo(batch, {
        autoAlpha: 0,
        xPercent: (_, target) => 100 * (target.classList.contains('iv-st-from-right-f') ? 1 : -1),
      }, {
        duration: .4,
        autoAlpha: 1,
        xPercent: 0,
        ease: 'power2.out',
        stagger: .1,
        clearProps: 'transform',
      });
    },
    start: 'top 80%',
    once: true,
  });
  const elementsVerticalFast = getElementsForAnimation(container, '.iv-st-from-top-f, .iv-st-from-bottom-f');
  ScrollTrigger.batch(elementsVerticalFast, {
    onEnter: batch => {
      gsap.fromTo(batch, {
        autoAlpha: 0,
        y: (_, target) => 80 * (target.classList.contains('iv-st-from-bottom-f') ? 1 : -1),
      }, {
        duration: .4,
        autoAlpha: 1,
        y: 0,
        ease: 'power2.out',
        stagger: .1,
        clearProps: 'transform',
      });
    },
    onEnterBack: batch => {
      gsap.fromTo(batch, {
        autoAlpha: 0,
        y: (_, target) => 80 * (target.classList.contains('iv-st-from-bottom-f') ? 1 : -1),
      }, {
        duration: .4,
        autoAlpha: 1,
        yPercent: 0,
        ease: 'power2.out',
        stagger: .1,
        clearProps: 'transform',
      });
    },
    start: 'top 80%',
    once: true,
  });
  const elementsZoom = getElementsForAnimation(container, '.iv-st-zoom');
  ScrollTrigger.batch(elementsZoom, {
    onEnter: batch => {
      gsap.fromTo(batch, {
        autoAlpha: 0,
        scale: 0,
        transformOrigin: '50% 50%'
      }, {
        duration: .4,
        autoAlpha: 1,
        scale: 1,
        ease: 'power2.out',
        stagger: .1,
        clearProps: 'transform',
      });
    },
    onEnterBack: batch => {
      gsap.fromTo(batch, {
        autoAlpha: 0,
        scale: 0,
        transformOrigin: '50% 50%'
      }, {
        duration: .4,
        autoAlpha: 1,
        scale: 1,
        ease: 'power2.out',
        stagger: .1,
        clearProps: 'transform',
      });
    },
    start: 'top 80%',
    once: true,
  });
  const elementsGradient = getElementsForAnimation(container, '.gradient-animation');
  ScrollTrigger.batch(elementsGradient, {
    onEnter: batch => {
      gsap.to(batch, {backgroundPosition: '100%', duration: 0.3})
    },
    start: 'top 60%',
    once: true,
  });
}

ScrollTrigger.addEventListener('refreshInit', () => {
  gsap.set('.iv-st-from-top, .iv-st-from-bottom, .iv-st-from-top-f, .iv-st-from-bottom-f', {
    yPercent: 0,
    y: 0
  })
  gsap.set('.iv-st-zoom', {scale: 1})
});
