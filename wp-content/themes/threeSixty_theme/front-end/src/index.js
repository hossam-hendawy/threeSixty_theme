import './styles/style.scss';
import {gsap} from 'gsap';
import ScrollTrigger from 'gsap/ScrollTrigger';
import header from './blocks/header_block';
import hero from './blocks/hero_block';
import {initBlocks} from './blocks';
import {getHeightOfViewPort} from './scripts/functions/getHeightOfViewPort';
import {generateDataHover} from './scripts/functions/generateDataHover';
import {copyToClipboard} from "./scripts/general/CopyToClipboard";
import {initModal} from "./scripts/general/custom-modal";
import {openPopup} from "./scripts/general/open-popup";
import {scrollToHash} from './scripts/general/scroll-to-hash';
import {activePage} from "./scripts/general/active-page";
import {updateSVGInPictures} from "./scripts/general/updateSVGInPictures";

const reInvokableFunction = async (container = document) => {
  container.querySelector('header') && await header(container.querySelector('header'));
  container.querySelector('.hero_block') && await hero(container.querySelector('.hero_block'));
  await initBlocks(container);
  generateDataHover(container);
  // scrollToHash(container);
  getHeightOfViewPort(container);
  activePage(container);
  initModal()
  openPopup(container);
  copyToClipboard(container);
  updateSVGInPictures();
  ScrollTrigger.refresh(false);

};
let loaded = false;

async function onLoad() {
  gsap.config({
    nullTargetWarn: false,
  });
  if (document.readyState === 'interactive' && !loaded) {
    loaded = true;
    gsap.registerPlugin(ScrollTrigger);
    await reInvokableFunction();
    document.body.classList.add('loaded');
    //region force page to scroll to element if url has hash
    let currentURL = window.location.href;
    let blockID = currentURL.split('#');
    if (blockID && blockID[1]) {
      let block = document.querySelector('#' + blockID[1]);
      if (block) {
        document.body.classList.remove('loaded');
        setTimeout(() => {
          window.scrollTo(0, (block.getBoundingClientRect().top + window.scrollY - window.headerSticky ? window.headerSticky : 93))
          document.body.classList.add('loaded');
        }, 400)
      }
    }
    //endregion force page to scroll to element if url has hash
  }
}

onLoad();

document.onreadystatechange = function () {
  onLoad();
};
