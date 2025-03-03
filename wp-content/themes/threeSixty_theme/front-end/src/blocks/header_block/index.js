import './style.scss';
import {gsap} from "gsap";
import {imageLazyLoading} from '../../scripts/functions/imageLazyLoading';
import {animations} from '../../scripts/general/animations';
import {ScrollTrigger} from "gsap/ScrollTrigger";
gsap.registerPlugin(ScrollTrigger)
/**
 *
 * @param header {HTMLElement}
 * @returns {Promise<void>}
 */
export default async (header) => {
  const burgerMenu = header.querySelector('.burger-menu');
  const menuLinks = header.querySelector('.navbar');

  let lastScroll = 0;

  const scrollHandler = () => {
    const currentScroll = window.scrollY;
    header.classList.toggle('sticky', currentScroll >= 20);
    header.classList.toggle("hide", currentScroll >= 200 && currentScroll > lastScroll);
    // if (window.innerWidth > 992) {
    //   header.classList.toggle("hide", currentScroll >= 200 && currentScroll > lastScroll);
    // }
    lastScroll = currentScroll;
  };

  window.addEventListener("scroll", scrollHandler);


  if (!burgerMenu) return;
  const burgerTl = gsap.timeline({paused: true});
  const burgerSpans = burgerMenu.querySelectorAll('span');
  gsap.set(burgerSpans, {transformOrigin: 'center'});
  burgerTl
    .to(burgerSpans, {
      y: gsap.utils.wrap([`0.8rem`, 0, `-0.8rem`]),
      duration: 0.25,
    })
    .set(burgerSpans, {autoAlpha: gsap.utils.wrap([1, 0, 1])})
    .to(burgerSpans, {rotation: gsap.utils.wrap([45, 0, -45])})
    .set(burgerSpans, {rotation: gsap.utils.wrap([45, 0, 135])});
  burgerMenu.addEventListener('click', function () {
    if (burgerMenu.classList.contains('burger-menu-active')) {
      // region allow page scroll
      document.documentElement.classList.remove('modal-opened');
      // endregion allow page scroll
      burgerMenu.classList.remove('burger-menu-active');
      menuLinks.classList.remove('header-links-active');
      header.classList.remove('header-active');
      burgerTl.reverse();
    } else {
      burgerMenu.classList.add('burger-menu-active');
      menuLinks.classList.add('header-links-active');
      header.classList.add('header-active');
      burgerTl.play();
      // region prevent page scroll
      document.documentElement.classList.add('modal-opened');
      // endregion prevent page scroll
      gsap.fromTo(menuLinks.querySelectorAll('.menu-item , .mobile-cta'), {
        y: 30,
        autoAlpha: 0,
      }, {
        y: 0,
        autoAlpha: 1,
        stagger: .05,
        duration: .4,
        delay: .5,
      });
    }
  });



  header.querySelectorAll('a').forEach(anchor => {
    anchor.addEventListener('click', event => {
      if ((anchor.href === window.location.href || anchor.href === window.location.href + '#' || anchor.href === window.location.href.slice(0, -1))) {
        event.stopPropagation();
      }
    });
  });


  animations(header);
  imageLazyLoading(header);
};

