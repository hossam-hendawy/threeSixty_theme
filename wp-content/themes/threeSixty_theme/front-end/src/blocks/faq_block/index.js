import './style.scss';
import {imageLazyLoading} from "../../scripts/functions/imageLazyLoading";
import {animations} from "../../scripts/general/animations";
import {accordion} from "../../scripts/general/accordion";

/**
 * @author DELL
 * @param block {HTMLElement}
 * @returns {Promise<void>}
 */
const faqBlock = async (block) => {

  // add block code here
  const accordion = block.querySelector(".accordion");

  accordion.addEventListener("click", (e) => {
    const activePanel = e.target.closest(".accordion-panel");
    if (!activePanel) return;
    toggleAccordion(activePanel);
  });

  function toggleAccordion(panelToActivate) {
    const activeButton = panelToActivate.querySelector("button");
    const activePanel = panelToActivate.querySelector(".accordion-content");
    const activePanelIsOpened = activeButton.getAttribute("aria-expanded");

    if (activePanelIsOpened === "true") {
      panelToActivate.querySelector("button").setAttribute("aria-expanded", false);
      panelToActivate.querySelector(".accordion-content").setAttribute("aria-hidden", true);
    } else {
      panelToActivate.querySelector("button").setAttribute("aria-expanded", true);
      panelToActivate.querySelector(".accordion-content").setAttribute("aria-hidden", false);
    }
  }

// testing the new hidden value

    animations(block);
    imageLazyLoading(block);
};

export default faqBlock;

