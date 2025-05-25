"use strict";
/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["webpackChunkthreeSixty_theme"] = self["webpackChunkthreeSixty_theme"] || []).push([["src_blocks_testimonials_block_index_js"],{

/***/ "./src/blocks/testimonials_block/index.js":
/*!************************************************!*\
  !*** ./src/blocks/testimonials_block/index.js ***!
  \************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./style.scss */ \"./src/blocks/testimonials_block/style.scss\");\n/* harmony import */ var _scripts_functions_imageLazyLoading__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../scripts/functions/imageLazyLoading */ \"./src/scripts/functions/imageLazyLoading.js\");\n/* harmony import */ var _scripts_general_animations__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../scripts/general/animations */ \"./src/scripts/general/animations/index.js\");\n/* harmony import */ var swiper__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! swiper */ \"./node_modules/swiper/swiper.mjs\");\n/* harmony import */ var swiper_modules__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! swiper/modules */ \"./node_modules/swiper/modules/index.mjs\");\n/* harmony import */ var gsap__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! gsap */ \"./node_modules/gsap/index.js\");\n/* harmony import */ var gsap_ScrollTrigger__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! gsap/ScrollTrigger */ \"./node_modules/gsap/ScrollTrigger.js\");\n\n\n\n\n\n\n\ngsap__WEBPACK_IMPORTED_MODULE_5__.gsap.registerPlugin(gsap_ScrollTrigger__WEBPACK_IMPORTED_MODULE_6__.ScrollTrigger);\n\n/**\n * @author DELL\n * @param block {HTMLElement}\n * @returns {Promise<void>}\n */\nconst testimonialsBlock = async block => {\n  let testimonialsSwiper = block.querySelector('.testimonials-swiper');\n\n  // add block code here\n  const swiper = new swiper__WEBPACK_IMPORTED_MODULE_3__.Swiper(testimonialsSwiper, {\n    slidesPerView: 1,\n    spaceBetween: 16,\n    modules: [swiper_modules__WEBPACK_IMPORTED_MODULE_4__.Navigation],\n    breakpoints: {\n      600: {\n        spaceBetween: 20,\n        slidesPerView: 2\n      },\n      768: {\n        slidesPerView: 2\n      },\n      992: {\n        slidesPerView: 3\n      },\n      1280: {\n        slidesPerView: 2.39,\n        spaceBetween: 30\n      }\n    },\n    navigation: {\n      nextEl: [...block.querySelectorAll(\".swiper-button-next\")],\n      prevEl: [...block.querySelectorAll(\".swiper-button-prev\")]\n    }\n  });\n  const logo = block.querySelector('svg.logo');\n  if (logo) {\n    gsap__WEBPACK_IMPORTED_MODULE_5__.gsap.fromTo(logo, {\n      opacity: 0\n    }, {\n      opacity: 1,\n      duration: 1,\n      ease: \"power2.out\",\n      scrollTrigger: {\n        trigger: block,\n        start: \"top 80%\",\n        toggleActions: \"play none none none\"\n      }\n    });\n  }\n\n  // testing the new hidden value\n  (0,_scripts_general_animations__WEBPACK_IMPORTED_MODULE_2__.animations)(block);\n  (0,_scripts_functions_imageLazyLoading__WEBPACK_IMPORTED_MODULE_1__.imageLazyLoading)(block);\n};\n/* harmony default export */ __webpack_exports__[\"default\"] = (testimonialsBlock);\n\n//# sourceURL=webpack://threeSixty_theme/./src/blocks/testimonials_block/index.js?");

/***/ }),

/***/ "./src/blocks/testimonials_block/style.scss":
/*!**************************************************!*\
  !*** ./src/blocks/testimonials_block/style.scss ***!
  \**************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n// extracted by mini-css-extract-plugin\n\n\n//# sourceURL=webpack://threeSixty_theme/./src/blocks/testimonials_block/style.scss?");

/***/ })

}]);