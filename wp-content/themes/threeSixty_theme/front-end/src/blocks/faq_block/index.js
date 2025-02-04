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

  const accordion = block.querySelector(".accordion");

  const firstPanel = accordion.querySelector(".accordion-panel");
  if (firstPanel) {
    toggleAccordion(firstPanel, true);
  }

  accordion.addEventListener("click", (e) => {
    const activePanel = e.target.closest(".accordion-panel");
    if (!activePanel) return;
    toggleAccordion(activePanel);
  });

  function toggleAccordion(panelToActivate, forceOpen = false) {
    const activeButton = panelToActivate.querySelector("button");
    const activePanel = panelToActivate.querySelector(".accordion-content");
    const activePanelIsOpened = activeButton.getAttribute("aria-expanded");

    if (activePanelIsOpened === "true" && !forceOpen) {
      activeButton.setAttribute("aria-expanded", false);
      activePanel.setAttribute("aria-hidden", true);
    } else {
      activeButton.setAttribute("aria-expanded", true);
      activePanel.setAttribute("aria-hidden", false);
    }
  }


    animations(block);
    imageLazyLoading(block);
};

export default faqBlock;

