import './style.scss';
import {imageLazyLoading} from "../../scripts/functions/imageLazyLoading";
import {animations} from "../../scripts/general/animations";
/**
 * @author HOSSAM
 * @param block {HTMLElement}
 * @returns {Promise<void>}
 */
const pageNotFound = async (block) => {

  // add block code here
// testing the new hidden value 

    animations(block);
    imageLazyLoading(block);
};

export default pageNotFound;

