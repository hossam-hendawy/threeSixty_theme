"use strict";
/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["webpackChunkthreeSixty_theme"] = self["webpackChunkthreeSixty_theme"] || []).push([["src_blocks_recent_posts_index_js"],{

/***/ "./src/blocks/recent_posts/index.js":
/*!******************************************!*\
  !*** ./src/blocks/recent_posts/index.js ***!
  \******************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./style.scss */ \"./src/blocks/recent_posts/style.scss\");\n/* harmony import */ var _scripts_functions_imageLazyLoading__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../scripts/functions/imageLazyLoading */ \"./src/scripts/functions/imageLazyLoading.js\");\n/* harmony import */ var _scripts_general_animations__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../scripts/general/animations */ \"./src/scripts/general/animations/index.js\");\n/* harmony import */ var swiper__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! swiper */ \"./node_modules/swiper/swiper.mjs\");\n/* harmony import */ var swiper_modules__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! swiper/modules */ \"./node_modules/swiper/modules/index.mjs\");\n\n\n\n\n\n\n/**\r\n * @author\r\n * @param block {HTMLElement}\r\n * @returns {Promise<void>}\r\n */\nconst recentPosts = async block => {\n  // add block code here\n  const swiper = new swiper__WEBPACK_IMPORTED_MODULE_3__.Swiper(block.querySelector('.recent-posts-swiper'), {\n    slidesPerView: 'auto',\n    spaceBetween: 16,\n    modules: [swiper_modules__WEBPACK_IMPORTED_MODULE_4__.Navigation],\n    breakpoints: {\n      600: {\n        slidesPerView: 2\n      },\n      1024: {\n        slidesPerView: 3,\n        spaceBetween: 20\n      },\n      1280: {\n        slidesPerView: 3,\n        spaceBetween: 32\n      }\n    },\n    navigation: {\n      nextEl: block.querySelector(\".swiper-button-next\"),\n      prevEl: block.querySelector(\".swiper-button-prev\")\n    }\n  });\n  // testing the new hidden value\n  (0,_scripts_general_animations__WEBPACK_IMPORTED_MODULE_2__.animations)(block);\n  (0,_scripts_functions_imageLazyLoading__WEBPACK_IMPORTED_MODULE_1__.imageLazyLoading)(block);\n};\n/* harmony default export */ __webpack_exports__[\"default\"] = (recentPosts);\n\n//# sourceURL=webpack://threeSixty_theme/./src/blocks/recent_posts/index.js?");

/***/ }),

/***/ "./src/blocks/recent_posts/style.scss":
/*!********************************************!*\
  !*** ./src/blocks/recent_posts/style.scss ***!
  \********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n// extracted by mini-css-extract-plugin\n\n\n//# sourceURL=webpack://threeSixty_theme/./src/blocks/recent_posts/style.scss?");

/***/ })

}]);