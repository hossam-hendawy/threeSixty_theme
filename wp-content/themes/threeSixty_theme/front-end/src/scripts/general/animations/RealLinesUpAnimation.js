import {gsap} from 'gsap';
import {ScrollTrigger} from 'gsap/ScrollTrigger';
import {SplitText} from 'gsap/SplitText';
import {getElementsForAnimation} from "../../functions/getElementsForAnimation";
import {debounce} from "../../functions/debounce";
import {wrap} from "../../functions/wrap_unwrap";

gsap.registerPlugin(ScrollTrigger);
gsap.registerPlugin(SplitText);

export function realLinesUpAnimation(container = document, selector = '.real-line-up') {
  const realLinesToSplitTemp = getElementsForAnimation(container, selector);
  if (!realLinesToSplitTemp) return;
  const realLinesToSplit = [];
  for (const realLinesToSplitTempElement of realLinesToSplitTemp) {
    realLinesToSplitTempElement.innerHTML = realLinesToSplitTempElement.innerHTML.split('<p>&nbsp;</p>').join('<br/>')
    if (realLinesToSplitTempElement.children.length) {
      for (const child of realLinesToSplitTempElement.children) {
        if (child.nodeName === 'BR' || child.nodeName === 'SVG') {
          continue;
        }
        realLinesToSplit.push(child);
      }
    } else {
      realLinesToSplit.push(realLinesToSplitTempElement);
    }
  }
  
  let splitRealLines;
  
  prepareLines();
  
  window.addEventListener('resize', debounce(function () {
    
    gsap.set(splitRealLines.lines, {yPercent: 0});
    splitRealLines.revert();
    prepareLines();
  }, 500));
  
  
  function prepareLines() {
    splitRealLines = new SplitText(realLinesToSplit, {
      type: 'lines',
      linesClass: 'child-line',
      reduceWhiteSpace: false,
    });
    
    splitRealLines.lines.forEach(line => {
      let splitRealLineParent = document.createElement('div');
      splitRealLineParent.classList.add('line-overflow');
      wrap(line, splitRealLineParent);
    });
    gsap.set(splitRealLines.lines, {yPercent: 110});
    
    ScrollTrigger.batch(splitRealLines.lines, {
      onEnter: batch => gsap.timeline()
          .to(batch, {yPercent: 0, duration: 0.35, stagger: 0.05})
          .set(batch.map(e => e.parentElement), {overflow: 'visible'}, '-=.2'),
      onEnterBack: batch => gsap.timeline()
          .to(batch, {yPercent: 0, duration: 0.35, stagger: 0.05})
          .set(batch.map(e => e.parentElement), {overflow: 'visible'}, '-=.2'),
      start: 'top 80%',
      once: true,
    });
  }
  
  gsap.set(realLinesToSplitTemp, {autoAlpha: 1});
  
}