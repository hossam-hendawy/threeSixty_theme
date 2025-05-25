"use strict";
/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["webpackChunkthreeSixty_theme"] = self["webpackChunkthreeSixty_theme"] || []).push([["src_blocks_who_we_are_block_index_js"],{

/***/ "./src/blocks/who_we_are_block/index.js":
/*!**********************************************!*\
  !*** ./src/blocks/who_we_are_block/index.js ***!
  \**********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./style.scss */ \"./src/blocks/who_we_are_block/style.scss\");\n/* harmony import */ var _scripts_functions_imageLazyLoading__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../scripts/functions/imageLazyLoading */ \"./src/scripts/functions/imageLazyLoading.js\");\n/* harmony import */ var _scripts_general_animations__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../scripts/general/animations */ \"./src/scripts/general/animations/index.js\");\n/* harmony import */ var gsap__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! gsap */ \"./node_modules/gsap/index.js\");\n/* harmony import */ var gsap_ScrollTrigger__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! gsap/ScrollTrigger */ \"./node_modules/gsap/ScrollTrigger.js\");\n\n\n\n\n\ngsap__WEBPACK_IMPORTED_MODULE_3__.gsap.registerPlugin(gsap_ScrollTrigger__WEBPACK_IMPORTED_MODULE_4__.ScrollTrigger);\n\n/**\r\n * @author DELL\r\n * @param block {HTMLElement}\r\n * @returns {Promise<void>}\r\n */\n\nconst whoWeAreBlock = async block => {\n  const logo = block.querySelector('svg.logo');\n  if (logo) {\n    gsap__WEBPACK_IMPORTED_MODULE_3__.gsap.fromTo(logo, {\n      opacity: 0\n    }, {\n      opacity: 1,\n      duration: 1.5,\n      ease: \"power2.out\",\n      scrollTrigger: {\n        trigger: block,\n        start: \"top 80%\",\n        toggleActions: \"play none none none\"\n      }\n    });\n  }\n  (0,_scripts_general_animations__WEBPACK_IMPORTED_MODULE_2__.animations)(block);\n  (0,_scripts_functions_imageLazyLoading__WEBPACK_IMPORTED_MODULE_1__.imageLazyLoading)(block);\n};\n/* harmony default export */ __webpack_exports__[\"default\"] = (whoWeAreBlock);\n\n//# sourceURL=webpack://threeSixty_theme/./src/blocks/who_we_are_block/index.js?");

/***/ }),

/***/ "./src/blocks/who_we_are_block/style.scss":
/*!************************************************!*\
  !*** ./src/blocks/who_we_are_block/style.scss ***!
  \************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n// extracted by mini-css-extract-plugin\n\n\n//# sourceURL=webpack://threeSixty_theme/./src/blocks/who_we_are_block/style.scss?");

/***/ })

}]);