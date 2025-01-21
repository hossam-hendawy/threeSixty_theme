import $ from "jquery";
import "select2";

export function InitSelect2(block) {

  const dropdowns = block.querySelectorAll(".dropdown");

  function initSelect(elm) {
    const placeholderText = elm.getAttribute("data-placeholder");
    const svgArrow = `
      <svg width="27" height="31" viewBox="0 0 27 31" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M21.375 10.9792L13.5 20.0209L5.625 10.9792" stroke="black" stroke-width="1.5" stroke-linecap="round"
              stroke-linejoin="round"/>
      </svg>
    `;

    $(elm).select2({
      width: "100%",
      minimumResultsForSearch: -1,
      dropdownParent: elm.closest('.select2-wrapper'),
      placeholder: placeholderText
    });

    $(elm).next('.select2').find('.select2-selection__arrow').html(svgArrow);
  }

  dropdowns.forEach(function (dropdown) {
    initSelect(dropdown);
  });
  // endregion


}
