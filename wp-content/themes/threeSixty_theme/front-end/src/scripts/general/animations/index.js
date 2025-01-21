import {parallaxAnimation} from './parallaxAnimation';
import {imageRevealAnimation} from './imagesRevealAnimation';
import {wordsUpAnimation} from './wordsUpAnimation';
import {linesUpAnimation} from './linesUpAnimation';
import {inViewAnimations} from './InViewAnimations';
import {realLinesUpAnimation} from './RealLinesUpAnimation';
import {spriteSheetAnimation} from "./spriteSheetAnimation";
import {drawDottedLineAnimation} from "./drawDottedLineAnimation";
import {gsap} from 'gsap';
import ScrollTrigger from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger)


export function animations(container = document) {
  imageRevealAnimation(container);
  wordsUpAnimation(container);
  linesUpAnimation(container);
  inViewAnimations(container);
  spriteSheetAnimation(container);
  parallaxAnimation(container);
  realLinesUpAnimation(container);
  drawDottedLineAnimation(container);
}
