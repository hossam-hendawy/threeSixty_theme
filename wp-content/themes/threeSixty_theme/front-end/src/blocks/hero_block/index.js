import './style.scss';
import {imageLazyLoading} from '../../scripts/functions/imageLazyLoading';
import {animations} from '../../scripts/general/animations';
import {gsap} from 'gsap';
import {ScrollTrigger} from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);
const heroBlock = async (block) => {
  const innerCircleDimension = 13;
  const maxDimension = Math.max(window.innerWidth, window.innerHeight);
  const scaleFactor = maxDimension / innerCircleDimension * 1.2;
  const irisContainer = block.querySelector('.iris-container');
  const tesLogo = block.querySelector('.site-logo');
  const contentWrapper = block.querySelector('.hero-content-wrapper');
  const drone = block.querySelector('.drone');
  const mountains = block.querySelectorAll('.hero-cover-image');
  const scrollText = block.querySelectorAll('.scroll-text');
  const header = document.querySelector('header');

  gsap.timeline({
    delay: 3, defaults: {ease: 'none'}, scrollTrigger: {
      trigger: block,
      scrub: 1,
      start: 'top top',
      end: '600%',
      pin: true,
      onToggle({isActive, end}) {
        header.classList.toggle('fixed', isActive);
        header.style.top = (isActive ? 0 : end) + 'px';
      },
    },
  })
      .addLabel('seeFromIris')
      .to(scrollText, {opacity: 0, duration: 0.2})
      .to(irisContainer, {scale: scaleFactor, duration: 3, ease: 'power2.in'})
      .to(irisContainer.querySelector('.iris-shape'), {
        opacity: 0,
        duration: 2,
      }, 'seeFromIris+=1')
    // .to(irisContainer, {opacity: 0, duration: 1}, '<60%')
      .from(tesLogo, {y: () => window.innerHeight, duration: 2}, 1.5)
      .from(drone,
        {y: () => window.innerHeight, duration: 1, ease: 'power2.out'}, '<70%')
      .from(contentWrapper, {opacity: 0, duration: 1}, '<50%')
      .to(mountains, {filter: 'blur(8px)'}, 3)
      .to(mountains, {scale: 1.2, duration: 2.5}, 'seeFromIris')
      // .set({}, {}, '+=.5');

  setTimeout(() => window.scrollTo(0, 0), 500);

  animations(block);
  imageLazyLoading(block);
};

export default heroBlock;
