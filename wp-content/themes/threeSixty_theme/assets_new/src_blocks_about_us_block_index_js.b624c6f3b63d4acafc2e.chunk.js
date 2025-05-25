"use strict";
/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["webpackChunkthreeSixty_theme"] = self["webpackChunkthreeSixty_theme"] || []).push([["src_blocks_about_us_block_index_js"],{

/***/ "./node_modules/gsap/CSSRulePlugin.js":
/*!********************************************!*\
  !*** ./node_modules/gsap/CSSRulePlugin.js ***!
  \********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   CSSRulePlugin: function() { return /* binding */ CSSRulePlugin; },\n/* harmony export */   \"default\": function() { return /* binding */ CSSRulePlugin; }\n/* harmony export */ });\n/*!\n * CSSRulePlugin 3.11.4\n * https://greensock.com\n *\n * @license Copyright 2008-2022, GreenSock. All rights reserved.\n * Subject to the terms at https://greensock.com/standard-license or for\n * Club GreenSock members, the agreement issued with that membership.\n * @author: Jack Doyle, jack@greensock.com\n*/\n\n/* eslint-disable */\nvar gsap,\n    _coreInitted,\n    _win,\n    _doc,\n    CSSPlugin,\n    _windowExists = function _windowExists() {\n  return typeof window !== \"undefined\";\n},\n    _getGSAP = function _getGSAP() {\n  return gsap || _windowExists() && (gsap = window.gsap) && gsap.registerPlugin && gsap;\n},\n    _checkRegister = function _checkRegister() {\n  if (!_coreInitted) {\n    _initCore();\n\n    if (!CSSPlugin) {\n      console.warn(\"Please gsap.registerPlugin(CSSPlugin, CSSRulePlugin)\");\n    }\n  }\n\n  return _coreInitted;\n},\n    _initCore = function _initCore(core) {\n  gsap = core || _getGSAP();\n\n  if (_windowExists()) {\n    _win = window;\n    _doc = document;\n  }\n\n  if (gsap) {\n    CSSPlugin = gsap.plugins.css;\n\n    if (CSSPlugin) {\n      _coreInitted = 1;\n    }\n  }\n};\n\nvar CSSRulePlugin = {\n  version: \"3.11.4\",\n  name: \"cssRule\",\n  init: function init(target, value, tween, index, targets) {\n    if (!_checkRegister() || typeof target.cssText === \"undefined\") {\n      return false;\n    }\n\n    var div = target._gsProxy = target._gsProxy || _doc.createElement(\"div\");\n\n    this.ss = target;\n    this.style = div.style;\n    div.style.cssText = target.cssText;\n    CSSPlugin.prototype.init.call(this, div, value, tween, index, targets); //we just offload all the work to the regular CSSPlugin and then copy the cssText back over to the rule in the render() method. This allows us to have all of the updates to CSSPlugin automatically flow through to CSSRulePlugin instead of having to maintain both\n  },\n  render: function render(ratio, data) {\n    var pt = data._pt,\n        style = data.style,\n        ss = data.ss,\n        i;\n\n    while (pt) {\n      pt.r(ratio, pt.d);\n      pt = pt._next;\n    }\n\n    i = style.length;\n\n    while (--i > -1) {\n      ss[style[i]] = style[style[i]];\n    }\n  },\n  getRule: function getRule(selector) {\n    _checkRegister();\n\n    var ruleProp = _doc.all ? \"rules\" : \"cssRules\",\n        styleSheets = _doc.styleSheets,\n        i = styleSheets.length,\n        pseudo = selector.charAt(0) === \":\",\n        j,\n        curSS,\n        cs,\n        a;\n    selector = (pseudo ? \"\" : \",\") + selector.split(\"::\").join(\":\").toLowerCase() + \",\"; //note: old versions of IE report tag name selectors as upper case, so we just change everything to lowercase.\n\n    if (pseudo) {\n      a = [];\n    }\n\n    while (i--) {\n      //Firefox may throw insecure operation errors when css is loaded from other domains, so try/catch.\n      try {\n        curSS = styleSheets[i][ruleProp];\n\n        if (!curSS) {\n          continue;\n        }\n\n        j = curSS.length;\n      } catch (e) {\n        console.warn(e);\n        continue;\n      }\n\n      while (--j > -1) {\n        cs = curSS[j];\n\n        if (cs.selectorText && (\",\" + cs.selectorText.split(\"::\").join(\":\").toLowerCase() + \",\").indexOf(selector) !== -1) {\n          //note: IE adds an extra \":\" to pseudo selectors, so .myClass:after becomes .myClass::after, so we need to strip the extra one out.\n          if (pseudo) {\n            a.push(cs.style);\n          } else {\n            return cs.style;\n          }\n        }\n      }\n    }\n\n    return a;\n  },\n  register: _initCore\n};\n_getGSAP() && gsap.registerPlugin(CSSRulePlugin);\n\n\n//# sourceURL=webpack://threeSixty_theme/./node_modules/gsap/CSSRulePlugin.js?");

/***/ }),

