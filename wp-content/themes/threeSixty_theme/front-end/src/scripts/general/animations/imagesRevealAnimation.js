import {gsap} from 'gsap';
import {ScrollTrigger} from 'gsap/ScrollTrigger';
import {getElementsForAnimation} from "../../functions/getElementsForAnimation";


gsap.registerPlugin(ScrollTrigger);

export function createImageRevealTimeline(imageWrapper, direction){
  const clip = {
    xtl: 0,
    xtr: 0,
    xbl: 0,
    xbr: 0,
    ytl: 0,
    ytr: 0,
    ybl: 0,
    ybr: 0,
  }

  const createTransformOrigin = direction => {
    switch (direction) {
      case 'top':
        return 'center top';
      case 'bottom':
        return 'center bottom';
      case 'left':
        return 'left center';
      case 'right':
        return 'right center';
    }
  };

  const createTimeline = direction => {
    switch (direction) {
      case 'top':
        return gsap.timeline({defaults: {duration: 0.65}})
          .set(clip, {
            xtr: 100,
            xbr: 100,
          })
          .to(clip, {
            ybl: 100
          }, 0)
          .to(clip, {
            ybr: 100
          }, '<15%')
      case 'bottom':
        return gsap.timeline({defaults: {duration: 0.65}})
          .set(clip, {
            xtr: 100,
            xbr: 100,
            ytl: 100,
            ytr: 100,
            ybl: 100,
            ybr: 100,
          })
          .to(clip, {
            ytl: 0
          }, 0)
          .to(clip, {
            ytr: 0
          }, '<15%')
      case 'left':
        return gsap.timeline({defaults: {duration: 0.65}})
          .set(clip, {
            ybl: 100,
            ybr: 100,
          })
          .to(clip, {
            xtr: 100
          }, 0)
          .to(clip, {
            xbr: 100
          }, '<15%')
      case 'right':
        return gsap.timeline({defaults: {duration: 0.65}})
          .set(clip, {
            xtl: 100,
            xtr: 100,
            xbl: 100,
            xbr: 100,
            ybl: 100,
            ybr: 100,
          })
          .to(clip, {
            xtl: 0
          }, 0)
          .to(clip, {
            xbl: 0
          }, '<15%')
    }
  }


  return gsap.timeline({
    onUpdate() {
      imageWrapper.style.clipPath = `polygon(${clip.xtl}% ${clip.ytl}%, ${clip.xtr}% ${clip.ytr}%, ${clip.xbr}% ${clip.ybr}%, ${clip.xbl}% ${clip.ybl}%)`;
    }
  })
    .add(createTimeline(direction), 0)
    .fromTo(imageWrapper, {
      opacity: 0,
    }, {
      opacity: 1,
      duration: 0.65,
    }, 0)
    .fromTo(imageWrapper.querySelector('img'), {
      scale: 1.2,
      transformOrigin: createTransformOrigin(direction)
    }, {
      scale: 1,
      duration: 0.65,
    }, 0)

}


export function imageRevealAnimation(container = document, selector = '[data-reveal-direction]', directionData = 'revealDirection') {
  const imageWrappers = getElementsForAnimation(container, selector);
  for (const imageWrapper of imageWrappers) {
    ScrollTrigger.create({
      trigger: imageWrapper,
      start: 'top 75%',
      animation: createImageRevealTimeline(imageWrapper, imageWrapper.dataset[directionData])
    })
  }
}
