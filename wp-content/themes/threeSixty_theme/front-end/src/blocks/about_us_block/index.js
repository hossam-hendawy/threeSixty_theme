import './style.scss';
import {imageLazyLoading} from "../../scripts/functions/imageLazyLoading";
import {animations} from "../../scripts/general/animations";
/**
 * @author DELL
 * @param block {HTMLElement}
 * @returns {Promise<void>}
 */
const aboutUsBlock = async (block) => {

  // add block code here
// testing the new hidden value 

    animations(block);
    imageLazyLoading(block);
};

export default aboutUsBlock;

