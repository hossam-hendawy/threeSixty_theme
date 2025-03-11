import './style.scss';
import {imageLazyLoading} from "../../scripts/functions/imageLazyLoading";
import {animations} from "../../scripts/general/animations";
/**
 * @author DELL
 * @param block {HTMLElement}
 * @returns {Promise<void>}
 */
const mabBlock = async (block) => {

  // add block code here
  block.querySelectorAll('.hotspot-circle').forEach(circle => {
    circle.addEventListener('click', function () {
      const hiddenContent = this.querySelector('.hidden-content');
      hiddenContent.classList.toggle('show');
    });
  });


// testing the new hidden value

    animations(block);
    imageLazyLoading(block);
};

export default mabBlock;

