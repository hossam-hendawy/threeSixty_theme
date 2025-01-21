import './style.scss';
import { imageLazyLoading } from "../../scripts/functions/imageLazyLoading";
import { animations } from "../../scripts/general/animations";

const faqBlock = async (block) => {

  let currentAccordionListener = null;

  // region accordion
  function addAccordionListener(contentBlock) {
    const accordion = contentBlock.querySelector(".accordion");

    if (!accordion) return;

    const handleAccordionClick = (e) => {
      const activePanel = e.target.closest(".accordion-panel");
      if (!activePanel) return;
      toggleAccordion(activePanel, contentBlock);
    };

    accordion.addEventListener("click", handleAccordionClick);
    currentAccordionListener = { accordion, handleAccordionClick };
  }

  function removeAccordionListener() {
    if (currentAccordionListener) {
      const { accordion, handleAccordionClick } = currentAccordionListener;
      accordion.removeEventListener("click", handleAccordionClick);
      currentAccordionListener = null;
    }
  }

  function toggleAccordion(panelToActivate, contentBlock) {
    contentBlock.querySelectorAll(".accordion-panel").forEach((panel) => {
      const button = panel.querySelector("button");
      const content = panel.querySelector(".accordion-content");

      if (panel !== panelToActivate) {
        button.setAttribute("aria-expanded", false);
        content.setAttribute("aria-hidden", true);
      }
    });

    const activeButton = panelToActivate.querySelector("button");
    const activePanel = panelToActivate.querySelector(".accordion-content");
    const isOpened = activeButton.getAttribute("aria-expanded");

    if (isOpened === "true") {
      activeButton.setAttribute("aria-expanded", false);
      activePanel.setAttribute("aria-hidden", true);
    } else {
      activeButton.setAttribute("aria-expanded", true);
      activePanel.setAttribute("aria-hidden", false);
    }
  }
  // endregion

  // region tabs
  const tabs = block.querySelectorAll(".tab");
  const tabContents = block.querySelectorAll(".tab-content");

  tabs.forEach(tab => {
    tab.addEventListener("click", () => {
      // Clear active states
      tabs.forEach(tab => tab.classList.remove("active"));
      tabContents.forEach(content => {
        content.classList.remove("active");
        removeAccordionListener();
      });

      // Set current tab active
      tab.classList.add("active");
      const contentToShow = block.querySelector(`.tab-content[data-content="${tab.dataset.tab}"]`);
      contentToShow.classList.add("active");

      // Add accordion listener for the active tab
      addAccordionListener(contentToShow);
    });
  });

  // Initial setup for the first active tab
  const firstActiveTab = block.querySelector(".tab.active");
  if (firstActiveTab) {
    const initialContent = block.querySelector(`.tab-content[data-content="${firstActiveTab.dataset.tab}"]`);
    initialContent.classList.add("active");
    addAccordionListener(initialContent);
  }

  // endregion

  animations(block);
  imageLazyLoading(block);
};

export default faqBlock;
