import './style.scss';
import {imageLazyLoading} from "../../scripts/functions/imageLazyLoading";
import {animations} from "../../scripts/general/animations";
import {heroAnimation} from "../../scripts/general/heroAnimation";


/**
 * @author DELL
 * @param block {HTMLElement}
 * @returns {Promise<void>}
 */
const aboutUsBlock = async (block) => {

  heroAnimation(block, ".about_us_block:after");

  animations(block);
  imageLazyLoading(block);
};

export default aboutUsBlock;

