import {gsap} from 'gsap';
import {ScrollTrigger} from 'gsap/ScrollTrigger';
import {SplitText} from 'gsap/SplitText';
import {getElementsForAnimation} from '../../functions/getElementsForAnimation';

gsap.registerPlugin(ScrollTrigger);
gsap.registerPlugin(SplitText);

export function wordsUpAnimation(container = document, selector = '.word-up') {
  const wordsToSplit = getElementsForAnimation(container, selector);
  if (!wordsToSplit) return;
  new SplitText(wordsToSplit, {
    type: 'words',
    wordsClass: 'word-overflow',
  });
  let splitWords = new SplitText(wordsToSplit, {
    type: 'words',
    wordsClass: 'child-word',
  });
  gsap.set(splitWords.words, {yPercent: 100});
  ScrollTrigger.batch(splitWords.words, {
    onEnter: batch => gsap.timeline()
        .to(batch, {yPercent: 0, duration: 0.35, stagger: 0.05})
        .set(batch.map(e => e.parentElement), {overflow: 'visible'}, '-=.2'),
    onEnterBack: batch => gsap.timeline()
        .to(batch, {yPercent: 0, duration: 0.35, stagger: 0.05})
        .set(batch.map(e => e.parentElement), {overflow: 'visible'}, '-=.2'),
    start: 'top 80%',
    batchMax: 15,
    once: true,
  });
  gsap.set(wordsToSplit, {autoAlpha: 1});
}