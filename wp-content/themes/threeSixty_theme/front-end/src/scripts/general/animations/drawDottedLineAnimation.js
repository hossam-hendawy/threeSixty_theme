import {gsap} from 'gsap';
import {ScrollTrigger} from 'gsap/ScrollTrigger';
import {DrawSVGPlugin} from 'gsap/DrawSVGPlugin';
import {getElementsForAnimation} from '../../functions/getElementsForAnimation';

gsap.registerPlugin(ScrollTrigger, DrawSVGPlugin);


export function drawDottedLineAnimation(container = document, selector = 'animation-i-e') {
  const dottedLines = getElementsForAnimation(container, '.dots-'+selector);
  const circles = getElementsForAnimation(container, '.circle-'+selector);
  for (let dottedLine of dottedLines) {
    gsap.fromTo(dottedLine, {
      drawSVG: "0% 0%"
    }, {
      drawSVG: "0% 100%",
      delay: 1,
      duration: 4,
      ease: "linear",
      scrollTrigger: {
        trigger: dottedLine,
        start: "bottom 90%",
        end: "+=300px",
        scrub: .5
      }
    })
  }

  for (let circle of circles) {
    gsap.fromTo(circle, {
      scale: 0,
      transformOrigin: "center"
    }, {
      scale: 1,
      transformOrigin: "center",
      stagger: .1,
      scrollTrigger: {
        trigger: circle,
        start: "center 60%",
        end: "+=150px",
        scrub: .5
      }
    })
  }
}
