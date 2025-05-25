"use strict";
/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["webpackChunkthreeSixty_theme"] = self["webpackChunkthreeSixty_theme"] || []).push([["src_blocks_blog_listing_block_index_js"],{

/***/ "./src/blocks/blog_listing_block/index.js":
/*!************************************************!*\
  !*** ./src/blocks/blog_listing_block/index.js ***!
  \************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./style.scss */ \"./src/blocks/blog_listing_block/style.scss\");\n/* harmony import */ var _scripts_functions_imageLazyLoading__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../scripts/functions/imageLazyLoading */ \"./src/scripts/functions/imageLazyLoading.js\");\n/* harmony import */ var _scripts_general_animations__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../scripts/general/animations */ \"./src/scripts/general/animations/index.js\");\n\n\n\n\n/**\n * @author DELL\n * @param block {HTMLElement}\n * @returns {Promise<void>}\n */\nconst blogListingBlock = async block => {\n  let currentPage = 1;\n  let totalPages = 1;\n  const prevPageBtn = block.querySelector(\"#prev-page\");\n  const nextPageBtn = block.querySelector(\"#next-page\");\n  const postContainer = block.querySelector(\"#post-container\");\n  const numbersContainer = block.querySelector(\".numbers\");\n  const loadingSpinner = document.createElement(\"div\");\n  loadingSpinner.classList.add(\"loading-spinner\");\n  loadingSpinner.style.display = \"none\";\n  postContainer.appendChild(loadingSpinner);\n  function updateButtonStates() {\n    prevPageBtn.classList.toggle(\"disabled\", currentPage === 1);\n    nextPageBtn.classList.toggle(\"disabled\", currentPage >= totalPages);\n  }\n  function scrollToBottomContent(block) {\n    const target = block.querySelector(\".bottom-content-wrapper\");\n    if (target) {\n      target.scrollIntoView({\n        behavior: \"smooth\",\n        block: \"start\"\n      });\n    }\n  }\n  const currentLangAttr = document.documentElement.lang || 'en';\n  const currentLang = currentLangAttr === 'en-US' ? 'en' : currentLangAttr;\n  let local = false;\n  let url;\n  function loadPosts(page) {\n    if (local) {\n      url = `${window.location.origin}/threeSixty_theme/wp-content/themes/threeSixty_theme/load-posts.php?page=${page}&lang=${currentLang}`;\n    } else {\n      url = `${window.location.origin}/wp-content/themes/threeSixty_theme/load-posts.php?page=${page}&lang=${currentLang}`;\n    }\n    postContainer.innerHTML = \"\";\n    postContainer.appendChild(loadingSpinner);\n    loadingSpinner.style.display = \"block\";\n    fetch(url).then(response => {\n      if (!response.ok) {\n        throw new Error(`HTTP error! Status: ${response.status}`);\n      }\n      return response.json();\n    }).then(data => {\n      loadingSpinner.style.display = \"none\";\n      postContainer.innerHTML = data.posts;\n      totalPages = data.totalPages;\n      currentPage = page;\n      generatePagination();\n      updateButtonStates();\n    }).catch(error => {\n      loadingSpinner.style.display = \"none\";\n      console.error(\"Error loading posts:\", error);\n    });\n  }\n  function generatePagination() {\n    numbersContainer.innerHTML = \"\";\n    for (let i = 1; i <= totalPages; i++) {\n      let numberElement = document.createElement(\"div\");\n      numberElement.classList.add(\"number\", \"text-sm\", \"medium\", \"gray-600\");\n      numberElement.innerText = i;\n      if (i === currentPage) {\n        numberElement.classList.add(\"active\");\n      }\n      numberElement.addEventListener(\"click\", () => {\n        if (i !== currentPage) {\n          loadPosts(i);\n          scrollToBottomContent(block);\n        }\n      });\n      numbersContainer.appendChild(numberElement);\n    }\n  }\n  nextPageBtn.addEventListener(\"click\", function () {\n    if (currentPage < totalPages) {\n      loadPosts(currentPage + 1);\n      scrollToBottomContent(block);\n    }\n  });\n  prevPageBtn.addEventListener(\"click\", function () {\n    if (currentPage > 1) {\n      loadPosts(currentPage - 1);\n      scrollToBottomContent(block);\n    }\n  });\n  loadPosts(currentPage);\n  (0,_scripts_general_animations__WEBPACK_IMPORTED_MODULE_2__.animations)(block);\n  (0,_scripts_functions_imageLazyLoading__WEBPACK_IMPORTED_MODULE_1__.imageLazyLoading)(block);\n};\n/* harmony default export */ __webpack_exports__[\"default\"] = (blogListingBlock);\n\n//# sourceURL=webpack://threeSixty_theme/./src/blocks/blog_listing_block/index.js?");

/***/ }),

/***/ "./src/blocks/blog_listing_block/style.scss":
/*!**************************************************!*\
  !*** ./src/blocks/blog_listing_block/style.scss ***!
  \**************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n// extracted by mini-css-extract-plugin\n\n\n//# sourceURL=webpack://threeSixty_theme/./src/blocks/blog_listing_block/style.scss?");

/***/ })

}]);