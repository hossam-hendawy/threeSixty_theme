"use strict";
/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["webpackChunkthreeSixty_theme"] = self["webpackChunkthreeSixty_theme"] || []).push([["src_blocks_footer_block_index_js"],{

/***/ "./src/blocks/footer_block/index.js":
/*!******************************************!*\
  !*** ./src/blocks/footer_block/index.js ***!
  \******************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./style.scss */ \"./src/blocks/footer_block/style.scss\");\n/* harmony import */ var _scripts_functions_imageLazyLoading__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../scripts/functions/imageLazyLoading */ \"./src/scripts/functions/imageLazyLoading.js\");\n/* harmony import */ var _scripts_general_animations__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../scripts/general/animations */ \"./src/scripts/general/animations/index.js\");\n/* harmony import */ var _scripts_general_accordion__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../scripts/general/accordion */ \"./src/scripts/general/accordion.js\");\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (async function () {\n  let footer = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : document;\n  const media = window.matchMedia('(max-width: 599px)');\n  if (media.matches) {\n    (0,_scripts_general_accordion__WEBPACK_IMPORTED_MODULE_3__.accordion)(footer.querySelectorAll(\".accordion\"));\n  }\n  (0,_scripts_general_animations__WEBPACK_IMPORTED_MODULE_2__.animations)(footer);\n  (0,_scripts_functions_imageLazyLoading__WEBPACK_IMPORTED_MODULE_1__.imageLazyLoading)(footer);\n});\n\n//# sourceURL=webpack://threeSixty_theme/./src/blocks/footer_block/index.js?");

/***/ }),

/***/ "./src/blocks/footer_block/style.scss":
/*!********************************************!*\
  !*** ./src/blocks/footer_block/style.scss ***!
  \********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n// extracted by mini-css-extract-plugin\n\n\n//# sourceURL=webpack://threeSixty_theme/./src/blocks/footer_block/style.scss?");

/***/ }),

/***/ "./src/scripts/general/accordion.js":
/*!******************************************!*\
  !*** ./src/scripts/general/accordion.js ***!
  \******************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   accordion: function() { return /* binding */ accordion; }\n/* harmony export */ });\n/* harmony import */ var gsap__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! gsap */ \"./node_modules/gsap/index.js\");\n\nfunction accordion(accordions) {\n  if (accordions.length === 0) return;\n  accordions.forEach(accordion => {\n    const accordionHead = accordion.querySelector('.accordion-head');\n    const accordionBody = accordion.querySelector('.accordion-body');\n    accordionHead?.addEventListener('click', e => {\n      if (!accordionBody) {\n        return;\n      }\n      const isOpened = accordion?.classList.toggle('accordion-active');\n      if (!isOpened) {\n        gsap__WEBPACK_IMPORTED_MODULE_0__[\"default\"].to(accordionBody, {\n          height: 0\n        });\n      } else {\n        gsap__WEBPACK_IMPORTED_MODULE_0__[\"default\"].to(Array.from(accordions).map(otherAccordion => {\n          const otherAccordionBody = otherAccordion.querySelector('.accordion-body');\n          if (otherAccordionBody && accordion !== otherAccordion) {\n            otherAccordion?.classList.remove('accordion-active');\n            gsap__WEBPACK_IMPORTED_MODULE_0__[\"default\"].set(otherAccordion, {\n              zIndex: 1\n            });\n          }\n          return otherAccordionBody;\n        }), {\n          height: 0\n        });\n        gsap__WEBPACK_IMPORTED_MODULE_0__[\"default\"].set(accordion, {\n          zIndex: 2\n        });\n        gsap__WEBPACK_IMPORTED_MODULE_0__[\"default\"].to(accordionBody, {\n          height: 'auto'\n        });\n      }\n    });\n  });\n}\n\n//# sourceURL=webpack://threeSixty_theme/./src/scripts/general/accordion.js?");

/***/ })

}]);