/***/ "./src/blocks/about_us_block/index.js":
/*!********************************************!*\
  !*** ./src/blocks/about_us_block/index.js ***!
  \********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./style.scss */ \"./src/blocks/about_us_block/style.scss\");\n/* harmony import */ var _scripts_functions_imageLazyLoading__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../scripts/functions/imageLazyLoading */ \"./src/scripts/functions/imageLazyLoading.js\");\n/* harmony import */ var _scripts_general_animations__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../scripts/general/animations */ \"./src/scripts/general/animations/index.js\");\n/* harmony import */ var _scripts_general_heroAnimation__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../scripts/general/heroAnimation */ \"./src/scripts/general/heroAnimation.js\");\n\n\n\n\n\n/**\r\n * @author DELL\r\n * @param block {HTMLElement}\r\n * @returns {Promise<void>}\r\n */\nconst aboutUsBlock = async block => {\n  (0,_scripts_general_heroAnimation__WEBPACK_IMPORTED_MODULE_3__.heroAnimation)(block, \".about_us_block:after\");\n  (0,_scripts_general_animations__WEBPACK_IMPORTED_MODULE_2__.animations)(block);\n  (0,_scripts_functions_imageLazyLoading__WEBPACK_IMPORTED_MODULE_1__.imageLazyLoading)(block);\n};\n/* harmony default export */ __webpack_exports__[\"default\"] = (aboutUsBlock);\n\n//# sourceURL=webpack://threeSixty_theme/./src/blocks/about_us_block/index.js?");

/***/ }),

/***/ "./src/blocks/about_us_block/style.scss":
/*!**********************************************!*\
  !*** ./src/blocks/about_us_block/style.scss ***!
  \**********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n// extracted by mini-css-extract-plugin\n\n\n//# sourceURL=webpack://threeSixty_theme/./src/blocks/about_us_block/style.scss?");

/***/ }),

/***/ "./src/scripts/general/heroAnimation.js":
/*!**********************************************!*\
  !*** ./src/scripts/general/heroAnimation.js ***!
  \**********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   heroAnimation: function() { return /* binding */ heroAnimation; }\n/* harmony export */ });\n/* harmony import */ var gsap__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! gsap */ \"./node_modules/gsap/index.js\");\n/* harmony import */ var gsap_CSSRulePlugin__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! gsap/CSSRulePlugin */ \"./node_modules/gsap/CSSRulePlugin.js\");\n\n\ngsap__WEBPACK_IMPORTED_MODULE_0__.gsap.registerPlugin(gsap_CSSRulePlugin__WEBPACK_IMPORTED_MODULE_1__[\"default\"]);\nfunction heroAnimation(block) {\n  let selector = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : \".hero-block:after\";\n  const isolationMode = block.querySelector('.isolation-mode img');\n  const afterElement = gsap_CSSRulePlugin__WEBPACK_IMPORTED_MODULE_1__[\"default\"].getRule(selector);\n  const tl = gsap__WEBPACK_IMPORTED_MODULE_0__.gsap.timeline();\n  if (afterElement) {\n    tl.fromTo(afterElement, {\n      opacity: 0\n    }, {\n      opacity: 1,\n      duration: 1.2,\n      ease: \"power2.out\",\n      delay: 0.2\n    });\n  }\n  if (isolationMode) {\n    tl.fromTo(isolationMode, {\n      opacity: 0\n    }, {\n      opacity: 1,\n      duration: 1,\n      ease: \"power2.out\"\n    });\n  }\n}\n\n//# sourceURL=webpack://threeSixty_theme/./src/scripts/general/heroAnimation.js?");

/***/ })

}]);