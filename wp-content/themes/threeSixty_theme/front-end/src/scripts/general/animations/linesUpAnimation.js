import {gsap} from 'gsap';
import {ScrollTrigger} from 'gsap/ScrollTrigger';
import {SplitText} from 'gsap/SplitText';
import {getElementsForAnimation} from '../../functions/getElementsForAnimation';

gsap.registerPlugin(ScrollTrigger);
gsap.registerPlugin(SplitText);

export function linesUpAnimation(container = document, selector = '.line-up') {
  const linesToSplit = getElementsForAnimation(container, selector);
  if (!linesToSplit) return;
  new SplitText(linesToSplit, {
    type: 'words',
    wordsClass: 'word-overflow',
  });
  let splitLines = new SplitText(linesToSplit, {
    type: 'words',
    wordsClass: 'child-word',
  });
  gsap.set(splitLines.words, {yPercent: 130});
  ScrollTrigger.batch(splitLines.words, {
    onEnter: batch => gsap.timeline().to(batch, {
      yPercent: 0, duration: 1, stagger: {
        from: 'start',
        axis: 'y',
        amount: 2.5,
      },
    })
        .set(batch.map(e => e.parentElement), {overflow: 'visible'}, '-=.2'),
    onEnterBack: batch => gsap.timeline().to(batch, {
      yPercent: 0, duration: 1, stagger: {
        from: 'start',
        axis: 'y',
        amount: 2.5,
      },
    })
        .set(batch.map(e => e.parentElement), {overflow: 'visible'}, '-=.2'),
    start: 'top 80%',
    once: true,
  });
}