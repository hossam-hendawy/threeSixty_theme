"use strict";
/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["webpackChunkthreeSixty_theme"] = self["webpackChunkthreeSixty_theme"] || []).push([["src_blocks_faq_block_index_js"],{

/***/ "./src/blocks/faq_block/index.js":
/*!***************************************!*\
  !*** ./src/blocks/faq_block/index.js ***!
  \***************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./style.scss */ \"./src/blocks/faq_block/style.scss\");\n/* harmony import */ var _scripts_functions_imageLazyLoading__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../scripts/functions/imageLazyLoading */ \"./src/scripts/functions/imageLazyLoading.js\");\n/* harmony import */ var _scripts_general_animations__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../scripts/general/animations */ \"./src/scripts/general/animations/index.js\");\n/* harmony import */ var _scripts_general_accordion__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../scripts/general/accordion */ \"./src/scripts/general/accordion.js\");\n\n\n\n\n\n/**\r\n * @author DELL\r\n * @param block {HTMLElement}\r\n * @returns {Promise<void>}\r\n */\nconst faqBlock = async block => {\n  const accordion = block.querySelector(\".accordion\");\n  const firstPanel = accordion.querySelector(\".accordion-panel\");\n  if (firstPanel) {\n    toggleAccordion(firstPanel, true);\n  }\n  accordion.addEventListener(\"click\", e => {\n    const activePanel = e.target.closest(\".accordion-panel\");\n    if (!activePanel) return;\n    toggleAccordion(activePanel);\n  });\n  function toggleAccordion(panelToActivate) {\n    let forceOpen = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;\n    const activeButton = panelToActivate.querySelector(\"button\");\n    const activePanel = panelToActivate.querySelector(\".accordion-content\");\n    const activePanelIsOpened = activeButton.getAttribute(\"aria-expanded\");\n    if (activePanelIsOpened === \"true\" && !forceOpen) {\n      activeButton.setAttribute(\"aria-expanded\", false);\n      activePanel.setAttribute(\"aria-hidden\", true);\n    } else {\n      activeButton.setAttribute(\"aria-expanded\", true);\n      activePanel.setAttribute(\"aria-hidden\", false);\n    }\n  }\n  (0,_scripts_general_animations__WEBPACK_IMPORTED_MODULE_2__.animations)(block);\n  (0,_scripts_functions_imageLazyLoading__WEBPACK_IMPORTED_MODULE_1__.imageLazyLoading)(block);\n};\n/* harmony default export */ __webpack_exports__[\"default\"] = (faqBlock);\n\n//# sourceURL=webpack://threeSixty_theme/./src/blocks/faq_block/index.js?");

/***/ }),

/***/ "./src/blocks/faq_block/style.scss":
/*!*****************************************!*\
  !*** ./src/blocks/faq_block/style.scss ***!
  \*****************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n// extracted by mini-css-extract-plugin\n\n\n//# sourceURL=webpack://threeSixty_theme/./src/blocks/faq_block/style.scss?");

/***/ }),

/***/ "./src/scripts/general/accordion.js":
/*!******************************************!*\
  !*** ./src/scripts/general/accordion.js ***!
  \******************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   accordion: function() { return /* binding */ accordion; }\n/* harmony export */ });\n/* harmony import */ var gsap__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! gsap */ \"./node_modules/gsap/index.js\");\n\nfunction accordion(accordions) {\n  if (accordions.length === 0) return;\n  accordions.forEach(accordion => {\n    const accordionHead = accordion.querySelector('.accordion-head');\n    const accordionBody = accordion.querySelector('.accordion-body');\n    accordionHead?.addEventListener('click', e => {\n      if (!accordionBody) {\n        return;\n      }\n      const isOpened = accordion?.classList.toggle('accordion-active');\n      if (!isOpened) {\n        gsap__WEBPACK_IMPORTED_MODULE_0__[\"default\"].to(accordionBody, {\n          height: 0\n        });\n      } else {\n        gsap__WEBPACK_IMPORTED_MODULE_0__[\"default\"].to(Array.from(accordions).map(otherAccordion => {\n          const otherAccordionBody = otherAccordion.querySelector('.accordion-body');\n          if (otherAccordionBody && accordion !== otherAccordion) {\n            otherAccordion?.classList.remove('accordion-active');\n            gsap__WEBPACK_IMPORTED_MODULE_0__[\"default\"].set(otherAccordion, {\n              zIndex: 1\n            });\n          }\n          return otherAccordionBody;\n        }), {\n          height: 0\n        });\n        gsap__WEBPACK_IMPORTED_MODULE_0__[\"default\"].set(accordion, {\n          zIndex: 2\n        });\n        gsap__WEBPACK_IMPORTED_MODULE_0__[\"default\"].to(accordionBody, {\n          height: 'auto'\n        });\n      }\n    });\n  });\n}\n\n//# sourceURL=webpack://threeSixty_theme/./src/scripts/general/accordion.js?");

/***/ })

}]);