import {gsap} from 'gsap';
import {ScrollTrigger} from 'gsap/ScrollTrigger';
import {wrap} from "../../functions/wrap_unwrap";
import {getElementsForAnimation} from "../../functions/getElementsForAnimation";

gsap.registerPlugin(ScrollTrigger);

const spriteSheetsCleaningCallback = [];

export function spriteSheetAnimation(container = document) {
  
  while (spriteSheetsCleaningCallback.length) {
    spriteSheetsCleaningCallback.pop()();
  }
  
  const spriteSheets = getElementsForAnimation(container, 'img.sprite-sheet');
  
  for (const spriteSheet of spriteSheets) {
    
    // data for animation
    const {
      frames,
      frame_x,
      frame_y,
      width = '100%',
      loop_start_index = 0,
      duration = 2,
      initial_duration = 0,
      repeat = -1,
      repeatDelay = 0
    } = spriteSheet.dataset;
    let checkImageDimensions, interval, initialInterval, repeatDelaySetTimeout,
        currentIndex = 0,
        remainingRepeat = repeat,
        stopInterval = false,
        gridSize = {x: 0, y: 0};
    
    
    spriteSheet.classList.remove('sprite-sheet');
    spriteSheet.classList.add('sprite-sheet__img');
    const parent = document.createElement('DIV');
    parent.classList.add('sprite-sheet__parent');
    parent.style.width = width;
    const aspectRatio = document.createElement('DIV');
    aspectRatio.classList.add('sprite-sheet__aspect-ratio');
    wrap(spriteSheet, parent);
    wrap(spriteSheet, aspectRatio);
    const nextFrame = () => {
      currentIndex++;
      spriteSheet.style.transform = `translate(${((currentIndex % frames) % (gridSize.x)) / -gridSize.x * 100}%,${((~~((currentIndex % frames) / gridSize.x)) % (gridSize.y)) / -gridSize.y * 100}%)`;
    }
    
    new Promise(resolve => {
      checkImageDimensions = setInterval(function () {
        if (spriteSheet.naturalWidth) {
          clearInterval(checkImageDimensions);
          resolve()
        }
      }, 10);
    })
        .then(() => {
          gridSize.x = spriteSheet.naturalWidth / frame_x;
          gridSize.y = spriteSheet.naturalHeight / frame_y;
          spriteSheet.style.width = 100 * gridSize.x + '%';
          spriteSheet.style.height = 100 * gridSize.y + '%';
          aspectRatio.style.paddingTop = frame_y / frame_x * 100 + '%'
        })
    
    
    Promise.all([
      new Promise(resolve => spriteSheet.complete ? resolve() : (spriteSheet.onload = resolve)),
      new Promise(resolve => ScrollTrigger.create({
        trigger: spriteSheet.parentNode,
        onEnter: resolve,
        onEnterBack: resolve,
        start: 'center 80%',
        once: true,
      })),
    ]).then(() => {
      
      const createInterval = () => {
        let stop = false;
        return setInterval(
            () => {
              if (stop) return;
              nextFrame();
              
              if (currentIndex + 1 >= frames) {
                stop = true;
                clearInterval(interval);
                if (remainingRepeat !== 0) {
                  repeatDelaySetTimeout = setTimeout(() => {
                    currentIndex = loop_start_index - 1;
                    remainingRepeat--;
                    interval = createInterval();
                  }, repeatDelay * 1000);
                }
              }
            },
            duration * 1000 / (frames - loop_start_index),
        );
      };
      if (loop_start_index !== 0) {
        initialInterval = setInterval(
            () => {
              if (stopInterval) return;
              nextFrame()
              if (currentIndex + 1 >= loop_start_index) {
                stopInterval = true;
                clearInterval(initialInterval);
              }
            },
            initial_duration * 1000 / loop_start_index,
        );
      }
      setTimeout(() => interval = createInterval(), loop_start_index ? initial_duration : 0);
    })
    spriteSheetsCleaningCallback.push(() => {
      spriteSheet.onload = null;
      clearInterval(checkImageDimensions);
      clearInterval(initialInterval);
      clearInterval(interval);
      clearTimeout(repeatDelaySetTimeout);
    })
  }
  
}
