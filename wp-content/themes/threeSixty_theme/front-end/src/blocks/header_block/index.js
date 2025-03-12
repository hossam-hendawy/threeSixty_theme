import './style.scss';
import {gsap} from "gsap";
import {imageLazyLoading} from '../../scripts/functions/imageLazyLoading';
import {animations} from '../../scripts/general/animations';
import {ScrollTrigger} from "gsap/ScrollTrigger";
gsap.registerPlugin(ScrollTrigger);
/**
 *
 * @param header {HTMLElement}
 * @returns {Promise<void>}
 */
export default async (header) => {
  const body = document.querySelector('body');
  const megaMenus = header.querySelectorAll(".menu-item.has-mega-menu");
  const desktopMegaWrappers = header.querySelectorAll(".mega-menu-wrapper");
  const burgerMenu = header.querySelector('.burger-menu');
  const menuLinks = header.querySelector('.navbar');

  const megaMenuHeight = header.querySelector('.mega-menu-wrapper')?.getBoundingClientRect().height || 0;

  let lastScroll = 0;
  const scrollHandler = () => {
    const currentScroll = window.scrollY;
    header.classList.toggle('sticky', currentScroll >= 600);
    header.classList.toggle("hide", currentScroll >= 200 && currentScroll > lastScroll);

    // if (window.innerWidth > 992) {
    //   header.classList.toggle("hide", currentScroll >= 200 && currentScroll > lastScroll);
    // }

    if (currentScroll >= megaMenuHeight /2){
      desktopMegaWrappers.forEach(wrapper => {
        wrapper.classList.remove('active');
      });
      megaMenus.forEach(wrapper => {
        wrapper.classList.remove('active');
      });
    }


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
      document.documentElement.classList.remove('stop-scroll');
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
      document.documentElement.classList.add('stop-scroll');
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

  megaMenus.forEach((menuItem) => {
    menuItem.addEventListener("click", function (e) {
      e.stopPropagation();
      const isActive = this.classList.contains("active");
      megaMenus.forEach(item => item.classList.remove("active"));
      desktopMegaWrappers.forEach(wrapper => wrapper.classList.remove("active"));
      body.classList.remove("active");
      // document.documentElement.classList.remove('modal-opened');

      if (!isActive) {
        this.classList.add("active");

        const submenuIndex = this.getAttribute("data-submenu-index");
        const targetWrapper = header.querySelector(`.mega-menu-wrapper[data-index="${submenuIndex}"]`);

        if (targetWrapper) {
          targetWrapper.classList.add("active");
        }

        // body.classList.add("active");
        // document.documentElement.classList.add('modal-opened');
      }
    });
  });

  const backSteps = header.querySelectorAll(".back-step");
  backSteps.forEach(step => {
    step.addEventListener("click", () => {
      desktopMegaWrappers.forEach(wrapper => wrapper.classList.remove("active"));
      megaMenus.forEach(menuItem => menuItem.classList.remove("active"));
    });
  });

  document.addEventListener("click", (event) => {
    const isMenuClick = event.target.closest(".menu-item.has-mega-menu");
    const isMegaMenuClick = event.target.closest(".mega-menu-wrapper");
    document.documentElement.classList.remove('modal-opened');
    if (!isMenuClick && !isMegaMenuClick) {
      megaMenus.forEach(menuItem => menuItem.classList.remove("active"));
      desktopMegaWrappers.forEach(wrapper => wrapper.classList.remove("active"));
      // body.classList.remove("active");
    }
  });

  animations(header);
  imageLazyLoading(header);
};
