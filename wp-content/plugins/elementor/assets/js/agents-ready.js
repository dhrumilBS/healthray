(function(react, react_dom, _elementor_ui, _elementor_icons, _elementor_ui_Alert, _elementor_ui_Button, _elementor_ui_Container, _elementor_ui_Divider, _elementor_ui_Stack, _wordpress_i18n, _wordpress_api_fetch, _elementor_ui_Chip, _elementor_ui_Typography, _elementor_ui_Dialog, _elementor_ui_DialogActions, _elementor_ui_DialogContent, _elementor_ui_DialogContentText, _elementor_ui_DialogTitle, _elementor_ui_DialogHeader, _elementor_ui_CloseButton, _elementor_ui_IconButton, _elementor_ui_InputAdornment, _elementor_ui_MenuItem, _elementor_ui_MenuList, _elementor_ui_Popover, _elementor_ui_TextField, _elementor_ui_Box, _elementor_ui_Switch, _elementor_ui_Avatar, _elementor_ui_Collapse, _elementor_ui_Link, _elementor_ui_Tooltip, _elementor_ui_CircularProgress, _elementor_ui_Autocomplete, _elementor_ui_Accordion, _elementor_ui_AccordionDetails, _elementor_ui_AccordionSummary, _elementor_ui_Infotip, _elementor_ui_SvgIcon, _elementor_ui_Checkbox, _elementor_ui_FormControlLabel, _elementor_ui_Select, _elementor_ui_Skeleton) {

//#region \0rolldown/runtime.js
	var __create = Object.create;
	var __defProp = Object.defineProperty;
	var __name = (target, value) => __defProp(target, "name", {
		value,
		configurable: true
	});
	var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
	var __getOwnPropNames = Object.getOwnPropertyNames;
	var __getProtoOf = Object.getPrototypeOf;
	var __hasOwnProp = Object.prototype.hasOwnProperty;
	var __commonJSMin = (cb, mod) => () => (mod || (cb((mod = { exports: {} }).exports, mod), cb = null), mod.exports);
	var __copyProps = (to, from, except, desc) => {
		if (from && typeof from === "object" || typeof from === "function") {
			for (var keys = __getOwnPropNames(from), i = 0, n = keys.length, key; i < n; i++) {
				key = keys[i];
				if (!__hasOwnProp.call(to, key) && key !== except) {
					__defProp(to, key, {
						get: ((k) => from[k]).bind(null, key),
						enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable
					});
				}
			}
		}
		return to;
	};
	var __toESM = (mod, isNodeMode, target) => (target = mod != null ? __create(__getProtoOf(mod)) : {}, __copyProps(isNodeMode || !mod || !mod.__esModule ? __defProp(target, "default", {
		value: mod,
		enumerable: true
	}) : target, mod));

//#endregion
react = __toESM(react);
react_dom = __toESM(react_dom);
_elementor_ui_Alert = __toESM(_elementor_ui_Alert);
_elementor_ui_Button = __toESM(_elementor_ui_Button);
_elementor_ui_Container = __toESM(_elementor_ui_Container);
_elementor_ui_Divider = __toESM(_elementor_ui_Divider);
_elementor_ui_Stack = __toESM(_elementor_ui_Stack);
_wordpress_api_fetch = __toESM(_wordpress_api_fetch);
_elementor_ui_Chip = __toESM(_elementor_ui_Chip);
_elementor_ui_Typography = __toESM(_elementor_ui_Typography);
_elementor_ui_Dialog = __toESM(_elementor_ui_Dialog);
_elementor_ui_DialogActions = __toESM(_elementor_ui_DialogActions);
_elementor_ui_DialogContent = __toESM(_elementor_ui_DialogContent);
_elementor_ui_DialogContentText = __toESM(_elementor_ui_DialogContentText);
_elementor_ui_DialogTitle = __toESM(_elementor_ui_DialogTitle);
_elementor_ui_DialogHeader = __toESM(_elementor_ui_DialogHeader);
_elementor_ui_CloseButton = __toESM(_elementor_ui_CloseButton);
_elementor_ui_IconButton = __toESM(_elementor_ui_IconButton);
_elementor_ui_InputAdornment = __toESM(_elementor_ui_InputAdornment);
_elementor_ui_MenuItem = __toESM(_elementor_ui_MenuItem);
_elementor_ui_MenuList = __toESM(_elementor_ui_MenuList);
_elementor_ui_Popover = __toESM(_elementor_ui_Popover);
_elementor_ui_TextField = __toESM(_elementor_ui_TextField);
_elementor_ui_Box = __toESM(_elementor_ui_Box);
_elementor_ui_Switch = __toESM(_elementor_ui_Switch);
_elementor_ui_Avatar = __toESM(_elementor_ui_Avatar);
_elementor_ui_Collapse = __toESM(_elementor_ui_Collapse);
_elementor_ui_Link = __toESM(_elementor_ui_Link);
_elementor_ui_Tooltip = __toESM(_elementor_ui_Tooltip);
_elementor_ui_CircularProgress = __toESM(_elementor_ui_CircularProgress);
_elementor_ui_Autocomplete = __toESM(_elementor_ui_Autocomplete);
_elementor_ui_Accordion = __toESM(_elementor_ui_Accordion);
_elementor_ui_AccordionDetails = __toESM(_elementor_ui_AccordionDetails);
_elementor_ui_AccordionSummary = __toESM(_elementor_ui_AccordionSummary);
_elementor_ui_Infotip = __toESM(_elementor_ui_Infotip);
_elementor_ui_SvgIcon = __toESM(_elementor_ui_SvgIcon);
_elementor_ui_Checkbox = __toESM(_elementor_ui_Checkbox);
_elementor_ui_FormControlLabel = __toESM(_elementor_ui_FormControlLabel);
_elementor_ui_Select = __toESM(_elementor_ui_Select);
_elementor_ui_Skeleton = __toESM(_elementor_ui_Skeleton);

//#region node_modules/react-dom/client.js
	var require_client = /* @__PURE__ */ __commonJSMin(((exports) => {
		var m$1 = (globalThis.ReactDOM);
		var i = m$1.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;
		exports.createRoot = function(c, o) {
			i.usingClientEntryPoint = true;
			try {
				return m$1.createRoot(c, o);
			} finally {
				i.usingClientEntryPoint = false;
			}
		};
	}));

//#endregion
//#region assets/dev/js/utils/react.js
	var import_client = require_client();
	/**
	* Support conditional rendering of a React App to the DOM, based on the React version.
	* We use `createRoot` when available, but fallback to `ReactDOM.render` for older versions.
	*
	* @param {Promise.resolve(React).ReactElement} app        The app to render.
	* @param {HTMLElement}                  domElement The DOM element to render the app into.
	*
	* @return {{ unmount: () => void }} The unmount function.
	*/
	function render(app, domElement) {
		var unmountFunction;
		try {
			var root = (0, import_client.createRoot)(domElement);
			root.render(app);
			unmountFunction = function unmountFunction() {
				root.unmount();
			};
		} catch (e) {
			react_dom.render(app, domElement);
			unmountFunction = function unmountFunction() {
				react_dom.unmountComponentAtNode(domElement);
			};
		}
		return { unmount: unmountFunction };
	}
	var react_default = { render };

//#endregion
//#region node_modules/prop-types/node_modules/react-is/cjs/react-is.development.js
/** @license React v16.13.1
	* react-is.development.js
	*
	* Copyright (c) Facebook, Inc. and its affiliates.
	*
	* This source code is licensed under the MIT license found in the
	* LICENSE file in the root directory of this source tree.
	*/
	var require_react_is_development = /* @__PURE__ */ __commonJSMin(((exports) => {
		(function() {
			"use strict";
			var hasSymbol = typeof Symbol === "function" && Symbol.for;
			var REACT_ELEMENT_TYPE = hasSymbol ? Symbol.for("react.element") : 60103;
			var REACT_PORTAL_TYPE = hasSymbol ? Symbol.for("react.portal") : 60106;
			var REACT_FRAGMENT_TYPE = hasSymbol ? Symbol.for("react.fragment") : 60107;
			var REACT_STRICT_MODE_TYPE = hasSymbol ? Symbol.for("react.strict_mode") : 60108;
			var REACT_PROFILER_TYPE = hasSymbol ? Symbol.for("react.profiler") : 60114;
			var REACT_PROVIDER_TYPE = hasSymbol ? Symbol.for("react.provider") : 60109;
			var REACT_CONTEXT_TYPE = hasSymbol ? Symbol.for("react.context") : 60110;
			var REACT_ASYNC_MODE_TYPE = hasSymbol ? Symbol.for("react.async_mode") : 60111;
			var REACT_CONCURRENT_MODE_TYPE = hasSymbol ? Symbol.for("react.concurrent_mode") : 60111;
			var REACT_FORWARD_REF_TYPE = hasSymbol ? Symbol.for("react.forward_ref") : 60112;
			var REACT_SUSPENSE_TYPE = hasSymbol ? Symbol.for("react.suspense") : 60113;
			var REACT_SUSPENSE_LIST_TYPE = hasSymbol ? Symbol.for("react.suspense_list") : 60120;
			var REACT_MEMO_TYPE = hasSymbol ? Symbol.for("react.memo") : 60115;
			var REACT_LAZY_TYPE = hasSymbol ? Symbol.for("react.lazy") : 60116;
			var REACT_BLOCK_TYPE = hasSymbol ? Symbol.for("react.block") : 60121;
			var REACT_FUNDAMENTAL_TYPE = hasSymbol ? Symbol.for("react.fundamental") : 60117;
			var REACT_RESPONDER_TYPE = hasSymbol ? Symbol.for("react.responder") : 60118;
			var REACT_SCOPE_TYPE = hasSymbol ? Symbol.for("react.scope") : 60119;
			function isValidElementType(type) {
				return typeof type === "string" || typeof type === "function" || type === REACT_FRAGMENT_TYPE || type === REACT_CONCURRENT_MODE_TYPE || type === REACT_PROFILER_TYPE || type === REACT_STRICT_MODE_TYPE || type === REACT_SUSPENSE_TYPE || type === REACT_SUSPENSE_LIST_TYPE || typeof type === "object" && type !== null && (type.$$typeof === REACT_LAZY_TYPE || type.$$typeof === REACT_MEMO_TYPE || type.$$typeof === REACT_PROVIDER_TYPE || type.$$typeof === REACT_CONTEXT_TYPE || type.$$typeof === REACT_FORWARD_REF_TYPE || type.$$typeof === REACT_FUNDAMENTAL_TYPE || type.$$typeof === REACT_RESPONDER_TYPE || type.$$typeof === REACT_SCOPE_TYPE || type.$$typeof === REACT_BLOCK_TYPE);
			}
			function typeOf(object) {
				if (typeof object === "object" && object !== null) {
					var $$typeof = object.$$typeof;
					switch ($$typeof) {
						case REACT_ELEMENT_TYPE:
							var type = object.type;
							switch (type) {
								case REACT_ASYNC_MODE_TYPE:
								case REACT_CONCURRENT_MODE_TYPE:
								case REACT_FRAGMENT_TYPE:
								case REACT_PROFILER_TYPE:
								case REACT_STRICT_MODE_TYPE:
								case REACT_SUSPENSE_TYPE: return type;
								default:
									var $$typeofType = type && type.$$typeof;
									switch ($$typeofType) {
										case REACT_CONTEXT_TYPE:
										case REACT_FORWARD_REF_TYPE:
										case REACT_LAZY_TYPE:
										case REACT_MEMO_TYPE:
										case REACT_PROVIDER_TYPE: return $$typeofType;
										default: return $$typeof;
									}
							}
						case REACT_PORTAL_TYPE: return $$typeof;
					}
				}
			}
			var AsyncMode = REACT_ASYNC_MODE_TYPE;
			var ConcurrentMode = REACT_CONCURRENT_MODE_TYPE;
			var ContextConsumer = REACT_CONTEXT_TYPE;
			var ContextProvider = REACT_PROVIDER_TYPE;
			var Element = REACT_ELEMENT_TYPE;
			var ForwardRef = REACT_FORWARD_REF_TYPE;
			var Fragment = REACT_FRAGMENT_TYPE;
			var Lazy = REACT_LAZY_TYPE;
			var Memo = REACT_MEMO_TYPE;
			var Portal = REACT_PORTAL_TYPE;
			var Profiler = REACT_PROFILER_TYPE;
			var StrictMode = REACT_STRICT_MODE_TYPE;
			var Suspense = REACT_SUSPENSE_TYPE;
			var hasWarnedAboutDeprecatedIsAsyncMode = false;
			function isAsyncMode(object) {
				if (!hasWarnedAboutDeprecatedIsAsyncMode) {
					hasWarnedAboutDeprecatedIsAsyncMode = true;
					console["warn"]("The ReactIs.isAsyncMode() alias has been deprecated, and will be removed in React 17+. Update your code to use ReactIs.isConcurrentMode() instead. It has the exact same API.");
				}
				return isConcurrentMode(object) || typeOf(object) === REACT_ASYNC_MODE_TYPE;
			}
			function isConcurrentMode(object) {
				return typeOf(object) === REACT_CONCURRENT_MODE_TYPE;
			}
			function isContextConsumer(object) {
				return typeOf(object) === REACT_CONTEXT_TYPE;
			}
			function isContextProvider(object) {
				return typeOf(object) === REACT_PROVIDER_TYPE;
			}
			function isElement(object) {
				return typeof object === "object" && object !== null && object.$$typeof === REACT_ELEMENT_TYPE;
			}
			function isForwardRef(object) {
				return typeOf(object) === REACT_FORWARD_REF_TYPE;
			}
			function isFragment(object) {
				return typeOf(object) === REACT_FRAGMENT_TYPE;
			}
			function isLazy(object) {
				return typeOf(object) === REACT_LAZY_TYPE;
			}
			function isMemo(object) {
				return typeOf(object) === REACT_MEMO_TYPE;
			}
			function isPortal(object) {
				return typeOf(object) === REACT_PORTAL_TYPE;
			}
			function isProfiler(object) {
				return typeOf(object) === REACT_PROFILER_TYPE;
			}
			function isStrictMode(object) {
				return typeOf(object) === REACT_STRICT_MODE_TYPE;
			}
			function isSuspense(object) {
				return typeOf(object) === REACT_SUSPENSE_TYPE;
			}
			exports.AsyncMode = AsyncMode;
			exports.ConcurrentMode = ConcurrentMode;
			exports.ContextConsumer = ContextConsumer;
			exports.ContextProvider = ContextProvider;
			exports.Element = Element;
			exports.ForwardRef = ForwardRef;
			exports.Fragment = Fragment;
			exports.Lazy = Lazy;
			exports.Memo = Memo;
			exports.Portal = Portal;
			exports.Profiler = Profiler;
			exports.StrictMode = StrictMode;
			exports.Suspense = Suspense;
			exports.isAsyncMode = isAsyncMode;
			exports.isConcurrentMode = isConcurrentMode;
			exports.isContextConsumer = isContextConsumer;
			exports.isContextProvider = isContextProvider;
			exports.isElement = isElement;
			exports.isForwardRef = isForwardRef;
			exports.isFragment = isFragment;
			exports.isLazy = isLazy;
			exports.isMemo = isMemo;
			exports.isPortal = isPortal;
			exports.isProfiler = isProfiler;
			exports.isStrictMode = isStrictMode;
			exports.isSuspense = isSuspense;
			exports.isValidElementType = isValidElementType;
			exports.typeOf = typeOf;
		})();
	}));

//#endregion
//#region node_modules/prop-types/node_modules/react-is/index.js
	var require_react_is = /* @__PURE__ */ __commonJSMin(((exports, module) => {
		module.exports = require_react_is_development();
	}));

//#endregion
//#region node_modules/object-assign/index.js
/*
	object-assign
	(c) Sindre Sorhus
	@license MIT
	*/
	var require_object_assign = /* @__PURE__ */ __commonJSMin(((exports, module) => {
		var getOwnPropertySymbols = Object.getOwnPropertySymbols;
		var hasOwnProperty = Object.prototype.hasOwnProperty;
		var propIsEnumerable = Object.prototype.propertyIsEnumerable;
		function toObject(val) {
			if (val === null || val === void 0) throw new TypeError("Object.assign cannot be called with null or undefined");
			return Object(val);
		}
		function shouldUseNative() {
			try {
				if (!Object.assign) return false;
				var test1 = /* @__PURE__ */ new String("abc");
				test1[5] = "de";
				if (Object.getOwnPropertyNames(test1)[0] === "5") return false;
				var test2 = {};
				for (var i = 0; i < 10; i++) test2["_" + String.fromCharCode(i)] = i;
				if (Object.getOwnPropertyNames(test2).map(function(n) {
					return test2[n];
				}).join("") !== "0123456789") return false;
				var test3 = {};
				"abcdefghijklmnopqrst".split("").forEach(function(letter) {
					test3[letter] = letter;
				});
				if (Object.keys(Object.assign({}, test3)).join("") !== "abcdefghijklmnopqrst") return false;
				return true;
			} catch (err) {
				return false;
			}
		}
		module.exports = shouldUseNative() ? Object.assign : function(target, source) {
			var from;
			var to = toObject(target);
			var symbols;
			for (var s = 1; s < arguments.length; s++) {
				from = Object(arguments[s]);
				for (var key in from) if (hasOwnProperty.call(from, key)) to[key] = from[key];
				if (getOwnPropertySymbols) {
					symbols = getOwnPropertySymbols(from);
					for (var i = 0; i < symbols.length; i++) if (propIsEnumerable.call(from, symbols[i])) to[symbols[i]] = from[symbols[i]];
				}
			}
			return to;
		};
	}));

//#endregion
//#region node_modules/prop-types/lib/ReactPropTypesSecret.js
/**
	* Copyright (c) 2013-present, Facebook, Inc.
	*
	* This source code is licensed under the MIT license found in the
	* LICENSE file in the root directory of this source tree.
	*/
	var require_ReactPropTypesSecret = /* @__PURE__ */ __commonJSMin(((exports, module) => {
		var ReactPropTypesSecret = "SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED";
		module.exports = ReactPropTypesSecret;
	}));

//#endregion
//#region node_modules/prop-types/lib/has.js
	var require_has = /* @__PURE__ */ __commonJSMin(((exports, module) => {
		module.exports = Function.call.bind(Object.prototype.hasOwnProperty);
	}));

//#endregion
//#region node_modules/prop-types/checkPropTypes.js
/**
	* Copyright (c) 2013-present, Facebook, Inc.
	*
	* This source code is licensed under the MIT license found in the
	* LICENSE file in the root directory of this source tree.
	*/
	var require_checkPropTypes = /* @__PURE__ */ __commonJSMin(((exports, module) => {
		var printWarning = function() {};
		var ReactPropTypesSecret = require_ReactPropTypesSecret();
		var loggedTypeFailures = {};
		var has = require_has();
		printWarning = function(text) {
			var message = "Warning: " + text;
			if (typeof console !== "undefined") console.error(message);
			try {
				throw new Error(message);
			} catch (x) {}
		};
		/**
		* Assert that the values match with the type specs.
		* Error messages are memorized and will only be shown once.
		*
		* @param {object} typeSpecs Map of name to a ReactPropType
		* @param {object} values Runtime values that need to be type-checked
		* @param {string} location e.g. "prop", "context", "child context"
		* @param {string} componentName Name of the component for error messages.
		* @param {?Function} getStack Returns the component stack.
		* @private
		*/
		function checkPropTypes(typeSpecs, values, location, componentName, getStack) {
			for (var typeSpecName in typeSpecs) if (has(typeSpecs, typeSpecName)) {
				var error;
				try {
					if (typeof typeSpecs[typeSpecName] !== "function") {
						var err = Error((componentName || "React class") + ": " + location + " type `" + typeSpecName + "` is invalid; it must be a function, usually from the `prop-types` package, but received `" + typeof typeSpecs[typeSpecName] + "`.This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.");
						err.name = "Invariant Violation";
						throw err;
					}
					error = typeSpecs[typeSpecName](values, typeSpecName, componentName, location, null, ReactPropTypesSecret);
				} catch (ex) {
					error = ex;
				}
				if (error && !(error instanceof Error)) printWarning((componentName || "React class") + ": type specification of " + location + " `" + typeSpecName + "` is invalid; the type checker function must return `null` or an `Error` but returned a " + typeof error + ". You may have forgotten to pass an argument to the type checker creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and shape all require an argument).");
				if (error instanceof Error && !(error.message in loggedTypeFailures)) {
					loggedTypeFailures[error.message] = true;
					var stack = getStack ? getStack() : "";
					printWarning("Failed " + location + " type: " + error.message + (stack != null ? stack : ""));
				}
			}
		}
		/**
		* Resets warning cache when testing.
		*
		* @private
		*/
		checkPropTypes.resetWarningCache = function() {
			loggedTypeFailures = {};
		};
		module.exports = checkPropTypes;
	}));

//#endregion
//#region node_modules/prop-types/factoryWithTypeCheckers.js
/**
	* Copyright (c) 2013-present, Facebook, Inc.
	*
	* This source code is licensed under the MIT license found in the
	* LICENSE file in the root directory of this source tree.
	*/
	var require_factoryWithTypeCheckers = /* @__PURE__ */ __commonJSMin(((exports, module) => {
		var ReactIs = require_react_is();
		var assign = require_object_assign();
		var ReactPropTypesSecret = require_ReactPropTypesSecret();
		var has = require_has();
		var checkPropTypes = require_checkPropTypes();
		var printWarning = function() {};
		printWarning = function(text) {
			var message = "Warning: " + text;
			if (typeof console !== "undefined") console.error(message);
			try {
				throw new Error(message);
			} catch (x) {}
		};
		function emptyFunctionThatReturnsNull() {
			return null;
		}
		module.exports = function(isValidElement, throwOnDirectAccess) {
			var ITERATOR_SYMBOL = typeof Symbol === "function" && Symbol.iterator;
			var FAUX_ITERATOR_SYMBOL = "@@iterator";
			/**
			* Returns the iterator method function contained on the iterable object.
			*
			* Be sure to invoke the function with the iterable as context:
			*
			*     var iteratorFn = getIteratorFn(myIterable);
			*     if (iteratorFn) {
			*       var iterator = iteratorFn.call(myIterable);
			*       ...
			*     }
			*
			* @param {?object} maybeIterable
			* @return {?function}
			*/
			function getIteratorFn(maybeIterable) {
				var iteratorFn = maybeIterable && (ITERATOR_SYMBOL && maybeIterable[ITERATOR_SYMBOL] || maybeIterable[FAUX_ITERATOR_SYMBOL]);
				if (typeof iteratorFn === "function") return iteratorFn;
			}
			/**
			* Collection of methods that allow declaration and validation of props that are
			* supplied to React components. Example usage:
			*
			*   var Props = require('ReactPropTypes');
			*   var MyArticle = React.createClass({
			*     propTypes: {
			*       // An optional string prop named "description".
			*       description: Props.string,
			*
			*       // A required enum prop named "category".
			*       category: Props.oneOf(['News','Photos']).isRequired,
			*
			*       // A prop named "dialog" that requires an instance of Dialog.
			*       dialog: Props.instanceOf(Dialog).isRequired
			*     },
			*     render: function() { ... }
			*   });
			*
			* A more formal specification of how these methods are used:
			*
			*   type := array|bool|func|object|number|string|oneOf([...])|instanceOf(...)
			*   decl := ReactPropTypes.{type}(.isRequired)?
			*
			* Each and every declaration produces a function with the same signature. This
			* allows the creation of custom validation functions. For example:
			*
			*  var MyLink = React.createClass({
			*    propTypes: {
			*      // An optional string or URI prop named "href".
			*      href: function(props, propName, componentName) {
			*        var propValue = props[propName];
			*        if (propValue != null && typeof propValue !== 'string' &&
			*            !(propValue instanceof URI)) {
			*          return new Error(
			*            'Expected a string or an URI for ' + propName + ' in ' +
			*            componentName
			*          );
			*        }
			*      }
			*    },
			*    render: function() {...}
			*  });
			*
			* @internal
			*/
			var ANONYMOUS = "<<anonymous>>";
			var ReactPropTypes = {
				array: createPrimitiveTypeChecker("array"),
				bigint: createPrimitiveTypeChecker("bigint"),
				bool: createPrimitiveTypeChecker("boolean"),
				func: createPrimitiveTypeChecker("function"),
				number: createPrimitiveTypeChecker("number"),
				object: createPrimitiveTypeChecker("object"),
				string: createPrimitiveTypeChecker("string"),
				symbol: createPrimitiveTypeChecker("symbol"),
				any: createAnyTypeChecker(),
				arrayOf: createArrayOfTypeChecker,
				element: createElementTypeChecker(),
				elementType: createElementTypeTypeChecker(),
				instanceOf: createInstanceTypeChecker,
				node: createNodeChecker(),
				objectOf: createObjectOfTypeChecker,
				oneOf: createEnumTypeChecker,
				oneOfType: createUnionTypeChecker,
				shape: createShapeTypeChecker,
				exact: createStrictShapeTypeChecker
			};
			/**
			* inlined Object.is polyfill to avoid requiring consumers ship their own
			* https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/is
			*/
			function is(x, y) {
				if (x === y) return x !== 0 || 1 / x === 1 / y;
				else return x !== x && y !== y;
			}
			/**
			* We use an Error-like object for backward compatibility as people may call
			* PropTypes directly and inspect their output. However, we don't use real
			* Errors anymore. We don't inspect their stack anyway, and creating them
			* is prohibitively expensive if they are created too often, such as what
			* happens in oneOfType() for any type before the one that matched.
			*/
			function PropTypeError(message, data) {
				this.message = message;
				this.data = data && typeof data === "object" ? data : {};
				this.stack = "";
			}
			PropTypeError.prototype = Error.prototype;
			function createChainableTypeChecker(validate) {
				var manualPropTypeCallCache = {};
				var manualPropTypeWarningCount = 0;
				function checkType(isRequired, props, propName, componentName, location, propFullName, secret) {
					componentName = componentName || ANONYMOUS;
					propFullName = propFullName || propName;
					if (secret !== ReactPropTypesSecret) {
						if (throwOnDirectAccess) {
							var err = /* @__PURE__ */ new Error("Calling PropTypes validators directly is not supported by the `prop-types` package. Use `PropTypes.checkPropTypes()` to call them. Read more at http://fb.me/use-check-prop-types");
							err.name = "Invariant Violation";
							throw err;
						} else if (typeof console !== "undefined") {
							var cacheKey = componentName + ":" + propName;
							if (!manualPropTypeCallCache[cacheKey] && manualPropTypeWarningCount < 3) {
								printWarning("You are manually calling a React.PropTypes validation function for the `" + propFullName + "` prop on `" + componentName + "`. This is deprecated and will throw in the standalone `prop-types` package. You may be seeing this warning due to a third-party PropTypes library. See https://fb.me/react-warning-dont-call-proptypes for details.");
								manualPropTypeCallCache[cacheKey] = true;
								manualPropTypeWarningCount++;
							}
						}
					}
					if (props[propName] == null) {
						if (isRequired) {
							if (props[propName] === null) return new PropTypeError("The " + location + " `" + propFullName + "` is marked as required " + ("in `" + componentName + "`, but its value is `null`."));
							return new PropTypeError("The " + location + " `" + propFullName + "` is marked as required in " + ("`" + componentName + "`, but its value is `undefined`."));
						}
						return null;
					} else return validate(props, propName, componentName, location, propFullName);
				}
				var chainedCheckType = checkType.bind(null, false);
				chainedCheckType.isRequired = checkType.bind(null, true);
				return chainedCheckType;
			}
			function createPrimitiveTypeChecker(expectedType) {
				function validate(props, propName, componentName, location, propFullName, secret) {
					var propValue = props[propName];
					if (getPropType(propValue) !== expectedType) {
						var preciseType = getPreciseType(propValue);
						return new PropTypeError("Invalid " + location + " `" + propFullName + "` of type " + ("`" + preciseType + "` supplied to `" + componentName + "`, expected ") + ("`" + expectedType + "`."), { expectedType });
					}
					return null;
				}
				return createChainableTypeChecker(validate);
			}
			function createAnyTypeChecker() {
				return createChainableTypeChecker(emptyFunctionThatReturnsNull);
			}
			function createArrayOfTypeChecker(typeChecker) {
				function validate(props, propName, componentName, location, propFullName) {
					if (typeof typeChecker !== "function") return new PropTypeError("Property `" + propFullName + "` of component `" + componentName + "` has invalid PropType notation inside arrayOf.");
					var propValue = props[propName];
					if (!Array.isArray(propValue)) {
						var propType = getPropType(propValue);
						return new PropTypeError("Invalid " + location + " `" + propFullName + "` of type " + ("`" + propType + "` supplied to `" + componentName + "`, expected an array."));
					}
					for (var i = 0; i < propValue.length; i++) {
						var error = typeChecker(propValue, i, componentName, location, propFullName + "[" + i + "]", ReactPropTypesSecret);
						if (error instanceof Error) return error;
					}
					return null;
				}
				return createChainableTypeChecker(validate);
			}
			function createElementTypeChecker() {
				function validate(props, propName, componentName, location, propFullName) {
					var propValue = props[propName];
					if (!isValidElement(propValue)) {
						var propType = getPropType(propValue);
						return new PropTypeError("Invalid " + location + " `" + propFullName + "` of type " + ("`" + propType + "` supplied to `" + componentName + "`, expected a single ReactElement."));
					}
					return null;
				}
				return createChainableTypeChecker(validate);
			}
			function createElementTypeTypeChecker() {
				function validate(props, propName, componentName, location, propFullName) {
					var propValue = props[propName];
					if (!ReactIs.isValidElementType(propValue)) {
						var propType = getPropType(propValue);
						return new PropTypeError("Invalid " + location + " `" + propFullName + "` of type " + ("`" + propType + "` supplied to `" + componentName + "`, expected a single ReactElement type."));
					}
					return null;
				}
				return createChainableTypeChecker(validate);
			}
			function createInstanceTypeChecker(expectedClass) {
				function validate(props, propName, componentName, location, propFullName) {
					if (!(props[propName] instanceof expectedClass)) {
						var expectedClassName = expectedClass.name || ANONYMOUS;
						var actualClassName = getClassName(props[propName]);
						return new PropTypeError("Invalid " + location + " `" + propFullName + "` of type " + ("`" + actualClassName + "` supplied to `" + componentName + "`, expected ") + ("instance of `" + expectedClassName + "`."));
					}
					return null;
				}
				return createChainableTypeChecker(validate);
			}
			function createEnumTypeChecker(expectedValues) {
				if (!Array.isArray(expectedValues)) {
					if (arguments.length > 1) printWarning("Invalid arguments supplied to oneOf, expected an array, got " + arguments.length + " arguments. A common mistake is to write oneOf(x, y, z) instead of oneOf([x, y, z]).");
					else printWarning("Invalid argument supplied to oneOf, expected an array.");
					return emptyFunctionThatReturnsNull;
				}
				function validate(props, propName, componentName, location, propFullName) {
					var propValue = props[propName];
					for (var i = 0; i < expectedValues.length; i++) if (is(propValue, expectedValues[i])) return null;
					var valuesString = JSON.stringify(expectedValues, function replacer(key, value) {
						if (getPreciseType(value) === "symbol") return String(value);
						return value;
					});
					return new PropTypeError("Invalid " + location + " `" + propFullName + "` of value `" + String(propValue) + "` " + ("supplied to `" + componentName + "`, expected one of " + valuesString + "."));
				}
				return createChainableTypeChecker(validate);
			}
			function createObjectOfTypeChecker(typeChecker) {
				function validate(props, propName, componentName, location, propFullName) {
					if (typeof typeChecker !== "function") return new PropTypeError("Property `" + propFullName + "` of component `" + componentName + "` has invalid PropType notation inside objectOf.");
					var propValue = props[propName];
					var propType = getPropType(propValue);
					if (propType !== "object") return new PropTypeError("Invalid " + location + " `" + propFullName + "` of type " + ("`" + propType + "` supplied to `" + componentName + "`, expected an object."));
					for (var key in propValue) if (has(propValue, key)) {
						var error = typeChecker(propValue, key, componentName, location, propFullName + "." + key, ReactPropTypesSecret);
						if (error instanceof Error) return error;
					}
					return null;
				}
				return createChainableTypeChecker(validate);
			}
			function createUnionTypeChecker(arrayOfTypeCheckers) {
				if (!Array.isArray(arrayOfTypeCheckers)) {
					printWarning("Invalid argument supplied to oneOfType, expected an instance of array.");
					return emptyFunctionThatReturnsNull;
				}
				for (var i = 0; i < arrayOfTypeCheckers.length; i++) {
					var checker = arrayOfTypeCheckers[i];
					if (typeof checker !== "function") {
						printWarning("Invalid argument supplied to oneOfType. Expected an array of check functions, but received " + getPostfixForTypeWarning(checker) + " at index " + i + ".");
						return emptyFunctionThatReturnsNull;
					}
				}
				function validate(props, propName, componentName, location, propFullName) {
					var expectedTypes = [];
					for (var i = 0; i < arrayOfTypeCheckers.length; i++) {
						var checker = arrayOfTypeCheckers[i];
						var checkerResult = checker(props, propName, componentName, location, propFullName, ReactPropTypesSecret);
						if (checkerResult == null) return null;
						if (checkerResult.data && has(checkerResult.data, "expectedType")) expectedTypes.push(checkerResult.data.expectedType);
					}
					var expectedTypesMessage = expectedTypes.length > 0 ? ", expected one of type [" + expectedTypes.join(", ") + "]" : "";
					return new PropTypeError("Invalid " + location + " `" + propFullName + "` supplied to " + ("`" + componentName + "`" + expectedTypesMessage + "."));
				}
				return createChainableTypeChecker(validate);
			}
			function createNodeChecker() {
				function validate(props, propName, componentName, location, propFullName) {
					if (!isNode(props[propName])) return new PropTypeError("Invalid " + location + " `" + propFullName + "` supplied to " + ("`" + componentName + "`, expected a ReactNode."));
					return null;
				}
				return createChainableTypeChecker(validate);
			}
			function invalidValidatorError(componentName, location, propFullName, key, type) {
				return new PropTypeError((componentName || "React class") + ": " + location + " type `" + propFullName + "." + key + "` is invalid; it must be a function, usually from the `prop-types` package, but received `" + type + "`.");
			}
			function createShapeTypeChecker(shapeTypes) {
				function validate(props, propName, componentName, location, propFullName) {
					var propValue = props[propName];
					var propType = getPropType(propValue);
					if (propType !== "object") return new PropTypeError("Invalid " + location + " `" + propFullName + "` of type `" + propType + "` " + ("supplied to `" + componentName + "`, expected `object`."));
					for (var key in shapeTypes) {
						var checker = shapeTypes[key];
						if (typeof checker !== "function") return invalidValidatorError(componentName, location, propFullName, key, getPreciseType(checker));
						var error = checker(propValue, key, componentName, location, propFullName + "." + key, ReactPropTypesSecret);
						if (error) return error;
					}
					return null;
				}
				return createChainableTypeChecker(validate);
			}
			function createStrictShapeTypeChecker(shapeTypes) {
				function validate(props, propName, componentName, location, propFullName) {
					var propValue = props[propName];
					var propType = getPropType(propValue);
					if (propType !== "object") return new PropTypeError("Invalid " + location + " `" + propFullName + "` of type `" + propType + "` " + ("supplied to `" + componentName + "`, expected `object`."));
					for (var key in assign({}, props[propName], shapeTypes)) {
						var checker = shapeTypes[key];
						if (has(shapeTypes, key) && typeof checker !== "function") return invalidValidatorError(componentName, location, propFullName, key, getPreciseType(checker));
						if (!checker) return new PropTypeError("Invalid " + location + " `" + propFullName + "` key `" + key + "` supplied to `" + componentName + "`.\nBad object: " + JSON.stringify(props[propName], null, "  ") + "\nValid keys: " + JSON.stringify(Object.keys(shapeTypes), null, "  "));
						var error = checker(propValue, key, componentName, location, propFullName + "." + key, ReactPropTypesSecret);
						if (error) return error;
					}
					return null;
				}
				return createChainableTypeChecker(validate);
			}
			function isNode(propValue) {
				switch (typeof propValue) {
					case "number":
					case "string":
					case "undefined": return true;
					case "boolean": return !propValue;
					case "object":
						if (Array.isArray(propValue)) return propValue.every(isNode);
						if (propValue === null || isValidElement(propValue)) return true;
						var iteratorFn = getIteratorFn(propValue);
						if (iteratorFn) {
							var iterator = iteratorFn.call(propValue);
							var step;
							if (iteratorFn !== propValue.entries) {
								while (!(step = iterator.next()).done) if (!isNode(step.value)) return false;
							} else while (!(step = iterator.next()).done) {
								var entry = step.value;
								if (entry) {
									if (!isNode(entry[1])) return false;
								}
							}
						} else return false;
						return true;
					default: return false;
				}
			}
			function isSymbol(propType, propValue) {
				if (propType === "symbol") return true;
				if (!propValue) return false;
				if (propValue["@@toStringTag"] === "Symbol") return true;
				if (typeof Symbol === "function" && propValue instanceof Symbol) return true;
				return false;
			}
			function getPropType(propValue) {
				var propType = typeof propValue;
				if (Array.isArray(propValue)) return "array";
				if (propValue instanceof RegExp) return "object";
				if (isSymbol(propType, propValue)) return "symbol";
				return propType;
			}
			function getPreciseType(propValue) {
				if (typeof propValue === "undefined" || propValue === null) return "" + propValue;
				var propType = getPropType(propValue);
				if (propType === "object") {
					if (propValue instanceof Date) return "date";
					else if (propValue instanceof RegExp) return "regexp";
				}
				return propType;
			}
			function getPostfixForTypeWarning(value) {
				var type = getPreciseType(value);
				switch (type) {
					case "array":
					case "object": return "an " + type;
					case "boolean":
					case "date":
					case "regexp": return "a " + type;
					default: return type;
				}
			}
			function getClassName(propValue) {
				if (!propValue.constructor || !propValue.constructor.name) return ANONYMOUS;
				return propValue.constructor.name;
			}
			ReactPropTypes.checkPropTypes = checkPropTypes;
			ReactPropTypes.resetWarningCache = checkPropTypes.resetWarningCache;
			ReactPropTypes.PropTypes = ReactPropTypes;
			return ReactPropTypes;
		};
	}));

//#endregion
//#region node_modules/prop-types/index.js
	var require_prop_types = /* @__PURE__ */ __commonJSMin(((exports, module) => {
		var ReactIs = require_react_is();
		var throwOnDirectAccess = true;
		module.exports = require_factoryWithTypeCheckers()(ReactIs.isElement, throwOnDirectAccess);
	}));

//#endregion
//#region node_modules/@babel/runtime/helpers/esm/extends.js
var import_prop_types = /* @__PURE__ */ __toESM(require_prop_types());
	function _extends$1() {
		return _extends$1 = Object.assign ? Object.assign.bind() : function(n) {
			for (var e = 1; e < arguments.length; e++) {
				var t = arguments[e];
				for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]);
			}
			return n;
		}, _extends$1.apply(null, arguments);
	}
	__name(_extends$1, "_extends");

//#endregion
//#region node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js
	function _arrayWithHoles(r) {
		if (Array.isArray(r)) return r;
	}

//#endregion
//#region node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js
	function _iterableToArrayLimit(r, l) {
		var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"];
		if (null != t) {
			var e;
			var n;
			var i;
			var u;
			var a = [];
			var f = !0;
			var o = !1;
			try {
				if (i = (t = t.call(r)).next, 0 === l) {
					if (Object(t) !== t) return;
					f = !1;
				} else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0);
			} catch (r) {
				o = !0, n = r;
			} finally {
				try {
					if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return;
				} finally {
					if (o) throw n;
				}
			}
			return a;
		}
	}

//#endregion
//#region node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js
	function _arrayLikeToArray(r, a) {
		(null == a || a > r.length) && (a = r.length);
		for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e];
		return n;
	}

//#endregion
//#region node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js
	function _unsupportedIterableToArray(r, a) {
		if (r) {
			if ("string" == typeof r) return _arrayLikeToArray(r, a);
			var t = {}.toString.call(r).slice(8, -1);
			return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0;
		}
	}

//#endregion
//#region node_modules/@babel/runtime/helpers/esm/nonIterableRest.js
	function _nonIterableRest() {
		throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
	}

//#endregion
//#region node_modules/@babel/runtime/helpers/esm/slicedToArray.js
	function _slicedToArray(r, e) {
		return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest();
	}

//#endregion
//#region node_modules/@babel/runtime/helpers/esm/typeof.js
	function _typeof$1(o) {
		"@babel/helpers - typeof";
		return _typeof$1 = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(o) {
			return typeof o;
		} : function(o) {
			return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
		}, _typeof$1(o);
	}
	__name(_typeof$1, "_typeof");

//#endregion
//#region node_modules/@babel/runtime/helpers/esm/toPrimitive.js
	function toPrimitive$1(t, r) {
		if ("object" != _typeof$1(t) || !t) return t;
		var e = t[Symbol.toPrimitive];
		if (void 0 !== e) {
			var i = e.call(t, r || "default");
			if ("object" != _typeof$1(i)) return i;
			throw new TypeError("@@toPrimitive must return a primitive value.");
		}
		return ("string" === r ? String : Number)(t);
	}
	__name(toPrimitive$1, "toPrimitive");

//#endregion
//#region node_modules/@babel/runtime/helpers/esm/toPropertyKey.js
	function toPropertyKey$1(t) {
		var i = toPrimitive$1(t, "string");
		return "symbol" == _typeof$1(i) ? i : i + "";
	}
	__name(toPropertyKey$1, "toPropertyKey");

//#endregion
//#region node_modules/@babel/runtime/helpers/esm/defineProperty.js
	function _defineProperty$1(e, r, t) {
		return (r = toPropertyKey$1(r)) in e ? Object.defineProperty(e, r, {
			value: t,
			enumerable: !0,
			configurable: !0,
			writable: !0
		}) : e[r] = t, e;
	}
	__name(_defineProperty$1, "_defineProperty");

//#endregion
//#region modules/agents/assets/js/admin/constants.js
	var AGENTS_WORD_GRADIENT = "linear-gradient(90.42deg, #0C0D0E 64.85%, #ED01EE 85.22%, #FFFFFF 102%)";
	var MODULE_LLMS_TXT = "llms-txt";
	var MODULE_MARKDOWN_CONTENT = "markdown-content";
	var MODULE_BOT_ACCESS_CONTROL = "bot-access-control";
	var MODULE_AGENT_DISCOVERY = "agent-discovery";
	var MODULE_BROWSER_TOOLS = "browser-tools";
	var LLMS_SETTINGS_KEY = "llms_txt";
	var MARKDOWN_SETTINGS_KEY = "markdown_content";
	var BOT_ACCESS_SETTINGS_KEY = "bot_access_control";
	var AGENT_DISCOVERY_SETTINGS_KEY = "agent_discovery";
	var BROWSER_TOOLS_SETTINGS_KEY = "browser_tools";
	var BUSINESS_DETAILS_SETTINGS_KEY = "business_details";
	var BOT_PERMISSION_SEARCH = "search";
	var BOT_PERMISSION_AI_INPUT = "aiInput";
	var BOT_PERMISSION_AI_TRAIN = "aiTrain";
	var BOT_PERMISSIONS = [
		BOT_PERMISSION_SEARCH,
		BOT_PERMISSION_AI_INPUT,
		BOT_PERMISSION_AI_TRAIN
	];
	var BOT_PERMISSION_SETTING_KEYS = _defineProperty$1(_defineProperty$1(_defineProperty$1({}, BOT_PERMISSION_SEARCH, "search"), BOT_PERMISSION_AI_INPUT, "ai_input"), BOT_PERMISSION_AI_TRAIN, "ai_train");
	var DEFAULT_BOT_PERMISSIONS = _defineProperty$1(_defineProperty$1(_defineProperty$1({}, BOT_PERMISSION_SEARCH, true), BOT_PERMISSION_AI_INPUT, true), BOT_PERMISSION_AI_TRAIN, false);
	var BOT_TABLE_COLUMN_WIDTHS = _defineProperty$1(_defineProperty$1(_defineProperty$1(_defineProperty$1({}, BOT_PERMISSION_SEARCH, 150), BOT_PERMISSION_AI_INPUT, 150), BOT_PERMISSION_AI_TRAIN, 160), "action", 132);
	var LLMS_FILE_NAME = "llms.txt";
	var MAX_EXCLUDED_POSTS = 5e3;
	var MODULE_STATUS_ENABLED = "enabled";
	var MODULE_STATUS_DISABLED = "disabled";
	var MODULE_STATUS_WARNING = "warning";
	var getModules = function getModules() {
		return [
			{
				id: MODULE_LLMS_TXT,
				title: (0, _wordpress_i18n.__)("llms.txt: site guide for AI", "elementor"),
				description: (0, _wordpress_i18n.__)("Help AI agents understand and recommend your site", "elementor")
			},
			{
				id: MODULE_MARKDOWN_CONTENT,
				title: (0, _wordpress_i18n.__)("Markdown pages: clean text for AI", "elementor"),
				description: (0, _wordpress_i18n.__)("Help agents get clearer content", "elementor")
			},
			{
				id: MODULE_BOT_ACCESS_CONTROL,
				title: (0, _wordpress_i18n.__)("robots.txt rules: AI bot access", "elementor"),
				description: (0, _wordpress_i18n.__)("Decide how AI agents can use your content", "elementor")
			},
			{
				id: MODULE_AGENT_DISCOVERY,
				title: (0, _wordpress_i18n.__)("Discovery files (/.well-known)", "elementor"),
				description: (0, _wordpress_i18n.__)("Help agents find what your site offers", "elementor")
			},
			{
				id: MODULE_BROWSER_TOOLS,
				title: (0, _wordpress_i18n.__)("WebMCP tools: in-browser agent actions", "elementor"),
				description: (0, _wordpress_i18n.__)("Help visitors’ agents find answers on your site", "elementor")
			}
		];
	};

//#endregion
//#region modules/agents/assets/js/admin/analytics/agent-ready-events.js
	var EVENT_WELCOME_VIEWED = "agent_readiness_welcome_viewed";
	var EVENT_ACTIVATION_CLICKED = "agent_readiness_activation_clicked";
	var EVENT_FEATURE_TOGGLED = "agent_readiness_feature_toggled";
	var EVENT_POST_TYPE_TOGGLED = "agent_readiness_post_type_toggled";
	var EVENT_BOT_PERMISSION_CHANGED = "agent_readiness_bot_permission_changed";
	var APP_TYPE_WP_ADMIN = "wpadmin";
	var LOCATION_L1 = "agent_readiness";
	var WINDOW_ONBOARDING = "agent_readiness_onboarding";
	var WINDOW_SETTINGS = "agent_readiness_settings";
	var FEATURE_MASTER = "master";
	var FEATURE_LLMS_TXT = "llms_txt";
	var FEATURE_MARKDOWN_CONTENT = "markdown_content";
	var FEATURE_MARKDOWN = "markdown";
	var FEATURE_BOT_ACCESS = "bot_access";
	var FEATURE_WELL_KNOWN = "well_known";
	var SCOPE_SINGLE = "single";
	var SCOPE_BULK = "bulk";
	var TARGET_TYPE_CHECKBOX = "checkbox";
	var TARGET_TYPE_BUTTON = "button";

//#endregion
//#region modules/agents/assets/js/admin/analytics/track-agent-ready-event.js
	function ownKeys$26(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$26, "ownKeys");
	function _objectSpread$25(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$26(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$26(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$25, "_objectSpread");
	var dispatch = function dispatch(eventName, payload) {
		var _window$elementorComm;
		var _window$elementorComm2;
		return (_window$elementorComm = window.elementorCommon) === null || _window$elementorComm === void 0 || (_window$elementorComm = _window$elementorComm.eventsManager) === null || _window$elementorComm === void 0 || (_window$elementorComm2 = _window$elementorComm.dispatchEvent) === null || _window$elementorComm2 === void 0 ? void 0 : _window$elementorComm2.call(_window$elementorComm, eventName, _objectSpread$25({
			app_type: APP_TYPE_WP_ADMIN,
			location_l1: LOCATION_L1
		}, payload));
	};
	var trackWelcomeViewed = function trackWelcomeViewed() {
		return dispatch(EVENT_WELCOME_VIEWED, {
			window_name: WINDOW_ONBOARDING,
			interaction_type: "view",
			target_type: "screen",
			target_name: "welcome_screen",
			interaction_result: "viewed",
			target_location: "main",
			location_l2: "activation"
		});
	};
	var trackActivationClicked = function trackActivationClicked() {
		return dispatch(EVENT_ACTIVATION_CLICKED, {
			window_name: WINDOW_ONBOARDING,
			interaction_type: "click",
			target_type: "button",
			target_name: "activate_button",
			interaction_result: "all_defaults_activated",
			target_location: "main",
			location_l2: "activation"
		});
	};
	var trackFeatureToggled = function trackFeatureToggled(_ref) {
		var featureName = _ref.featureName;
		var isEnabled = _ref.isEnabled;
		var hasConflict = _ref.hasConflict;
		return dispatch(EVENT_FEATURE_TOGGLED, {
			window_name: WINDOW_SETTINGS,
			interaction_type: "click",
			target_type: "toggle",
			target_name: "feature_toggle",
			feature_name: featureName,
			interaction_result: isEnabled ? "feature_enabled" : "feature_disabled",
			target_location: "overview",
			location_l2: "overview",
			has_conflict: !!hasConflict
		});
	};
	var trackPostTypeToggled = function trackPostTypeToggled(_ref2) {
		var featureName = _ref2.featureName;
		var postType = _ref2.postType;
		var isIncluded = _ref2.isIncluded;
		return dispatch(EVENT_POST_TYPE_TOGGLED, {
			window_name: WINDOW_SETTINGS,
			interaction_type: "click",
			target_type: "toggle",
			target_name: "post_type_toggle",
			feature_name: featureName,
			post_type: postType,
			interaction_result: isIncluded ? "included" : "excluded",
			target_location: "main",
			location_l2: featureName
		});
	};
	var trackBotPermissionChanged = function trackBotPermissionChanged(_ref3) {
		var scope = _ref3.scope;
		var crawlerId = _ref3.crawlerId;
		var permissionType = _ref3.permissionType;
		var isAllowed = _ref3.isAllowed;
		var targetType = _ref3.targetType;
		return dispatch(EVENT_BOT_PERMISSION_CHANGED, _objectSpread$25(_objectSpread$25({
			window_name: WINDOW_SETTINGS,
			interaction_type: "click",
			target_type: targetType,
			target_name: "bot_permission_control",
			scope,
			crawler_id: crawlerId
		}, permissionType && { permission_type: permissionType }), {}, {
			interaction_result: isAllowed ? "allowed" : "blocked",
			target_location: "bot_access_table",
			location_l2: "bot_access"
		}));
	};

//#endregion
//#region node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js
	function asyncGeneratorStep$1(n, t, e, r, o, a, c) {
		try {
			var i = n[a](c);
			var u = i.value;
		} catch (n) {
			e(n);
			return;
		}
		i.done ? t(u) : Promise.resolve(u).then(r, o);
	}
	__name(asyncGeneratorStep$1, "asyncGeneratorStep");
	function _asyncToGenerator$1(n) {
		return function() {
			var t = this;
			var e = arguments;
			return new Promise(function(r, o) {
				var a = n.apply(t, e);
				function _next(n) {
					asyncGeneratorStep$1(a, r, o, _next, _throw, "next", n);
				}
				function _throw(n) {
					asyncGeneratorStep$1(a, r, o, _next, _throw, "throw", n);
				}
				_next(void 0);
			});
		};
	}
	__name(_asyncToGenerator$1, "_asyncToGenerator");

//#endregion
//#region node_modules/@babel/runtime/helpers/OverloadYield.js
	var require_OverloadYield = /* @__PURE__ */ __commonJSMin(((exports, module) => {
		function _OverloadYield(e, d) {
			this.v = e, this.k = d;
		}
		module.exports = _OverloadYield, module.exports.__esModule = true, module.exports["default"] = module.exports;
	}));

//#endregion
//#region node_modules/@babel/runtime/helpers/regeneratorDefine.js
	var require_regeneratorDefine = /* @__PURE__ */ __commonJSMin(((exports, module) => {
		function _regeneratorDefine(e, r, n, t) {
			var i = Object.defineProperty;
			try {
				i({}, "", {});
			} catch (e) {
				i = 0;
			}
			module.exports = _regeneratorDefine = function regeneratorDefine(e, r, n, t) {
				function o(r, n) {
					_regeneratorDefine(e, r, function(e) {
						return this._invoke(r, n, e);
					});
				}
				r ? i ? i(e, r, {
					value: n,
					enumerable: !t,
					configurable: !t,
					writable: !t
				}) : e[r] = n : (o("next", 0), o("throw", 1), o("return", 2));
			}, module.exports.__esModule = true, module.exports["default"] = module.exports, _regeneratorDefine(e, r, n, t);
		}
		module.exports = _regeneratorDefine, module.exports.__esModule = true, module.exports["default"] = module.exports;
	}));

//#endregion
//#region node_modules/@babel/runtime/helpers/regenerator.js
	var require_regenerator$1 = /* @__PURE__ */ __commonJSMin(((exports, module) => {
		var regeneratorDefine = require_regeneratorDefine();
		function _regenerator() {
			/*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */
			var e;
			var t;
			var r = "function" == typeof Symbol ? Symbol : {};
			var n = r.iterator || "@@iterator";
			var o = r.toStringTag || "@@toStringTag";
			function i(r, n, o, i) {
				var c = n && n.prototype instanceof Generator ? n : Generator;
				var u = Object.create(c.prototype);
				return regeneratorDefine(u, "_invoke", function(r, n, o) {
					var i;
					var c;
					var u;
					var f = 0;
					var p = o || [];
					var y = !1;
					var G = {
						p: 0,
						n: 0,
						v: e,
						a: d,
						f: d.bind(e, 4),
						d: function d(t, r) {
							return i = t, c = 0, u = e, G.n = r, a;
						}
					};
					function d(r, n) {
						for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) {
							var o;
							var i = p[t];
							var d = G.p;
							var l = i[2];
							r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0));
						}
						if (o || r > 1) return a;
						throw y = !0, n;
					}
					return function(o, p, l) {
						if (f > 1) throw TypeError("Generator is already running");
						for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) {
							i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u);
							try {
								if (f = 2, i) {
									if (c || (o = "next"), t = i[o]) {
										if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object");
										if (!t.done) return t;
										u = t.value, c < 2 && (c = 0);
									} else 1 === c && (t = i["return"]) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1);
									i = e;
								} else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break;
							} catch (t) {
								i = e, c = 1, u = t;
							} finally {
								f = 1;
							}
						}
						return {
							value: t,
							done: y
						};
					};
				}(r, o, i), !0), u;
			}
			var a = {};
			function Generator() {}
			function GeneratorFunction() {}
			function GeneratorFunctionPrototype() {}
			t = Object.getPrototypeOf;
			var c = [][n] ? t(t([][n]())) : (regeneratorDefine(t = {}, n, function() {
				return this;
			}), t);
			var u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c);
			function f(e) {
				return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, regeneratorDefine(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e;
			}
			return GeneratorFunction.prototype = GeneratorFunctionPrototype, regeneratorDefine(u, "constructor", GeneratorFunctionPrototype), regeneratorDefine(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", regeneratorDefine(GeneratorFunctionPrototype, o, "GeneratorFunction"), regeneratorDefine(u), regeneratorDefine(u, o, "Generator"), regeneratorDefine(u, n, function() {
				return this;
			}), regeneratorDefine(u, "toString", function() {
				return "[object Generator]";
			}), (module.exports = _regenerator = function _regenerator() {
				return {
					w: i,
					m: f
				};
			}, module.exports.__esModule = true, module.exports["default"] = module.exports)();
		}
		module.exports = _regenerator, module.exports.__esModule = true, module.exports["default"] = module.exports;
	}));

//#endregion
//#region node_modules/@babel/runtime/helpers/regeneratorAsyncIterator.js
	var require_regeneratorAsyncIterator = /* @__PURE__ */ __commonJSMin(((exports, module) => {
		var OverloadYield = require_OverloadYield();
		var regeneratorDefine = require_regeneratorDefine();
		function AsyncIterator(t, e) {
			function n(r, o, i, f) {
				try {
					var c = t[r](o);
					var u = c.value;
					return u instanceof OverloadYield ? e.resolve(u.v).then(function(t) {
						n("next", t, i, f);
					}, function(t) {
						n("throw", t, i, f);
					}) : e.resolve(u).then(function(t) {
						c.value = t, i(c);
					}, function(t) {
						return n("throw", t, i, f);
					});
				} catch (t) {
					f(t);
				}
			}
			var r;
			this.next || (regeneratorDefine(AsyncIterator.prototype), regeneratorDefine(AsyncIterator.prototype, "function" == typeof Symbol && Symbol.asyncIterator || "@asyncIterator", function() {
				return this;
			})), regeneratorDefine(this, "_invoke", function(t, o, i) {
				function f() {
					return new e(function(e, r) {
						n(t, i, e, r);
					});
				}
				return r = r ? r.then(f, f) : f();
			}, !0);
		}
		module.exports = AsyncIterator, module.exports.__esModule = true, module.exports["default"] = module.exports;
	}));

//#endregion
//#region node_modules/@babel/runtime/helpers/regeneratorAsyncGen.js
	var require_regeneratorAsyncGen = /* @__PURE__ */ __commonJSMin(((exports, module) => {
		var regenerator = require_regenerator$1();
		var regeneratorAsyncIterator = require_regeneratorAsyncIterator();
		function _regeneratorAsyncGen(r, e, t, o, n) {
			return new regeneratorAsyncIterator(regenerator().w(r, e, t, o), n || Promise);
		}
		module.exports = _regeneratorAsyncGen, module.exports.__esModule = true, module.exports["default"] = module.exports;
	}));

//#endregion
//#region node_modules/@babel/runtime/helpers/regeneratorAsync.js
	var require_regeneratorAsync = /* @__PURE__ */ __commonJSMin(((exports, module) => {
		var regeneratorAsyncGen = require_regeneratorAsyncGen();
		function _regeneratorAsync(n, e, r, t, o) {
			var a = regeneratorAsyncGen(n, e, r, t, o);
			return a.next().then(function(n) {
				return n.done ? n.value : a.next();
			});
		}
		module.exports = _regeneratorAsync, module.exports.__esModule = true, module.exports["default"] = module.exports;
	}));

//#endregion
//#region node_modules/@babel/runtime/helpers/regeneratorKeys.js
	var require_regeneratorKeys = /* @__PURE__ */ __commonJSMin(((exports, module) => {
		function _regeneratorKeys(e) {
			var n = Object(e);
			var r = [];
			for (var t in n) r.unshift(t);
			return function e() {
				for (; r.length;) if ((t = r.pop()) in n) return e.value = t, e.done = !1, e;
				return e.done = !0, e;
			};
		}
		module.exports = _regeneratorKeys, module.exports.__esModule = true, module.exports["default"] = module.exports;
	}));

//#endregion
//#region node_modules/@babel/runtime/helpers/typeof.js
	var require_typeof = /* @__PURE__ */ __commonJSMin(((exports, module) => {
		function _typeof(o) {
			"@babel/helpers - typeof";
			return module.exports = _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(o) {
				return typeof o;
			} : function(o) {
				return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
			}, module.exports.__esModule = true, module.exports["default"] = module.exports, _typeof(o);
		}
		module.exports = _typeof, module.exports.__esModule = true, module.exports["default"] = module.exports;
	}));

//#endregion
//#region node_modules/@babel/runtime/helpers/regeneratorValues.js
	var require_regeneratorValues = /* @__PURE__ */ __commonJSMin(((exports, module) => {
		var _typeof = require_typeof()["default"];
		function _regeneratorValues(e) {
			if (null != e) {
				var t = e["function" == typeof Symbol && Symbol.iterator || "@@iterator"];
				var r = 0;
				if (t) return t.call(e);
				if ("function" == typeof e.next) return e;
				if (!isNaN(e.length)) return { next: function next() {
					return e && r >= e.length && (e = void 0), {
						value: e && e[r++],
						done: !e
					};
				} };
			}
			throw new TypeError(_typeof(e) + " is not iterable");
		}
		module.exports = _regeneratorValues, module.exports.__esModule = true, module.exports["default"] = module.exports;
	}));

//#endregion
//#region node_modules/@babel/runtime/helpers/regeneratorRuntime.js
	var require_regeneratorRuntime = /* @__PURE__ */ __commonJSMin(((exports, module) => {
		var OverloadYield = require_OverloadYield();
		var regenerator = require_regenerator$1();
		var regeneratorAsync = require_regeneratorAsync();
		var regeneratorAsyncGen = require_regeneratorAsyncGen();
		var regeneratorAsyncIterator = require_regeneratorAsyncIterator();
		var regeneratorKeys = require_regeneratorKeys();
		var regeneratorValues = require_regeneratorValues();
		function _regeneratorRuntime() {
			"use strict";
			var r = regenerator();
			var e = r.m(_regeneratorRuntime);
			var t = (Object.getPrototypeOf ? Object.getPrototypeOf(e) : e.__proto__).constructor;
			function n(r) {
				var e = "function" == typeof r && r.constructor;
				return !!e && (e === t || "GeneratorFunction" === (e.displayName || e.name));
			}
			var o = {
				"throw": 1,
				"return": 2,
				"break": 3,
				"continue": 3
			};
			function a(r) {
				var e;
				var t;
				return function(n) {
					e || (e = {
						stop: function stop() {
							return t(n.a, 2);
						},
						"catch": function _catch() {
							return n.v;
						},
						abrupt: function abrupt(r, e) {
							return t(n.a, o[r], e);
						},
						delegateYield: function delegateYield(r, o, a) {
							return e.resultName = o, t(n.d, regeneratorValues(r), a);
						},
						finish: function finish(r) {
							return t(n.f, r);
						}
					}, t = function t(r, _t, o) {
						n.p = e.prev, n.n = e.next;
						try {
							return r(_t, o);
						} finally {
							e.next = n.n;
						}
					}), e.resultName && (e[e.resultName] = n.v, e.resultName = void 0), e.sent = n.v, e.next = n.n;
					try {
						return r.call(this, e);
					} finally {
						n.p = e.prev, n.n = e.next;
					}
				};
			}
			return (module.exports = _regeneratorRuntime = function _regeneratorRuntime() {
				return {
					wrap: function wrap(e, t, n, o) {
						return r.w(a(e), t, n, o && o.reverse());
					},
					isGeneratorFunction: n,
					mark: r.m,
					awrap: function awrap(r, e) {
						return new OverloadYield(r, e);
					},
					AsyncIterator: regeneratorAsyncIterator,
					async: function async(r, e, t, o, u) {
						return (n(e) ? regeneratorAsyncGen : regeneratorAsync)(a(r), e, t, o, u);
					},
					keys: regeneratorKeys,
					values: regeneratorValues
				};
			}, module.exports.__esModule = true, module.exports["default"] = module.exports)();
		}
		module.exports = _regeneratorRuntime, module.exports.__esModule = true, module.exports["default"] = module.exports;
	}));

//#endregion
//#region node_modules/@babel/runtime/regenerator/index.js
	var require_regenerator = /* @__PURE__ */ __commonJSMin(((exports, module) => {
		var runtime = require_regeneratorRuntime()();
		module.exports = runtime;
		try {
			regeneratorRuntime = runtime;
		} catch (accidentalStrictMode) {
			if (typeof globalThis === "object") globalThis.regeneratorRuntime = runtime;
			else Function("r", "regeneratorRuntime = r")(runtime);
		}
	}));

//#endregion
//#region modules/agents/assets/js/admin/scanner/scanner-check-settings.js
var import_regenerator = /* @__PURE__ */ __toESM(require_regenerator());
	var TOGGLE_CHECK_MODULE_KEYS = {
		"agent-path": AGENT_DISCOVERY_SETTINGS_KEY,
		"agent-discovery": AGENT_DISCOVERY_SETTINGS_KEY,
		"agent-discovery-file": AGENT_DISCOVERY_SETTINGS_KEY,
		"ard-manifest": AGENT_DISCOVERY_SETTINGS_KEY,
		"bot-rules": BOT_ACCESS_SETTINGS_KEY,
		"bot-access-control": BOT_ACCESS_SETTINGS_KEY,
		"llms-txt-exists": LLMS_SETTINGS_KEY,
		"llms-txt": LLMS_SETTINGS_KEY,
		"markdown-pages": MARKDOWN_SETTINGS_KEY,
		"markdown-content": MARKDOWN_SETTINGS_KEY,
		"agent-friendly-404s": MARKDOWN_SETTINGS_KEY,
		"agent-friendly-404": MARKDOWN_SETTINGS_KEY,
		"webmcp-support": BROWSER_TOOLS_SETTINGS_KEY,
		"webmcp-manifest": BROWSER_TOOLS_SETTINGS_KEY,
		webmcp: BROWSER_TOOLS_SETTINGS_KEY,
		"webmcp-json": BROWSER_TOOLS_SETTINGS_KEY,
		"agent-browser-tools": BROWSER_TOOLS_SETTINGS_KEY,
		"browser-tools": BROWSER_TOOLS_SETTINGS_KEY
	};
	var UNAVAILABLE_REASON_CONFIG_KEYS = _defineProperty$1(_defineProperty$1(_defineProperty$1({}, LLMS_SETTINGS_KEY, "llmsConfig"), BOT_ACCESS_SETTINGS_KEY, "botAccessConfig"), BROWSER_TOOLS_SETTINGS_KEY, "browserToolsConfig");
	var isSettingEnabled = function isSettingEnabled(config) {
		return Boolean(config === null || config === void 0 ? void 0 : config.enabled);
	};
	var isAvailable = function isAvailable(config) {
		return !(config !== null && config !== void 0 && config.unavailableReason);
	};
	var normalizeCheckId = function normalizeCheckId(checkId) {
		return "string" === typeof checkId ? checkId.trim().toLowerCase() : "";
	};
	var getToggleModuleKey = function getToggleModuleKey(checkId) {
		var _TOGGLE_CHECK_MODULE_;
		return (_TOGGLE_CHECK_MODULE_ = TOGGLE_CHECK_MODULE_KEYS[normalizeCheckId(checkId)]) !== null && _TOGGLE_CHECK_MODULE_ !== void 0 ? _TOGGLE_CHECK_MODULE_ : null;
	};
	var getModuleUnavailableReason = function getModuleUnavailableReason(moduleKey) {
		var _configs$configKey;
		var configs = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
		var configKey = UNAVAILABLE_REASON_CONFIG_KEYS[moduleKey];
		return configKey && ((_configs$configKey = configs[configKey]) === null || _configs$configKey === void 0 ? void 0 : _configs$configKey.unavailableReason) || null;
	};
	var isAgentReadyModuleToggleDisabled = function isAgentReadyModuleToggleDisabled(moduleKey) {
		return null !== getModuleUnavailableReason(moduleKey, arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {});
	};
	var getToggleSettingsPatch = function getToggleSettingsPatch(checkId, isEnabled) {
		var moduleKey = getToggleModuleKey(checkId);
		if (!moduleKey) return null;
		return _defineProperty$1({}, moduleKey, { enabled: isEnabled });
	};
	var getInitialModuleEnabled = function getInitialModuleEnabled() {
		var _ref2 = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : {};
		var llmsConfig = _ref2.llmsConfig;
		var markdownConfig = _ref2.markdownConfig;
		var botAccessConfig = _ref2.botAccessConfig;
		var agentDiscoveryConfig = _ref2.agentDiscoveryConfig;
		var browserToolsConfig = _ref2.browserToolsConfig;
		return _defineProperty$1(_defineProperty$1(_defineProperty$1(_defineProperty$1(_defineProperty$1({}, LLMS_SETTINGS_KEY, isSettingEnabled(llmsConfig) && isAvailable(llmsConfig)), MARKDOWN_SETTINGS_KEY, isSettingEnabled(markdownConfig)), BOT_ACCESS_SETTINGS_KEY, isSettingEnabled(botAccessConfig) && isAvailable(botAccessConfig)), AGENT_DISCOVERY_SETTINGS_KEY, isSettingEnabled(agentDiscoveryConfig)), BROWSER_TOOLS_SETTINGS_KEY, isSettingEnabled(browserToolsConfig) && isAvailable(browserToolsConfig));
	};
	var hasDirtyModuleChecks = function hasDirtyModuleChecks(checks, dirtyModuleKeys) {
		return checks.some(function(check) {
			var moduleKey = getToggleModuleKey(check.id);
			return Boolean(moduleKey && (dirtyModuleKeys === null || dirtyModuleKeys === void 0 ? void 0 : dirtyModuleKeys[moduleKey]));
		});
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/scanner-check-copy.js
	var CHECK_COPY = {};
	var getCheckCopy = function getCheckCopy(checkId) {
		var _CHECK_COPY$checkId;
		return (_CHECK_COPY$checkId = CHECK_COPY[checkId]) !== null && _CHECK_COPY$checkId !== void 0 ? _CHECK_COPY$checkId : {};
	};
	var getCheckTitle = function getCheckTitle(checkId, label, isPassing) {
		var copy = getCheckCopy(checkId);
		return (isPassing ? copy.passingTitle : copy.failingTitle) || label;
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/scanner-constants.js
	function ownKeys$25(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$25, "ownKeys");
	function _objectSpread$24(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$25(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$25(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$24, "_objectSpread");
	var SCANNER_STATE_FIRST_RUN = "first_run";
	var SCANNER_STATE_PROGRESS = "progress";
	var SCANNER_STATE_FIRST_CHECK = "first_check";
	var SCANNER_STATE_ACTIVATING = "activating";
	var SCANNER_STATE_FIXES = "fixes";
	var SCANNER_STATE_ERROR_PROGRESS = "error_progress";
	var SCANNER_STATE_ERROR_REPORT = "error_report";
	var SCANNER_STATE_RESCANNING = "rescanning";
	var FIX_WITH_TOGGLE = "toggle";
	var FIX_WITH_ANGIE = "angie";
	var FIX_WITH_PREFERENCE = "preference";
	var FIX_WITH_LINK = "link";
	var FIX_WITH_MANUAL = "manual";
	var isPromptBasedFix = function isPromptBasedFix(check) {
		return "angie" === (check === null || check === void 0 ? void 0 : check.fixWith) && Boolean(check === null || check === void 0 ? void 0 : check.fixPrompt);
	};
	var FIX_BY_SEO_PLUGIN = "seo plugin";
	var FIX_BY_ANGIE = "angie";
	var FIX_BY_ANGIE_MCP = "angie / mcp";
	var FIX_BY_ALLY = "ally";
	var FIX_BY_MANUAL = "manual";
	var FIX_BY_BUILT_IN = "built in";
	var PRIORITY_REQUIRED = "required";
	var PRIORITY_RECOMMENDED = "recommended";
	var TIER_REQUIRED = "required";
	var TIER_RECOMMENDED = "recommended";
	var TIER_EMERGING = "emerging";
	var PRIORITY_BY_TIER = _defineProperty$1(_defineProperty$1({}, TIER_REQUIRED, PRIORITY_REQUIRED), TIER_RECOMMENDED, PRIORITY_RECOMMENDED);
	var normalizeTier = function normalizeTier(tier) {
		return "string" === typeof tier ? tier.toLowerCase() : "";
	};
	var getPriorityFromTier = function getPriorityFromTier(tier) {
		var _PRIORITY_BY_TIER$nor;
		return (_PRIORITY_BY_TIER$nor = PRIORITY_BY_TIER[normalizeTier(tier)]) !== null && _PRIORITY_BY_TIER$nor !== void 0 ? _PRIORITY_BY_TIER$nor : null;
	};
	var isEmergingTier = function isEmergingTier(tier) {
		return TIER_EMERGING === normalizeTier(tier);
	};
	var getCheckPriorityLabel = function getCheckPriorityLabel(_ref) {
		var tier = _ref.tier;
		var priority = _ref.priority;
		if ("required" === priority) return (0, _wordpress_i18n.__)("Required", "elementor");
		if ("recommended" === priority) return (0, _wordpress_i18n.__)("Recommended", "elementor");
		return isEmergingTier(tier) ? (0, _wordpress_i18n.__)("Emerging", "elementor") : "";
	};
	var CHECK_STATUS_PASS = "pass";
	var CHECK_STATUS_FAIL = "fail";
	var CHECK_STATUS_PARTIAL = "partial";
	var FAILING_CHECK_STATUSES = [CHECK_STATUS_FAIL, CHECK_STATUS_PARTIAL];
	var normalizeCheckStatus = function normalizeCheckStatus(status) {
		return "string" === typeof status ? status.toLowerCase() : "";
	};
	var getCheckStatusDotColor = function getCheckStatusDotColor(status) {
		var normalized = normalizeCheckStatus(status);
		if ("partial" === normalized) return "warning.light";
		if ("pass" === normalized) return "success.light";
		return "error.light";
	};
	var getCheckStatusLabel = function getCheckStatusLabel(status) {
		var normalized = normalizeCheckStatus(status);
		if ("partial" === normalized) return (0, _wordpress_i18n.__)("Partial", "elementor");
		if ("pass" === normalized) return (0, _wordpress_i18n.__)("Pass", "elementor");
		if ("fail" === normalized) return (0, _wordpress_i18n.__)("Fail", "elementor");
		return "";
	};
	var OUTSIDE_SCAN_STATUS_FAILED = "failed";
	var SCAN_FAILURE_REASON_BLOCKED = "blocked";
	var SCAN_FAILURE_REASON_LOCAL = "local";
	var STAGE_REACH = "reach";
	var STAGE_IDENTIFY = "identify";
	var STAGE_ACT = "act";
	var STAGE_COMMERCE = "commerce";
	var PROGRESS_STEP_DONE = "done";
	var PROGRESS_STEP_ACTIVE = "active";
	var PROGRESS_STEP_PENDING = "pending";
	var PROGRESS_STEP_FAILED = "failed";
	var getStageLabels = function getStageLabels() {
		return _defineProperty$1(_defineProperty$1(_defineProperty$1(_defineProperty$1({}, STAGE_REACH, (0, _wordpress_i18n.__)("Find", "elementor")), STAGE_IDENTIFY, (0, _wordpress_i18n.__)("Read", "elementor")), "act", (0, _wordpress_i18n.__)("Act", "elementor")), STAGE_COMMERCE, (0, _wordpress_i18n.__)("Commerce", "elementor"));
	};
	var getStageQuestions = function getStageQuestions() {
		return _defineProperty$1(_defineProperty$1(_defineProperty$1(_defineProperty$1({}, STAGE_REACH, (0, _wordpress_i18n.__)("Can agents get to your site?", "elementor")), STAGE_IDENTIFY, (0, _wordpress_i18n.__)("Can agents understand what your business offers?", "elementor")), "act", (0, _wordpress_i18n.__)("Can agents take action, like filling in a form?", "elementor")), STAGE_COMMERCE, "");
	};
	var SCANNER_COLORS = {
		paper: "#fff",
		tableHeader: "#f3f4f7",
		ringTrack: "#edeef2",
		banner: "#fbf4eb",
		bannerBorder: "#d97706",
		activate: "#bb5b1d",
		activateHover: "#a14e19",
		accent: "#c00bb9",
		accentHover: "#a10a9b",
		rowHover: "#f9fafa",
		switchOffTrack: "#d5dadf",
		infoChipBackground: "#ebf2fe",
		infoChipText: "#1945a4",
		secondaryChipBackground: "#f0f0f2",
		secondaryChipText: "#383e44",
		countBadge: "#fbf2fb",
		stepBadge: "#f3f4f7",
		promptBackground: "#f9fafa"
	};
	var DOT_FIELD = {
		size: 1.8,
		grid: 7,
		maxDots: 15e3,
		rgb: "89, 89, 89",
		opacityMin: .02,
		opacityStep: .02,
		opacitySteps: 11,
		opacityBias: 2,
		opacityFloor: .4,
		maxDevicePixelRatio: 2,
		hashColumn: 127.1,
		hashRow: 311.7,
		hashScale: 43758.5453,
		driftScaleX: .02,
		driftScaleY: .011,
		secondaryDrift: .7
	};
	var SCORE_TIER_LOW = "low";
	var SCORE_TIER_MID = "mid";
	var SCORE_TIER_HIGH = "high";
	var SCORE_RING_GRADIENT_VECTOR = {
		x1: -28,
		y1: -35.5,
		x2: 95.5,
		y2: 56
	};
	var SCORE_RING_GRADIENTS = _defineProperty$1(_defineProperty$1(_defineProperty$1({}, "low", _objectSpread$24({
		from: "#93003F",
		to: "#EF4444"
	}, SCORE_RING_GRADIENT_VECTOR)), "mid", _objectSpread$24({
		from: "#D97706",
		to: "#FFCB00"
	}, SCORE_RING_GRADIENT_VECTOR)), SCORE_TIER_HIGH, _objectSpread$24({
		from: "#0A875A",
		to: "#10B981"
	}, SCORE_RING_GRADIENT_VECTOR));
	var getStageHealthLabels = function getStageHealthLabels() {
		return _defineProperty$1(_defineProperty$1(_defineProperty$1({}, "low", (0, _wordpress_i18n.__)("Needs work", "elementor")), "mid", (0, _wordpress_i18n.__)("Getting there", "elementor")), SCORE_TIER_HIGH, (0, _wordpress_i18n.__)("Strong", "elementor"));
	};
	var STAGE_HEALTH_COLORS = _defineProperty$1(_defineProperty$1(_defineProperty$1({}, "low", {
		fill: "error.light",
		text: "error.main"
	}), "mid", {
		fill: "warning.light",
		text: "warning.main"
	}), SCORE_TIER_HIGH, {
		fill: "success.light",
		text: "success.main"
	});
	var STAGE_COMING_SOON_FILL = "action.disabled";
	var SCANNER_LAYOUT = {
		pageGap: 32,
		summaryBlockBottomSpace: 48,
		progressCardWidth: 450,
		progressTrackWash: .6,
		wrapInlinePadding: 32,
		wrapBlockStart: 24,
		progressPaneMinHeight: "calc(100vh - var(--e-admin-bar-height, 0px) - var(--e-top-bar-header-height, 0px))",
		checkingFontSize: 13,
		checkingLineHeight: "18px",
		checkingLetterSpacing: "0.16px",
		actionAreaWidth: 180,
		actionColumnWidth: 200,
		statusIconSlot: 20,
		completedIconSize: 24,
		completedActionHeight: 28,
		chevronSize: 24,
		countBadgeSize: 24,
		drawerRadius: "12px",
		preferenceRows: 4,
		prioritySelectWidth: 120,
		skeletonBlockHeight: 16,
		skeletonHeadingWidth: 120,
		skeletonHeadingHeight: 32,
		skeletonMetaWidth: 406,
		skeletonSectionHeadingWidth: 138,
		skeletonStageHeight: 130,
		skeletonRowHeight: 80,
		skeletonRowCount: 4,
		tableColumnWidth: 150,
		passingStageWidth: 100,
		fixRowMinHeight: 61,
		passingRowHeight: 46,
		tableHeaderHeight: 32,
		statusDotSize: 8,
		ringSize: 100,
		ringStroke: 10,
		cardRadius: "12px",
		floatingBarWidth: 500,
		floatingBarHeight: 52,
		floatingBarBottomOffset: 24,
		featureCardRadius: "16px"
	};
	var SCANNER_MOTION = {
		ringArriveMs: 950,
		ringActivatingMs: 2e3,
		ringLandMs: 800,
		drawerMs: 220,
		chevronMs: 220,
		completedCheckMs: 320,
		rowAppearMs: 260,
		dotShimmerMs: 12e3,
		errorProgressPauseMs: 1500,
		rescanMinMs: 800,
		easeOutExpo: "cubic-bezier(.22, 1, .36, 1)",
		easeOutCubic: "cubic-bezier(.33, 1, .68, 1)",
		overshoot: "cubic-bezier(.34, 1.56, .64, 1)"
	};
	var REDUCED_MOTION_MEDIA = "(prefers-reduced-motion: reduce)";
	var REDUCED_MOTION_QUERY = "@media ".concat(REDUCED_MOTION_MEDIA);
	var REDUCED_MOTION_SX = _defineProperty$1({}, REDUCED_MOTION_QUERY, {
		transition: "none",
		animation: "none"
	});
	var VISUALLY_HIDDEN_SX = {
		position: "absolute",
		width: "1px",
		height: "1px",
		overflow: "hidden",
		clip: "rect(0 0 0 0)",
		whiteSpace: "nowrap"
	};
	var SCAN_SOURCE_URL = "https://ora.ai/docs";
	var sectionHeadingSx = {
		fontSize: "15px",
		fontWeight: 600,
		lineHeight: "normal",
		letterSpacing: "1px",
		textTransform: "uppercase"
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/scanner-check-fixes.js
	var PROMPT_MARKER = "prompt:";
	var QUOTED_TEXT = /"([^"]*)"/;
	var ANGIE_FIX_BY = [
		FIX_BY_ANGIE,
		FIX_BY_ANGIE_MCP,
		FIX_BY_ALLY
	];
	var normalizeFix = function normalizeFix(fixText) {
		return "string" === typeof fixText ? fixText : "";
	};
	var normalizeFixBy = function normalizeFixBy(fixBy) {
		return "string" === typeof fixBy ? fixBy.trim().toLowerCase() : "";
	};
	var stripPromptPrefix = function stripPromptPrefix(text) {
		var trimmed = text.trim();
		if (trimmed.toLowerCase().startsWith(PROMPT_MARKER)) return trimmed.slice(PROMPT_MARKER.length).trim();
		return trimmed;
	};
	var getQuotedOrPromptText = function getQuotedOrPromptText(instructionText) {
		var quoted = instructionText.match(QUOTED_TEXT);
		if (quoted) return stripPromptPrefix(quoted[1]);
		var markerAt = instructionText.toLowerCase().indexOf(PROMPT_MARKER);
		if (markerAt >= 0) return instructionText.slice(markerAt + PROMPT_MARKER.length).trim();
		return "";
	};
	var hasPromptMarker = function hasPromptMarker(instructionText) {
		return instructionText.toLowerCase().includes(PROMPT_MARKER);
	};
	var getInstructionText = function getInstructionText(check) {
		var fixDetails = normalizeFix(check === null || check === void 0 ? void 0 : check.fixDetails).trim();
		if (fixDetails && "n/a" !== fixDetails.toLowerCase()) return fixDetails;
		return normalizeFix(check === null || check === void 0 ? void 0 : check.fix).trim();
	};
	var manualFix = function manualFix(howToFix) {
		return {
			fixWith: FIX_WITH_MANUAL,
			fixPrompt: "",
			howToFix,
			resolvedByActivation: false
		};
	};
	var angieFix = function angieFix(instructionText) {
		var prompt = getQuotedOrPromptText(instructionText) || instructionText.trim();
		if (!prompt) return manualFix("");
		return {
			fixWith: FIX_WITH_ANGIE,
			fixPrompt: prompt,
			howToFix: prompt,
			resolvedByActivation: false
		};
	};
	var toggleFix = function toggleFix(howToFix) {
		return {
			fixWith: FIX_WITH_TOGGLE,
			fixPrompt: "",
			howToFix,
			resolvedByActivation: true
		};
	};
	var seoFix = function seoFix(howToFix) {
		return {
			fixWith: "seo",
			fixPrompt: "",
			howToFix,
			resolvedByActivation: false
		};
	};
	var classifyCheckFix = function classifyCheckFix(check) {
		var instructionText = getInstructionText(check);
		if (getToggleModuleKey(check === null || check === void 0 ? void 0 : check.id)) return toggleFix(instructionText);
		var fixBy = normalizeFixBy(check === null || check === void 0 ? void 0 : check.fixBy);
		if ("seo plugin" === fixBy) return seoFix(instructionText);
		if (ANGIE_FIX_BY.includes(fixBy)) return angieFix(instructionText);
		if ("manual" === fixBy || "built in" === fixBy) {
			if (hasPromptMarker(instructionText)) return angieFix(instructionText);
			return manualFix(instructionText);
		}
		if (hasPromptMarker(instructionText)) return angieFix(instructionText);
		return manualFix(instructionText);
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/scanner-score.js
	var READINESS_SCORE_MAX = 100;
	var getScoreTier = function getScoreTier(score) {
		if (score <= 30) return "low";
		if (score < 80) return "mid";
		return SCORE_TIER_HIGH;
	};
	var getStagePassPercent = function getStagePassPercent(stage) {
		if (stage.isComingSoon || stage.total <= 0) return null;
		return normalizeReadinessScore(stage.earned / stage.total * 100);
	};
	var getStageHealthTier = function getStageHealthTier(stage) {
		var passPercent = getStagePassPercent(stage);
		return null === passPercent ? null : getScoreTier(passPercent);
	};
	var getVisibleChecks = function getVisibleChecks(scan) {
		var _scan$checks;
		return (_scan$checks = scan === null || scan === void 0 ? void 0 : scan.checks) !== null && _scan$checks !== void 0 ? _scan$checks : [];
	};
	var normalizeReadinessScore = function normalizeReadinessScore(score) {
		if ("number" !== typeof score) return null;
		return Math.round(Math.min(Math.max(score, 0), 100));
	};
	var getBaseScore = function getBaseScore(scan) {
		return normalizeReadinessScore(scan === null || scan === void 0 ? void 0 : scan.score);
	};
	var hasPendingFixes = function hasPendingFixes(checks, resolvedIds) {
		return checks.some(function(check) {
			return resolvedIds[check.id];
		});
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/scanner-report-adapter.js
	function ownKeys$24(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$24, "ownKeys");
	function _objectSpread$23(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$24(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$24(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$23, "_objectSpread");
	var LAYER_STAGES = {
		discovery: STAGE_REACH,
		accessibility: STAGE_IDENTIFY,
		usability: "act",
		payments: STAGE_COMMERCE
	};
	var toNumber = function toNumber(value) {
		return "number" === typeof value ? value : 0;
	};
	var TASK_STAGES = {
		find: STAGE_REACH,
		read: STAGE_IDENTIFY,
		act: "act"
	};
	var toLowerKey = function toLowerKey(value) {
		return "string" === typeof value ? value.toLowerCase() : "";
	};
	var getCheckStage = function getCheckStage(check) {
		var _TASK_STAGES$toLowerK;
		return (_TASK_STAGES$toLowerK = TASK_STAGES[toLowerKey(check.task)]) !== null && _TASK_STAGES$toLowerK !== void 0 ? _TASK_STAGES$toLowerK : LAYER_STAGES[toLowerKey(check.layer)];
	};
	var toStages = function toStages(checks) {
		return Object.values(LAYER_STAGES).map(function(stageId) {
			var stageChecks = checks.filter(function(check) {
				return check.scorable && getCheckStage(check) === stageId;
			});
			var total = stageChecks.length;
			return _objectSpread$23({
				id: stageId,
				earned: stageChecks.filter(function(check) {
					return CHECK_STATUS_PASS === check.status;
				}).length,
				total
			}, "commerce" === stageId && 0 === total ? { isComingSoon: true } : {});
		});
	};
	var getActiveWeight = function getActiveWeight(layers) {
		return Object.values(layers).filter(function(layer) {
			return toNumber(layer === null || layer === void 0 ? void 0 : layer.possible) > 0;
		}).reduce(function(total, layer) {
			return total + toNumber(layer.weight);
		}, 0);
	};
	var getScoreShare = function getScoreShare(checkPoints, layer, activeWeight) {
		var layerPossible = toNumber(layer === null || layer === void 0 ? void 0 : layer.possible);
		if (!checkPoints || !layerPossible || !activeWeight) return 0;
		return Math.round(checkPoints / layerPossible * (toNumber(layer.weight) / activeWeight) * 100);
	};
	var getMissingPoints = function getMissingPoints(check) {
		return check.scorable ? Math.max(0, toNumber(check.possible) - toNumber(check.earned)) : 0;
	};
	var toFailingCheck = function toFailingCheck(check, layers, activeWeight) {
		var _check$message;
		var _check$description;
		var classified = classifyCheckFix(check);
		return {
			id: check.id,
			title: getCheckTitle(check.id, check.label, false),
			technicalName: check.label,
			points: getScoreShare(getMissingPoints(check), layers[check.layer], activeWeight),
			tier: check.tier,
			priority: getPriorityFromTier(check.tier),
			status: check.status,
			stage: getCheckStage(check),
			evidence: (_check$message = check.message) !== null && _check$message !== void 0 ? _check$message : "",
			explanation: {
				whatItMeans: (_check$description = check.description) !== null && _check$description !== void 0 ? _check$description : "",
				howToFix: classified.howToFix
			},
			fixPrompt: classified.fixPrompt,
			fixWith: classified.fixWith,
			resolvedByActivation: classified.resolvedByActivation
		};
	};
	var toPassingCheck = function toPassingCheck(check, layers, activeWeight) {
		var _check$description2;
		var _check$message2;
		return {
			id: check.id,
			title: getCheckTitle(check.id, check.label, true),
			technicalName: check.label,
			description: (_check$description2 = check.description) !== null && _check$description2 !== void 0 ? _check$description2 : "",
			evidence: (_check$message2 = check.message) !== null && _check$message2 !== void 0 ? _check$message2 : "",
			stage: getCheckStage(check),
			points: getScoreShare(toNumber(check.earned), layers[check.layer], activeWeight)
		};
	};
	var byPointsDescending = function byPointsDescending(first, second) {
		return second.points - first.points;
	};
	var toRetryTimestamp = function toRetryTimestamp(retryAt) {
		var timestamp = retryAt ? Date.parse(retryAt) : NaN;
		return Number.isNaN(timestamp) ? null : timestamp;
	};
	var getLaterRetryAt = function getLaterRetryAt(currentRetryAt, nextRetryAt) {
		var nextTimestamp = toRetryTimestamp(nextRetryAt);
		if (null === nextTimestamp) return currentRetryAt !== null && currentRetryAt !== void 0 ? currentRetryAt : null;
		var currentTimestamp = toRetryTimestamp(currentRetryAt);
		if (null === currentTimestamp || nextTimestamp >= currentTimestamp) return nextRetryAt;
		return currentRetryAt;
	};
	var toScanResult = function toScanResult(report) {
		var _report$layers;
		var _report$checks;
		var _report$scannedAt;
		var _ref;
		var retryAt = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : null;
		var layers = (_report$layers = report === null || report === void 0 ? void 0 : report.layers) !== null && _report$layers !== void 0 ? _report$layers : {};
		var checks = (_report$checks = report === null || report === void 0 ? void 0 : report.checks) !== null && _report$checks !== void 0 ? _report$checks : [];
		var activeWeight = getActiveWeight(layers);
		return {
			score: normalizeReadinessScore(report === null || report === void 0 ? void 0 : report.score),
			ranAt: (_report$scannedAt = report === null || report === void 0 ? void 0 : report.scannedAt) !== null && _report$scannedAt !== void 0 ? _report$scannedAt : null,
			retryAt: (_ref = retryAt !== null && retryAt !== void 0 ? retryAt : report === null || report === void 0 ? void 0 : report.retryAt) !== null && _ref !== void 0 ? _ref : null,
			stages: toStages(checks),
			checks: checks.filter(function(check) {
				return FAILING_CHECK_STATUSES.includes(check.status);
			}).map(function(check) {
				return toFailingCheck(check, layers, activeWeight);
			}).sort(byPointsDescending),
			passingChecks: checks.filter(function(check) {
				return CHECK_STATUS_PASS === check.status;
			}).map(function(check) {
				return toPassingCheck(check, layers, activeWeight);
			}),
			outsideScan: {
				status: "ok",
				reason: null
			},
			activation: { scoreAfter: null }
		};
	};
	var toFailedScanResult = function toFailedScanResult() {
		return {
			score: null,
			ranAt: null,
			retryAt: null,
			stages: [],
			checks: [],
			passingChecks: [],
			outsideScan: {
				status: OUTSIDE_SCAN_STATUS_FAILED,
				reason: arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : null
			},
			activation: { scoreAfter: null }
		};
	};

//#endregion
//#region modules/agents/assets/js/admin/api.js
	var AGENT_READY_SETTINGS_PATH = "/elementor/v1/settings/elementor_agent_ready_settings";
	var SCANNER_SCAN_PATH = "/elementor/v1/agents-ready/scan";
	var HTTP_NOT_FOUND = 404;
	var ANGIE_PLUGIN_SLUG = "angie";
	var PLUGIN_FOLDER_EXISTS = "folder_exists";
	var toScanError = function toScanError(reason) {
		var _reason$message;
		var _reason$data$status;
		var _reason$data;
		var _reason$data$meta$rea;
		var _reason$data2;
		var _reason$data$meta$ret;
		var _reason$data3;
		return Object.assign(new Error((_reason$message = reason === null || reason === void 0 ? void 0 : reason.message) !== null && _reason$message !== void 0 ? _reason$message : ""), {
			status: (_reason$data$status = reason === null || reason === void 0 || (_reason$data = reason.data) === null || _reason$data === void 0 ? void 0 : _reason$data.status) !== null && _reason$data$status !== void 0 ? _reason$data$status : null,
			reason: (_reason$data$meta$rea = reason === null || reason === void 0 || (_reason$data2 = reason.data) === null || _reason$data2 === void 0 || (_reason$data2 = _reason$data2.meta) === null || _reason$data2 === void 0 ? void 0 : _reason$data2.reason) !== null && _reason$data$meta$rea !== void 0 ? _reason$data$meta$rea : null,
			retryAt: (_reason$data$meta$ret = reason === null || reason === void 0 || (_reason$data3 = reason.data) === null || _reason$data3 === void 0 || (_reason$data3 = _reason$data3.meta) === null || _reason$data3 === void 0 ? void 0 : _reason$data3.retryAt) !== null && _reason$data$meta$ret !== void 0 ? _reason$data$meta$ret : null
		});
	};
	var runSiteScan = /*#__PURE__*/ function() {
		var _ref = _asyncToGenerator$1(/*#__PURE__*/ import_regenerator.default.mark(function _callee() {
			var _response$data;
			var _response$data2;
			var response;
			var _t;
			return import_regenerator.default.wrap(function(_context) {
				while (1) switch (_context.prev = _context.next) {
					case 0:
						_context.prev = 0;
						_context.next = 1;
						return (0, _wordpress_api_fetch.default)({
							path: SCANNER_SCAN_PATH,
							method: "POST"
						});
					case 1:
						response = _context.sent;
						return _context.abrupt("return", toScanResult(response === null || response === void 0 || (_response$data = response.data) === null || _response$data === void 0 ? void 0 : _response$data.report, response === null || response === void 0 || (_response$data2 = response.data) === null || _response$data2 === void 0 ? void 0 : _response$data2.retryAt));
					case 2:
						_context.prev = 2;
						_t = _context["catch"](0);
						throw toScanError(_t);
					case 3:
					case "end": return _context.stop();
				}
			}, _callee, null, [[0, 2]]);
		}));
		return function runSiteScan() {
			return _ref.apply(this, arguments);
		};
	}();
	var isPluginError = function isPluginError(error) {
		return Boolean(error && "string" === typeof error.code && "string" === typeof error.message);
	};
	var activateAngiePlugin = function activateAngiePlugin(pluginPath) {
		return (0, _wordpress_api_fetch.default)({
			path: "/wp/v2/plugins/".concat(pluginPath),
			method: "POST",
			data: { status: "active" }
		});
	};
	var installAngiePlugin = /*#__PURE__*/ function() {
		var _ref2 = _asyncToGenerator$1(/*#__PURE__*/ import_regenerator.default.mark(function _callee2() {
			var _t2;
			var _t3;
			return import_regenerator.default.wrap(function(_context2) {
				while (1) switch (_context2.prev = _context2.next) {
					case 0:
						_context2.prev = 0;
						_context2.next = 1;
						return (0, _wordpress_api_fetch.default)({
							path: "/wp/v2/plugins",
							method: "POST",
							data: {
								slug: ANGIE_PLUGIN_SLUG,
								status: "active"
							}
						});
					case 1: return _context2.abrupt("return", { success: true });
					case 2:
						_context2.prev = 2;
						_t2 = _context2["catch"](0);
						if (!(isPluginError(_t2) && PLUGIN_FOLDER_EXISTS === _t2.code)) {
							_context2.next = 6;
							break;
						}
						_context2.prev = 3;
						_context2.next = 4;
						return activateAngiePlugin("".concat(ANGIE_PLUGIN_SLUG, "/").concat(ANGIE_PLUGIN_SLUG));
					case 4: return _context2.abrupt("return", { success: true });
					case 5:
						_context2.prev = 5;
						_t3 = _context2["catch"](3);
						return _context2.abrupt("return", {
							success: false,
							error: isPluginError(_t3) ? _t3.message : ""
						});
					case 6: return _context2.abrupt("return", {
						success: false,
						error: isPluginError(_t2) ? _t2.message : ""
					});
					case 7:
					case "end": return _context2.stop();
				}
			}, _callee2, null, [[0, 2], [3, 5]]);
		}));
		return function installAngiePlugin() {
			return _ref2.apply(this, arguments);
		};
	}();
	var activateAgentsReady = function activateAgentsReady() {
		return elementorCommon.ajax.addRequest("agents_ready_activate");
	};
	var saveAgentReadySettings = function saveAgentReadySettings(settings) {
		return (0, _wordpress_api_fetch.default)({
			path: AGENT_READY_SETTINGS_PATH,
			method: "PUT",
			data: { value: settings }
		});
	};
	var UNMAPPED_TOGGLE_FIX_ERROR = (0, _wordpress_i18n.__)("This fix cannot be applied from the scanner yet.", "elementor");
	var saveToggleFix = function saveToggleFix(checkId, isEnabled) {
		var patch = getToggleSettingsPatch(checkId, isEnabled);
		if (!patch) return Promise.reject(new Error(UNMAPPED_TOGGLE_FIX_ERROR));
		return saveAgentReadySettings(patch);
	};
	var applyCheckFix = function applyCheckFix(checkId) {
		var payload = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
		if (Object.prototype.hasOwnProperty.call(payload, "value")) return Promise.resolve({
			checkId,
			payload
		});
		return saveToggleFix(checkId, Boolean(payload.enabled));
	};
	var undoCheckFix = function undoCheckFix(checkId) {
		var patch = getToggleSettingsPatch(checkId, false);
		if (!patch) return Promise.resolve({ checkId });
		return saveAgentReadySettings(patch);
	};
	var fetchLlmsFile = /*#__PURE__*/ function() {
		var _ref3 = _asyncToGenerator$1(/*#__PURE__*/ import_regenerator.default.mark(function _callee3(url) {
			var fileUrl;
			var response;
			return import_regenerator.default.wrap(function(_context3) {
				while (1) switch (_context3.prev = _context3.next) {
					case 0:
						fileUrl = new URL(url);
						fileUrl.searchParams.set("ver", Date.now().toString());
						_context3.next = 1;
						return fetch(fileUrl.toString(), { cache: "no-store" });
					case 1:
						response = _context3.sent;
						if (!(HTTP_NOT_FOUND === response.status)) {
							_context3.next = 2;
							break;
						}
						return _context3.abrupt("return", "");
					case 2:
						if (response.ok) {
							_context3.next = 3;
							break;
						}
						throw new Error(response.statusText);
					case 3: return _context3.abrupt("return", response.text());
					case 4:
					case "end": return _context3.stop();
				}
			}, _callee3);
		}));
		return function fetchLlmsFile(_x) {
			return _ref3.apply(this, arguments);
		};
	}();
	var fetchMarkdownPreview = function fetchMarkdownPreview(postId) {
		return new Promise(function(resolve, reject) {
			elementorCommon.ajax.addRequest("agents_ready_preview_markdown", {
				data: { postId },
				success: function success(response) {
					var _response$content;
					return resolve({
						content: (_response$content = response === null || response === void 0 ? void 0 : response.content) !== null && _response$content !== void 0 ? _response$content : "",
						isOverridden: Boolean(response === null || response === void 0 ? void 0 : response.isOverridden)
					});
				},
				error: reject
			});
		});
	};
	var searchMarkdownItems = function searchMarkdownItems(term) {
		return new Promise(function(resolve, reject) {
			elementorCommon.ajax.addRequest("agents_ready_search_markdown", {
				data: { term },
				success: function success(response) {
					var _response$items;
					return resolve((_response$items = response === null || response === void 0 ? void 0 : response.items) !== null && _response$items !== void 0 ? _response$items : []);
				},
				error: reject
			});
		});
	};
	var listContentItems = function listContentItems(_ref4) {
		var module = _ref4.module;
		var postType = _ref4.postType;
		var term = _ref4.term;
		var page = _ref4.page;
		return new Promise(function(resolve, reject) {
			elementorCommon.ajax.addRequest("agents_ready_list_content_items", {
				data: {
					module,
					postType,
					term,
					page
				},
				success: function success(response) {
					var _response$items2;
					return resolve({
						items: (_response$items2 = response === null || response === void 0 ? void 0 : response.items) !== null && _response$items2 !== void 0 ? _response$items2 : [],
						hasMore: Boolean(response === null || response === void 0 ? void 0 : response.hasMore)
					});
				},
				error: reject
			});
		});
	};
	var requestAjaxAction = function requestAjaxAction(action) {
		var data = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
		return new Promise(function(resolve, reject) {
			elementorCommon.ajax.addRequest(action, {
				data,
				success: resolve,
				error: reject
			});
		});
	};
	var saveLlmsContent = function saveLlmsContent(content) {
		return requestAjaxAction("agents_ready_save_llms_content", { content });
	};
	var revertLlmsContent = function revertLlmsContent() {
		return requestAjaxAction("agents_ready_revert_llms_content");
	};
	var takeOverLlmsFile = function takeOverLlmsFile() {
		return requestAjaxAction("agents_ready_takeover_llms_file");
	};
	var saveMarkdownContent = function saveMarkdownContent(postId, content) {
		return requestAjaxAction("agents_ready_save_markdown_content", {
			postId,
			content
		});
	};
	var revertMarkdownContent = function revertMarkdownContent(postId) {
		return requestAjaxAction("agents_ready_revert_markdown_content", { postId });
	};

//#endregion
//#region modules/agents/assets/js/admin/hooks/get-save-error-message.js
	var getSaveErrorMessage = function getSaveErrorMessage(reason) {
		if (reason && "string" === typeof reason.message && reason.message) return reason.message;
		if ("string" === typeof reason && reason) return reason;
		return (0, _wordpress_i18n.__)("Something went wrong. Please try again.", "elementor");
	};

//#endregion
//#region modules/agents/assets/js/admin/hooks/use-agent-discovery-settings.js
	var useAgentDiscoverySettings = function useAgentDiscoverySettings(initialState) {
		var _useState2 = _slicedToArray((0, react.useState)(initialState.enabled), 2);
		var isEnabled = _useState2[0];
		var setIsEnabled = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)(false), 2);
		var isSaving = _useState4[0];
		var setIsSaving = _useState4[1];
		var _useState6 = _slicedToArray((0, react.useState)(""), 2);
		var saveError = _useState6[0];
		var setSaveError = _useState6[1];
		return {
			files: initialState.files,
			isEnabled,
			isSaving,
			saveError,
			setIsEnabled,
			toggleEnabled: function toggleEnabled() {
				var previousIsEnabled = isEnabled;
				var nextIsEnabled = !isEnabled;
				trackFeatureToggled({
					featureName: FEATURE_WELL_KNOWN,
					isEnabled: nextIsEnabled,
					hasConflict: false
				});
				setSaveError("");
				setIsSaving(true);
				setIsEnabled(nextIsEnabled);
				return saveAgentReadySettings(_defineProperty$1({}, AGENT_DISCOVERY_SETTINGS_KEY, { enabled: nextIsEnabled })).catch(function(reason) {
					setIsEnabled(previousIsEnabled);
					setSaveError(getSaveErrorMessage(reason));
				}).finally(function() {
					return setIsSaving(false);
				});
			}
		};
	};

//#endregion
//#region modules/agents/assets/js/admin/hooks/use-all-modules-toggle.js
	var useAllModulesToggle = function useAllModulesToggle(modules) {
		var _useState2 = _slicedToArray((0, react.useState)(false), 2);
		var isSaving = _useState2[0];
		var setIsSaving = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)(""), 2);
		var saveError = _useState4[0];
		var setSaveError = _useState4[1];
		var toggleableModules = modules.filter(function(module) {
			return module.isToggleable;
		});
		var isAnyEnabled = toggleableModules.some(function(module) {
			return module.settings.isEnabled;
		});
		var isAnyModuleSaving = modules.some(function(module) {
			return module.settings.isSaving;
		});
		return {
			isAnyEnabled,
			isDisabled: isSaving || isAnyModuleSaving || 0 === toggleableModules.length,
			isSaving,
			saveError,
			toggleAll: function toggleAll() {
				var nextIsEnabled = !isAnyEnabled;
				var previousStates = toggleableModules.map(function(module) {
					return [module, module.settings.isEnabled];
				});
				trackFeatureToggled({
					featureName: FEATURE_MASTER,
					isEnabled: nextIsEnabled,
					hasConflict: modules.some(function(module) {
						return module.settings.hasPhysicalFile;
					})
				});
				setSaveError("");
				setIsSaving(true);
				toggleableModules.forEach(function(module) {
					return module.settings.setIsEnabled(nextIsEnabled);
				});
				return saveAgentReadySettings(Object.fromEntries(toggleableModules.map(function(module) {
					return [module.settingsKey, { enabled: nextIsEnabled }];
				}))).catch(function(reason) {
					previousStates.forEach(function(_ref) {
						var _ref2 = _slicedToArray(_ref, 2);
						var module = _ref2[0];
						var isEnabled = _ref2[1];
						return module.settings.setIsEnabled(isEnabled);
					});
					setSaveError(getSaveErrorMessage(reason));
				}).finally(function() {
					return setIsSaving(false);
				});
			}
		};
	};

//#endregion
//#region node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js
	function _arrayWithoutHoles(r) {
		if (Array.isArray(r)) return _arrayLikeToArray(r);
	}

//#endregion
//#region node_modules/@babel/runtime/helpers/esm/iterableToArray.js
	function _iterableToArray(r) {
		if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r);
	}

//#endregion
//#region node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
	function _nonIterableSpread() {
		throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
	}

//#endregion
//#region node_modules/@babel/runtime/helpers/esm/toConsumableArray.js
	function _toConsumableArray(r) {
		return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread();
	}

//#endregion
//#region modules/agents/assets/js/admin/components/bot-access/bot-access-rules.js
	function ownKeys$23(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$23, "ownKeys");
	function _objectSpread$22(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$23(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$23(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$22, "_objectSpread");
	var withAllPermissions = function withAllPermissions(value) {
		return Object.fromEntries(BOT_PERMISSIONS.map(function(permission) {
			return [permission, value];
		}));
	};
	var isRowAllBlocked = function isRowAllBlocked(bot) {
		return BOT_PERMISSIONS.every(function(permission) {
			return !bot[permission];
		});
	};
	var isColumnAllBlocked = function isColumnAllBlocked(bots, permission) {
		return bots.every(function(bot) {
			return !bot[permission];
		});
	};
	var setColumn = function setColumn(bots, permission, value) {
		return bots.map(function(bot) {
			return _objectSpread$22(_objectSpread$22({}, bot), {}, _defineProperty$1({}, permission, value));
		});
	};
	var setRow = function setRow(bots, token, value) {
		return bots.map(function(bot) {
			return bot.token === token ? _objectSpread$22(_objectSpread$22({}, bot), withAllPermissions(value)) : bot;
		});
	};
	var togglePermission = function togglePermission(bots, token, permission) {
		return bots.map(function(bot) {
			return bot.token === token ? _objectSpread$22(_objectSpread$22({}, bot), {}, _defineProperty$1({}, permission, !bot[permission])) : bot;
		});
	};
	var addBot = function addBot(bots, token, catalogTokens) {
		if (bots.some(function(bot) {
			return bot.token === token;
		})) return bots;
		return [].concat(_toConsumableArray(bots), [_objectSpread$22({ token }, DEFAULT_BOT_PERMISSIONS)]).sort(function(first, second) {
			return catalogTokens.indexOf(first.token) - catalogTokens.indexOf(second.token);
		});
	};
	var toSettingsBots = function toSettingsBots(bots) {
		return Object.fromEntries(bots.map(function(bot) {
			return [bot.token, Object.fromEntries(BOT_PERMISSIONS.map(function(permission) {
				return [BOT_PERMISSION_SETTING_KEYS[permission], bot[permission]];
			}))];
		}));
	};

//#endregion
//#region modules/agents/assets/js/admin/hooks/use-bot-access-settings.js
	var useBotAccessSettings = function useBotAccessSettings(initialState) {
		var _initialState$unavail;
		var catalog = initialState.catalog;
		var hasPhysicalFile = initialState.hasPhysicalFile;
		var unavailableReason = (_initialState$unavail = initialState.unavailableReason) !== null && _initialState$unavail !== void 0 ? _initialState$unavail : null;
		var catalogTokens = catalog.map(function(bot) {
			return bot.token;
		});
		var _useState2 = _slicedToArray((0, react.useState)(initialState.enabled), 2);
		var isEnabled = _useState2[0];
		var setIsEnabled = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)(initialState.bots), 2);
		var bots = _useState4[0];
		var setBots = _useState4[1];
		var _useState6 = _slicedToArray((0, react.useState)(false), 2);
		var isSaving = _useState6[0];
		var setIsSaving = _useState6[1];
		var _useState8 = _slicedToArray((0, react.useState)(""), 2);
		var saveError = _useState8[0];
		var setSaveError = _useState8[1];
		var saveSettings = function saveSettings(nextIsEnabled, nextBots) {
			var previousIsEnabled = isEnabled;
			var previousBots = bots;
			setSaveError("");
			setIsSaving(true);
			setIsEnabled(nextIsEnabled);
			setBots(nextBots);
			return saveAgentReadySettings(_defineProperty$1({}, BOT_ACCESS_SETTINGS_KEY, {
				enabled: nextIsEnabled,
				bots: toSettingsBots(nextBots)
			})).catch(function(reason) {
				setIsEnabled(previousIsEnabled);
				setBots(previousBots);
				setSaveError(getSaveErrorMessage(reason));
			}).finally(function() {
				return setIsSaving(false);
			});
		};
		return {
			addBot: /* @__PURE__ */ __name(function addBot$1(token) {
				return saveSettings(isEnabled, addBot(bots, token, catalogTokens));
			}, "addBot"),
			bots,
			catalog,
			hasPhysicalFile,
			isEnabled: isEnabled && !unavailableReason,
			isSaving,
			saveError,
			setColumn: /* @__PURE__ */ __name(function setColumn$1(permission, value) {
				trackBotPermissionChanged({
					scope: SCOPE_BULK,
					crawlerId: "all",
					permissionType: BOT_PERMISSION_SETTING_KEYS[permission],
					isAllowed: value,
					targetType: TARGET_TYPE_BUTTON
				});
				return saveSettings(isEnabled, setColumn(bots, permission, value));
			}, "setColumn"),
			setIsEnabled,
			setRow: /* @__PURE__ */ __name(function setRow$1(token, value) {
				trackBotPermissionChanged({
					scope: SCOPE_BULK,
					crawlerId: token,
					isAllowed: value,
					targetType: TARGET_TYPE_BUTTON
				});
				return saveSettings(isEnabled, setRow(bots, token, value));
			}, "setRow"),
			toggleEnabled: function toggleEnabled() {
				trackFeatureToggled({
					featureName: FEATURE_BOT_ACCESS,
					isEnabled: !isEnabled,
					hasConflict: hasPhysicalFile
				});
				return saveSettings(!isEnabled, bots);
			},
			togglePermission: /* @__PURE__ */ __name(function togglePermission$1(token, permission) {
				var currentBot = bots.find(function(bot) {
					return bot.token === token;
				});
				trackBotPermissionChanged({
					scope: SCOPE_SINGLE,
					crawlerId: token,
					permissionType: BOT_PERMISSION_SETTING_KEYS[permission],
					isAllowed: !(currentBot !== null && currentBot !== void 0 && currentBot[permission]),
					targetType: TARGET_TYPE_CHECKBOX
				});
				return saveSettings(isEnabled, togglePermission(bots, token, permission));
			}, "togglePermission"),
			unavailableReason
		};
	};

//#endregion
//#region modules/agents/assets/js/admin/hooks/use-browser-tools-settings.js
	var useBrowserToolsSettings = function useBrowserToolsSettings(initialState) {
		var _initialState$busines;
		var _initialState$unavail;
		var _useState2 = _slicedToArray((0, react.useState)(initialState.enabled), 2);
		var isEnabled = _useState2[0];
		var setIsEnabled = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)((_initialState$busines = initialState.businessDetails) !== null && _initialState$busines !== void 0 ? _initialState$busines : ""), 2);
		var businessDetails = _useState4[0];
		var setBusinessDetails = _useState4[1];
		var _useState6 = _slicedToArray((0, react.useState)(false), 2);
		var isSaving = _useState6[0];
		var setIsSaving = _useState6[1];
		var _useState8 = _slicedToArray((0, react.useState)(""), 2);
		var saveError = _useState8[0];
		var setSaveError = _useState8[1];
		return {
			businessDetails,
			isAvailable: initialState.isAvailable,
			isEnabled,
			isSaving,
			maxBusinessDetailsLength: initialState.maxBusinessDetailsLength,
			saveBusinessDetails: function saveBusinessDetails(details) {
				var trimmedDetails = details.trim();
				return saveAgentReadySettings(_defineProperty$1({}, BROWSER_TOOLS_SETTINGS_KEY, _defineProperty$1({}, BUSINESS_DETAILS_SETTINGS_KEY, trimmedDetails))).then(function() {
					return setBusinessDetails(trimmedDetails);
				}).catch(function(reason) {
					return Promise.reject(getSaveErrorMessage(reason));
				});
			},
			saveError,
			setIsEnabled,
			tools: initialState.tools,
			toggleEnabled: function toggleEnabled() {
				var previousIsEnabled = isEnabled;
				var nextIsEnabled = !isEnabled;
				setSaveError("");
				setIsSaving(true);
				setIsEnabled(nextIsEnabled);
				return saveAgentReadySettings(_defineProperty$1({}, BROWSER_TOOLS_SETTINGS_KEY, { enabled: nextIsEnabled })).catch(function(reason) {
					setIsEnabled(previousIsEnabled);
					setSaveError(getSaveErrorMessage(reason));
				}).finally(function() {
					return setIsSaving(false);
				});
			},
			unavailableReason: (_initialState$unavail = initialState.unavailableReason) !== null && _initialState$unavail !== void 0 ? _initialState$unavail : null
		};
	};

//#endregion
//#region modules/agents/assets/js/admin/hooks/use-post-exclusions.js
	function ownKeys$22(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$22, "ownKeys");
	function _objectSpread$21(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$22(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$22(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$21, "_objectSpread");
	var EXCLUSIONS_SAVE_DEBOUNCE_MS = 500;
	var getExcludedCounts = function getExcludedCounts(postTypes) {
		return Object.fromEntries(postTypes.map(function(postType) {
			var _postType$excludedCou;
			return [postType.name, (_postType$excludedCou = postType.excludedCount) !== null && _postType$excludedCou !== void 0 ? _postType$excludedCou : 0];
		}));
	};
	var isSameList = function isSameList(first, second) {
		return first.length === second.length && first.every(function(id, index) {
			return id === second[index];
		});
	};
	var withExcludedCounts = function withExcludedCounts(postTypes, counts) {
		return postTypes.map(function(postType) {
			return Object.prototype.hasOwnProperty.call(counts, postType.name) ? _objectSpread$21(_objectSpread$21({}, postType), {}, { excludedCount: counts[postType.name] }) : postType;
		});
	};
	var usePostExclusions = function usePostExclusions(_ref) {
		var settingsKey = _ref.settingsKey;
		var _ref$initialExcludedP = _ref.initialExcludedPostIds;
		var initialExcludedPostIds = _ref$initialExcludedP === void 0 ? [] : _ref$initialExcludedP;
		var initialPostTypes = _ref.initialPostTypes;
		var setPostTypes = _ref.setPostTypes;
		var setSaveError = _ref.setSaveError;
		var onSaved = _ref.onSaved;
		var _useState2 = _slicedToArray((0, react.useState)(initialExcludedPostIds), 2);
		var excludedPostIds = _useState2[0];
		var setExcludedPostIds = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)(0), 2);
		var savedVersion = _useState4[0];
		var setSavedVersion = _useState4[1];
		var currentRef = (0, react.useRef)({
			ids: initialExcludedPostIds,
			counts: getExcludedCounts(initialPostTypes)
		});
		var savedRef = (0, react.useRef)(currentRef.current);
		var timerRef = (0, react.useRef)(null);
		var queueRef = (0, react.useRef)(Promise.resolve());
		var onSavedRef = (0, react.useRef)(onSaved);
		(0, react.useEffect)(function() {
			onSavedRef.current = onSaved;
		});
		(0, react.useEffect)(function() {
			return function() {
				return clearTimeout(timerRef.current);
			};
		}, []);
		var restoreSaved = (0, react.useCallback)(function(reason) {
			currentRef.current = savedRef.current;
			setExcludedPostIds(savedRef.current.ids);
			setPostTypes(function(postTypes) {
				return withExcludedCounts(postTypes, savedRef.current.counts);
			});
			setSaveError(getSaveErrorMessage(reason));
		}, [setPostTypes, setSaveError]);
		var persist = (0, react.useCallback)(function() {
			timerRef.current = null;
			queueRef.current = queueRef.current.then(function() {
				var pending = currentRef.current;
				if (isSameList(pending.ids, savedRef.current.ids)) return;
				return saveAgentReadySettings(_defineProperty$1({}, settingsKey, { excluded_post_ids: pending.ids })).then(function() {
					var _onSavedRef$current;
					savedRef.current = pending;
					setSavedVersion(function(version) {
						return version + 1;
					});
					(_onSavedRef$current = onSavedRef.current) === null || _onSavedRef$current === void 0 || _onSavedRef$current.call(onSavedRef);
				}).catch(restoreSaved);
			});
			return queueRef.current;
		}, [settingsKey, restoreSaved]);
		var togglePostExclusion = (0, react.useCallback)(function(postTypeName, postId) {
			var _counts$postTypeName;
			var _currentRef$current = currentRef.current;
			var ids = _currentRef$current.ids;
			var counts = _currentRef$current.counts;
			var isExcluded = ids.includes(postId);
			if (!isExcluded && ids.length >= 5e3) {
				setSaveError((0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("You can exclude up to %d items.", "elementor"), MAX_EXCLUDED_POSTS));
				return;
			}
			var nextCounts = _objectSpread$21(_objectSpread$21({}, counts), {}, _defineProperty$1({}, postTypeName, Math.max(0, ((_counts$postTypeName = counts[postTypeName]) !== null && _counts$postTypeName !== void 0 ? _counts$postTypeName : 0) + (isExcluded ? -1 : 1))));
			currentRef.current = {
				ids: isExcluded ? ids.filter(function(id) {
					return id !== postId;
				}) : [].concat(_toConsumableArray(ids), [postId]),
				counts: nextCounts
			};
			setExcludedPostIds(currentRef.current.ids);
			setPostTypes(function(postTypes) {
				return withExcludedCounts(postTypes, _defineProperty$1({}, postTypeName, nextCounts[postTypeName]));
			});
			setSaveError("");
			clearTimeout(timerRef.current);
			timerRef.current = setTimeout(persist, 500);
		}, [
			persist,
			setPostTypes,
			setSaveError
		]);
		return {
			excludedPostIds,
			exclusionsSavedVersion: savedVersion,
			flushExclusions: (0, react.useCallback)(function() {
				if (!timerRef.current) return queueRef.current;
				clearTimeout(timerRef.current);
				return persist();
			}, [persist]),
			togglePostExclusion
		};
	};

//#endregion
//#region modules/agents/assets/js/admin/hooks/use-llms-settings.js
	function ownKeys$21(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$21, "ownKeys");
	function _objectSpread$20(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$21(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$21(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$20, "_objectSpread");
	var PHYSICAL_FILE_REASON_CODE = "physical_file";
	var getIncludedPostTypeNames$1 = function getIncludedPostTypeNames(postTypes) {
		return postTypes.filter(function(postType) {
			return postType.included;
		}).map(function(postType) {
			return postType.name;
		});
	};
	var useLlmsSettings = function useLlmsSettings(initialState) {
		var _initialState$unavail;
		var fileUrl = initialState.fileUrl;
		var _useState2 = _slicedToArray((0, react.useState)(initialState.hasPhysicalFile), 2);
		var hasPhysicalFile = _useState2[0];
		var setHasPhysicalFile = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)((_initialState$unavail = initialState.unavailableReason) !== null && _initialState$unavail !== void 0 ? _initialState$unavail : null), 2);
		var unavailableReason = _useState4[0];
		var setUnavailableReason = _useState4[1];
		var _useState6 = _slicedToArray((0, react.useState)(initialState.enabled), 2);
		var isEnabled = _useState6[0];
		var setIsEnabled = _useState6[1];
		var _useState8 = _slicedToArray((0, react.useState)(initialState.isManuallyEdited), 2);
		var isManuallyEdited = _useState8[0];
		var setIsManuallyEdited = _useState8[1];
		var _useState0 = _slicedToArray((0, react.useState)(initialState.postTypes), 2);
		var postTypes = _useState0[0];
		var setPostTypes = _useState0[1];
		var _useState10 = _slicedToArray((0, react.useState)(""), 2);
		var content = _useState10[0];
		var setContent = _useState10[1];
		var _useState12 = _slicedToArray((0, react.useState)(false), 2);
		var isSaving = _useState12[0];
		var setIsSaving = _useState12[1];
		var _useState14 = _slicedToArray((0, react.useState)(""), 2);
		var saveError = _useState14[0];
		var setSaveError = _useState14[1];
		var refreshContent = (0, react.useCallback)(function() {
			if (hasPhysicalFile) return;
			fetchLlmsFile(fileUrl).then(setContent).catch(function() {
				return setContent("");
			});
		}, [fileUrl, hasPhysicalFile]);
		(0, react.useEffect)(function() {
			refreshContent();
		}, [refreshContent]);
		var exclusions = usePostExclusions({
			settingsKey: LLMS_SETTINGS_KEY,
			initialExcludedPostIds: initialState.excludedPostIds,
			initialPostTypes: initialState.postTypes,
			setPostTypes,
			setSaveError,
			onSaved: refreshContent
		});
		var saveSettings = function saveSettings(nextIsEnabled, nextPostTypes) {
			var previousIsEnabled = isEnabled;
			var previousPostTypes = postTypes;
			setSaveError("");
			setIsSaving(true);
			setIsEnabled(nextIsEnabled);
			setPostTypes(nextPostTypes);
			return saveAgentReadySettings(_defineProperty$1({}, LLMS_SETTINGS_KEY, {
				enabled: nextIsEnabled,
				post_types: getIncludedPostTypeNames$1(nextPostTypes)
			})).then(refreshContent).catch(function(reason) {
				setIsEnabled(previousIsEnabled);
				setPostTypes(previousPostTypes);
				setSaveError(getSaveErrorMessage(reason));
			}).finally(function() {
				return setIsSaving(false);
			});
		};
		return _objectSpread$20(_objectSpread$20({}, exclusions), {}, {
			content,
			hasPhysicalFile,
			isEnabled: isEnabled && !unavailableReason,
			isManuallyEdited,
			isSaving,
			saveError,
			postTypes,
			revertContent: function revertContent() {
				return revertLlmsContent().then(function() {
					setIsManuallyEdited(false);
					refreshContent();
				});
			},
			saveContent: function saveContent(nextContent) {
				return saveLlmsContent(nextContent).then(function() {
					setIsManuallyEdited(true);
					refreshContent();
				});
			},
			setIsEnabled,
			takeOverFile: function takeOverFile() {
				return takeOverLlmsFile().then(function() {
					setHasPhysicalFile(false);
					setIsManuallyEdited(false);
					setIsEnabled(true);
					setUnavailableReason(function(reason) {
						return PHYSICAL_FILE_REASON_CODE === (reason === null || reason === void 0 ? void 0 : reason.code) ? null : reason;
					});
				});
			},
			toggleEnabled: function toggleEnabled() {
				trackFeatureToggled({
					featureName: FEATURE_LLMS_TXT,
					isEnabled: !isEnabled,
					hasConflict: hasPhysicalFile
				});
				return saveSettings(!isEnabled, postTypes);
			},
			togglePostType: function togglePostType(name) {
				var _nextPostTypes$find;
				var nextPostTypes = postTypes.map(function(postType) {
					return postType.name === name ? _objectSpread$20(_objectSpread$20({}, postType), {}, { included: !postType.included }) : postType;
				});
				trackPostTypeToggled({
					featureName: FEATURE_LLMS_TXT,
					postType: name,
					isIncluded: (_nextPostTypes$find = nextPostTypes.find(function(postType) {
						return postType.name === name;
					})) === null || _nextPostTypes$find === void 0 ? void 0 : _nextPostTypes$find.included
				});
				return saveSettings(isEnabled, nextPostTypes);
			},
			unavailableReason
		});
	};

//#endregion
//#region modules/agents/assets/js/admin/hooks/use-markdown-settings.js
	function ownKeys$20(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$20, "ownKeys");
	function _objectSpread$19(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$20(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$20(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$19, "_objectSpread");
	var getIncludedPostTypeNames = function getIncludedPostTypeNames(postTypes) {
		return postTypes.filter(function(postType) {
			return postType.included;
		}).map(function(postType) {
			return postType.name;
		});
	};
	var useMarkdownSettings = function useMarkdownSettings(initialState) {
		var _useState2 = _slicedToArray((0, react.useState)(initialState.enabled), 2);
		var isEnabled = _useState2[0];
		var setIsEnabled = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)(initialState.postTypes), 2);
		var postTypes = _useState4[0];
		var setPostTypes = _useState4[1];
		var _useState6 = _slicedToArray((0, react.useState)(false), 2);
		var isSaving = _useState6[0];
		var setIsSaving = _useState6[1];
		var _useState8 = _slicedToArray((0, react.useState)(""), 2);
		var saveError = _useState8[0];
		var setSaveError = _useState8[1];
		var exclusions = usePostExclusions({
			settingsKey: MARKDOWN_SETTINGS_KEY,
			initialExcludedPostIds: initialState.excludedPostIds,
			initialPostTypes: initialState.postTypes,
			setPostTypes,
			setSaveError
		});
		var saveSettings = function saveSettings(nextIsEnabled, nextPostTypes) {
			var previousIsEnabled = isEnabled;
			var previousPostTypes = postTypes;
			setSaveError("");
			setIsSaving(true);
			setIsEnabled(nextIsEnabled);
			setPostTypes(nextPostTypes);
			return saveAgentReadySettings(_defineProperty$1({}, MARKDOWN_SETTINGS_KEY, {
				enabled: nextIsEnabled,
				post_types: getIncludedPostTypeNames(nextPostTypes)
			})).catch(function(reason) {
				setIsEnabled(previousIsEnabled);
				setPostTypes(previousPostTypes);
				setSaveError(getSaveErrorMessage(reason));
			}).finally(function() {
				return setIsSaving(false);
			});
		};
		return _objectSpread$19(_objectSpread$19({}, exclusions), {}, {
			isEnabled,
			isSaving,
			postTypes,
			saveError,
			setIsEnabled,
			toggleEnabled: function toggleEnabled() {
				trackFeatureToggled({
					featureName: FEATURE_MARKDOWN_CONTENT,
					isEnabled: !isEnabled,
					hasConflict: false
				});
				return saveSettings(!isEnabled, postTypes);
			},
			togglePostType: function togglePostType(name) {
				var _nextPostTypes$find;
				var nextPostTypes = postTypes.map(function(postType) {
					return postType.name === name ? _objectSpread$19(_objectSpread$19({}, postType), {}, { included: !postType.included }) : postType;
				});
				trackPostTypeToggled({
					featureName: FEATURE_MARKDOWN,
					postType: name,
					isIncluded: (_nextPostTypes$find = nextPostTypes.find(function(postType) {
						return postType.name === name;
					})) === null || _nextPostTypes$find === void 0 ? void 0 : _nextPostTypes$find.included
				});
				return saveSettings(isEnabled, nextPostTypes);
			}
		});
	};

//#endregion
//#region modules/agents/assets/js/admin/components/agent-discovery/agent-discovery-features.js
	var getAgentDiscoveryFeatures = function getAgentDiscoveryFeatures() {
		return [
			{
				id: "capability-catalog",
				title: (0, _wordpress_i18n.__)("Capability catalog", "elementor"),
				description: (0, _wordpress_i18n.__)("Lists everything agents can use on this site, so they find it in one fetch.", "elementor"),
				slugs: ["agent.json", "ard.json"]
			},
			{
				id: "api-catalog",
				title: (0, _wordpress_i18n.__)("API catalog", "elementor"),
				description: (0, _wordpress_i18n.__)("Names this site’s REST API and feed for automated discovery", "elementor"),
				slugs: ["api-catalog"]
			},
			{
				id: "sign-in",
				title: (0, _wordpress_i18n.__)("Sign-in guide for agents and OAuth discovery", "elementor"),
				description: (0, _wordpress_i18n.__)("Explains how an agent can sign in and shares the standard sign-in details agents need.", "elementor"),
				slugs: [
					"auth.md",
					"oauth-protected-resource",
					"oauth-authorization-server"
				]
			},
			{
				id: "agent-skills",
				title: (0, _wordpress_i18n.__)("Agent skills", "elementor"),
				description: (0, _wordpress_i18n.__)("Lists the actions this site lets agents perform.", "elementor"),
				slugs: ["agent-skills"]
			}
		];
	};
	var getIncludedFeatures = function getIncludedFeatures(files) {
		var availableSlugs = new Set(files.map(function(file) {
			return file.slug;
		}));
		return getAgentDiscoveryFeatures().filter(function(feature) {
			return feature.slugs.some(function(slug) {
				return availableSlugs.has(slug);
			});
		});
	};

//#endregion
//#region modules/agents/assets/js/admin/components/agent-discovery/agent-discovery-panel.js
	var FILES_PANE_WIDTH = 500;
	var CHIPS_GAP = 1.25;
	var getChipLinkProps = function getChipLinkProps(url) {
		return {
			component: "a",
			href: url,
			target: "_blank",
			rel: "noopener noreferrer",
			clickable: true
		};
	};
	var AgentDiscoveryPanel = function AgentDiscoveryPanel(_ref) {
		var settings = _ref.settings;
		var files = settings.files;
		var isEnabled = settings.isEnabled;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			width: "100%",
			maxWidth: "100%",
			minWidth: 0,
			overflow: "auto"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 5,
			pl: 7,
			pr: 6,
			py: 4,
			flexGrow: 1,
			flexShrink: 1,
			flexBasis: 0,
			minWidth: 0
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 1 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, { variant: "subtitle1" }, (0, _wordpress_i18n.__)("Show agents how to use your site", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, (0, _wordpress_i18n.__)("Publishes standard discovery files in your site’s /.well-known path. Agents can find what your site offers and how to use it.", "elementor"))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 2 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, { variant: "subtitle1" }, (0, _wordpress_i18n.__)("What’s included", "elementor")), getIncludedFeatures(files).map(function(_ref2) {
			var id = _ref2.id;
			var title = _ref2.title;
			var description = _ref2.description;
			return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
				key: id,
				spacing: 1
			}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
				direction: "row",
				alignItems: "center",
				spacing: 1
			}, /*#__PURE__*/ react.default.createElement(_elementor_icons.CircleCheckIcon, { fontSize: "tiny" }), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, { variant: "subtitle2" }, title)), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
				variant: "body2",
				color: "text.secondary"
			}, description));
		}))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			flexWrap: "wrap",
			alignContent: "flex-start",
			gap: CHIPS_GAP,
			p: 3,
			width: FILES_PANE_WIDTH,
			maxWidth: "100%",
			minWidth: 0,
			flexShrink: 1,
			bgcolor: "grey.50"
		}, files.map(function(_ref3) {
			var slug = _ref3.slug;
			var url = _ref3.url;
			return /*#__PURE__*/ react.default.createElement(_elementor_ui_Chip.default, _extends$1({
				key: slug,
				label: slug,
				size: "small",
				shape: "rounded"
			}, isEnabled ? getChipLinkProps(url) : {}));
		})));
	};
	AgentDiscoveryPanel.propTypes = { settings: import_prop_types.default.shape({
		files: import_prop_types.default.arrayOf(import_prop_types.default.shape({
			slug: import_prop_types.default.string.isRequired,
			url: import_prop_types.default.string.isRequired
		})).isRequired,
		isEnabled: import_prop_types.default.bool.isRequired
	}).isRequired };

//#endregion
//#region modules/agents/assets/js/admin/components/content-preview-dialog.js
	var WP_ADMIN_BAR_Z_INDEX = 99999;
	var DIALOG_Z_INDEX$2 = WP_ADMIN_BAR_Z_INDEX + 1;
	var ContentPreviewDialog = function ContentPreviewDialog(_ref) {
		var title = _ref.title;
		var onClose = _ref.onClose;
		var children = _ref.children;
		var _ref$actions = _ref.actions;
		var actions = _ref$actions === void 0 ? null : _ref$actions;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Dialog.default, {
			open: true,
			maxWidth: "lg",
			fullWidth: true,
			scroll: "paper",
			onClose,
			sx: { zIndex: DIALOG_Z_INDEX$2 },
			PaperProps: { sx: {
				position: "absolute",
				top: "50%",
				left: "50%",
				transform: "translate(-50%, -50%)",
				m: 0,
				maxHeight: "calc(100% - 64px)"
			} }
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogHeader.default, {
			onClose,
			logo: false
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogTitle.default, null, title)), /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogContent.default, { dividers: true }, children), actions && /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogActions.default, null, actions));
	};
	ContentPreviewDialog.propTypes = {
		title: import_prop_types.default.string.isRequired,
		onClose: import_prop_types.default.func.isRequired,
		children: import_prop_types.default.node.isRequired,
		actions: import_prop_types.default.node
	};

//#endregion
//#region modules/agents/assets/js/admin/components/all-modules-control.js
	var AllModulesControl = function AllModulesControl(_ref) {
		var isAnyEnabled = _ref.isAnyEnabled;
		var isDisabled = _ref.isDisabled;
		var onToggleAll = _ref.onToggleAll;
		var _useState2 = _slicedToArray((0, react.useState)(false), 2);
		var isConfirmOpen = _useState2[0];
		var setIsConfirmOpen = _useState2[1];
		var closeConfirm = function closeConfirm() {
			return setIsConfirmOpen(false);
		};
		var confirmTurnOff = /*#__PURE__*/ function() {
			var _ref2 = _asyncToGenerator$1(/*#__PURE__*/ import_regenerator.default.mark(function _callee() {
				return import_regenerator.default.wrap(function(_context) {
					while (1) switch (_context.prev = _context.next) {
						case 0:
							_context.next = 1;
							return onToggleAll();
						case 1: closeConfirm();
						case 2:
						case "end": return _context.stop();
					}
				}, _callee);
			}));
			return function confirmTurnOff() {
				return _ref2.apply(this, arguments);
			};
		}();
		if (!isAnyEnabled) return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 1,
			alignItems: "flex-start"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, { variant: "subtitle1" }, (0, _wordpress_i18n.__)("Agent Ready is off", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, (0, _wordpress_i18n.__)("Turn the features back on to let agents find, read and use your site.", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "outlined",
			size: "small",
			disabled: isDisabled,
			onClick: onToggleAll,
			sx: { mt: 1 }
		}, (0, _wordpress_i18n.__)("Turn on", "elementor")));
		return /*#__PURE__*/ react.default.createElement(react.default.Fragment, null, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 1,
			alignItems: "flex-start"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, { variant: "subtitle1" }, (0, _wordpress_i18n.__)("Turn off Agent Ready", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, (0, _wordpress_i18n.__)("Agents won’t be able to use any of these features until you turn them back on.", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "outlined",
			color: "error",
			size: "small",
			disabled: isDisabled,
			onClick: function onClick() {
				return setIsConfirmOpen(true);
			},
			sx: { mt: 1 }
		}, (0, _wordpress_i18n.__)("Turn off", "elementor"))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Dialog.default, {
			open: isConfirmOpen,
			onClose: closeConfirm,
			maxWidth: "sm",
			fullWidth: true,
			sx: { zIndex: DIALOG_Z_INDEX$2 }
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogTitle.default, null, (0, _wordpress_i18n.__)("Turn off Agent Ready?", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogContent.default, null, /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogContentText.default, { paragraph: true }, (0, _wordpress_i18n.__)("All the features above will be turned off, and agents will no longer be able to use them. Your settings are kept.", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogContentText.default, null, (0, _wordpress_i18n.__)("You can turn them back on at any time.", "elementor"))), /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogActions.default, null, /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			color: "secondary",
			onClick: closeConfirm,
			disabled: isDisabled
		}, (0, _wordpress_i18n.__)("Cancel", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "contained",
			color: "error",
			onClick: confirmTurnOff,
			disabled: isDisabled
		}, (0, _wordpress_i18n.__)("Turn off", "elementor")))));
	};
	AllModulesControl.propTypes = {
		isAnyEnabled: import_prop_types.default.bool.isRequired,
		isDisabled: import_prop_types.default.bool,
		onToggleAll: import_prop_types.default.func.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/components/wp-admin-sx.js
	function ownKeys$19(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$19, "ownKeys");
	function _objectSpread$18(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$19(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$19(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$18, "_objectSpread");
	var wpAdminInputResetSx = { ".wp-admin & .MuiInputBase-input, & .MuiInputBase-input:focus": {
		backgroundColor: "initial",
		border: 0,
		boxShadow: "none",
		minHeight: "auto",
		outline: 0
	} };
	var muiSwitchInputReset = {
		top: 0,
		left: 0,
		width: "100%",
		height: "100%",
		margin: 0,
		padding: 0,
		minWidth: 0,
		minHeight: 0,
		border: 0,
		background: "transparent",
		boxShadow: "none",
		position: "absolute",
		opacity: "0 !important",
		"&::before, &::after": {
			content: "none",
			display: "none"
		}
	};
	var wpAdminSwitchSx = {
		"& .MuiSwitch-input": _objectSpread$18({}, muiSwitchInputReset),
		"& .MuiSwitch-input:focus": {
			outline: 0,
			boxShadow: "none"
		}
	};

//#endregion
//#region modules/agents/assets/js/admin/components/bot-access/add-bot-control.js
	var CONTROL_WIDTH = 248;
	var FIELD_HEIGHT = 28;
	var FIELD_RADIUS$1 = "8px";
	var MENU_WIDTH = 254;
	var MENU_RADIUS = "16px";
	var MENU_LIST_MAX_HEIGHT = 240;
	var OPEN_KEYS = [
		"Enter",
		" ",
		"ArrowDown"
	];
	var MENU_ID = "e-agents-add-bot-menu";
	var matchesQuery = function matchesQuery(bot, query) {
		var normalizedQuery = query.trim().toLowerCase();
		return !normalizedQuery || bot.name.toLowerCase().includes(normalizedQuery) || bot.vendor.toLowerCase().includes(normalizedQuery);
	};
	var AddBotControl = function AddBotControl(_ref) {
		var _options$find;
		var options = _ref.options;
		var isDisabled = _ref.isDisabled;
		var onAdd = _ref.onAdd;
		var anchorRef = (0, react.useRef)(null);
		var searchInputRef = (0, react.useRef)(null);
		var _useState2 = _slicedToArray((0, react.useState)(false), 2);
		var isOpen = _useState2[0];
		var setIsOpen = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)(""), 2);
		var query = _useState4[0];
		var setQuery = _useState4[1];
		var _useState6 = _slicedToArray((0, react.useState)(null), 2);
		var selectedToken = _useState6[0];
		var setSelectedToken = _useState6[1];
		var selectedBot = (_options$find = options.find(function(bot) {
			return bot.token === selectedToken;
		})) !== null && _options$find !== void 0 ? _options$find : null;
		var filteredOptions = options.filter(function(bot) {
			return matchesQuery(bot, query);
		});
		var isFieldDisabled = isDisabled || !options.length;
		var open = function open() {
			if (!isFieldDisabled) setIsOpen(true);
		};
		var close = function close() {
			setIsOpen(false);
			setQuery("");
		};
		var select = function select(token) {
			setSelectedToken(token);
			close();
		};
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			spacing: 1,
			alignItems: "center",
			width: CONTROL_WIDTH
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			ref: anchorRef,
			role: "combobox",
			tabIndex: isFieldDisabled ? -1 : 0,
			"aria-label": (0, _wordpress_i18n.__)("Select bot", "elementor"),
			"aria-haspopup": "listbox",
			"aria-expanded": isOpen,
			"aria-controls": isOpen ? MENU_ID : void 0,
			"aria-disabled": isFieldDisabled,
			onClick: open,
			onKeyDown: function handleFieldKeyDown(event) {
				if (OPEN_KEYS.includes(event.key)) {
					event.preventDefault();
					open();
				}
			},
			direction: "row",
			alignItems: "center",
			spacing: .5,
			flex: 1,
			minWidth: 0,
			height: FIELD_HEIGHT,
			pl: 1.5,
			pr: 1,
			border: 1,
			borderColor: isOpen ? "text.primary" : "divider",
			borderRadius: FIELD_RADIUS$1,
			bgcolor: isFieldDisabled ? "action.disabledBackground" : "background.paper",
			sx: {
				cursor: isFieldDisabled ? "default" : "pointer",
				"&:hover": !isFieldDisabled && { borderColor: "text.primary" },
				"&:focus-visible": {
					outline: "none",
					borderColor: "text.primary"
				}
			}
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "caption",
			color: selectedBot && !isFieldDisabled ? "text.primary" : "text.disabled",
			flexGrow: 1,
			noWrap: true
		}, selectedBot ? selectedBot.name : (0, _wordpress_i18n.__)("Add bot rule", "elementor")), selectedBot && !isFieldDisabled && /*#__PURE__*/ react.default.createElement(_elementor_ui_IconButton.default, {
			size: "small",
			"aria-label": (0, _wordpress_i18n.__)("Clear", "elementor"),
			onClick: function handleClear(event) {
				event.stopPropagation();
				setSelectedToken(null);
			}
		}, /*#__PURE__*/ react.default.createElement(_elementor_icons.XIcon, { fontSize: "tiny" })), /*#__PURE__*/ react.default.createElement(_elementor_icons.ChevronDownIcon, {
			fontSize: "tiny",
			color: isFieldDisabled ? "disabled" : "action"
		})), /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			size: "small",
			variant: "contained",
			color: "secondary",
			disabled: isFieldDisabled || !selectedBot,
			onClick: function handleAdd() {
				onAdd(selectedBot.token);
				setSelectedToken(null);
			}
		}, (0, _wordpress_i18n.__)("Add", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Popover.default, {
			open: isOpen,
			anchorEl: anchorRef.current,
			onClose: close,
			TransitionProps: { onEntered: function onEntered() {
				var _searchInputRef$curre;
				return (_searchInputRef$curre = searchInputRef.current) === null || _searchInputRef$curre === void 0 ? void 0 : _searchInputRef$curre.focus();
			} },
			anchorOrigin: {
				vertical: "bottom",
				horizontal: "left"
			},
			transformOrigin: {
				vertical: "top",
				horizontal: "left"
			},
			slotProps: { paper: { sx: {
				width: MENU_WIDTH,
				borderRadius: MENU_RADIUS,
				mt: .5
			} } }
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			pl: 2,
			pr: 1,
			py: 1
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "subtitle2",
			flexGrow: 1
		}, (0, _wordpress_i18n.__)("Customize a bot's access", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_CloseButton.default, {
			"aria-label": (0, _wordpress_i18n.__)("Close", "elementor"),
			slotProps: { icon: { fontSize: "tiny" } },
			onClick: close
		})), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			px: 2,
			pb: 1.5
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_TextField.default, {
			inputRef: searchInputRef,
			fullWidth: true,
			size: "tiny",
			value: query,
			onChange: function onChange(event) {
				return setQuery(event.target.value);
			},
			placeholder: (0, _wordpress_i18n.__)("Search", "elementor"),
			inputProps: { "aria-label": (0, _wordpress_i18n.__)("Search bots", "elementor") },
			InputProps: {
				startAdornment: /*#__PURE__*/ react.default.createElement(_elementor_ui_InputAdornment.default, { position: "start" }, /*#__PURE__*/ react.default.createElement(_elementor_icons.ZoomIcon, { fontSize: "tiny" })),
				sx: { borderRadius: FIELD_RADIUS$1 }
			},
			sx: wpAdminInputResetSx
		})), /*#__PURE__*/ react.default.createElement(_elementor_ui_Divider.default, null), /*#__PURE__*/ react.default.createElement(_elementor_ui_MenuList.default, {
			id: MENU_ID,
			role: "listbox",
			dense: true,
			sx: {
				maxHeight: MENU_LIST_MAX_HEIGHT,
				overflowY: "auto"
			}
		}, filteredOptions.map(function(bot) {
			return /*#__PURE__*/ react.default.createElement(_elementor_ui_MenuItem.default, {
				key: bot.token,
				role: "option",
				dense: true,
				selected: bot.token === selectedToken,
				"aria-selected": bot.token === selectedToken,
				onClick: function onClick() {
					return select(bot.token);
				}
			}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
				variant: "caption",
				color: "text.primary",
				noWrap: true
			}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
				component: "span",
				variant: "inherit",
				fontWeight: "fontWeightBold"
			}, bot.name), " ", /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
				component: "span",
				variant: "inherit",
				color: "text.secondary"
			}, bot.vendor)));
		})), !filteredOptions.length && /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "caption",
			color: "text.secondary",
			component: "p",
			px: 2,
			pb: 1.5
		}, (0, _wordpress_i18n.__)("No bots found", "elementor"))));
	};
	AddBotControl.propTypes = {
		options: import_prop_types.default.arrayOf(import_prop_types.default.shape({
			token: import_prop_types.default.string.isRequired,
			name: import_prop_types.default.string.isRequired,
			vendor: import_prop_types.default.string.isRequired
		})).isRequired,
		isDisabled: import_prop_types.default.bool.isRequired,
		onAdd: import_prop_types.default.func.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/components/bot-access/bot-avatar.js
	function ownKeys$18(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$18, "ownKeys");
	function _objectSpread$17(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$18(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$18(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$17, "_objectSpread");
	var AVATAR_SIZE = 32;
	var MUTED_OPACITY = .4;
	var MAX_INITIALS = 2;
	var getInitials = function getInitials(vendor) {
		return vendor.split(/\s+/).map(function(word) {
			return word.charAt(0);
		}).join("").slice(0, MAX_INITIALS).toUpperCase();
	};
	var BotAvatar = function BotAvatar(_ref) {
		var name = _ref.name;
		var vendor = _ref.vendor;
		var logoUrl = _ref.logoUrl;
		var _ref$isMuted = _ref.isMuted;
		var isMuted = _ref$isMuted === void 0 ? false : _ref$isMuted;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Avatar.default, {
			src: logoUrl || void 0,
			alt: name,
			sx: _objectSpread$17({
				width: AVATAR_SIZE,
				height: AVATAR_SIZE,
				opacity: isMuted ? MUTED_OPACITY : 1,
				typography: "caption"
			}, logoUrl && {
				bgcolor: "transparent",
				"& img": { objectFit: "contain" }
			})
		}, getInitials(vendor));
	};
	BotAvatar.propTypes = {
		name: import_prop_types.default.string.isRequired,
		vendor: import_prop_types.default.string.isRequired,
		logoUrl: import_prop_types.default.string,
		isMuted: import_prop_types.default.bool
	};

//#endregion
//#region modules/agents/assets/js/admin/components/bot-access/bot-permissions.js
	var getBotPermissions = function getBotPermissions() {
		return [
			{
				permission: BOT_PERMISSION_SEARCH,
				icon: _elementor_icons.SearchIcon,
				title: (0, _wordpress_i18n.__)("Search", "elementor"),
				description: (0, _wordpress_i18n.__)("Use content in search results", "elementor")
			},
			{
				permission: BOT_PERMISSION_AI_INPUT,
				icon: _elementor_icons.ContentIcon,
				title: (0, _wordpress_i18n.__)("AI input", "elementor"),
				description: (0, _wordpress_i18n.__)("Use content to answer prompts", "elementor")
			},
			{
				permission: BOT_PERMISSION_AI_TRAIN,
				icon: _elementor_icons.AcademyIcon,
				title: (0, _wordpress_i18n.__)("AI training", "elementor"),
				description: (0, _wordpress_i18n.__)("Use content to train models", "elementor")
			}
		];
	};

//#endregion
//#region modules/agents/assets/js/admin/components/bot-access/bot-access-row.js
	var ROW_PADDING_Y = 1.625;
	var BotAccessRow = function BotAccessRow(_ref) {
		var bot = _ref.bot;
		var catalogBot = _ref.catalogBot;
		var isDisabled = _ref.isDisabled;
		var onTogglePermission = _ref.onTogglePermission;
		var onSetRow = _ref.onSetRow;
		var isMuted = isRowAllBlocked(bot);
		var textColor = isMuted ? "text.disabled" : "text.primary";
		var vendorColor = isMuted ? "text.disabled" : "text.secondary";
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			px: 3,
			py: ROW_PADDING_Y,
			borderBottom: 1,
			borderColor: "divider"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			spacing: 1.5,
			alignItems: "center",
			flexGrow: 1,
			minWidth: 0
		}, /*#__PURE__*/ react.default.createElement(BotAvatar, {
			name: catalogBot.name,
			vendor: catalogBot.vendor,
			logoUrl: catalogBot.logoUrl,
			isMuted
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			spacing: 1,
			alignItems: "baseline",
			minWidth: 0
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "subtitle2",
			color: textColor,
			noWrap: true
		}, catalogBot.name), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: vendorColor,
			noWrap: true
		}, catalogBot.vendor))), getBotPermissions().map(function(_ref2) {
			var permission = _ref2.permission;
			var title = _ref2.title;
			return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
				key: permission,
				width: BOT_TABLE_COLUMN_WIDTHS[permission],
				flexShrink: 0
			}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Switch.default, {
				size: "small",
				checked: bot[permission],
				disabled: isDisabled,
				onChange: function onChange() {
					return onTogglePermission(bot.token, permission);
				},
				inputProps: { "aria-label": (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("%1$s for %2$s", "elementor"), title, catalogBot.name) },
				sx: wpAdminSwitchSx
			}));
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			justifyContent: "flex-end",
			width: BOT_TABLE_COLUMN_WIDTHS.action,
			flexShrink: 0
		}, isMuted ? /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			size: "small",
			variant: "text",
			color: "info",
			disabled: isDisabled,
			onClick: function onClick() {
				return onSetRow(bot.token, true);
			}
		}, (0, _wordpress_i18n.__)("Enable", "elementor")) : /*#__PURE__*/ react.default.createElement(_elementor_ui_IconButton.default, {
			size: "small",
			disabled: isDisabled,
			onClick: function onClick() {
				return onSetRow(bot.token, false);
			},
			"aria-label": (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("Block %s", "elementor"), catalogBot.name)
		}, /*#__PURE__*/ react.default.createElement(_elementor_icons.BanIcon, { fontSize: "small" }))));
	};
	BotAccessRow.propTypes = {
		bot: import_prop_types.default.shape({
			token: import_prop_types.default.string.isRequired,
			search: import_prop_types.default.bool.isRequired,
			aiInput: import_prop_types.default.bool.isRequired,
			aiTrain: import_prop_types.default.bool.isRequired
		}).isRequired,
		catalogBot: import_prop_types.default.shape({
			name: import_prop_types.default.string.isRequired,
			vendor: import_prop_types.default.string.isRequired,
			logoUrl: import_prop_types.default.string
		}).isRequired,
		isDisabled: import_prop_types.default.bool.isRequired,
		onTogglePermission: import_prop_types.default.func.isRequired,
		onSetRow: import_prop_types.default.func.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/components/bot-access/bot-access-table.js
	var HEADER_PADDING_Y$1 = 1.25;
	var BotAccessTable = function BotAccessTable(_ref) {
		var bots = _ref.bots;
		var catalog = _ref.catalog;
		var isDisabled = _ref.isDisabled;
		var onTogglePermission = _ref.onTogglePermission;
		var onSetRow = _ref.onSetRow;
		var catalogByToken = Object.fromEntries(catalog.map(function(catalogBot) {
			return [catalogBot.token, catalogBot];
		}));
		var rows = bots.filter(function(bot) {
			return catalogByToken[bot.token];
		});
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, null, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			px: 3,
			py: HEADER_PADDING_Y$1,
			borderBottom: 1,
			borderColor: "divider"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary",
			flexGrow: 1
		}, (0, _wordpress_i18n.__)("Agent", "elementor")), getBotPermissions().map(function(_ref2) {
			var permission = _ref2.permission;
			var title = _ref2.title;
			return /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
				key: permission,
				variant: "body2",
				color: "text.secondary",
				width: BOT_TABLE_COLUMN_WIDTHS[permission],
				flexShrink: 0
			}, title);
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			width: BOT_TABLE_COLUMN_WIDTHS.action,
			flexShrink: 0
		})), rows.map(function(bot) {
			return /*#__PURE__*/ react.default.createElement(BotAccessRow, {
				key: bot.token,
				bot,
				catalogBot: catalogByToken[bot.token],
				isDisabled,
				onTogglePermission,
				onSetRow
			});
		}));
	};
	BotAccessTable.propTypes = {
		bots: import_prop_types.default.arrayOf(import_prop_types.default.shape({ token: import_prop_types.default.string.isRequired })).isRequired,
		catalog: import_prop_types.default.arrayOf(import_prop_types.default.shape({ token: import_prop_types.default.string.isRequired })).isRequired,
		isDisabled: import_prop_types.default.bool.isRequired,
		onTogglePermission: import_prop_types.default.func.isRequired,
		onSetRow: import_prop_types.default.func.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/components/bot-access/bot-permission-group.js
	var CARD_RADIUS = "8px";
	var BotPermissionGroup = function BotPermissionGroup(_ref) {
		var Icon = _ref.icon;
		var title = _ref.title;
		var description = _ref.description;
		var isAllBlocked = _ref.isAllBlocked;
		var isDisabled = _ref.isDisabled;
		var onSetAll = _ref.onSetAll;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			spacing: 1.5,
			alignItems: "flex-start",
			flex: 1,
			minWidth: 0,
			p: 2,
			border: 1,
			borderColor: "divider",
			borderRadius: CARD_RADIUS,
			bgcolor: "background.paper"
		}, /*#__PURE__*/ react.default.createElement(Icon, {
			fontSize: "small",
			color: "action"
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 1.5,
			alignItems: "flex-start",
			minWidth: 0
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: .5 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, { variant: "subtitle2" }, title), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, description)), isAllBlocked ? /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			size: "small",
			variant: "outlined",
			color: "info",
			startIcon: /*#__PURE__*/ react.default.createElement(_elementor_icons.CircleCheckIcon, null),
			disabled: isDisabled,
			onClick: function onClick() {
				return onSetAll(true);
			},
			"aria-label": (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("Enable all %s", "elementor"), title)
		}, (0, _wordpress_i18n.__)("Enable all", "elementor")) : /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			size: "small",
			variant: "outlined",
			color: "secondary",
			startIcon: /*#__PURE__*/ react.default.createElement(_elementor_icons.BanIcon, null),
			disabled: isDisabled,
			onClick: function onClick() {
				return onSetAll(false);
			},
			"aria-label": (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("Block all %s", "elementor"), title)
		}, (0, _wordpress_i18n.__)("Block all", "elementor"))));
	};
	BotPermissionGroup.propTypes = {
		icon: import_prop_types.default.elementType.isRequired,
		title: import_prop_types.default.string.isRequired,
		description: import_prop_types.default.string.isRequired,
		isAllBlocked: import_prop_types.default.bool.isRequired,
		isDisabled: import_prop_types.default.bool.isRequired,
		onSetAll: import_prop_types.default.func.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/components/bot-access/bot-access-panel.js
	var SECTIONS_SPACING = 5;
	var CARDS_SPACING = 3;
	var HEADER_PADDING_Y = 2.5;
	var BotAccessPanel = function BotAccessPanel(_ref) {
		var settings = _ref.settings;
		var addBot = settings.addBot;
		var bots = settings.bots;
		var catalog = settings.catalog;
		var hasPhysicalFile = settings.hasPhysicalFile;
		var isEnabled = settings.isEnabled;
		var isSaving = settings.isSaving;
		var setColumn = settings.setColumn;
		var setRow = settings.setRow;
		var togglePermission = settings.togglePermission;
		var isDisabled = !isEnabled || isSaving;
		var managedTokens = new Set(bots.map(function(bot) {
			return bot.token;
		}));
		var availableBots = catalog.filter(function(bot) {
			return !managedTokens.has(bot.token);
		});
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: SECTIONS_SPACING,
			px: 6,
			py: 3
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 2 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, { variant: "subtitle1" }, (0, _wordpress_i18n.__)("Decide how AI agents can use your content", "elementor")), hasPhysicalFile && /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { direction: "row" }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Chip.default, {
			icon: /*#__PURE__*/ react.default.createElement(_elementor_icons.AlertCircleIcon, null),
			label: (0, _wordpress_i18n.__)("A robots.txt file already exists and can’t be managed here.", "elementor"),
			color: "warning",
			size: "small",
			shape: "rounded",
			variant: "standard"
		})), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			spacing: CARDS_SPACING
		}, getBotPermissions().map(function(_ref2) {
			var permission = _ref2.permission;
			var icon = _ref2.icon;
			var title = _ref2.title;
			var description = _ref2.description;
			return /*#__PURE__*/ react.default.createElement(BotPermissionGroup, {
				key: permission,
				icon,
				title,
				description,
				isAllBlocked: isColumnAllBlocked(bots, permission),
				isDisabled: isDisabled || !bots.length,
				onSetAll: function onSetAll(value) {
					return setColumn(permission, value);
				}
			});
		}))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, null, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			justifyContent: "space-between",
			py: HEADER_PADDING_Y
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, { variant: "subtitle1" }, (0, _wordpress_i18n.__)("Manage bot access", "elementor")), /*#__PURE__*/ react.default.createElement(AddBotControl, {
			options: availableBots,
			isDisabled,
			onAdd: addBot
		})), /*#__PURE__*/ react.default.createElement(BotAccessTable, {
			bots,
			catalog,
			isDisabled,
			onTogglePermission: togglePermission,
			onSetRow: setRow
		})));
	};
	BotAccessPanel.propTypes = { settings: import_prop_types.default.shape({
		addBot: import_prop_types.default.func.isRequired,
		bots: import_prop_types.default.array.isRequired,
		catalog: import_prop_types.default.array.isRequired,
		hasPhysicalFile: import_prop_types.default.bool.isRequired,
		isEnabled: import_prop_types.default.bool.isRequired,
		isSaving: import_prop_types.default.bool.isRequired,
		setColumn: import_prop_types.default.func.isRequired,
		setRow: import_prop_types.default.func.isRequired,
		togglePermission: import_prop_types.default.func.isRequired
	}).isRequired };

//#endregion
//#region modules/agents/assets/js/admin/components/browser-tools/business-details-form.js
	var EDITOR_MIN_ROWS$1 = 5;
	var BusinessDetailsForm = function BusinessDetailsForm(_ref) {
		var details = _ref.details;
		var maxLength = _ref.maxLength;
		var onSave = _ref.onSave;
		var onClose = _ref.onClose;
		var _useState2 = _slicedToArray((0, react.useState)(details), 2);
		var draft = _useState2[0];
		var setDraft = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)(false), 2);
		var isSaving = _useState4[0];
		var setIsSaving = _useState4[1];
		var _useState6 = _slicedToArray((0, react.useState)(""), 2);
		var error = _useState6[0];
		var setError = _useState6[1];
		var isSaveDisabled = isSaving || draft.trim() === details;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			component: "form",
			spacing: 1.5,
			onSubmit: function handleSubmit(event) {
				event.preventDefault();
				setIsSaving(true);
				setError("");
				onSave(draft).then(onClose).catch(function(message) {
					setError(message);
					setIsSaving(false);
				});
			}
		}, error && /*#__PURE__*/ react.default.createElement(_elementor_ui_Alert.default, { severity: "error" }, error), /*#__PURE__*/ react.default.createElement(_elementor_ui_TextField.default, {
			value: draft,
			onChange: function onChange(event) {
				return setDraft(event.target.value);
			},
			multiline: true,
			fullWidth: true,
			minRows: EDITOR_MIN_ROWS$1,
			placeholder: (0, _wordpress_i18n.__)("Opening hours, phone number, email, address and social profiles. Leave empty to use your site’s structured data.", "elementor"),
			helperText: (0, _wordpress_i18n.__)("Agents use this text instead of the details found in your site’s structured data.", "elementor"),
			inputProps: {
				"aria-label": (0, _wordpress_i18n.__)("Business details", "elementor"),
				maxLength
			},
			sx: wpAdminInputResetSx
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			spacing: 1
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			type: "submit",
			variant: "contained",
			size: "small",
			disabled: isSaveDisabled
		}, (0, _wordpress_i18n.__)("Save", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			type: "button",
			color: "secondary",
			size: "small",
			onClick: onClose,
			disabled: isSaving
		}, (0, _wordpress_i18n.__)("Cancel", "elementor"))));
	};
	BusinessDetailsForm.propTypes = {
		details: import_prop_types.default.string.isRequired,
		maxLength: import_prop_types.default.number,
		onSave: import_prop_types.default.func.isRequired,
		onClose: import_prop_types.default.func.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/components/browser-tools/browser-tool-item.js
	var BUSINESS_INFO_TOOL = "query_business_info";
	var READ_PAGE_CONTENT_TOOL = "read_page_content";
	var TOOL_ICONS = _defineProperty$1(_defineProperty$1({
		search_site_content: _elementor_icons.SearchIcon,
		get_site_structure: _elementor_icons.SitemapIcon
	}, READ_PAGE_CONTENT_TOOL, _elementor_icons.FileTextIcon), BUSINESS_INFO_TOOL, _elementor_icons.BriefcaseIcon);
	var BrowserToolItem = function BrowserToolItem(_ref) {
		var tool = _ref.tool;
		var _ref$businessDetails = _ref.businessDetails;
		var businessDetails = _ref$businessDetails === void 0 ? "" : _ref$businessDetails;
		var maxBusinessDetailsLength = _ref.maxBusinessDetailsLength;
		var onSaveBusinessDetails = _ref.onSaveBusinessDetails;
		var isMarkdownEnabled = _ref.isMarkdownEnabled;
		var name = tool.name;
		var label = tool.label;
		var description = tool.description;
		var _useState2 = _slicedToArray((0, react.useState)(false), 2);
		var isEditingDetails = _useState2[0];
		var setIsEditingDetails = _useState2[1];
		var Icon = TOOL_ICONS[name];
		var showEditDetails = BUSINESS_INFO_TOOL === name;
		var showMarkdownRequired = "read_page_content" === name && !isMarkdownEnabled;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 1 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			spacing: 1
		}, Icon && /*#__PURE__*/ react.default.createElement(Icon, {
			fontSize: "tiny",
			color: "inherit"
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "subtitle2",
			color: "text.primary"
		}, label)), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, description), showMarkdownRequired && /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.tertiary"
		}, (0, _wordpress_i18n.__)("Turn on Markdown content to make this tool available.", "elementor")), showEditDetails && !isEditingDetails && /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			size: "small",
			color: "info",
			onClick: function onClick() {
				return setIsEditingDetails(true);
			},
			sx: {
				alignSelf: "flex-start",
				"&&": { ml: -.625 }
			}
		}, (0, _wordpress_i18n.__)("Edit details", "elementor")), showEditDetails && isEditingDetails && /*#__PURE__*/ react.default.createElement(BusinessDetailsForm, {
			details: businessDetails,
			maxLength: maxBusinessDetailsLength,
			onSave: onSaveBusinessDetails,
			onClose: function onClose() {
				return setIsEditingDetails(false);
			}
		}));
	};
	BrowserToolItem.propTypes = {
		tool: import_prop_types.default.shape({
			name: import_prop_types.default.string.isRequired,
			label: import_prop_types.default.string.isRequired,
			description: import_prop_types.default.string.isRequired
		}).isRequired,
		businessDetails: import_prop_types.default.string,
		maxBusinessDetailsLength: import_prop_types.default.number,
		onSaveBusinessDetails: import_prop_types.default.func,
		isMarkdownEnabled: import_prop_types.default.bool.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/components/browser-tools/browser-tools-panel.js
	var TOOLS_PER_COLUMN = 3;
	var INTRO_MAX_WIDTH = 470;
	var splitIntoColumns = function splitIntoColumns(tools) {
		var columns = [];
		for (var start = 0; start < tools.length; start += TOOLS_PER_COLUMN) columns.push(tools.slice(start, start + TOOLS_PER_COLUMN));
		return columns;
	};
	var BrowserToolsPanel = function BrowserToolsPanel(_ref) {
		var settings = _ref.settings;
		var isMarkdownEnabled = _ref.isMarkdownEnabled;
		var businessDetails = settings.businessDetails;
		var isAvailable = settings.isAvailable;
		var maxBusinessDetailsLength = settings.maxBusinessDetailsLength;
		var saveBusinessDetails = settings.saveBusinessDetails;
		var tools = settings.tools;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 5,
			pl: 7,
			pr: 6,
			py: 4,
			minWidth: 0
		}, !isAvailable && /*#__PURE__*/ react.default.createElement(_elementor_ui_Alert.default, { severity: "info" }, (0, _wordpress_i18n.__)("Agent browser tools need WordPress 6.9 or later.", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 1,
			maxWidth: INTRO_MAX_WIDTH
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, { variant: "subtitle1" }, (0, _wordpress_i18n.__)("Let visitors’ agents search and read your site", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, (0, _wordpress_i18n.__)("Adds read-only WebMCP tools to your site. Visitors using an AI agent get answers from your content and business details, and agents can search pages and understand your site’s structure.", "elementor"))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 2 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, { variant: "subtitle1" }, (0, _wordpress_i18n.__)("What’s included", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			spacing: 4
		}, splitIntoColumns(tools).map(function(column, index) {
			return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
				key: index,
				spacing: 2,
				flexGrow: 1,
				flexShrink: 1,
				flexBasis: 0,
				minWidth: 0,
				divider: /*#__PURE__*/ react.default.createElement(_elementor_ui_Divider.default, null)
			}, column.map(function(tool) {
				return /*#__PURE__*/ react.default.createElement(BrowserToolItem, {
					key: tool.name,
					tool,
					businessDetails,
					maxBusinessDetailsLength,
					onSaveBusinessDetails: saveBusinessDetails,
					isMarkdownEnabled
				});
			}));
		}))));
	};
	BrowserToolsPanel.propTypes = {
		settings: import_prop_types.default.shape({
			businessDetails: import_prop_types.default.string,
			isAvailable: import_prop_types.default.bool.isRequired,
			maxBusinessDetailsLength: import_prop_types.default.number,
			saveBusinessDetails: import_prop_types.default.func,
			tools: import_prop_types.default.arrayOf(import_prop_types.default.object).isRequired
		}).isRequired,
		isMarkdownEnabled: import_prop_types.default.bool.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/components/confirm-dialog.js
	var ConfirmDialog = function ConfirmDialog(_ref) {
		var title = _ref.title;
		var message = _ref.message;
		var confirmLabel = _ref.confirmLabel;
		var onConfirm = _ref.onConfirm;
		var onClose = _ref.onClose;
		var _useState2 = _slicedToArray((0, react.useState)(false), 2);
		var isSubmitting = _useState2[0];
		var setIsSubmitting = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)(""), 2);
		var error = _useState4[0];
		var setError = _useState4[1];
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Dialog.default, {
			open: true,
			maxWidth: "xs",
			fullWidth: true,
			onClose,
			sx: { zIndex: DIALOG_Z_INDEX$2 }
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogHeader.default, {
			onClose,
			logo: false
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogTitle.default, null, title)), /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogContent.default, null, error && /*#__PURE__*/ react.default.createElement(_elementor_ui_Alert.default, {
			severity: "error",
			sx: { mb: 2 }
		}, error), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, { variant: "body2" }, message)), /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogActions.default, null, /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			color: "secondary",
			onClick: onClose,
			disabled: isSubmitting
		}, (0, _wordpress_i18n.__)("Cancel", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "contained",
			onClick: function handleConfirm() {
				setIsSubmitting(true);
				setError("");
				onConfirm().then(onClose).catch(function(reason) {
					setError(getSaveErrorMessage(reason));
					setIsSubmitting(false);
				});
			},
			disabled: isSubmitting
		}, confirmLabel)));
	};
	ConfirmDialog.propTypes = {
		title: import_prop_types.default.string.isRequired,
		message: import_prop_types.default.string.isRequired,
		confirmLabel: import_prop_types.default.string.isRequired,
		onConfirm: import_prop_types.default.func.isRequired,
		onClose: import_prop_types.default.func.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/components/content-edit-dialog.js
	var EDITOR_MIN_ROWS = 20;
	var getErrorMessage = function getErrorMessage(reason) {
		return "string" === typeof reason && reason ? reason : (0, _wordpress_i18n.__)("Something went wrong. Please try again.", "elementor");
	};
	var ContentEditDialog = function ContentEditDialog(_ref) {
		var title = _ref.title;
		var content = _ref.content;
		var isEditing = _ref.isEditing;
		var onClose = _ref.onClose;
		var onSave = _ref.onSave;
		var _useState2 = _slicedToArray((0, react.useState)(content), 2);
		var draft = _useState2[0];
		var setDraft = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)(false), 2);
		var isSaving = _useState4[0];
		var setIsSaving = _useState4[1];
		var _useState6 = _slicedToArray((0, react.useState)(""), 2);
		var error = _useState6[0];
		var setError = _useState6[1];
		var isSaveDisabled = isSaving || !draft.trim() || draft === content;
		var actions = isEditing ? /*#__PURE__*/ react.default.createElement(react.default.Fragment, null, /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			color: "secondary",
			onClick: onClose
		}, (0, _wordpress_i18n.__)("Cancel", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "contained",
			onClick: function handleSave() {
				setIsSaving(true);
				setError("");
				onSave(draft).then(onClose).catch(function(reason) {
					setError(getErrorMessage(reason));
					setIsSaving(false);
				});
			},
			disabled: isSaveDisabled
		}, (0, _wordpress_i18n.__)("Save", "elementor"))) : null;
		return /*#__PURE__*/ react.default.createElement(ContentPreviewDialog, {
			title,
			onClose,
			actions
		}, error && /*#__PURE__*/ react.default.createElement(_elementor_ui_Alert.default, {
			severity: "error",
			sx: { mb: 2 }
		}, error), isEditing ? /*#__PURE__*/ react.default.createElement(_elementor_ui_TextField.default, {
			value: draft,
			onChange: function onChange(event) {
				return setDraft(event.target.value);
			},
			multiline: true,
			fullWidth: true,
			minRows: EDITOR_MIN_ROWS,
			inputProps: { "aria-label": title },
			InputProps: { sx: { fontFamily: "monospace" } }
		}) : /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			component: "pre",
			m: 0,
			typography: "body2",
			fontFamily: "monospace",
			whiteSpace: "pre-wrap"
		}, content));
	};
	ContentEditDialog.propTypes = {
		title: import_prop_types.default.string.isRequired,
		content: import_prop_types.default.string.isRequired,
		isEditing: import_prop_types.default.bool.isRequired,
		onClose: import_prop_types.default.func.isRequired,
		onSave: import_prop_types.default.func.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/hooks/use-markdown-items.js
	var SEARCH_DEBOUNCE_MS = 300;
	var useMarkdownItems = function useMarkdownItems(_ref) {
		var _ref$term = _ref.term;
		var term = _ref$term === void 0 ? "" : _ref$term;
		var _ref$refreshKey = _ref.refreshKey;
		var refreshKey = _ref$refreshKey === void 0 ? "" : _ref$refreshKey;
		var _ref$isPaused = _ref.isPaused;
		var isPaused = _ref$isPaused === void 0 ? false : _ref$isPaused;
		var _useState2 = _slicedToArray((0, react.useState)([]), 2);
		var items = _useState2[0];
		var setItems = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)(false), 2);
		var isLoading = _useState4[0];
		var setIsLoading = _useState4[1];
		(0, react.useEffect)(function() {
			if (isPaused) {
				setIsLoading(false);
				return;
			}
			var isCurrent = true;
			var trimmedTerm = term.trim();
			var load = function load() {
				searchMarkdownItems(trimmedTerm).then(function(nextItems) {
					if (isCurrent) setItems(nextItems);
				}).catch(function() {
					if (isCurrent) setItems([]);
				}).finally(function() {
					if (isCurrent) setIsLoading(false);
				});
			};
			setIsLoading(true);
			if (!trimmedTerm) {
				load();
				return function() {
					isCurrent = false;
				};
			}
			var timer = setTimeout(load, 300);
			return function() {
				isCurrent = false;
				clearTimeout(timer);
			};
		}, [
			term,
			refreshKey,
			isPaused
		]);
		return {
			items,
			isLoading
		};
	};

//#endregion
//#region modules/agents/assets/js/admin/hooks/use-content-items.js
	function ownKeys$17(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$17, "ownKeys");
	function _objectSpread$16(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$17(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$17(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$16, "_objectSpread");
	var FIRST_PAGE = 1;
	var LOADING_STATE = {
		items: [],
		page: 0,
		hasMore: false,
		isLoading: true,
		hasError: false
	};
	var appendNewItems = function appendNewItems(items, nextItems) {
		var knownIds = new Set(items.map(function(item) {
			return item.id;
		}));
		return [].concat(_toConsumableArray(items), _toConsumableArray(nextItems.filter(function(item) {
			return !knownIds.has(item.id);
		})));
	};
	var useContentItems = function useContentItems(_ref) {
		var module = _ref.module;
		var postType = _ref.postType;
		var _ref$term = _ref.term;
		var term = _ref$term === void 0 ? "" : _ref$term;
		var _useState2 = _slicedToArray((0, react.useState)(LOADING_STATE), 2);
		var state = _useState2[0];
		var setState = _useState2[1];
		var requestIdRef = (0, react.useRef)(0);
		var trimmedTerm = term.trim();
		var ignorePendingRequest = (0, react.useCallback)(function() {
			return ++requestIdRef.current;
		}, []);
		var load = (0, react.useCallback)(function(page) {
			var requestId = ignorePendingRequest();
			var isFirstPage = FIRST_PAGE === page;
			setState(function(previous) {
				return isFirstPage ? LOADING_STATE : _objectSpread$16(_objectSpread$16({}, previous), {}, { isLoading: true });
			});
			listContentItems({
				module,
				postType,
				term: trimmedTerm,
				page
			}).then(function(_ref2) {
				var items = _ref2.items;
				var hasMore = _ref2.hasMore;
				if (requestId !== requestIdRef.current) return;
				setState(function(previous) {
					return {
						items: isFirstPage ? items : appendNewItems(previous.items, items),
						page,
						hasMore,
						isLoading: false,
						hasError: false
					};
				});
			}).catch(function() {
				if (requestId === requestIdRef.current) setState(function(previous) {
					return _objectSpread$16(_objectSpread$16({}, previous), {}, {
						hasMore: false,
						isLoading: false,
						hasError: true
					});
				});
			});
		}, [
			module,
			postType,
			trimmedTerm,
			ignorePendingRequest
		]);
		(0, react.useEffect)(function() {
			setState(LOADING_STATE);
			var timer = setTimeout(function() {
				return load(FIRST_PAGE);
			}, trimmedTerm ? 300 : 0);
			return function() {
				clearTimeout(timer);
				ignorePendingRequest();
			};
		}, [
			load,
			trimmedTerm,
			ignorePendingRequest
		]);
		var loadMore = (0, react.useCallback)(function() {
			if (!state.isLoading && state.hasMore) load(state.page + 1);
		}, [
			load,
			state.isLoading,
			state.hasMore,
			state.page
		]);
		var retry = (0, react.useCallback)(function() {
			return load(FIRST_PAGE);
		}, [load]);
		return {
			items: state.items,
			hasMore: state.hasMore,
			isLoading: state.isLoading,
			hasError: state.hasError,
			loadMore,
			retry
		};
	};

//#endregion
//#region modules/agents/assets/js/admin/components/post-exclusion-dialog.js
	var LIST_MAX_HEIGHT = 400;
	var LOAD_MORE_THRESHOLD_PX = 48;
	var PROGRESS_SIZE = 20;
	var FIELD_RADIUS = "8px";
	var isNearBottom = function isNearBottom(element) {
		return element.scrollHeight - element.scrollTop - element.clientHeight <= LOAD_MORE_THRESHOLD_PX;
	};
	var isNotScrollable = function isNotScrollable(element) {
		return element.scrollHeight <= element.clientHeight;
	};
	var ItemRow = function ItemRow(_ref) {
		var item = _ref.item;
		var isIncluded = _ref.isIncluded;
		var isDisabled = _ref.isDisabled;
		var onToggle = _ref.onToggle;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			spacing: 2,
			py: 1
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Link.default, {
			href: item.url,
			target: "_blank",
			rel: "noopener noreferrer",
			variant: "body2",
			color: "text.primary",
			underline: "hover",
			noWrap: true,
			flexGrow: 1,
			minWidth: 0
		}, item.title || (0, _wordpress_i18n.__)("(no title)", "elementor"), /*#__PURE__*/ react.default.createElement(_elementor_icons.ExternalLinkIcon, {
			fontSize: "tiny",
			sx: {
				ml: .5,
				verticalAlign: "middle"
			}
		})), /*#__PURE__*/ react.default.createElement(_elementor_ui_Switch.default, {
			size: "small",
			checked: isIncluded,
			disabled: isDisabled,
			onChange: onToggle,
			inputProps: { "aria-label": item.title },
			sx: wpAdminSwitchSx
		}));
	};
	ItemRow.propTypes = {
		item: import_prop_types.default.shape({
			id: import_prop_types.default.number.isRequired,
			title: import_prop_types.default.string.isRequired,
			url: import_prop_types.default.string.isRequired
		}).isRequired,
		isIncluded: import_prop_types.default.bool.isRequired,
		isDisabled: import_prop_types.default.bool.isRequired,
		onToggle: import_prop_types.default.func.isRequired
	};
	var PostExclusionDialog = function PostExclusionDialog(_ref2) {
		var _postType$excludedCou;
		var module = _ref2.module;
		var postType = _ref2.postType;
		var excludedPostIds = _ref2.excludedPostIds;
		var _ref2$isDisabled = _ref2.isDisabled;
		var isDisabled = _ref2$isDisabled === void 0 ? false : _ref2$isDisabled;
		var _onToggle = _ref2.onToggle;
		var onClose = _ref2.onClose;
		var listRef = (0, react.useRef)(null);
		var _useState2 = _slicedToArray((0, react.useState)(""), 2);
		var term = _useState2[0];
		var setTerm = _useState2[1];
		var _useContentItems = useContentItems({
			module,
			postType: postType.name,
			term
		});
		var items = _useContentItems.items;
		var isLoading = _useContentItems.isLoading;
		var hasError = _useContentItems.hasError;
		var loadMore = _useContentItems.loadMore;
		var retry = _useContentItems.retry;
		var excludedSet = new Set(excludedPostIds);
		(0, react.useEffect)(function() {
			if (listRef.current && isNotScrollable(listRef.current)) loadMore();
		}, [items, loadMore]);
		var handleScroll = function handleScroll(event) {
			if (isNearBottom(event.currentTarget)) loadMore();
		};
		var hasNoResults = !isLoading && !hasError && !items.length;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Dialog.default, {
			open: true,
			maxWidth: "sm",
			fullWidth: true,
			onClose,
			sx: { zIndex: DIALOG_Z_INDEX$2 }
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogHeader.default, {
			onClose,
			logo: false
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogTitle.default, null, postType.label)), /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogContent.default, { dividers: true }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 2 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("%1$d published · %2$d excluded", "elementor"), postType.count, (_postType$excludedCou = postType.excludedCount) !== null && _postType$excludedCou !== void 0 ? _postType$excludedCou : 0)), /*#__PURE__*/ react.default.createElement(_elementor_ui_TextField.default, {
			fullWidth: true,
			size: "tiny",
			value: term,
			onChange: function onChange(event) {
				return setTerm(event.target.value);
			},
			placeholder: (0, _wordpress_i18n.__)("Search", "elementor"),
			inputProps: { "aria-label": (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("Search %s", "elementor"), postType.label) },
			InputProps: {
				startAdornment: /*#__PURE__*/ react.default.createElement(_elementor_ui_InputAdornment.default, { position: "start" }, /*#__PURE__*/ react.default.createElement(_elementor_icons.ZoomIcon, { fontSize: "tiny" })),
				sx: { borderRadius: FIELD_RADIUS }
			},
			sx: wpAdminInputResetSx
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			ref: listRef,
			maxHeight: LIST_MAX_HEIGHT,
			overflow: "auto",
			onScroll: handleScroll,
			"data-testid": "post-exclusion-list"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, null, items.map(function(item) {
			return /*#__PURE__*/ react.default.createElement(ItemRow, {
				key: item.id,
				item,
				isIncluded: !excludedSet.has(item.id),
				isDisabled,
				onToggle: function onToggle() {
					return _onToggle(postType.name, item.id);
				}
			});
		})), isLoading && /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			alignItems: "center",
			py: 2
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_CircularProgress.default, {
			size: PROGRESS_SIZE,
			"aria-label": (0, _wordpress_i18n.__)("Loading", "elementor")
		})), hasError && /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			alignItems: "center",
			spacing: 1,
			py: 4
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary",
			align: "center"
		}, (0, _wordpress_i18n.__)("Couldn't load items.", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "info",
			size: "small",
			onClick: retry
		}, (0, _wordpress_i18n.__)("Try again", "elementor"))), hasNoResults && /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary",
			align: "center",
			py: 4
		}, (0, _wordpress_i18n.__)("Nothing matched your search.", "elementor"))))));
	};
	PostExclusionDialog.propTypes = {
		module: import_prop_types.default.string.isRequired,
		postType: import_prop_types.default.shape({
			name: import_prop_types.default.string.isRequired,
			label: import_prop_types.default.string.isRequired,
			count: import_prop_types.default.number.isRequired,
			excludedCount: import_prop_types.default.number
		}).isRequired,
		excludedPostIds: import_prop_types.default.arrayOf(import_prop_types.default.number).isRequired,
		isDisabled: import_prop_types.default.bool,
		onToggle: import_prop_types.default.func.isRequired,
		onClose: import_prop_types.default.func.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/components/post-type-list.js
	var iconsByPostType = {
		page: _elementor_icons.FileIcon,
		post: _elementor_icons.PinIcon
	};
	var PostTypeLabel = function PostTypeLabel(_ref) {
		var label = _ref.label;
		var onOpenItems = _ref.onOpenItems;
		if (!onOpenItems) return /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, { variant: "body2" }, label);
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Link.default, {
			component: "button",
			type: "button",
			variant: "body2",
			color: "text.primary",
			underline: "hover",
			onClick: onOpenItems,
			"aria-label": (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("Choose which %s to include", "elementor"), label)
		}, label);
	};
	PostTypeLabel.propTypes = {
		label: import_prop_types.default.string.isRequired,
		onOpenItems: import_prop_types.default.func
	};
	var PublishedStatus = function PublishedStatus(_ref2) {
		var count = _ref2.count;
		var excludedCount = _ref2.excludedCount;
		if (!excludedCount) return /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("%d published", "elementor"), count));
		var visibleCount = count - excludedCount;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Tooltip.default, { title: (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("%1$d visible to agents · %2$d excluded", "elementor"), visibleCount, excludedCount) }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("%1$d/%2$d published", "elementor"), visibleCount, count)));
	};
	PublishedStatus.propTypes = {
		count: import_prop_types.default.number.isRequired,
		excludedCount: import_prop_types.default.number.isRequired
	};
	var PostTypeRow = function PostTypeRow(_ref3) {
		var _iconsByPostType$name;
		var name = _ref3.name;
		var label = _ref3.label;
		var count = _ref3.count;
		var excludedCount = _ref3.excludedCount;
		var isIncluded = _ref3.isIncluded;
		var isDisabled = _ref3.isDisabled;
		var onToggle = _ref3.onToggle;
		var onOpenItems = _ref3.onOpenItems;
		var Icon = (_iconsByPostType$name = iconsByPostType[name]) !== null && _iconsByPostType$name !== void 0 ? _iconsByPostType$name : _elementor_icons.ArchiveTemplateIcon;
		var canOpenItems = Boolean(onOpenItems) && isIncluded && !isDisabled;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			py: 1
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			spacing: 1,
			width: 120
		}, /*#__PURE__*/ react.default.createElement(Icon, {
			fontSize: "small",
			color: "action"
		}), /*#__PURE__*/ react.default.createElement(PostTypeLabel, {
			label,
			onOpenItems: canOpenItems ? onOpenItems : void 0
		})), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			width: 120,
			flexGrow: 1
		}, /*#__PURE__*/ react.default.createElement(PublishedStatus, {
			count,
			excludedCount: isIncluded ? excludedCount : 0
		})), /*#__PURE__*/ react.default.createElement(_elementor_ui_Switch.default, {
			size: "small",
			checked: isIncluded,
			disabled: isDisabled,
			onChange: onToggle,
			inputProps: { "aria-label": label },
			sx: wpAdminSwitchSx
		}));
	};
	PostTypeRow.propTypes = {
		name: import_prop_types.default.string.isRequired,
		label: import_prop_types.default.string.isRequired,
		count: import_prop_types.default.number.isRequired,
		excludedCount: import_prop_types.default.number.isRequired,
		isIncluded: import_prop_types.default.bool.isRequired,
		isDisabled: import_prop_types.default.bool.isRequired,
		onToggle: import_prop_types.default.func.isRequired,
		onOpenItems: import_prop_types.default.func
	};
	var PostTypeList = function PostTypeList(_ref4) {
		var postTypes = _ref4.postTypes;
		var isDisabled = _ref4.isDisabled;
		var _onToggle = _ref4.onToggle;
		var exclusions = _ref4.exclusions;
		var _useState2 = _slicedToArray((0, react.useState)(false), 2);
		var isExpanded = _useState2[0];
		var setIsExpanded = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)(null), 2);
		var openPostTypeName = _useState4[0];
		var setOpenPostTypeName = _useState4[1];
		var openPostType = postTypes.find(function(postType) {
			return postType.name === openPostTypeName;
		});
		var closeItemsDialog = function closeItemsDialog() {
			setOpenPostTypeName(null);
			exclusions === null || exclusions === void 0 || exclusions.onClose();
		};
		var visiblePostTypes = postTypes.slice(0, 4);
		var hiddenPostTypes = postTypes.slice(4);
		var renderRows = function renderRows(items) {
			return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, null, items.map(function(postType) {
				var _postType$excludedCou;
				return /*#__PURE__*/ react.default.createElement(PostTypeRow, {
					key: postType.name,
					name: postType.name,
					label: postType.label,
					count: postType.count,
					excludedCount: (_postType$excludedCou = postType.excludedCount) !== null && _postType$excludedCou !== void 0 ? _postType$excludedCou : 0,
					isIncluded: postType.included,
					isDisabled,
					onToggle: function onToggle() {
						return _onToggle(postType.name);
					},
					onOpenItems: exclusions ? function() {
						return setOpenPostTypeName(postType.name);
					} : void 0
				});
			}));
		};
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { alignItems: "flex-start" }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { width: "100%" }, renderRows(visiblePostTypes), hiddenPostTypes.length > 0 && /*#__PURE__*/ react.default.createElement(_elementor_ui_Collapse.default, { in: isExpanded }, renderRows(hiddenPostTypes))), hiddenPostTypes.length > 0 && /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "info",
			size: "small",
			endIcon: isExpanded ? /*#__PURE__*/ react.default.createElement(_elementor_icons.ChevronUpIcon, null) : /*#__PURE__*/ react.default.createElement(_elementor_icons.ChevronDownIcon, null),
			onClick: function onClick() {
				return setIsExpanded(function(previous) {
					return !previous;
				});
			}
		}, isExpanded ? (0, _wordpress_i18n.__)("Show less", "elementor") : (0, _wordpress_i18n.__)("Show more", "elementor")), openPostType && exclusions && /*#__PURE__*/ react.default.createElement(PostExclusionDialog, {
			module: exclusions.module,
			postType: openPostType,
			excludedPostIds: exclusions.excludedPostIds,
			onToggle: exclusions.onToggle,
			onClose: closeItemsDialog
		}));
	};
	PostTypeList.propTypes = {
		postTypes: import_prop_types.default.arrayOf(import_prop_types.default.shape({
			name: import_prop_types.default.string.isRequired,
			label: import_prop_types.default.string.isRequired,
			count: import_prop_types.default.number.isRequired,
			included: import_prop_types.default.bool.isRequired,
			excludedCount: import_prop_types.default.number
		})).isRequired,
		isDisabled: import_prop_types.default.bool.isRequired,
		onToggle: import_prop_types.default.func.isRequired,
		exclusions: import_prop_types.default.shape({
			module: import_prop_types.default.string.isRequired,
			excludedPostIds: import_prop_types.default.arrayOf(import_prop_types.default.number).isRequired,
			onToggle: import_prop_types.default.func.isRequired,
			onClose: import_prop_types.default.func.isRequired
		})
	};

//#endregion
//#region modules/agents/assets/js/admin/components/content-preview-pane.js
	var PREVIEW_WIDTH = 500;
	var scrollbarSx = {
		scrollbarWidth: "none",
		"&::-webkit-scrollbar": { display: "none" }
	};
	var ContentPreviewPane = function ContentPreviewPane(_ref) {
		var header = _ref.header;
		var content = _ref.content;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 2,
			p: 4,
			width: PREVIEW_WIDTH,
			maxWidth: "100%",
			minWidth: 0,
			flexShrink: 1,
			bgcolor: "grey.50",
			sx: { contain: "size" }
		}, header, /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			component: "pre",
			m: 0,
			flexGrow: 1,
			minHeight: 0,
			overflow: "auto",
			typography: "caption",
			fontFamily: "monospace",
			sx: scrollbarSx
		}, content));
	};
	ContentPreviewPane.propTypes = {
		header: import_prop_types.default.node.isRequired,
		content: import_prop_types.default.string.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/components/llms-txt/llms-preview-pane.js
	var LlmsPreviewPane = function LlmsPreviewPane(_ref) {
		var content = _ref.content;
		var hasActions = _ref.hasActions;
		var onEdit = _ref.onEdit;
		var onExpand = _ref.onExpand;
		var header = /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			justifyContent: "space-between"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "subtitle2",
			component: "code",
			fontFamily: "monospace"
		}, LLMS_FILE_NAME), hasActions && /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			spacing: 1
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Tooltip.default, { title: (0, _wordpress_i18n.__)("Edit", "elementor") }, /*#__PURE__*/ react.default.createElement(_elementor_ui_IconButton.default, {
			variant: "outlined",
			size: "xsmall",
			onClick: onEdit,
			"aria-label": (0, _wordpress_i18n.__)("Edit", "elementor")
		}, /*#__PURE__*/ react.default.createElement(_elementor_icons.PencilIcon, { fontSize: "small" }))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Tooltip.default, { title: (0, _wordpress_i18n.__)("Full screen", "elementor") }, /*#__PURE__*/ react.default.createElement(_elementor_ui_IconButton.default, {
			variant: "outlined",
			size: "small",
			onClick: onExpand,
			"aria-label": (0, _wordpress_i18n.__)("Full screen", "elementor")
		}, /*#__PURE__*/ react.default.createElement(_elementor_icons.ArrowsDiagonalIcon, { fontSize: "xsmall" })))));
		return /*#__PURE__*/ react.default.createElement(ContentPreviewPane, {
			header,
			content
		});
	};
	LlmsPreviewPane.propTypes = {
		content: import_prop_types.default.string.isRequired,
		hasActions: import_prop_types.default.bool.isRequired,
		onEdit: import_prop_types.default.func.isRequired,
		onExpand: import_prop_types.default.func.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/components/llms-txt/llms-txt-panel.js
	function ownKeys$16(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$16, "ownKeys");
	function _objectSpread$15(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$16(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$16(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$15, "_objectSpread");
	var DIALOG_MODE_EDIT$1 = "edit";
	var DIALOG_MODE_VIEW$1 = "view";
	var DIALOG_MODE_REVERT$1 = "revert";
	var DIALOG_MODE_TAKE_OVER = "take-over";
	var getWarningMessage = function getWarningMessage(hasPhysicalFile, isManuallyEdited) {
		if (hasPhysicalFile) return (0, _wordpress_i18n.__)("This file is managed outside Agent Ready and can’t be controlled from here.", "elementor");
		if (isManuallyEdited) return (0, _wordpress_i18n.__)("This file was edited manually and no longer syncs automatically.", "elementor");
		return "";
	};
	var LlmsTxtPanel = function LlmsTxtPanel(_ref) {
		var settings = _ref.settings;
		var content = settings.content;
		var excludedPostIds = settings.excludedPostIds;
		var flushExclusions = settings.flushExclusions;
		var hasPhysicalFile = settings.hasPhysicalFile;
		var isEnabled = settings.isEnabled;
		var isManuallyEdited = settings.isManuallyEdited;
		var isSaving = settings.isSaving;
		var postTypes = settings.postTypes;
		var revertContent = settings.revertContent;
		var saveContent = settings.saveContent;
		var takeOverFile = settings.takeOverFile;
		var togglePostExclusion = settings.togglePostExclusion;
		var togglePostType = settings.togglePostType;
		var _useState2 = _slicedToArray((0, react.useState)(null), 2);
		var dialogMode = _useState2[0];
		var setDialogMode = _useState2[1];
		var canChooseItems = !hasPhysicalFile && !isManuallyEdited;
		var warningMessage = getWarningMessage(hasPhysicalFile, isManuallyEdited);
		var displayedPostTypes = hasPhysicalFile ? postTypes.map(function(postType) {
			return _objectSpread$15(_objectSpread$15({}, postType), {}, { included: false });
		}) : postTypes;
		var closeDialog = function closeDialog() {
			return setDialogMode(null);
		};
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			width: "100%",
			maxWidth: "100%",
			minWidth: 0,
			overflow: "auto"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 3,
			px: 6,
			py: 3,
			flexGrow: 1,
			flexShrink: 1,
			flexBasis: 0,
			minWidth: 0
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 1 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, { variant: "subtitle1" }, (0, _wordpress_i18n.__)("Help AI agents understand your site", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, (0, _wordpress_i18n.__)("llms.txt is a short guide that tells AI agents what your site is about and where its public content is. With llms.txt agents can find your key pages without crawling everything. We keep it up to date when you make changes to your site.", "elementor"))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 1 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, { variant: "subtitle1" }, (0, _wordpress_i18n.__)("Choose what to include", "elementor")), /*#__PURE__*/ react.default.createElement(PostTypeList, {
			postTypes: displayedPostTypes,
			isDisabled: !isEnabled || isManuallyEdited || isSaving,
			onToggle: togglePostType,
			exclusions: canChooseItems ? {
				module: LLMS_SETTINGS_KEY,
				excludedPostIds,
				onToggle: togglePostExclusion,
				onClose: flushExclusions
			} : void 0
		})), warningMessage && /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			alignItems: "flex-start",
			spacing: 1
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Chip.default, {
			icon: /*#__PURE__*/ react.default.createElement(_elementor_icons.AlertCircleIcon, null),
			label: warningMessage,
			color: "warning",
			size: "small",
			shape: "rounded",
			variant: "standard"
		}), isManuallyEdited && !hasPhysicalFile && /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "outlined",
			size: "small",
			onClick: function onClick() {
				return setDialogMode(DIALOG_MODE_REVERT$1);
			}
		}, (0, _wordpress_i18n.__)("Revert to generated version", "elementor")), hasPhysicalFile && /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "outlined",
			size: "small",
			onClick: function onClick() {
				return setDialogMode(DIALOG_MODE_TAKE_OVER);
			}
		}, (0, _wordpress_i18n.__)("Take over llms.txt", "elementor")))), (isEnabled || hasPhysicalFile) && /*#__PURE__*/ react.default.createElement(LlmsPreviewPane, {
			content: hasPhysicalFile ? "" : content,
			hasActions: !hasPhysicalFile,
			onEdit: function onEdit() {
				return setDialogMode(DIALOG_MODE_EDIT$1);
			},
			onExpand: function onExpand() {
				return setDialogMode(DIALOG_MODE_VIEW$1);
			}
		}), DIALOG_MODE_REVERT$1 === dialogMode && /*#__PURE__*/ react.default.createElement(ConfirmDialog, {
			title: (0, _wordpress_i18n.__)("Revert to generated version?", "elementor"),
			message: (0, _wordpress_i18n.__)("Your manual edits will be discarded. llms.txt will be regenerated from your current site content and kept up to date automatically.", "elementor"),
			confirmLabel: (0, _wordpress_i18n.__)("Revert", "elementor"),
			onConfirm: revertContent,
			onClose: closeDialog
		}), DIALOG_MODE_TAKE_OVER === dialogMode && /*#__PURE__*/ react.default.createElement(ConfirmDialog, {
			title: (0, _wordpress_i18n.__)("Take over llms.txt?", "elementor"),
			message: (0, _wordpress_i18n.__)("The existing llms.txt file will be replaced with the version generated by Agent Ready. Its current content will be overwritten and can’t be recovered.", "elementor"),
			confirmLabel: (0, _wordpress_i18n.__)("Take over", "elementor"),
			onConfirm: takeOverFile,
			onClose: closeDialog
		}), [DIALOG_MODE_EDIT$1, DIALOG_MODE_VIEW$1].includes(dialogMode) && /*#__PURE__*/ react.default.createElement(ContentEditDialog, {
			title: "llms.txt",
			content,
			isEditing: DIALOG_MODE_EDIT$1 === dialogMode,
			onClose: closeDialog,
			onSave: saveContent
		}));
	};
	LlmsTxtPanel.propTypes = { settings: import_prop_types.default.shape({
		content: import_prop_types.default.string.isRequired,
		excludedPostIds: import_prop_types.default.arrayOf(import_prop_types.default.number).isRequired,
		flushExclusions: import_prop_types.default.func.isRequired,
		hasPhysicalFile: import_prop_types.default.bool.isRequired,
		isEnabled: import_prop_types.default.bool.isRequired,
		isManuallyEdited: import_prop_types.default.bool.isRequired,
		isSaving: import_prop_types.default.bool.isRequired,
		postTypes: import_prop_types.default.array.isRequired,
		revertContent: import_prop_types.default.func.isRequired,
		saveContent: import_prop_types.default.func.isRequired,
		takeOverFile: import_prop_types.default.func.isRequired,
		togglePostExclusion: import_prop_types.default.func.isRequired,
		togglePostType: import_prop_types.default.func.isRequired
	}).isRequired };

//#endregion
//#region modules/agents/assets/js/admin/components/markdown-content/markdown-items.js
	var getIncludedTypesKey = function getIncludedTypesKey(postTypes) {
		return postTypes.filter(function(postType) {
			return postType.included;
		}).map(function(postType) {
			return postType.name;
		}).join(",");
	};
	var getMarkdownFileName = function getMarkdownFileName(item) {
		var segment = item.path.replace(/\/$/, "").split("/").pop();
		return segment ? "".concat(segment, ".md") : "index.md";
	};

//#endregion
//#region modules/agents/assets/js/admin/components/markdown-content/page-picker.js
	function ownKeys$15(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$15, "ownKeys");
	function _objectSpread$14(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$15(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$15(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$14, "_objectSpread");
	var PAGE_FIELD_WIDTH = 177;
	var PAGE_FIELD_RADIUS = "8px";
	var PAGE_MENU_WIDTH = 254;
	var PAGE_MENU_HEIGHT = 324;
	var PAGE_MENU_RADIUS = "16px";
	var EMPTY_STATE_WIDTH = 170;
	var SEARCH_PROGRESS_SIZE = 20;
	var USER_INPUT_REASON = "input";
	var itemShape = import_prop_types.default.shape({
		id: import_prop_types.default.number.isRequired,
		title: import_prop_types.default.string.isRequired,
		path: import_prop_types.default.string.isRequired,
		type: import_prop_types.default.string.isRequired,
		isOverridden: import_prop_types.default.bool
	});
	var withSelectedItem = function withSelectedItem(items, selectedItem) {
		if (!selectedItem || items.some(function(item) {
			return item.id === selectedItem.id;
		})) return items;
		return [selectedItem].concat(_toConsumableArray(items));
	};
	var EmptyState = function EmptyState(_ref) {
		var query = _ref.query;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			alignItems: "center",
			spacing: 1,
			py: 4,
			px: 2
		}, /*#__PURE__*/ react.default.createElement(_elementor_icons.FileIcon, {
			color: "action",
			fontSize: "large"
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "subtitle2",
			color: "text.secondary",
			align: "center",
			width: EMPTY_STATE_WIDTH
		}, (0, _wordpress_i18n.__)("Sorry, nothing matched", "elementor"), /*#__PURE__*/ react.default.createElement("br", null), (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("“%s”.", "elementor"), query)));
	};
	EmptyState.propTypes = { query: import_prop_types.default.string.isRequired };
	var PagePicker = function PagePicker(_ref2) {
		var items = _ref2.items;
		var selectedItem = _ref2.selectedItem;
		var onSelect = _ref2.onSelect;
		var _ref2$disabled = _ref2.disabled;
		var disabled = _ref2$disabled === void 0 ? false : _ref2$disabled;
		var _useState2 = _slicedToArray((0, react.useState)(false), 2);
		var isOpen = _useState2[0];
		var setIsOpen = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)(""), 2);
		var query = _useState4[0];
		var setQuery = _useState4[1];
		var hasQuery = "" !== query.trim();
		var _useMarkdownItems = useMarkdownItems({
			term: query,
			isPaused: !isOpen || !hasQuery
		});
		var searchResults = _useMarkdownItems.items;
		var isLoading = _useMarkdownItems.isLoading;
		var isSearching = hasQuery && isLoading;
		var visibleItems = hasQuery ? searchResults : items;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Autocomplete.default, {
			size: "tiny",
			disabled,
			disableClearable: true,
			open: isOpen,
			onOpen: function onOpen() {
				return setIsOpen(true);
			},
			onClose: function close() {
				setIsOpen(false);
				setQuery("");
			},
			value: selectedItem !== null && selectedItem !== void 0 ? selectedItem : null,
			options: withSelectedItem(visibleItems, selectedItem),
			onChange: function onChange(event, item) {
				return item && onSelect(item);
			},
			onInputChange: function updateQuery(event, value, reason) {
				if (USER_INPUT_REASON === reason) setQuery(value);
			},
			filterOptions: function filterOptions() {
				return isSearching ? [] : visibleItems;
			},
			getOptionLabel: function getOptionLabel(item) {
				return item.title;
			},
			isOptionEqualToValue: function isOptionEqualToValue(option, value) {
				return option.id === value.id;
			},
			loading: isSearching,
			loadingText: /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
				alignItems: "center",
				py: 4
			}, /*#__PURE__*/ react.default.createElement(_elementor_ui_CircularProgress.default, {
				size: SEARCH_PROGRESS_SIZE,
				"aria-label": (0, _wordpress_i18n.__)("Searching pages", "elementor")
			})),
			noOptionsText: /*#__PURE__*/ react.default.createElement(EmptyState, { query }),
			renderOption: function renderOption(optionProps, item) {
				return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, _extends$1({ component: "li" }, optionProps, { key: item.id }), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
					variant: "body2",
					noWrap: true,
					minWidth: 0
				}, item.title), item.isOverridden && /*#__PURE__*/ react.default.createElement(_elementor_ui_Chip.default, {
					label: (0, _wordpress_i18n.__)("Edited", "elementor"),
					size: "tiny",
					shape: "rounded",
					color: "warning",
					variant: "standard",
					sx: { ml: 1 }
				}));
			},
			ListboxProps: { sx: { maxHeight: PAGE_MENU_HEIGHT } },
			slotProps: {
				popper: {
					placement: "bottom-start",
					style: { width: PAGE_MENU_WIDTH }
				},
				paper: { sx: { borderRadius: PAGE_MENU_RADIUS } }
			},
			sx: { width: PAGE_FIELD_WIDTH },
			renderInput: function renderInput(params) {
				return /*#__PURE__*/ react.default.createElement(_elementor_ui_TextField.default, _extends$1({}, params, {
					placeholder: (0, _wordpress_i18n.__)("Select a page", "elementor"),
					sx: wpAdminInputResetSx,
					InputProps: _objectSpread$14(_objectSpread$14({}, params.InputProps), {}, { sx: { borderRadius: PAGE_FIELD_RADIUS } }),
					inputProps: _objectSpread$14(_objectSpread$14({}, params.inputProps), {}, { "aria-label": (0, _wordpress_i18n.__)("Select a page", "elementor") })
				}));
			}
		});
	};
	PagePicker.propTypes = {
		items: import_prop_types.default.arrayOf(itemShape).isRequired,
		selectedItem: itemShape,
		onSelect: import_prop_types.default.func.isRequired,
		disabled: import_prop_types.default.bool
	};

//#endregion
//#region modules/agents/assets/js/admin/components/markdown-content/markdown-preview-pane.js
	var MarkdownPreviewPane = function MarkdownPreviewPane(_ref) {
		var items = _ref.items;
		var selectedItem = _ref.selectedItem;
		var content = _ref.content;
		var hasContent = _ref.hasContent;
		var onSelect = _ref.onSelect;
		var onEdit = _ref.onEdit;
		var onExpand = _ref.onExpand;
		var header = /*#__PURE__*/ react.default.createElement(react.default.Fragment, null, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			justifyContent: "space-between"
		}, /*#__PURE__*/ react.default.createElement(PagePicker, {
			items,
			selectedItem,
			onSelect
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			spacing: 1
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Tooltip.default, { title: (0, _wordpress_i18n.__)("Edit", "elementor") }, /*#__PURE__*/ react.default.createElement("span", null, /*#__PURE__*/ react.default.createElement(_elementor_ui_IconButton.default, {
			variant: "outlined",
			size: "xsmall",
			onClick: onEdit,
			disabled: !hasContent,
			"aria-label": (0, _wordpress_i18n.__)("Edit", "elementor")
		}, /*#__PURE__*/ react.default.createElement(_elementor_icons.PencilIcon, { fontSize: "small" })))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Tooltip.default, { title: (0, _wordpress_i18n.__)("Full screen", "elementor") }, /*#__PURE__*/ react.default.createElement(_elementor_ui_IconButton.default, {
			variant: "outlined",
			size: "small",
			onClick: onExpand,
			"aria-label": (0, _wordpress_i18n.__)("Full screen", "elementor")
		}, /*#__PURE__*/ react.default.createElement(_elementor_icons.ArrowsDiagonalIcon, { fontSize: "xsmall" }))))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "caption",
			color: "text.secondary"
		}, getMarkdownFileName(selectedItem)));
		return /*#__PURE__*/ react.default.createElement(ContentPreviewPane, {
			header,
			content
		});
	};
	MarkdownPreviewPane.propTypes = {
		items: import_prop_types.default.array.isRequired,
		selectedItem: import_prop_types.default.shape({
			id: import_prop_types.default.number.isRequired,
			title: import_prop_types.default.string.isRequired,
			path: import_prop_types.default.string.isRequired
		}).isRequired,
		content: import_prop_types.default.string.isRequired,
		hasContent: import_prop_types.default.bool.isRequired,
		onSelect: import_prop_types.default.func.isRequired,
		onEdit: import_prop_types.default.func.isRequired,
		onExpand: import_prop_types.default.func.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/components/markdown-content/markdown-content-panel.js
	var DIALOG_MODE_EDIT = "edit";
	var DIALOG_MODE_VIEW = "view";
	var DIALOG_MODE_REVERT = "revert";
	var MarkdownContentPanel = function MarkdownContentPanel(_ref) {
		var _ref2;
		var _selectedItem$id;
		var settings = _ref.settings;
		var excludedPostIds = settings.excludedPostIds;
		var exclusionsSavedVersion = settings.exclusionsSavedVersion;
		var flushExclusions = settings.flushExclusions;
		var isEnabled = settings.isEnabled;
		var isSaving = settings.isSaving;
		var postTypes = settings.postTypes;
		var togglePostExclusion = settings.togglePostExclusion;
		var togglePostType = settings.togglePostType;
		var _useState2 = _slicedToArray((0, react.useState)(0), 2);
		var revision = _useState2[0];
		var setRevision = _useState2[1];
		var itemsKey = "".concat(getIncludedTypesKey(postTypes), "|").concat(exclusionsSavedVersion, "|").concat(revision);
		var defaultItems = useMarkdownItems({
			refreshKey: itemsKey,
			isPaused: !isEnabled || isSaving
		}).items;
		var _useState4 = _slicedToArray((0, react.useState)(null), 2);
		var picked = _useState4[0];
		var setPicked = _useState4[1];
		var _useState6 = _slicedToArray((0, react.useState)(""), 2);
		var content = _useState6[0];
		var setContent = _useState6[1];
		var _useState8 = _slicedToArray((0, react.useState)(false), 2);
		var isOverridden = _useState8[0];
		var setIsOverridden = _useState8[1];
		var _useState0 = _slicedToArray((0, react.useState)(null), 2);
		var dialogMode = _useState0[0];
		var setDialogMode = _useState0[1];
		var pickedItem = (picked === null || picked === void 0 ? void 0 : picked.itemsKey) === itemsKey ? picked.item : null;
		var selectedItem = isEnabled ? (_ref2 = pickedItem !== null && pickedItem !== void 0 ? pickedItem : defaultItems[0]) !== null && _ref2 !== void 0 ? _ref2 : null : null;
		var selectedItemId = (_selectedItem$id = selectedItem === null || selectedItem === void 0 ? void 0 : selectedItem.id) !== null && _selectedItem$id !== void 0 ? _selectedItem$id : 0;
		var selectItem = function selectItem(item) {
			return setPicked({
				itemsKey,
				item
			});
		};
		var closeDialog = function closeDialog() {
			return setDialogMode(null);
		};
		var refreshAfterChange = function refreshAfterChange() {
			return setRevision(function(current) {
				return current + 1;
			});
		};
		var saveContent = function saveContent(nextContent) {
			return saveMarkdownContent(selectedItemId, nextContent).then(refreshAfterChange);
		};
		var revertContent = function revertContent() {
			return revertMarkdownContent(selectedItemId).then(refreshAfterChange);
		};
		(0, react.useEffect)(function() {
			if (!selectedItemId) return;
			var isCurrent = true;
			setContent("");
			fetchMarkdownPreview(selectedItemId).then(function(preview) {
				if (isCurrent) {
					setContent(preview.content);
					setIsOverridden(preview.isOverridden);
				}
			}).catch(function() {
				if (isCurrent) {
					setContent("");
					setIsOverridden(false);
				}
			});
			return function() {
				isCurrent = false;
			};
		}, [selectedItemId, revision]);
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			width: "100%",
			maxWidth: "100%",
			minWidth: 0,
			overflow: "auto"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 3,
			px: 6,
			py: 3,
			flexGrow: 1,
			flexShrink: 1,
			flexBasis: 0,
			minWidth: 0
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 1 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, { variant: "subtitle1" }, (0, _wordpress_i18n.__)("Help agents get clearer content", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, (0, _wordpress_i18n.__)("Publishes a clean Markdown version of the pages and posts, so agents can read just the text and hierarchy, without layout code.", "elementor"))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 1 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, { variant: "subtitle1" }, (0, _wordpress_i18n.__)("Choose what to include", "elementor")), /*#__PURE__*/ react.default.createElement(PostTypeList, {
			postTypes,
			isDisabled: !isEnabled || isSaving,
			onToggle: togglePostType,
			exclusions: {
				module: MARKDOWN_SETTINGS_KEY,
				excludedPostIds,
				onToggle: togglePostExclusion,
				onClose: flushExclusions
			}
		})), selectedItem && isOverridden && /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			alignItems: "flex-start",
			spacing: 1
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Chip.default, {
			icon: /*#__PURE__*/ react.default.createElement(_elementor_icons.AlertCircleIcon, null),
			label: (0, _wordpress_i18n.__)("This page was edited manually and no longer syncs automatically.", "elementor"),
			color: "warning",
			size: "small",
			shape: "rounded",
			variant: "standard"
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "outlined",
			size: "small",
			onClick: function onClick() {
				return setDialogMode(DIALOG_MODE_REVERT);
			}
		}, (0, _wordpress_i18n.__)("Revert to generated version", "elementor")))), selectedItem && /*#__PURE__*/ react.default.createElement(MarkdownPreviewPane, {
			items: defaultItems,
			selectedItem,
			content,
			onSelect: selectItem,
			hasContent: "" !== content,
			onEdit: function onEdit() {
				return setDialogMode(DIALOG_MODE_EDIT);
			},
			onExpand: function onExpand() {
				return setDialogMode(DIALOG_MODE_VIEW);
			}
		}), [DIALOG_MODE_EDIT, DIALOG_MODE_VIEW].includes(dialogMode) && selectedItem && /*#__PURE__*/ react.default.createElement(ContentEditDialog, {
			title: getMarkdownFileName(selectedItem),
			content,
			isEditing: DIALOG_MODE_EDIT === dialogMode,
			onClose: closeDialog,
			onSave: saveContent
		}), DIALOG_MODE_REVERT === dialogMode && /*#__PURE__*/ react.default.createElement(ConfirmDialog, {
			title: (0, _wordpress_i18n.__)("Revert to generated version?", "elementor"),
			message: (0, _wordpress_i18n.__)("Your manual edits will be discarded. The markdown for this page will be regenerated from your current site content and kept up to date automatically.", "elementor"),
			confirmLabel: (0, _wordpress_i18n.__)("Revert", "elementor"),
			onConfirm: revertContent,
			onClose: closeDialog
		}));
	};
	MarkdownContentPanel.propTypes = { settings: import_prop_types.default.shape({
		excludedPostIds: import_prop_types.default.arrayOf(import_prop_types.default.number).isRequired,
		exclusionsSavedVersion: import_prop_types.default.number.isRequired,
		flushExclusions: import_prop_types.default.func.isRequired,
		isEnabled: import_prop_types.default.bool.isRequired,
		isSaving: import_prop_types.default.bool.isRequired,
		postTypes: import_prop_types.default.array.isRequired,
		togglePostExclusion: import_prop_types.default.func.isRequired,
		togglePostType: import_prop_types.default.func.isRequired
	}).isRequired };

//#endregion
//#region node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js
	function _objectWithoutPropertiesLoose$1(r, e) {
		if (null == r) return {};
		var t = {};
		for (var n in r) if ({}.hasOwnProperty.call(r, n)) {
			if (-1 !== e.indexOf(n)) continue;
			t[n] = r[n];
		}
		return t;
	}
	__name(_objectWithoutPropertiesLoose$1, "_objectWithoutPropertiesLoose");

//#endregion
//#region node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js
	function _objectWithoutProperties$1(e, t) {
		if (null == e) return {};
		var o;
		var r;
		var i = _objectWithoutPropertiesLoose$1(e, t);
		if (Object.getOwnPropertySymbols) {
			var n = Object.getOwnPropertySymbols(e);
			for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]);
		}
		return i;
	}
	__name(_objectWithoutProperties$1, "_objectWithoutProperties");

//#endregion
//#region modules/agents/assets/js/admin/components/disabled-reason-switch.js
	var _excluded$2 = ["disabledReason", "inputProps"];
	function ownKeys$14(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$14, "ownKeys");
	function _objectSpread$13(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$14(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$14(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$13, "_objectSpread");
	var REASON_MAX_WIDTH$1 = 280;
	var WRAPPER_STYLE = { display: "inline-flex" };
	var VISUALLY_HIDDEN_STYLE = {
		position: "absolute",
		width: "1px",
		height: "1px",
		overflow: "hidden",
		clip: "rect(0 0 0 0)",
		whiteSpace: "nowrap"
	};
	var DisabledReasonSwitch = function DisabledReasonSwitch(_ref) {
		var _ref$disabledReason = _ref.disabledReason;
		var disabledReason = _ref$disabledReason === void 0 ? null : _ref$disabledReason;
		var _ref$inputProps = _ref.inputProps;
		var inputProps = _ref$inputProps === void 0 ? {} : _ref$inputProps;
		var switchProps = _objectWithoutProperties$1(_ref, _excluded$2);
		var reasonId = (0, react.useId)();
		if (!disabledReason) return /*#__PURE__*/ react.default.createElement(_elementor_ui_Switch.default, _extends$1({}, switchProps, { inputProps }));
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Infotip.default, {
			arrow: true,
			placement: "top",
			content: /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
				p: 2,
				maxWidth: REASON_MAX_WIDTH$1,
				boxSizing: "border-box"
			}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
				variant: "body2",
				color: "text.primary"
			}, disabledReason))
		}, /*#__PURE__*/ react.default.createElement("span", { style: WRAPPER_STYLE }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Switch.default, _extends$1({}, switchProps, { inputProps: _objectSpread$13(_objectSpread$13({}, inputProps), {}, { "aria-describedby": reasonId }) })), /*#__PURE__*/ react.default.createElement("span", {
			id: reasonId,
			style: VISUALLY_HIDDEN_STYLE
		}, disabledReason)));
	};
	DisabledReasonSwitch.propTypes = {
		disabledReason: import_prop_types.default.string,
		inputProps: import_prop_types.default.object
	};

//#endregion
//#region modules/agents/assets/js/admin/components/module-accordion.js
	var stopEventPropagation = function stopEventPropagation(event) {
		return event.stopPropagation();
	};
	var statusIcons = _defineProperty$1(_defineProperty$1(_defineProperty$1({}, MODULE_STATUS_ENABLED, /*#__PURE__*/ react.default.createElement(_elementor_icons.CircleCheckFilledIcon, { color: "success" })), MODULE_STATUS_DISABLED, /*#__PURE__*/ react.default.createElement(_elementor_icons.CircleXFilledIcon, { color: "error" })), MODULE_STATUS_WARNING, /*#__PURE__*/ react.default.createElement(_elementor_icons.AlertCircleIcon, { color: "warning" }));
	var ModuleAccordion = function ModuleAccordion(_ref) {
		var title = _ref.title;
		var description = _ref.description;
		var status = _ref.status;
		var isEnabled = _ref.isEnabled;
		var isToggleDisabled = _ref.isToggleDisabled;
		var _ref$disabledReason = _ref.disabledReason;
		var disabledReason = _ref$disabledReason === void 0 ? null : _ref$disabledReason;
		var onToggle = _ref.onToggle;
		var isExpanded = _ref.isExpanded;
		var onExpandedChange = _ref.onExpandedChange;
		var children = _ref.children;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Accordion.default, {
			variant: "outlined",
			expanded: isExpanded,
			onChange: onExpandedChange,
			sx: {
				borderRadius: "12px",
				maxWidth: "100%",
				minWidth: 0
			}
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_AccordionSummary.default, {
			expandIcon: null,
			sx: {
				py: 2,
				borderBottom: isExpanded ? 1 : 0,
				borderColor: "divider"
			}
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			justifyContent: "space-between",
			width: "100%"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			spacing: 1
		}, statusIcons[status], /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, { variant: "h6" }, title), !isExpanded && /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.tertiary"
		}, description)), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			spacing: 2
		}, /*#__PURE__*/ react.default.createElement(DisabledReasonSwitch, {
			size: "small",
			checked: isEnabled,
			disabled: isToggleDisabled,
			disabledReason,
			onChange: onToggle,
			onClick: stopEventPropagation,
			sx: wpAdminSwitchSx
		}), isExpanded ? /*#__PURE__*/ react.default.createElement(_elementor_icons.ChevronUpIcon, { fontSize: "small" }) : /*#__PURE__*/ react.default.createElement(_elementor_icons.ChevronDownIcon, { fontSize: "small" })))), /*#__PURE__*/ react.default.createElement(_elementor_ui_AccordionDetails.default, { sx: { p: 0 } }, children));
	};
	ModuleAccordion.propTypes = {
		title: import_prop_types.default.string.isRequired,
		description: import_prop_types.default.string.isRequired,
		status: import_prop_types.default.oneOf([
			MODULE_STATUS_ENABLED,
			MODULE_STATUS_DISABLED,
			MODULE_STATUS_WARNING
		]).isRequired,
		isEnabled: import_prop_types.default.bool.isRequired,
		isToggleDisabled: import_prop_types.default.bool,
		disabledReason: import_prop_types.default.string,
		onToggle: import_prop_types.default.func.isRequired,
		isExpanded: import_prop_types.default.bool.isRequired,
		onExpandedChange: import_prop_types.default.func.isRequired,
		children: import_prop_types.default.node.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/components/modules-status.js
	var SEGMENT_WIDTH = 54;
	var BAR_HEIGHT$1 = 8;
	var BAR_RADIUS$1 = "100px";
	var getBarColor = function getBarColor(index, score, total) {
		if (total === score) return "success.main";
		return index < score ? "warning.main" : "action.disabled";
	};
	var getScoreIcon = function getScoreIcon(score, total) {
		if (total === score) return /*#__PURE__*/ react.default.createElement(_elementor_icons.CircleCheckFilledIcon, {
			color: "success",
			fontSize: "small"
		});
		if (0 === score) return /*#__PURE__*/ react.default.createElement(_elementor_icons.CircleXFilledIcon, {
			color: "error",
			fontSize: "small"
		});
		return /*#__PURE__*/ react.default.createElement(_elementor_icons.AlertCircleIcon, {
			color: "warning",
			fontSize: "small"
		});
	};
	var ModulesStatus = function ModulesStatus(_ref) {
		var score = _ref.score;
		var total = _ref.total;
		var _ref$label = _ref.label;
		var label = _ref$label === void 0 ? (0, _wordpress_i18n.__)("Active", "elementor") : _ref$label;
		var _ref$showInfoIcon = _ref.showInfoIcon;
		var showInfoIcon = _ref$showInfoIcon === void 0 ? true : _ref$showInfoIcon;
		var infoIcon = _ref.infoIcon;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			alignItems: "flex-start",
			spacing: 1,
			width: total * SEGMENT_WIDTH
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			spacing: 1,
			width: "100%"
		}, Array.from({ length: total }).map(function(_, index) {
			return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
				key: index,
				flex: 1,
				height: BAR_HEIGHT$1,
				borderRadius: BAR_RADIUS$1,
				bgcolor: getBarColor(index, score, total)
			});
		})), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			spacing: 1
		}, getScoreIcon(score, total), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "h6",
			color: "text.secondary"
		}, "".concat(score, "/").concat(total)), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			spacing: .5
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, label), showInfoIcon && (infoIcon !== null && infoIcon !== void 0 ? infoIcon : /*#__PURE__*/ react.default.createElement(_elementor_icons.InfoCircleIcon, {
			fontSize: "small",
			color: "action"
		})))));
	};
	ModulesStatus.propTypes = {
		score: import_prop_types.default.number.isRequired,
		total: import_prop_types.default.number.isRequired,
		label: import_prop_types.default.string,
		showInfoIcon: import_prop_types.default.bool,
		infoIcon: import_prop_types.default.node
	};

//#endregion
//#region modules/agents/assets/js/admin/components/page-title.js
	var agentsWordSx = {
		backgroundImage: AGENTS_WORD_GRADIENT,
		backgroundClip: "text",
		WebkitBackgroundClip: "text"
	};
	var PageTitle = function PageTitle(_ref) {
		var _ref$textAlign = _ref.textAlign;
		var textAlign = _ref$textAlign === void 0 ? "center" : _ref$textAlign;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			component: "h2",
			variant: "h4",
			fontWeight: 300,
			textAlign,
			m: 0,
			fontSize: "2.5rem",
			color: "transparent",
			sx: agentsWordSx
		}, (0, _wordpress_i18n.__)("Is your site ready for agents?", "elementor"));
	};
	PageTitle.propTypes = { textAlign: import_prop_types.default.string };

//#endregion
//#region modules/agents/assets/js/admin/components/score-standard-infotip.js
	var SCORE_STANDARD_INFOTIP_WIDTH = 320;
	var SCORE_STANDARD_TOOLTIP = (0, _wordpress_i18n.__)("Based on current standards. Score may change over time even if the site's content did not change", "elementor");
	var ScoreStandardInfotip = function ScoreStandardInfotip() {
		var _useState2 = _slicedToArray((0, react.useState)(false), 2);
		var isOpen = _useState2[0];
		var setIsOpen = _useState2[1];
		var open = function open() {
			return setIsOpen(true);
		};
		var close = function close() {
			return setIsOpen(false);
		};
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Infotip.default, {
			arrow: true,
			open: isOpen,
			placement: "right",
			disableHoverListener: true,
			disableFocusListener: true,
			content: /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
				position: "relative",
				width: SCORE_STANDARD_INFOTIP_WIDTH,
				boxSizing: "border-box",
				p: 2
			}, /*#__PURE__*/ react.default.createElement(_elementor_ui_CloseButton.default, {
				"aria-label": (0, _wordpress_i18n.__)("Close", "elementor"),
				onClick: close,
				sx: {
					position: "absolute",
					top: 1,
					right: 1
				},
				slotProps: { icon: { fontSize: "tiny" } }
			}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
				variant: "body2",
				color: "text.primary",
				pr: 3
			}, SCORE_STANDARD_TOOLTIP)),
			slotProps: { popper: {
				onMouseEnter: open,
				onMouseLeave: close
			} }
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			component: "span",
			display: "inline-flex",
			alignItems: "center",
			onMouseEnter: open,
			onMouseLeave: close
		}, /*#__PURE__*/ react.default.createElement(_elementor_icons.InfoCircleIcon, {
			fontSize: "small",
			color: "action"
		})));
	};

//#endregion
//#region modules/agents/assets/js/admin/components/modules-screen.js
	var scoreInfoIcon = /*#__PURE__*/ react.default.createElement(ScoreStandardInfotip, null);
	var modules = getModules();
	var getDisabledReason = function getDisabledReason(settings) {
		var _settings$unavailable;
		return ((_settings$unavailable = settings.unavailableReason) === null || _settings$unavailable === void 0 ? void 0 : _settings$unavailable.message) || null;
	};
	var getStatus = function getStatus(isEnabled) {
		if (arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : false) return MODULE_STATUS_WARNING;
		return isEnabled ? MODULE_STATUS_ENABLED : MODULE_STATUS_DISABLED;
	};
	var ModulesScreen = function ModulesScreen(_ref) {
		var _modules$0$id;
		var _modules$;
		var readinessScoreUrl = _ref.readinessScoreUrl;
		var agentDiscoveryConfig = _ref.agentDiscoveryConfig;
		var botAccessConfig = _ref.botAccessConfig;
		var browserToolsConfig = _ref.browserToolsConfig;
		var llmsConfig = _ref.llmsConfig;
		var markdownConfig = _ref.markdownConfig;
		var agentDiscoverySettings = useAgentDiscoverySettings(agentDiscoveryConfig);
		var botAccessSettings = useBotAccessSettings(botAccessConfig);
		var browserToolsSettings = useBrowserToolsSettings(browserToolsConfig);
		var llmsSettings = useLlmsSettings(llmsConfig);
		var markdownSettings = useMarkdownSettings(markdownConfig);
		var isLlmsUnavailable = Boolean(llmsSettings.unavailableReason);
		var isBotAccessUnavailable = Boolean(botAccessSettings.unavailableReason);
		var isBrowserToolsUnavailable = Boolean(browserToolsSettings.unavailableReason);
		var allModules = useAllModulesToggle([
			{
				settingsKey: LLMS_SETTINGS_KEY,
				settings: llmsSettings,
				isToggleable: !isLlmsUnavailable
			},
			{
				settingsKey: MARKDOWN_SETTINGS_KEY,
				settings: markdownSettings,
				isToggleable: true
			},
			{
				settingsKey: BOT_ACCESS_SETTINGS_KEY,
				settings: botAccessSettings,
				isToggleable: !isBotAccessUnavailable
			},
			{
				settingsKey: AGENT_DISCOVERY_SETTINGS_KEY,
				settings: agentDiscoverySettings,
				isToggleable: true
			},
			{
				settingsKey: BROWSER_TOOLS_SETTINGS_KEY,
				settings: browserToolsSettings,
				isToggleable: !isBrowserToolsUnavailable
			}
		]);
		var _useState2 = _slicedToArray((0, react.useState)((_modules$0$id = (_modules$ = modules[0]) === null || _modules$ === void 0 ? void 0 : _modules$.id) !== null && _modules$0$id !== void 0 ? _modules$0$id : null), 2);
		var expandedModuleId = _useState2[0];
		var setExpandedModuleId = _useState2[1];
		var enabledCount = [
			llmsSettings.isEnabled,
			markdownSettings.isEnabled,
			botAccessSettings.isEnabled,
			agentDiscoverySettings.isEnabled,
			browserToolsSettings.isEnabled && !isBrowserToolsUnavailable
		].filter(Boolean).length;
		var totalCount = modules.length - (isBrowserToolsUnavailable ? 1 : 0);
		var handleExpandedChange = function handleExpandedChange(moduleId) {
			return function(event, expanded) {
				setExpandedModuleId(expanded ? moduleId : null);
			};
		};
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Container.default, {
			disableGutters: true,
			maxWidth: "lg",
			sx: {
				width: "100%",
				minWidth: 0,
				pt: 6
			}
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 5,
			width: "100%",
			minWidth: 0
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			justifyContent: "space-between"
		}, /*#__PURE__*/ react.default.createElement(PageTitle, { textAlign: "left" }), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			alignItems: "flex-start",
			spacing: .5
		}, /*#__PURE__*/ react.default.createElement(ModulesStatus, {
			score: enabledCount,
			total: totalCount,
			infoIcon: scoreInfoIcon
		}), readinessScoreUrl && /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "primary",
			size: "small",
			href: readinessScoreUrl,
			endIcon: /*#__PURE__*/ react.default.createElement(_elementor_icons.ArrowRightIcon, { fontSize: "tiny" })
		}, (0, _wordpress_i18n.__)("See your score", "elementor")))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 2 }, allModules.saveError && /*#__PURE__*/ react.default.createElement(_elementor_ui_Alert.default, { severity: "error" }, allModules.saveError), llmsSettings.saveError && /*#__PURE__*/ react.default.createElement(_elementor_ui_Alert.default, { severity: "error" }, llmsSettings.saveError), markdownSettings.saveError && /*#__PURE__*/ react.default.createElement(_elementor_ui_Alert.default, { severity: "error" }, markdownSettings.saveError), botAccessSettings.saveError && /*#__PURE__*/ react.default.createElement(_elementor_ui_Alert.default, { severity: "error" }, botAccessSettings.saveError), agentDiscoverySettings.saveError && /*#__PURE__*/ react.default.createElement(_elementor_ui_Alert.default, { severity: "error" }, agentDiscoverySettings.saveError), browserToolsSettings.saveError && /*#__PURE__*/ react.default.createElement(_elementor_ui_Alert.default, { severity: "error" }, browserToolsSettings.saveError), modules.map(function renderModule(module) {
			var accordionProps = {
				isExpanded: expandedModuleId === module.id,
				onExpandedChange: handleExpandedChange(module.id)
			};
			if ("llms-txt" === module.id) return /*#__PURE__*/ react.default.createElement(ModuleAccordion, _extends$1({
				key: module.id,
				title: module.title,
				description: module.description,
				status: getStatus(llmsSettings.isEnabled, isLlmsUnavailable),
				isEnabled: llmsSettings.isEnabled,
				isToggleDisabled: isLlmsUnavailable || llmsSettings.isSaving || allModules.isSaving,
				disabledReason: getDisabledReason(llmsSettings),
				onToggle: llmsSettings.toggleEnabled
			}, accordionProps), /*#__PURE__*/ react.default.createElement(LlmsTxtPanel, { settings: llmsSettings }));
			if ("markdown-content" === module.id) return /*#__PURE__*/ react.default.createElement(ModuleAccordion, _extends$1({
				key: module.id,
				title: module.title,
				description: module.description,
				status: getStatus(markdownSettings.isEnabled),
				isEnabled: markdownSettings.isEnabled,
				isToggleDisabled: markdownSettings.isSaving || allModules.isSaving,
				onToggle: markdownSettings.toggleEnabled
			}, accordionProps), /*#__PURE__*/ react.default.createElement(MarkdownContentPanel, { settings: markdownSettings }));
			if ("bot-access-control" === module.id) return /*#__PURE__*/ react.default.createElement(ModuleAccordion, _extends$1({
				key: module.id,
				title: module.title,
				description: module.description,
				status: getStatus(botAccessSettings.isEnabled, isBotAccessUnavailable),
				isEnabled: botAccessSettings.isEnabled,
				isToggleDisabled: isBotAccessUnavailable || botAccessSettings.isSaving || allModules.isSaving,
				disabledReason: getDisabledReason(botAccessSettings),
				onToggle: botAccessSettings.toggleEnabled
			}, accordionProps), /*#__PURE__*/ react.default.createElement(BotAccessPanel, { settings: botAccessSettings }));
			if ("agent-discovery" === module.id) return /*#__PURE__*/ react.default.createElement(ModuleAccordion, _extends$1({
				key: module.id,
				title: module.title,
				description: module.description,
				status: getStatus(agentDiscoverySettings.isEnabled),
				isEnabled: agentDiscoverySettings.isEnabled,
				isToggleDisabled: agentDiscoverySettings.isSaving || allModules.isSaving,
				onToggle: agentDiscoverySettings.toggleEnabled
			}, accordionProps), /*#__PURE__*/ react.default.createElement(AgentDiscoveryPanel, { settings: agentDiscoverySettings }));
			if ("browser-tools" === module.id) return /*#__PURE__*/ react.default.createElement(ModuleAccordion, _extends$1({
				key: module.id,
				title: module.title,
				description: module.description,
				status: getStatus(browserToolsSettings.isEnabled, isBrowserToolsUnavailable),
				isEnabled: browserToolsSettings.isEnabled && !isBrowserToolsUnavailable,
				isToggleDisabled: isBrowserToolsUnavailable || browserToolsSettings.isSaving || allModules.isSaving,
				disabledReason: getDisabledReason(browserToolsSettings),
				onToggle: browserToolsSettings.toggleEnabled
			}, accordionProps), /*#__PURE__*/ react.default.createElement(BrowserToolsPanel, {
				settings: browserToolsSettings,
				isMarkdownEnabled: markdownSettings.isEnabled
			}));
			return null;
		})), /*#__PURE__*/ react.default.createElement(_elementor_ui_Divider.default, null), /*#__PURE__*/ react.default.createElement(AllModulesControl, {
			isAnyEnabled: allModules.isAnyEnabled,
			isDisabled: allModules.isDisabled,
			onToggleAll: allModules.toggleAll
		})));
	};
	ModulesScreen.propTypes = {
		readinessScoreUrl: import_prop_types.default.string,
		agentDiscoveryConfig: import_prop_types.default.shape({
			enabled: import_prop_types.default.bool.isRequired,
			files: import_prop_types.default.array.isRequired
		}).isRequired,
		botAccessConfig: import_prop_types.default.shape({
			enabled: import_prop_types.default.bool.isRequired,
			hasPhysicalFile: import_prop_types.default.bool.isRequired,
			unavailableReason: import_prop_types.default.shape({
				code: import_prop_types.default.string.isRequired,
				message: import_prop_types.default.string.isRequired
			}),
			bots: import_prop_types.default.array.isRequired,
			catalog: import_prop_types.default.array.isRequired
		}).isRequired,
		browserToolsConfig: import_prop_types.default.shape({
			enabled: import_prop_types.default.bool.isRequired,
			isAvailable: import_prop_types.default.bool.isRequired,
			unavailableReason: import_prop_types.default.shape({
				code: import_prop_types.default.string.isRequired,
				message: import_prop_types.default.string.isRequired
			}),
			tools: import_prop_types.default.array.isRequired,
			businessDetails: import_prop_types.default.string,
			maxBusinessDetailsLength: import_prop_types.default.number
		}).isRequired,
		llmsConfig: import_prop_types.default.shape({
			enabled: import_prop_types.default.bool.isRequired,
			isManuallyEdited: import_prop_types.default.bool.isRequired,
			hasPhysicalFile: import_prop_types.default.bool.isRequired,
			unavailableReason: import_prop_types.default.shape({
				code: import_prop_types.default.string.isRequired,
				message: import_prop_types.default.string.isRequired
			}),
			fileUrl: import_prop_types.default.string.isRequired,
			postTypes: import_prop_types.default.array.isRequired,
			excludedPostIds: import_prop_types.default.arrayOf(import_prop_types.default.number)
		}).isRequired,
		markdownConfig: import_prop_types.default.shape({
			enabled: import_prop_types.default.bool.isRequired,
			postTypes: import_prop_types.default.array.isRequired,
			excludedPostIds: import_prop_types.default.arrayOf(import_prop_types.default.number)
		}).isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/components/welcome-illustration.js
	var ILLUSTRATION_WIDTH = 205;
	var ILLUSTRATION_HEIGHT = 94;
	var WelcomeIllustration = function WelcomeIllustration() {
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_SvgIcon.default, {
			viewBox: "0 0 ".concat(ILLUSTRATION_WIDTH, " ").concat(ILLUSTRATION_HEIGHT),
			sx: {
				width: ILLUSTRATION_WIDTH,
				height: ILLUSTRATION_HEIGHT
			}
		}, /*#__PURE__*/ react.default.createElement("path", {
			d: "M133.046 0.5H172.091C173.479 0.5 174.809 1.05115 175.79 2.03223L191.412 17.6543C192.393 18.6354 192.945 19.9661 192.945 21.3535V80.2383L192.938 80.5078C192.797 83.2717 190.512 85.4697 187.713 85.4697H133.046C130.157 85.4697 127.815 83.1274 127.815 80.2383V5.73145C127.815 2.84223 130.157 0.5 133.046 0.5Z",
			fill: "#FFDFFB",
			stroke: "#C946C9"
		}), /*#__PURE__*/ react.default.createElement("path", {
			d: "M174.815 2.41406C174.815 1.96875 175.353 1.74571 175.668 2.06055L191.234 17.626C191.548 17.9409 191.325 18.4794 190.88 18.4795H180.046C177.157 18.4795 174.815 16.1373 174.815 13.248V2.41406Z",
			fill: "#FFDFFB",
			stroke: "#C946C9"
		}), /*#__PURE__*/ react.default.createElement("line", {
			x1: "140.815",
			y1: "30.5",
			x2: "155.009",
			y2: "30.5",
			stroke: "white",
			strokeWidth: "3",
			strokeLinecap: "round"
		}), /*#__PURE__*/ react.default.createElement("line", {
			x1: "140.815",
			y1: "39.0957",
			x2: "175.069",
			y2: "39.0957",
			stroke: "white",
			strokeWidth: "3",
			strokeLinecap: "round"
		}), /*#__PURE__*/ react.default.createElement("line", {
			x1: "140.815",
			y1: "47.6934",
			x2: "175.069",
			y2: "47.6934",
			stroke: "white",
			strokeWidth: "3",
			strokeLinecap: "round"
		}), /*#__PURE__*/ react.default.createElement("line", {
			x1: "140.815",
			y1: "56.291",
			x2: "149.277",
			y2: "56.291",
			stroke: "white",
			strokeWidth: "3",
			strokeLinecap: "round"
		}), /*#__PURE__*/ react.default.createElement("rect", {
			x: "163.5",
			y: "52.5",
			width: "41",
			height: "41",
			rx: "20.5",
			fill: "white",
			stroke: "#C945C9"
		}), /*#__PURE__*/ react.default.createElement("path", {
			d: "M174 69.6654C174 68.7813 174.351 67.9335 174.976 67.3083C175.601 66.6832 176.449 66.332 177.333 66.332H190.667C191.551 66.332 192.399 66.6832 193.024 67.3083C193.649 67.9335 194 68.7813 194 69.6654V78.832C194 79.7161 193.649 80.5639 193.024 81.1891C192.399 81.8142 191.551 82.1654 190.667 82.1654H177.333C176.449 82.1654 175.601 81.8142 174.976 81.1891C174.351 80.5639 174 79.7161 174 78.832V69.6654Z",
			stroke: "#C946C9",
			strokeWidth: "1.875",
			strokeLinecap: "round",
			strokeLinejoin: "round"
		}), /*#__PURE__*/ react.default.createElement("path", {
			d: "M184 63V66.3333",
			stroke: "#C946C9",
			strokeWidth: "1.875",
			strokeLinecap: "round",
			strokeLinejoin: "round"
		}), /*#__PURE__*/ react.default.createElement("path", {
			d: "M180.666 73V73.0158",
			stroke: "#C946C9",
			strokeWidth: "2.5",
			strokeLinecap: "round",
			strokeLinejoin: "round"
		}), /*#__PURE__*/ react.default.createElement("path", {
			d: "M187.334 73V73.0158",
			stroke: "#C946C9",
			strokeWidth: "2.5",
			strokeLinecap: "round",
			strokeLinejoin: "round"
		}), /*#__PURE__*/ react.default.createElement("g", { clipPath: "url(#clip0_94_8723)" }, /*#__PURE__*/ react.default.createElement("path", {
			d: "M101 52.998H101.5M104.5 52.998H106M109 52.998H115",
			stroke: "#696299",
			strokeWidth: "2",
			strokeLinecap: "round",
			strokeLinejoin: "round"
		}), /*#__PURE__*/ react.default.createElement("path", {
			d: "M111 56.998L115 52.998",
			stroke: "#696299",
			strokeWidth: "2",
			strokeLinecap: "round",
			strokeLinejoin: "round"
		}), /*#__PURE__*/ react.default.createElement("path", {
			d: "M111 48.998L115 52.998",
			stroke: "#696299",
			strokeWidth: "2",
			strokeLinecap: "round",
			strokeLinejoin: "round"
		})), /*#__PURE__*/ react.default.createElement("rect", {
			x: "0.5",
			y: "14.498",
			width: "88",
			height: "71",
			rx: "4.93396",
			fill: "white",
			stroke: "#696299"
		}), /*#__PURE__*/ react.default.createElement("circle", {
			cx: "7.7413",
			cy: "21.7901",
			r: "1.35849",
			fill: "#696299"
		}), /*#__PURE__*/ react.default.createElement("circle", {
			cx: "13.1754",
			cy: "21.7901",
			r: "1.35849",
			fill: "#696299"
		}), /*#__PURE__*/ react.default.createElement("circle", {
			cx: "18.6092",
			cy: "21.7901",
			r: "1.35849",
			fill: "#696299"
		}), /*#__PURE__*/ react.default.createElement("rect", {
			x: "12.0002",
			y: "37.0938",
			width: "30.1231",
			height: "20.3774",
			rx: "2.71698",
			fill: "#696299"
		}), /*#__PURE__*/ react.default.createElement("line", {
			x1: "49.6467",
			y1: "50.5352",
			x2: "58.6959",
			y2: "50.5352",
			stroke: "#696299",
			strokeWidth: "3",
			strokeLinecap: "round"
		}), /*#__PURE__*/ react.default.createElement("line", {
			x1: "49.6467",
			y1: "43.7422",
			x2: "73.7575",
			y2: "43.7422",
			stroke: "#696299",
			strokeWidth: "3",
			strokeLinecap: "round"
		}), /*#__PURE__*/ react.default.createElement("line", {
			x1: "0.000244141",
			y1: "28.5",
			x2: "89.0002",
			y2: "28.5",
			stroke: "#696299"
		}), /*#__PURE__*/ react.default.createElement("defs", null, /*#__PURE__*/ react.default.createElement("clipPath", { id: "clip0_94_8723" }, /*#__PURE__*/ react.default.createElement("rect", {
			width: "24",
			height: "24",
			fill: "white",
			transform: "translate(96.0002 40.998)"
		}))));
	};

//#endregion
//#region modules/agents/assets/js/admin/components/welcome-screen.js
	var WelcomeScreen = function WelcomeScreen() {
		var _useState2 = _slicedToArray((0, react.useState)(false), 2);
		var isActivating = _useState2[0];
		var setIsActivating = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)(false), 2);
		var hasError = _useState4[0];
		var setHasError = _useState4[1];
		(0, react.useEffect)(function() {
			trackWelcomeViewed();
		}, []);
		var handleActivate = /*#__PURE__*/ function() {
			var _ref = _asyncToGenerator$1(/*#__PURE__*/ import_regenerator.default.mark(function _callee() {
				return import_regenerator.default.wrap(function(_context) {
					while (1) switch (_context.prev = _context.next) {
						case 0:
							setHasError(false);
							setIsActivating(true);
							_context.prev = 1;
							_context.next = 2;
							return activateAgentsReady();
						case 2:
							trackActivationClicked();
							window.location.reload();
							_context.next = 4;
							break;
						case 3:
							_context.prev = 3;
							_context["catch"](1);
							setHasError(true);
							setIsActivating(false);
						case 4:
						case "end": return _context.stop();
					}
				}, _callee, null, [[1, 3]]);
			}));
			return function handleActivate() {
				return _ref.apply(this, arguments);
			};
		}();
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			alignItems: "center",
			spacing: 3,
			pt: 6
		}, /*#__PURE__*/ react.default.createElement(PageTitle, null), /*#__PURE__*/ react.default.createElement(WelcomeIllustration, null), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			alignItems: "center",
			spacing: .5,
			textAlign: "center"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, { variant: "h6" }, (0, _wordpress_i18n.__)("Optimize your site for AI agents", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary",
			maxWidth: 254
		}, (0, _wordpress_i18n.__)("Enable discovery, access controls, and Markdown tools in one click.", "elementor"))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "contained",
			color: "primary",
			size: "medium",
			loading: isActivating,
			onClick: handleActivate
		}, (0, _wordpress_i18n.__)("Activate", "elementor")), hasError && /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, { maxWidth: 360 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Alert.default, {
			severity: "error",
			variant: "standard",
			size: "small"
		}, (0, _wordpress_i18n.__)("Activation failed. Please try again.", "elementor"))));
	};

//#endregion
//#region modules/agents/assets/js/admin/app.js
	var App = function App(_ref) {
		var isRTL = _ref.isRTL;
		var isActivated = _ref.isActivated;
		var readinessScoreUrl = _ref.readinessScoreUrl;
		var agentDiscoveryConfig = _ref.agentDiscoveryConfig;
		var botAccessConfig = _ref.botAccessConfig;
		var browserToolsConfig = _ref.browserToolsConfig;
		var llmsConfig = _ref.llmsConfig;
		var markdownConfig = _ref.markdownConfig;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui.DirectionProvider, { rtl: isRTL }, /*#__PURE__*/ react.default.createElement(_elementor_ui.LocalizationProvider, null, /*#__PURE__*/ react.default.createElement(_elementor_ui.ThemeProvider, {
			colorScheme: "light",
			palette: "argon-beta"
		}, isActivated ? /*#__PURE__*/ react.default.createElement(ModulesScreen, {
			readinessScoreUrl,
			agentDiscoveryConfig,
			botAccessConfig,
			browserToolsConfig,
			llmsConfig,
			markdownConfig
		}) : /*#__PURE__*/ react.default.createElement(WelcomeScreen, null))));
	};
	App.propTypes = {
		isRTL: import_prop_types.default.bool,
		isActivated: import_prop_types.default.bool,
		readinessScoreUrl: import_prop_types.default.string,
		agentDiscoveryConfig: import_prop_types.default.object,
		botAccessConfig: import_prop_types.default.object,
		browserToolsConfig: import_prop_types.default.object,
		llmsConfig: import_prop_types.default.object,
		markdownConfig: import_prop_types.default.object
	};

//#endregion
//#region modules/agents/assets/js/admin/components/beta-chip.js
	var BetaChip = function BetaChip() {
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Chip.default, {
			size: "tiny",
			shape: "rounded",
			variant: "outlined",
			label: (0, _wordpress_i18n.__)("Beta", "elementor")
		});
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/scanner-button-sx.js
	var filledButtonSx = function filledButtonSx(background, hoverBackground) {
		return {
			bgcolor: background,
			color: SCANNER_COLORS.paper,
			boxShadow: "none",
			"&:hover": {
				bgcolor: hoverBackground,
				boxShadow: "none"
			}
		};
	};
	var scannerButtonSx = {
		accent: filledButtonSx(SCANNER_COLORS.accent, SCANNER_COLORS.accentHover),
		activate: filledButtonSx(SCANNER_COLORS.activate, SCANNER_COLORS.activateHover),
		plainText: {
			minWidth: 0,
			p: 0,
			fontSize: "14px",
			lineHeight: "20px",
			textTransform: "none",
			"&:hover": {
				bgcolor: "transparent",
				textDecoration: "underline"
			}
		}
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/activate-banner.js
	function ownKeys$13(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$13, "ownKeys");
	function _objectSpread$12(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$13(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$13(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$12, "_objectSpread");
	var SPINNER_SIZE = 14;
	var ActivateBanner = function ActivateBanner(_ref) {
		var isActivating = _ref.isActivating;
		var onActivate = _ref.onActivate;
		var agentAccessUrl = _ref.agentAccessUrl;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 1 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "flex-end",
			justifyContent: "space-between",
			spacing: 2.5,
			width: "100%",
			p: 2.5,
			bgcolor: SCANNER_COLORS.banner,
			border: 1,
			borderColor: SCANNER_COLORS.bannerBorder,
			borderRadius: SCANNER_LAYOUT.cardRadius,
			boxSizing: "border-box"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 1,
			flex: 1,
			minWidth: 0
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "subtitle1",
			color: "text.primary"
		}, (0, _wordpress_i18n.__)("Prepare your site for agents", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 0 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, (0, _wordpress_i18n.__)("Turn on the core agent readiness settings in one step, which should significantly improve your score. You can change each one anytime.", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "primary",
			size: "small",
			href: agentAccessUrl,
			endIcon: /*#__PURE__*/ react.default.createElement(_elementor_icons.ArrowUpRightIcon, { fontSize: "tiny" }),
			sx: _objectSpread$12(_objectSpread$12({}, scannerButtonSx.plainText), {}, {
				alignSelf: "flex-start",
				textDecoration: "underline"
			})
		}, (0, _wordpress_i18n.__)("Open agent readiness settings", "elementor")))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "contained",
			size: "medium",
			onClick: onActivate,
			disabled: isActivating,
			startIcon: isActivating ? /*#__PURE__*/ react.default.createElement(_elementor_ui_CircularProgress.default, {
				size: SPINNER_SIZE,
				color: "inherit"
			}) : null,
			sx: _objectSpread$12(_objectSpread$12({}, scannerButtonSx.activate), {}, { flexShrink: 0 })
		}, isActivating ? (0, _wordpress_i18n.__)("Turning on", "elementor") : (0, _wordpress_i18n.__)("Turn settings on", "elementor"))));
	};
	ActivateBanner.propTypes = {
		isActivating: import_prop_types.default.bool,
		onActivate: import_prop_types.default.func.isRequired,
		agentAccessUrl: import_prop_types.default.string.isRequired
	};

//#endregion
//#region node_modules/zod/v4/core/core.js
/** A special constant with type `never` */
	var NEVER = Object.freeze({ status: "aborted" });
	function $constructor(name, initializer, params) {
		var _params$Parent;
		function init(inst, def) {
			var _inst$_zod;
			var _a$traits;
			var _a;
			Object.defineProperty(inst, "_zod", {
				value: (_inst$_zod = inst._zod) !== null && _inst$_zod !== void 0 ? _inst$_zod : {},
				enumerable: false
			});
			(_a$traits = (_a = inst._zod).traits) !== null && _a$traits !== void 0 || (_a.traits = /* @__PURE__ */ new Set());
			inst._zod.traits.add(name);
			initializer(inst, def);
			for (const k in _.prototype) if (!(k in inst)) Object.defineProperty(inst, k, { value: _.prototype[k].bind(inst) });
			inst._zod.constr = _;
			inst._zod.def = def;
		}
		const Parent = (_params$Parent = params === null || params === void 0 ? void 0 : params.Parent) !== null && _params$Parent !== void 0 ? _params$Parent : Object;
		class Definition extends Parent {}
		Object.defineProperty(Definition, "name", { value: name });
		function _(def) {
			var _a$deferred;
			var _a;
			const inst = (params === null || params === void 0 ? void 0 : params.Parent) ? new Definition() : this;
			init(inst, def);
			(_a$deferred = (_a = inst._zod).deferred) !== null && _a$deferred !== void 0 || (_a.deferred = []);
			for (const fn of inst._zod.deferred) fn();
			return inst;
		}
		Object.defineProperty(_, "init", { value: init });
		Object.defineProperty(_, Symbol.hasInstance, { value: (inst) => {
			var _inst$_zod2;
			if ((params === null || params === void 0 ? void 0 : params.Parent) && inst instanceof params.Parent) return true;
			return inst === null || inst === void 0 || (_inst$_zod2 = inst._zod) === null || _inst$_zod2 === void 0 || (_inst$_zod2 = _inst$_zod2.traits) === null || _inst$_zod2 === void 0 ? void 0 : _inst$_zod2.has(name);
		} });
		Object.defineProperty(_, "name", { value: name });
		return _;
	}
	var $ZodAsyncError = class extends Error {
		constructor() {
			super(`Encountered Promise during synchronous parse. Use .parseAsync() instead.`);
		}
	};
	var globalConfig = {};
	function config(newConfig) {
		if (newConfig) Object.assign(globalConfig, newConfig);
		return globalConfig;
	}

//#endregion
//#region \0@oxc-project+runtime@0.140.0/helpers/esm/typeof.js
	function _typeof(o) {
		"@babel/helpers - typeof";
		return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(o) {
			return typeof o;
		} : function(o) {
			return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
		}, _typeof(o);
	}

//#endregion
//#region \0@oxc-project+runtime@0.140.0/helpers/esm/toPrimitive.js
	function toPrimitive(t, r) {
		if ("object" != _typeof(t) || !t) return t;
		var e = t[Symbol.toPrimitive];
		if (void 0 !== e) {
			var i = e.call(t, r || "default");
			if ("object" != _typeof(i)) return i;
			throw new TypeError("@@toPrimitive must return a primitive value.");
		}
		return ("string" === r ? String : Number)(t);
	}

//#endregion
//#region \0@oxc-project+runtime@0.140.0/helpers/esm/toPropertyKey.js
	function toPropertyKey(t) {
		var i = toPrimitive(t, "string");
		return "symbol" == _typeof(i) ? i : i + "";
	}

//#endregion
//#region \0@oxc-project+runtime@0.140.0/helpers/esm/defineProperty.js
	function _defineProperty(e, r, t) {
		return (r = toPropertyKey(r)) in e ? Object.defineProperty(e, r, {
			value: t,
			enumerable: !0,
			configurable: !0,
			writable: !0
		}) : e[r] = t, e;
	}

//#endregion
//#region \0@oxc-project+runtime@0.140.0/helpers/esm/objectSpread2.js
	function ownKeys$12(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$12, "ownKeys");
	function _objectSpread2(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$12(Object(t), !0).forEach(function(r) {
				_defineProperty(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$12(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}

//#endregion
//#region node_modules/zod/v4/core/util.js
	function getEnumValues(entries) {
		const numericValues = Object.values(entries).filter((v) => typeof v === "number");
		return Object.entries(entries).filter(([k, _]) => numericValues.indexOf(+k) === -1).map(([_, v]) => v);
	}
	function jsonStringifyReplacer(_, value) {
		if (typeof value === "bigint") return value.toString();
		return value;
	}
	function cached(getter) {
		return { get value() {
			{
				const value = getter();
				Object.defineProperty(this, "value", { value });
				return value;
			}
		} };
	}
	function nullish(input) {
		return input === null || input === void 0;
	}
	function cleanRegex(source) {
		const start = source.startsWith("^") ? 1 : 0;
		const end = source.endsWith("$") ? source.length - 1 : source.length;
		return source.slice(start, end);
	}
	function floatSafeRemainder(val, step) {
		const valDecCount = (val.toString().split(".")[1] || "").length;
		const stepDecCount = (step.toString().split(".")[1] || "").length;
		const decCount = valDecCount > stepDecCount ? valDecCount : stepDecCount;
		return Number.parseInt(val.toFixed(decCount).replace(".", "")) % Number.parseInt(step.toFixed(decCount).replace(".", "")) / Math.pow(10, decCount);
	}
	function defineLazy(object, key, getter) {
		Object.defineProperty(object, key, {
			get() {
				{
					const value = getter();
					object[key] = value;
					return value;
				}
			},
			set(v) {
				Object.defineProperty(object, key, { value: v });
			},
			configurable: true
		});
	}
	function assignProp(target, prop, value) {
		Object.defineProperty(target, prop, {
			value,
			writable: true,
			enumerable: true,
			configurable: true
		});
	}
	function esc(str) {
		return JSON.stringify(str);
	}
	var captureStackTrace = Error.captureStackTrace ? Error.captureStackTrace : (..._args) => {};
	function isObject(data) {
		return typeof data === "object" && data !== null && !Array.isArray(data);
	}
	var allowsEval = cached(() => {
		var _navigator;
		if (typeof navigator !== "undefined" && ((_navigator = navigator) === null || _navigator === void 0 || (_navigator = _navigator.userAgent) === null || _navigator === void 0 ? void 0 : _navigator.includes("Cloudflare"))) return false;
		try {
			new Function("");
			return true;
		} catch (_) {
			return false;
		}
	});
	function isPlainObject(o) {
		if (isObject(o) === false) return false;
		const ctor = o.constructor;
		if (ctor === void 0) return true;
		const prot = ctor.prototype;
		if (isObject(prot) === false) return false;
		if (Object.prototype.hasOwnProperty.call(prot, "isPrototypeOf") === false) return false;
		return true;
	}
	var propertyKeyTypes = /* @__PURE__ */ new Set([
		"string",
		"number",
		"symbol"
	]);
	function escapeRegex(str) {
		return str.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
	}
	function clone(inst, def, params) {
		const cl = new inst._zod.constr(def !== null && def !== void 0 ? def : inst._zod.def);
		if (!def || (params === null || params === void 0 ? void 0 : params.parent)) cl._zod.parent = inst;
		return cl;
	}
	function normalizeParams(_params) {
		const params = _params;
		if (!params) return {};
		if (typeof params === "string") return { error: () => params };
		if ((params === null || params === void 0 ? void 0 : params.message) !== void 0) {
			if ((params === null || params === void 0 ? void 0 : params.error) !== void 0) throw new Error("Cannot specify both `message` and `error` params");
			params.error = params.message;
		}
		delete params.message;
		if (typeof params.error === "string") return _objectSpread2(_objectSpread2({}, params), {}, { error: () => params.error });
		return params;
	}
	function optionalKeys(shape) {
		return Object.keys(shape).filter((k) => {
			return shape[k]._zod.optin === "optional" && shape[k]._zod.optout === "optional";
		});
	}
	var NUMBER_FORMAT_RANGES = {
		safeint: [Number.MIN_SAFE_INTEGER, Number.MAX_SAFE_INTEGER],
		int32: [-2147483648, 2147483647],
		uint32: [0, 4294967295],
		float32: [-34028234663852886e22, 34028234663852886e22],
		float64: [-Number.MAX_VALUE, Number.MAX_VALUE]
	};
	function pick(schema, mask) {
		const newShape = {};
		const currDef = schema._zod.def;
		for (const key in mask) {
			if (!(key in currDef.shape)) throw new Error(`Unrecognized key: "${key}"`);
			if (!mask[key]) continue;
			newShape[key] = currDef.shape[key];
		}
		return clone(schema, _objectSpread2(_objectSpread2({}, schema._zod.def), {}, {
			shape: newShape,
			checks: []
		}));
	}
	function omit(schema, mask) {
		const newShape = _objectSpread2({}, schema._zod.def.shape);
		const currDef = schema._zod.def;
		for (const key in mask) {
			if (!(key in currDef.shape)) throw new Error(`Unrecognized key: "${key}"`);
			if (!mask[key]) continue;
			delete newShape[key];
		}
		return clone(schema, _objectSpread2(_objectSpread2({}, schema._zod.def), {}, {
			shape: newShape,
			checks: []
		}));
	}
	function extend(schema, shape) {
		if (!isPlainObject(shape)) throw new Error("Invalid input to extend: expected a plain object");
		return clone(schema, _objectSpread2(_objectSpread2({}, schema._zod.def), {}, {
			get shape() {
				const _shape = _objectSpread2(_objectSpread2({}, schema._zod.def.shape), shape);
				assignProp(this, "shape", _shape);
				return _shape;
			},
			checks: []
		}));
	}
	function merge(a, b) {
		return clone(a, _objectSpread2(_objectSpread2({}, a._zod.def), {}, {
			get shape() {
				const _shape = _objectSpread2(_objectSpread2({}, a._zod.def.shape), b._zod.def.shape);
				assignProp(this, "shape", _shape);
				return _shape;
			},
			catchall: b._zod.def.catchall,
			checks: []
		}));
	}
	function partial(Class, schema, mask) {
		const oldShape = schema._zod.def.shape;
		const shape = _objectSpread2({}, oldShape);
		if (mask) for (const key in mask) {
			if (!(key in oldShape)) throw new Error(`Unrecognized key: "${key}"`);
			if (!mask[key]) continue;
			shape[key] = Class ? new Class({
				type: "optional",
				innerType: oldShape[key]
			}) : oldShape[key];
		}
		else for (const key in oldShape) shape[key] = Class ? new Class({
			type: "optional",
			innerType: oldShape[key]
		}) : oldShape[key];
		return clone(schema, _objectSpread2(_objectSpread2({}, schema._zod.def), {}, {
			shape,
			checks: []
		}));
	}
	function required(Class, schema, mask) {
		const oldShape = schema._zod.def.shape;
		const shape = _objectSpread2({}, oldShape);
		if (mask) for (const key in mask) {
			if (!(key in shape)) throw new Error(`Unrecognized key: "${key}"`);
			if (!mask[key]) continue;
			shape[key] = new Class({
				type: "nonoptional",
				innerType: oldShape[key]
			});
		}
		else for (const key in oldShape) shape[key] = new Class({
			type: "nonoptional",
			innerType: oldShape[key]
		});
		return clone(schema, _objectSpread2(_objectSpread2({}, schema._zod.def), {}, {
			shape,
			checks: []
		}));
	}
	function aborted(x, startIndex = 0) {
		for (let i = startIndex; i < x.issues.length; i++) {
			var _x$issues$i;
			if (((_x$issues$i = x.issues[i]) === null || _x$issues$i === void 0 ? void 0 : _x$issues$i.continue) !== true) return true;
		}
		return false;
	}
	function prefixIssues(path, issues) {
		return issues.map((iss) => {
			var _a$path;
			var _a;
			(_a$path = (_a = iss).path) !== null && _a$path !== void 0 || (_a.path = []);
			iss.path.unshift(path);
			return iss;
		});
	}
	function unwrapMessage(message) {
		return typeof message === "string" ? message : message === null || message === void 0 ? void 0 : message.message;
	}
	function finalizeIssue(iss, ctx, config) {
		var _iss$path;
		const full = _objectSpread2(_objectSpread2({}, iss), {}, { path: (_iss$path = iss.path) !== null && _iss$path !== void 0 ? _iss$path : [] });
		if (!iss.message) {
			var _ref;
			var _ref2;
			var _ref3;
			var _unwrapMessage;
			var _iss$inst;
			var _iss$inst$error;
			var _ctx$error;
			var _config$customError;
			var _config$localeError;
			full.message = (_ref = (_ref2 = (_ref3 = (_unwrapMessage = unwrapMessage((_iss$inst = iss.inst) === null || _iss$inst === void 0 || (_iss$inst = _iss$inst._zod.def) === null || _iss$inst === void 0 || (_iss$inst$error = _iss$inst.error) === null || _iss$inst$error === void 0 ? void 0 : _iss$inst$error.call(_iss$inst, iss))) !== null && _unwrapMessage !== void 0 ? _unwrapMessage : unwrapMessage(ctx === null || ctx === void 0 || (_ctx$error = ctx.error) === null || _ctx$error === void 0 ? void 0 : _ctx$error.call(ctx, iss))) !== null && _ref3 !== void 0 ? _ref3 : unwrapMessage((_config$customError = config.customError) === null || _config$customError === void 0 ? void 0 : _config$customError.call(config, iss))) !== null && _ref2 !== void 0 ? _ref2 : unwrapMessage((_config$localeError = config.localeError) === null || _config$localeError === void 0 ? void 0 : _config$localeError.call(config, iss))) !== null && _ref !== void 0 ? _ref : "Invalid input";
		}
		delete full.inst;
		delete full.continue;
		if (!(ctx === null || ctx === void 0 ? void 0 : ctx.reportInput)) delete full.input;
		return full;
	}
	function getLengthableOrigin(input) {
		if (Array.isArray(input)) return "array";
		if (typeof input === "string") return "string";
		return "unknown";
	}
	function issue(...args) {
		const [iss, input, inst] = args;
		if (typeof iss === "string") return {
			message: iss,
			code: "custom",
			input,
			inst
		};
		return _objectSpread2({}, iss);
	}

//#endregion
//#region node_modules/zod/v4/core/errors.js
	var initializer$1 = /* @__PURE__ */ __name((inst, def) => {
		inst.name = "$ZodError";
		Object.defineProperty(inst, "_zod", {
			value: inst._zod,
			enumerable: false
		});
		Object.defineProperty(inst, "issues", {
			value: def,
			enumerable: false
		});
		Object.defineProperty(inst, "message", {
			get() {
				return JSON.stringify(def, jsonStringifyReplacer, 2);
			},
			enumerable: true
		});
		Object.defineProperty(inst, "toString", {
			value: () => inst.message,
			enumerable: false
		});
	}, "initializer");
	var $ZodError = $constructor("$ZodError", initializer$1);
	var $ZodRealError = $constructor("$ZodError", initializer$1, { Parent: Error });
	function flattenError(error, mapper = (issue) => issue.message) {
		const fieldErrors = {};
		const formErrors = [];
		for (const sub of error.issues) if (sub.path.length > 0) {
			fieldErrors[sub.path[0]] = fieldErrors[sub.path[0]] || [];
			fieldErrors[sub.path[0]].push(mapper(sub));
		} else formErrors.push(mapper(sub));
		return {
			formErrors,
			fieldErrors
		};
	}
	function formatError(error, _mapper) {
		const mapper = _mapper || function(issue) {
			return issue.message;
		};
		const fieldErrors = { _errors: [] };
		const processError = (error) => {
			for (const issue of error.issues) if (issue.code === "invalid_union" && issue.errors.length) issue.errors.map((issues) => processError({ issues }));
			else if (issue.code === "invalid_key") processError({ issues: issue.issues });
			else if (issue.code === "invalid_element") processError({ issues: issue.issues });
			else if (issue.path.length === 0) fieldErrors._errors.push(mapper(issue));
			else {
				let curr = fieldErrors;
				let i = 0;
				while (i < issue.path.length) {
					const el = issue.path[i];
					if (!(i === issue.path.length - 1)) curr[el] = curr[el] || { _errors: [] };
					else {
						curr[el] = curr[el] || { _errors: [] };
						curr[el]._errors.push(mapper(issue));
					}
					curr = curr[el];
					i++;
				}
			}
		};
		processError(error);
		return fieldErrors;
	}

//#endregion
//#region \0@oxc-project+runtime@0.140.0/helpers/esm/asyncToGenerator.js
	function asyncGeneratorStep(n, t, e, r, o, a, c) {
		try {
			var i = n[a](c);
			var u = i.value;
		} catch (n) {
			e(n);
			return;
		}
		i.done ? t(u) : Promise.resolve(u).then(r, o);
	}
	function _asyncToGenerator(n) {
		return function() {
			var t = this;
			var e = arguments;
			return new Promise(function(r, o) {
				var a = n.apply(t, e);
				function _next(n) {
					asyncGeneratorStep(a, r, o, _next, _throw, "next", n);
				}
				function _throw(n) {
					asyncGeneratorStep(a, r, o, _next, _throw, "throw", n);
				}
				_next(void 0);
			});
		};
	}

//#endregion
//#region node_modules/zod/v4/core/parse.js
	var _parse = (_Err) => (schema, value, _ctx, _params) => {
		const ctx = _ctx ? Object.assign(_ctx, { async: false }) : { async: false };
		const result = schema._zod.run({
			value,
			issues: []
		}, ctx);
		if (result instanceof Promise) throw new $ZodAsyncError();
		if (result.issues.length) {
			var _params$Err;
			const e = new ((_params$Err = _params === null || _params === void 0 ? void 0 : _params.Err) !== null && _params$Err !== void 0 ? _params$Err : _Err)(result.issues.map((iss) => finalizeIssue(iss, ctx, config())));
			captureStackTrace(e, _params === null || _params === void 0 ? void 0 : _params.callee);
			throw e;
		}
		return result.value;
	};
	var _parseAsync = (_Err) => function() {
		var _ref = _asyncToGenerator(function* (schema, value, _ctx, params) {
			const ctx = _ctx ? Object.assign(_ctx, { async: true }) : { async: true };
			let result = schema._zod.run({
				value,
				issues: []
			}, ctx);
			if (result instanceof Promise) result = yield result;
			if (result.issues.length) {
				var _params$Err2;
				const e = new ((_params$Err2 = params === null || params === void 0 ? void 0 : params.Err) !== null && _params$Err2 !== void 0 ? _params$Err2 : _Err)(result.issues.map((iss) => finalizeIssue(iss, ctx, config())));
				captureStackTrace(e, params === null || params === void 0 ? void 0 : params.callee);
				throw e;
			}
			return result.value;
		});
		return function(_x, _x2, _x3, _x4) {
			return _ref.apply(this, arguments);
		};
	}();
	var _safeParse = (_Err) => (schema, value, _ctx) => {
		const ctx = _ctx ? _objectSpread2(_objectSpread2({}, _ctx), {}, { async: false }) : { async: false };
		const result = schema._zod.run({
			value,
			issues: []
		}, ctx);
		if (result instanceof Promise) throw new $ZodAsyncError();
		return result.issues.length ? {
			success: false,
			error: new (_Err !== null && _Err !== void 0 ? _Err : $ZodError)(result.issues.map((iss) => finalizeIssue(iss, ctx, config())))
		} : {
			success: true,
			data: result.value
		};
	};
	var safeParse$1 = /* @__PURE__*/ _safeParse($ZodRealError);
	var _safeParseAsync = (_Err) => function() {
		var _ref2 = _asyncToGenerator(function* (schema, value, _ctx) {
			const ctx = _ctx ? Object.assign(_ctx, { async: true }) : { async: true };
			let result = schema._zod.run({
				value,
				issues: []
			}, ctx);
			if (result instanceof Promise) result = yield result;
			return result.issues.length ? {
				success: false,
				error: new _Err(result.issues.map((iss) => finalizeIssue(iss, ctx, config())))
			} : {
				success: true,
				data: result.value
			};
		});
		return function(_x5, _x6, _x7) {
			return _ref2.apply(this, arguments);
		};
	}();
	var safeParseAsync$1 = /* @__PURE__*/ _safeParseAsync($ZodRealError);

//#endregion
//#region node_modules/zod/v4/core/regexes.js
	var cuid = /^[cC][^\s-]{8,}$/;
	var cuid2 = /^[0-9a-z]+$/;
	var ulid = /^[0-9A-HJKMNP-TV-Za-hjkmnp-tv-z]{26}$/;
	var xid = /^[0-9a-vA-V]{20}$/;
	var ksuid = /^[A-Za-z0-9]{27}$/;
	var nanoid = /^[a-zA-Z0-9_-]{21}$/;
	/** ISO 8601-1 duration regex. Does not support the 8601-2 extensions like negative durations or fractional/negative components. */
	var duration$1 = /^P(?:(\d+W)|(?!.*W)(?=\d|T\d)(\d+Y)?(\d+M)?(\d+D)?(T(?=\d)(\d+H)?(\d+M)?(\d+([.,]\d+)?S)?)?)$/;
	/** A regex for any UUID-like identifier: 8-4-4-4-12 hex pattern */
	var guid = /^([0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12})$/;
	/** Returns a regex for validating an RFC 4122 UUID.
	*
	* @param version Optionally specify a version 1-8. If no version is specified, all versions are supported. */
	var uuid = (version) => {
		if (!version) return /^([0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-8][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}|00000000-0000-0000-0000-000000000000)$/;
		return new RegExp(`^([0-9a-fA-F]{8}-[0-9a-fA-F]{4}-${version}[0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12})$`);
	};
	/** Practical email validation */
	var email = /^(?!\.)(?!.*\.\.)([A-Za-z0-9_'+\-\.]*)[A-Za-z0-9_+-]@([A-Za-z0-9][A-Za-z0-9\-]*\.)+[A-Za-z]{2,}$/;
	var _emoji$1 = `^(\\p{Extended_Pictographic}|\\p{Emoji_Component})+$`;
	function emoji() {
		return new RegExp(_emoji$1, "u");
	}
	var ipv4 = /^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])$/;
	var ipv6 = /^(([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}|::|([0-9a-fA-F]{1,4})?::([0-9a-fA-F]{1,4}:?){0,6})$/;
	var cidrv4 = /^((25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\/([0-9]|[1-2][0-9]|3[0-2])$/;
	var cidrv6 = /^(([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}|::|([0-9a-fA-F]{1,4})?::([0-9a-fA-F]{1,4}:?){0,6})\/(12[0-8]|1[01][0-9]|[1-9]?[0-9])$/;
	var base64 = /^$|^(?:[0-9a-zA-Z+/]{4})*(?:(?:[0-9a-zA-Z+/]{2}==)|(?:[0-9a-zA-Z+/]{3}=))?$/;
	var base64url = /^[A-Za-z0-9_-]*$/;
	var hostname = /^([a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]+$/;
	var e164 = /^\+(?:[0-9]){6,14}[0-9]$/;
	var dateSource = `(?:(?:\\d\\d[2468][048]|\\d\\d[13579][26]|\\d\\d0[48]|[02468][048]00|[13579][26]00)-02-29|\\d{4}-(?:(?:0[13578]|1[02])-(?:0[1-9]|[12]\\d|3[01])|(?:0[469]|11)-(?:0[1-9]|[12]\\d|30)|(?:02)-(?:0[1-9]|1\\d|2[0-8])))`;
	var date$1 = /*@__PURE__*/ new RegExp(`^${dateSource}$`);
	function timeSource(args) {
		const hhmm = `(?:[01]\\d|2[0-3]):[0-5]\\d`;
		return typeof args.precision === "number" ? args.precision === -1 ? `${hhmm}` : args.precision === 0 ? `${hhmm}:[0-5]\\d` : `${hhmm}:[0-5]\\d\\.\\d{${args.precision}}` : `${hhmm}(?::[0-5]\\d(?:\\.\\d+)?)?`;
	}
	function time$1(args) {
		return new RegExp(`^${timeSource(args)}$`);
	}
	__name(time$1, "time");
	function datetime$1(args) {
		const time = timeSource({ precision: args.precision });
		const opts = ["Z"];
		if (args.local) opts.push("");
		if (args.offset) opts.push(`([+-]\\d{2}:\\d{2})`);
		const timeRegex = `${time}(?:${opts.join("|")})`;
		return new RegExp(`^${dateSource}T(?:${timeRegex})$`);
	}
	__name(datetime$1, "datetime");
	var string$1 = /* @__PURE__ */ __name((params) => {
		var _params$minimum;
		var _params$maximum;
		const regex = params ? `[\\s\\S]{${(_params$minimum = params === null || params === void 0 ? void 0 : params.minimum) !== null && _params$minimum !== void 0 ? _params$minimum : 0},${(_params$maximum = params === null || params === void 0 ? void 0 : params.maximum) !== null && _params$maximum !== void 0 ? _params$maximum : ""}}` : `[\\s\\S]*`;
		return new RegExp(`^${regex}$`);
	}, "string");
	var integer = /^\d+$/;
	var number$1 = /^-?\d+(?:\.\d+)?/i;
	var boolean$1 = /true|false/i;
	var _null$2 = /null/i;
	var lowercase = /^[^A-Z]*$/;
	var uppercase = /^[^a-z]*$/;

//#endregion
//#region node_modules/zod/v4/core/checks.js
	var $ZodCheck = /*@__PURE__*/ $constructor("$ZodCheck", (inst, def) => {
		var _inst$_zod;
		var _a$onattach;
		var _a;
		(_inst$_zod = inst._zod) !== null && _inst$_zod !== void 0 || (inst._zod = {});
		inst._zod.def = def;
		(_a$onattach = (_a = inst._zod).onattach) !== null && _a$onattach !== void 0 || (_a.onattach = []);
	});
	var numericOriginMap = {
		number: "number",
		bigint: "bigint",
		object: "date"
	};
	var $ZodCheckLessThan = /*@__PURE__*/ $constructor("$ZodCheckLessThan", (inst, def) => {
		$ZodCheck.init(inst, def);
		const origin = numericOriginMap[typeof def.value];
		inst._zod.onattach.push((inst) => {
			var _ref;
			const bag = inst._zod.bag;
			const curr = (_ref = def.inclusive ? bag.maximum : bag.exclusiveMaximum) !== null && _ref !== void 0 ? _ref : Number.POSITIVE_INFINITY;
			if (def.value < curr) if (def.inclusive) bag.maximum = def.value;
			else bag.exclusiveMaximum = def.value;
		});
		inst._zod.check = (payload) => {
			if (def.inclusive ? payload.value <= def.value : payload.value < def.value) return;
			payload.issues.push({
				origin,
				code: "too_big",
				maximum: def.value,
				input: payload.value,
				inclusive: def.inclusive,
				inst,
				continue: !def.abort
			});
		};
	});
	var $ZodCheckGreaterThan = /*@__PURE__*/ $constructor("$ZodCheckGreaterThan", (inst, def) => {
		$ZodCheck.init(inst, def);
		const origin = numericOriginMap[typeof def.value];
		inst._zod.onattach.push((inst) => {
			var _ref2;
			const bag = inst._zod.bag;
			const curr = (_ref2 = def.inclusive ? bag.minimum : bag.exclusiveMinimum) !== null && _ref2 !== void 0 ? _ref2 : Number.NEGATIVE_INFINITY;
			if (def.value > curr) if (def.inclusive) bag.minimum = def.value;
			else bag.exclusiveMinimum = def.value;
		});
		inst._zod.check = (payload) => {
			if (def.inclusive ? payload.value >= def.value : payload.value > def.value) return;
			payload.issues.push({
				origin,
				code: "too_small",
				minimum: def.value,
				input: payload.value,
				inclusive: def.inclusive,
				inst,
				continue: !def.abort
			});
		};
	});
	var $ZodCheckMultipleOf = /*@__PURE__*/ $constructor("$ZodCheckMultipleOf", (inst, def) => {
		$ZodCheck.init(inst, def);
		inst._zod.onattach.push((inst) => {
			var _a$multipleOf;
			var _a;
			(_a$multipleOf = (_a = inst._zod.bag).multipleOf) !== null && _a$multipleOf !== void 0 || (_a.multipleOf = def.value);
		});
		inst._zod.check = (payload) => {
			if (typeof payload.value !== typeof def.value) throw new Error("Cannot mix number and bigint in multiple_of check.");
			if (typeof payload.value === "bigint" ? payload.value % def.value === BigInt(0) : floatSafeRemainder(payload.value, def.value) === 0) return;
			payload.issues.push({
				origin: typeof payload.value,
				code: "not_multiple_of",
				divisor: def.value,
				input: payload.value,
				inst,
				continue: !def.abort
			});
		};
	});
	var $ZodCheckNumberFormat = /*@__PURE__*/ $constructor("$ZodCheckNumberFormat", (inst, def) => {
		var _def$format;
		$ZodCheck.init(inst, def);
		def.format = def.format || "float64";
		const isInt = (_def$format = def.format) === null || _def$format === void 0 ? void 0 : _def$format.includes("int");
		const origin = isInt ? "int" : "number";
		const [minimum, maximum] = NUMBER_FORMAT_RANGES[def.format];
		inst._zod.onattach.push((inst) => {
			const bag = inst._zod.bag;
			bag.format = def.format;
			bag.minimum = minimum;
			bag.maximum = maximum;
			if (isInt) bag.pattern = integer;
		});
		inst._zod.check = (payload) => {
			const input = payload.value;
			if (isInt) {
				if (!Number.isInteger(input)) {
					payload.issues.push({
						expected: origin,
						format: def.format,
						code: "invalid_type",
						input,
						inst
					});
					return;
				}
				if (!Number.isSafeInteger(input)) {
					if (input > 0) payload.issues.push({
						input,
						code: "too_big",
						maximum: Number.MAX_SAFE_INTEGER,
						note: "Integers must be within the safe integer range.",
						inst,
						origin,
						continue: !def.abort
					});
					else payload.issues.push({
						input,
						code: "too_small",
						minimum: Number.MIN_SAFE_INTEGER,
						note: "Integers must be within the safe integer range.",
						inst,
						origin,
						continue: !def.abort
					});
					return;
				}
			}
			if (input < minimum) payload.issues.push({
				origin: "number",
				input,
				code: "too_small",
				minimum,
				inclusive: true,
				inst,
				continue: !def.abort
			});
			if (input > maximum) payload.issues.push({
				origin: "number",
				input,
				code: "too_big",
				maximum,
				inst
			});
		};
	});
	var $ZodCheckMaxLength = /*@__PURE__*/ $constructor("$ZodCheckMaxLength", (inst, def) => {
		var _a$when4;
		var _a;
		$ZodCheck.init(inst, def);
		(_a$when4 = (_a = inst._zod.def).when) !== null && _a$when4 !== void 0 || (_a.when = (payload) => {
			const val = payload.value;
			return !nullish(val) && val.length !== void 0;
		});
		inst._zod.onattach.push((inst) => {
			var _inst$_zod$bag$maximu2;
			const curr = (_inst$_zod$bag$maximu2 = inst._zod.bag.maximum) !== null && _inst$_zod$bag$maximu2 !== void 0 ? _inst$_zod$bag$maximu2 : Number.POSITIVE_INFINITY;
			if (def.maximum < curr) inst._zod.bag.maximum = def.maximum;
		});
		inst._zod.check = (payload) => {
			const input = payload.value;
			if (input.length <= def.maximum) return;
			const origin = getLengthableOrigin(input);
			payload.issues.push({
				origin,
				code: "too_big",
				maximum: def.maximum,
				inclusive: true,
				input,
				inst,
				continue: !def.abort
			});
		};
	});
	var $ZodCheckMinLength = /*@__PURE__*/ $constructor("$ZodCheckMinLength", (inst, def) => {
		var _a$when5;
		var _a;
		$ZodCheck.init(inst, def);
		(_a$when5 = (_a = inst._zod.def).when) !== null && _a$when5 !== void 0 || (_a.when = (payload) => {
			const val = payload.value;
			return !nullish(val) && val.length !== void 0;
		});
		inst._zod.onattach.push((inst) => {
			var _inst$_zod$bag$minimu2;
			const curr = (_inst$_zod$bag$minimu2 = inst._zod.bag.minimum) !== null && _inst$_zod$bag$minimu2 !== void 0 ? _inst$_zod$bag$minimu2 : Number.NEGATIVE_INFINITY;
			if (def.minimum > curr) inst._zod.bag.minimum = def.minimum;
		});
		inst._zod.check = (payload) => {
			const input = payload.value;
			if (input.length >= def.minimum) return;
			const origin = getLengthableOrigin(input);
			payload.issues.push({
				origin,
				code: "too_small",
				minimum: def.minimum,
				inclusive: true,
				input,
				inst,
				continue: !def.abort
			});
		};
	});
	var $ZodCheckLengthEquals = /*@__PURE__*/ $constructor("$ZodCheckLengthEquals", (inst, def) => {
		var _a$when6;
		var _a;
		$ZodCheck.init(inst, def);
		(_a$when6 = (_a = inst._zod.def).when) !== null && _a$when6 !== void 0 || (_a.when = (payload) => {
			const val = payload.value;
			return !nullish(val) && val.length !== void 0;
		});
		inst._zod.onattach.push((inst) => {
			const bag = inst._zod.bag;
			bag.minimum = def.length;
			bag.maximum = def.length;
			bag.length = def.length;
		});
		inst._zod.check = (payload) => {
			const input = payload.value;
			const length = input.length;
			if (length === def.length) return;
			const origin = getLengthableOrigin(input);
			const tooBig = length > def.length;
			payload.issues.push(_objectSpread2(_objectSpread2({ origin }, tooBig ? {
				code: "too_big",
				maximum: def.length
			} : {
				code: "too_small",
				minimum: def.length
			}), {}, {
				inclusive: true,
				exact: true,
				input: payload.value,
				inst,
				continue: !def.abort
			}));
		};
	});
	var $ZodCheckStringFormat = /*@__PURE__*/ $constructor("$ZodCheckStringFormat", (inst, def) => {
		var _a$check;
		var _b$check;
		var _a;
		var _b;
		$ZodCheck.init(inst, def);
		inst._zod.onattach.push((inst) => {
			const bag = inst._zod.bag;
			bag.format = def.format;
			if (def.pattern) {
				var _bag$patterns;
				(_bag$patterns = bag.patterns) !== null && _bag$patterns !== void 0 || (bag.patterns = /* @__PURE__ */ new Set());
				bag.patterns.add(def.pattern);
			}
		});
		if (def.pattern) (_a$check = (_a = inst._zod).check) !== null && _a$check !== void 0 || (_a.check = (payload) => {
			def.pattern.lastIndex = 0;
			if (def.pattern.test(payload.value)) return;
			payload.issues.push(_objectSpread2(_objectSpread2({
				origin: "string",
				code: "invalid_format",
				format: def.format,
				input: payload.value
			}, def.pattern ? { pattern: def.pattern.toString() } : {}), {}, {
				inst,
				continue: !def.abort
			}));
		});
		else (_b$check = (_b = inst._zod).check) !== null && _b$check !== void 0 || (_b.check = () => {});
	});
	var $ZodCheckRegex = /*@__PURE__*/ $constructor("$ZodCheckRegex", (inst, def) => {
		$ZodCheckStringFormat.init(inst, def);
		inst._zod.check = (payload) => {
			def.pattern.lastIndex = 0;
			if (def.pattern.test(payload.value)) return;
			payload.issues.push({
				origin: "string",
				code: "invalid_format",
				format: "regex",
				input: payload.value,
				pattern: def.pattern.toString(),
				inst,
				continue: !def.abort
			});
		};
	});
	var $ZodCheckLowerCase = /*@__PURE__*/ $constructor("$ZodCheckLowerCase", (inst, def) => {
		var _def$pattern;
		(_def$pattern = def.pattern) !== null && _def$pattern !== void 0 || (def.pattern = lowercase);
		$ZodCheckStringFormat.init(inst, def);
	});
	var $ZodCheckUpperCase = /*@__PURE__*/ $constructor("$ZodCheckUpperCase", (inst, def) => {
		var _def$pattern2;
		(_def$pattern2 = def.pattern) !== null && _def$pattern2 !== void 0 || (def.pattern = uppercase);
		$ZodCheckStringFormat.init(inst, def);
	});
	var $ZodCheckIncludes = /*@__PURE__*/ $constructor("$ZodCheckIncludes", (inst, def) => {
		$ZodCheck.init(inst, def);
		const escapedRegex = escapeRegex(def.includes);
		const pattern = new RegExp(typeof def.position === "number" ? `^.{${def.position}}${escapedRegex}` : escapedRegex);
		def.pattern = pattern;
		inst._zod.onattach.push((inst) => {
			var _bag$patterns2;
			const bag = inst._zod.bag;
			(_bag$patterns2 = bag.patterns) !== null && _bag$patterns2 !== void 0 || (bag.patterns = /* @__PURE__ */ new Set());
			bag.patterns.add(pattern);
		});
		inst._zod.check = (payload) => {
			if (payload.value.includes(def.includes, def.position)) return;
			payload.issues.push({
				origin: "string",
				code: "invalid_format",
				format: "includes",
				includes: def.includes,
				input: payload.value,
				inst,
				continue: !def.abort
			});
		};
	});
	var $ZodCheckStartsWith = /*@__PURE__*/ $constructor("$ZodCheckStartsWith", (inst, def) => {
		var _def$pattern3;
		$ZodCheck.init(inst, def);
		const pattern = new RegExp(`^${escapeRegex(def.prefix)}.*`);
		(_def$pattern3 = def.pattern) !== null && _def$pattern3 !== void 0 || (def.pattern = pattern);
		inst._zod.onattach.push((inst) => {
			var _bag$patterns3;
			const bag = inst._zod.bag;
			(_bag$patterns3 = bag.patterns) !== null && _bag$patterns3 !== void 0 || (bag.patterns = /* @__PURE__ */ new Set());
			bag.patterns.add(pattern);
		});
		inst._zod.check = (payload) => {
			if (payload.value.startsWith(def.prefix)) return;
			payload.issues.push({
				origin: "string",
				code: "invalid_format",
				format: "starts_with",
				prefix: def.prefix,
				input: payload.value,
				inst,
				continue: !def.abort
			});
		};
	});
	var $ZodCheckEndsWith = /*@__PURE__*/ $constructor("$ZodCheckEndsWith", (inst, def) => {
		var _def$pattern4;
		$ZodCheck.init(inst, def);
		const pattern = new RegExp(`.*${escapeRegex(def.suffix)}$`);
		(_def$pattern4 = def.pattern) !== null && _def$pattern4 !== void 0 || (def.pattern = pattern);
		inst._zod.onattach.push((inst) => {
			var _bag$patterns4;
			const bag = inst._zod.bag;
			(_bag$patterns4 = bag.patterns) !== null && _bag$patterns4 !== void 0 || (bag.patterns = /* @__PURE__ */ new Set());
			bag.patterns.add(pattern);
		});
		inst._zod.check = (payload) => {
			if (payload.value.endsWith(def.suffix)) return;
			payload.issues.push({
				origin: "string",
				code: "invalid_format",
				format: "ends_with",
				suffix: def.suffix,
				input: payload.value,
				inst,
				continue: !def.abort
			});
		};
	});
	var $ZodCheckOverwrite = /*@__PURE__*/ $constructor("$ZodCheckOverwrite", (inst, def) => {
		$ZodCheck.init(inst, def);
		inst._zod.check = (payload) => {
			payload.value = def.tx(payload.value);
		};
	});

//#endregion
//#region node_modules/zod/v4/core/doc.js
	var Doc = class {
		constructor(args = []) {
			this.content = [];
			this.indent = 0;
			if (this) this.args = args;
		}
		indented(fn) {
			this.indent += 1;
			fn(this);
			this.indent -= 1;
		}
		write(arg) {
			if (typeof arg === "function") {
				arg(this, { execution: "sync" });
				arg(this, { execution: "async" });
				return;
			}
			const lines = arg.split("\n").filter((x) => x);
			const minIndent = Math.min(...lines.map((x) => x.length - x.trimStart().length));
			const dedented = lines.map((x) => x.slice(minIndent)).map((x) => " ".repeat(this.indent * 2) + x);
			for (const line of dedented) this.content.push(line);
		}
		compile() {
			var _this;
			var _this$content;
			var _this2;
			const F = Function;
			const args = (_this = this) === null || _this === void 0 ? void 0 : _this.args;
			const lines = [...((_this$content = (_this2 = this) === null || _this2 === void 0 ? void 0 : _this2.content) !== null && _this$content !== void 0 ? _this$content : [``]).map((x) => `  ${x}`)];
			return new F(...args, lines.join("\n"));
		}
	};

//#endregion
//#region node_modules/zod/v4/core/versions.js
	var version = {
		major: 4,
		minor: 0,
		patch: 0
	};

//#endregion
//#region node_modules/zod/v4/core/schemas.js
	var $ZodType = /*@__PURE__*/ $constructor("$ZodType", (inst, def) => {
		var _inst;
		var _inst$_zod$def$checks;
		var _a;
		(_inst = inst) !== null && _inst !== void 0 || (inst = {});
		inst._zod.def = def;
		inst._zod.bag = inst._zod.bag || {};
		inst._zod.version = version;
		const checks = [...(_inst$_zod$def$checks = inst._zod.def.checks) !== null && _inst$_zod$def$checks !== void 0 ? _inst$_zod$def$checks : []];
		if (inst._zod.traits.has("$ZodCheck")) checks.unshift(inst);
		for (const ch of checks) for (const fn of ch._zod.onattach) fn(inst);
		if (checks.length === 0) {
			var _a$deferred;
			var _inst$_zod$deferred;
			(_a$deferred = (_a = inst._zod).deferred) !== null && _a$deferred !== void 0 || (_a.deferred = []);
			(_inst$_zod$deferred = inst._zod.deferred) === null || _inst$_zod$deferred === void 0 || _inst$_zod$deferred.push(() => {
				inst._zod.run = inst._zod.parse;
			});
		} else {
			const runChecks = (payload, checks, ctx) => {
				let isAborted = aborted(payload);
				let asyncResult;
				for (const ch of checks) {
					if (ch._zod.def.when) {
						if (!ch._zod.def.when(payload)) continue;
					} else if (isAborted) continue;
					const currLen = payload.issues.length;
					const _ = ch._zod.check(payload);
					if (_ instanceof Promise && (ctx === null || ctx === void 0 ? void 0 : ctx.async) === false) throw new $ZodAsyncError();
					if (asyncResult || _ instanceof Promise) {
						var _asyncResult;
						asyncResult = ((_asyncResult = asyncResult) !== null && _asyncResult !== void 0 ? _asyncResult : Promise.resolve()).then(_asyncToGenerator(function* () {
							yield _;
							if (payload.issues.length === currLen) return;
							if (!isAborted) isAborted = aborted(payload, currLen);
						}));
					} else {
						if (payload.issues.length === currLen) continue;
						if (!isAborted) isAborted = aborted(payload, currLen);
					}
				}
				if (asyncResult) return asyncResult.then(() => {
					return payload;
				});
				return payload;
			};
			inst._zod.run = (payload, ctx) => {
				const result = inst._zod.parse(payload, ctx);
				if (result instanceof Promise) {
					if (ctx.async === false) throw new $ZodAsyncError();
					return result.then((result) => runChecks(result, checks, ctx));
				}
				return runChecks(result, checks, ctx);
			};
		}
		inst["~standard"] = {
			validate: (value) => {
				try {
					var _r$error;
					const r = safeParse$1(inst, value);
					return r.success ? { value: r.data } : { issues: (_r$error = r.error) === null || _r$error === void 0 ? void 0 : _r$error.issues };
				} catch (_) {
					return safeParseAsync$1(inst, value).then((r) => {
						var _r$error2;
						return r.success ? { value: r.data } : { issues: (_r$error2 = r.error) === null || _r$error2 === void 0 ? void 0 : _r$error2.issues };
					});
				}
			},
			vendor: "zod",
			version: 1
		};
	});
	var $ZodString = /*@__PURE__*/ $constructor("$ZodString", (inst, def) => {
		var _pop;
		var _inst$_zod$bag$patter;
		var _inst$_zod$bag;
		$ZodType.init(inst, def);
		inst._zod.pattern = (_pop = [...(_inst$_zod$bag$patter = inst === null || inst === void 0 || (_inst$_zod$bag = inst._zod.bag) === null || _inst$_zod$bag === void 0 ? void 0 : _inst$_zod$bag.patterns) !== null && _inst$_zod$bag$patter !== void 0 ? _inst$_zod$bag$patter : []].pop()) !== null && _pop !== void 0 ? _pop : string$1(inst._zod.bag);
		inst._zod.parse = (payload, _) => {
			if (def.coerce) try {
				payload.value = String(payload.value);
			} catch (_) {}
			if (typeof payload.value === "string") return payload;
			payload.issues.push({
				expected: "string",
				code: "invalid_type",
				input: payload.value,
				inst
			});
			return payload;
		};
	});
	var $ZodStringFormat = /*@__PURE__*/ $constructor("$ZodStringFormat", (inst, def) => {
		$ZodCheckStringFormat.init(inst, def);
		$ZodString.init(inst, def);
	});
	var $ZodGUID = /*@__PURE__*/ $constructor("$ZodGUID", (inst, def) => {
		var _def$pattern;
		(_def$pattern = def.pattern) !== null && _def$pattern !== void 0 || (def.pattern = guid);
		$ZodStringFormat.init(inst, def);
	});
	var $ZodUUID = /*@__PURE__*/ $constructor("$ZodUUID", (inst, def) => {
		var _def$pattern3;
		if (def.version) {
			var _def$pattern2;
			const v = {
				v1: 1,
				v2: 2,
				v3: 3,
				v4: 4,
				v5: 5,
				v6: 6,
				v7: 7,
				v8: 8
			}[def.version];
			if (v === void 0) throw new Error(`Invalid UUID version: "${def.version}"`);
			(_def$pattern2 = def.pattern) !== null && _def$pattern2 !== void 0 || (def.pattern = uuid(v));
		} else (_def$pattern3 = def.pattern) !== null && _def$pattern3 !== void 0 || (def.pattern = uuid());
		$ZodStringFormat.init(inst, def);
	});
	var $ZodEmail = /*@__PURE__*/ $constructor("$ZodEmail", (inst, def) => {
		var _def$pattern4;
		(_def$pattern4 = def.pattern) !== null && _def$pattern4 !== void 0 || (def.pattern = email);
		$ZodStringFormat.init(inst, def);
	});
	var $ZodURL = /*@__PURE__*/ $constructor("$ZodURL", (inst, def) => {
		$ZodStringFormat.init(inst, def);
		inst._zod.check = (payload) => {
			try {
				const orig = payload.value;
				const url = new URL(orig);
				const href = url.href;
				if (def.hostname) {
					def.hostname.lastIndex = 0;
					if (!def.hostname.test(url.hostname)) payload.issues.push({
						code: "invalid_format",
						format: "url",
						note: "Invalid hostname",
						pattern: hostname.source,
						input: payload.value,
						inst,
						continue: !def.abort
					});
				}
				if (def.protocol) {
					def.protocol.lastIndex = 0;
					if (!def.protocol.test(url.protocol.endsWith(":") ? url.protocol.slice(0, -1) : url.protocol)) payload.issues.push({
						code: "invalid_format",
						format: "url",
						note: "Invalid protocol",
						pattern: def.protocol.source,
						input: payload.value,
						inst,
						continue: !def.abort
					});
				}
				if (!orig.endsWith("/") && href.endsWith("/")) payload.value = href.slice(0, -1);
				else payload.value = href;
				return;
			} catch (_) {
				payload.issues.push({
					code: "invalid_format",
					format: "url",
					input: payload.value,
					inst,
					continue: !def.abort
				});
			}
		};
	});
	var $ZodEmoji = /*@__PURE__*/ $constructor("$ZodEmoji", (inst, def) => {
		var _def$pattern5;
		(_def$pattern5 = def.pattern) !== null && _def$pattern5 !== void 0 || (def.pattern = emoji());
		$ZodStringFormat.init(inst, def);
	});
	var $ZodNanoID = /*@__PURE__*/ $constructor("$ZodNanoID", (inst, def) => {
		var _def$pattern6;
		(_def$pattern6 = def.pattern) !== null && _def$pattern6 !== void 0 || (def.pattern = nanoid);
		$ZodStringFormat.init(inst, def);
	});
	var $ZodCUID = /*@__PURE__*/ $constructor("$ZodCUID", (inst, def) => {
		var _def$pattern7;
		(_def$pattern7 = def.pattern) !== null && _def$pattern7 !== void 0 || (def.pattern = cuid);
		$ZodStringFormat.init(inst, def);
	});
	var $ZodCUID2 = /*@__PURE__*/ $constructor("$ZodCUID2", (inst, def) => {
		var _def$pattern8;
		(_def$pattern8 = def.pattern) !== null && _def$pattern8 !== void 0 || (def.pattern = cuid2);
		$ZodStringFormat.init(inst, def);
	});
	var $ZodULID = /*@__PURE__*/ $constructor("$ZodULID", (inst, def) => {
		var _def$pattern9;
		(_def$pattern9 = def.pattern) !== null && _def$pattern9 !== void 0 || (def.pattern = ulid);
		$ZodStringFormat.init(inst, def);
	});
	var $ZodXID = /*@__PURE__*/ $constructor("$ZodXID", (inst, def) => {
		var _def$pattern10;
		(_def$pattern10 = def.pattern) !== null && _def$pattern10 !== void 0 || (def.pattern = xid);
		$ZodStringFormat.init(inst, def);
	});
	var $ZodKSUID = /*@__PURE__*/ $constructor("$ZodKSUID", (inst, def) => {
		var _def$pattern11;
		(_def$pattern11 = def.pattern) !== null && _def$pattern11 !== void 0 || (def.pattern = ksuid);
		$ZodStringFormat.init(inst, def);
	});
	var $ZodISODateTime = /*@__PURE__*/ $constructor("$ZodISODateTime", (inst, def) => {
		var _def$pattern12;
		(_def$pattern12 = def.pattern) !== null && _def$pattern12 !== void 0 || (def.pattern = datetime$1(def));
		$ZodStringFormat.init(inst, def);
	});
	var $ZodISODate = /*@__PURE__*/ $constructor("$ZodISODate", (inst, def) => {
		var _def$pattern13;
		(_def$pattern13 = def.pattern) !== null && _def$pattern13 !== void 0 || (def.pattern = date$1);
		$ZodStringFormat.init(inst, def);
	});
	var $ZodISOTime = /*@__PURE__*/ $constructor("$ZodISOTime", (inst, def) => {
		var _def$pattern14;
		(_def$pattern14 = def.pattern) !== null && _def$pattern14 !== void 0 || (def.pattern = time$1(def));
		$ZodStringFormat.init(inst, def);
	});
	var $ZodISODuration = /*@__PURE__*/ $constructor("$ZodISODuration", (inst, def) => {
		var _def$pattern15;
		(_def$pattern15 = def.pattern) !== null && _def$pattern15 !== void 0 || (def.pattern = duration$1);
		$ZodStringFormat.init(inst, def);
	});
	var $ZodIPv4 = /*@__PURE__*/ $constructor("$ZodIPv4", (inst, def) => {
		var _def$pattern16;
		(_def$pattern16 = def.pattern) !== null && _def$pattern16 !== void 0 || (def.pattern = ipv4);
		$ZodStringFormat.init(inst, def);
		inst._zod.onattach.push((inst) => {
			const bag = inst._zod.bag;
			bag.format = `ipv4`;
		});
	});
	var $ZodIPv6 = /*@__PURE__*/ $constructor("$ZodIPv6", (inst, def) => {
		var _def$pattern17;
		(_def$pattern17 = def.pattern) !== null && _def$pattern17 !== void 0 || (def.pattern = ipv6);
		$ZodStringFormat.init(inst, def);
		inst._zod.onattach.push((inst) => {
			const bag = inst._zod.bag;
			bag.format = `ipv6`;
		});
		inst._zod.check = (payload) => {
			try {
				new URL(`http://[${payload.value}]`);
			} catch (_unused) {
				payload.issues.push({
					code: "invalid_format",
					format: "ipv6",
					input: payload.value,
					inst,
					continue: !def.abort
				});
			}
		};
	});
	var $ZodCIDRv4 = /*@__PURE__*/ $constructor("$ZodCIDRv4", (inst, def) => {
		var _def$pattern18;
		(_def$pattern18 = def.pattern) !== null && _def$pattern18 !== void 0 || (def.pattern = cidrv4);
		$ZodStringFormat.init(inst, def);
	});
	var $ZodCIDRv6 = /*@__PURE__*/ $constructor("$ZodCIDRv6", (inst, def) => {
		var _def$pattern19;
		(_def$pattern19 = def.pattern) !== null && _def$pattern19 !== void 0 || (def.pattern = cidrv6);
		$ZodStringFormat.init(inst, def);
		inst._zod.check = (payload) => {
			const [address, prefix] = payload.value.split("/");
			try {
				if (!prefix) throw new Error();
				const prefixNum = Number(prefix);
				if (`${prefixNum}` !== prefix) throw new Error();
				if (prefixNum < 0 || prefixNum > 128) throw new Error();
				new URL(`http://[${address}]`);
			} catch (_unused2) {
				payload.issues.push({
					code: "invalid_format",
					format: "cidrv6",
					input: payload.value,
					inst,
					continue: !def.abort
				});
			}
		};
	});
	function isValidBase64(data) {
		if (data === "") return true;
		if (data.length % 4 !== 0) return false;
		try {
			atob(data);
			return true;
		} catch (_unused3) {
			return false;
		}
	}
	var $ZodBase64 = /*@__PURE__*/ $constructor("$ZodBase64", (inst, def) => {
		var _def$pattern20;
		(_def$pattern20 = def.pattern) !== null && _def$pattern20 !== void 0 || (def.pattern = base64);
		$ZodStringFormat.init(inst, def);
		inst._zod.onattach.push((inst) => {
			inst._zod.bag.contentEncoding = "base64";
		});
		inst._zod.check = (payload) => {
			if (isValidBase64(payload.value)) return;
			payload.issues.push({
				code: "invalid_format",
				format: "base64",
				input: payload.value,
				inst,
				continue: !def.abort
			});
		};
	});
	function isValidBase64URL(data) {
		if (!base64url.test(data)) return false;
		const base64 = data.replace(/[-_]/g, (c) => c === "-" ? "+" : "/");
		return isValidBase64(base64.padEnd(Math.ceil(base64.length / 4) * 4, "="));
	}
	var $ZodBase64URL = /*@__PURE__*/ $constructor("$ZodBase64URL", (inst, def) => {
		var _def$pattern21;
		(_def$pattern21 = def.pattern) !== null && _def$pattern21 !== void 0 || (def.pattern = base64url);
		$ZodStringFormat.init(inst, def);
		inst._zod.onattach.push((inst) => {
			inst._zod.bag.contentEncoding = "base64url";
		});
		inst._zod.check = (payload) => {
			if (isValidBase64URL(payload.value)) return;
			payload.issues.push({
				code: "invalid_format",
				format: "base64url",
				input: payload.value,
				inst,
				continue: !def.abort
			});
		};
	});
	var $ZodE164 = /*@__PURE__*/ $constructor("$ZodE164", (inst, def) => {
		var _def$pattern22;
		(_def$pattern22 = def.pattern) !== null && _def$pattern22 !== void 0 || (def.pattern = e164);
		$ZodStringFormat.init(inst, def);
	});
	function isValidJWT(token, algorithm = null) {
		try {
			const tokensParts = token.split(".");
			if (tokensParts.length !== 3) return false;
			const [header] = tokensParts;
			if (!header) return false;
			const parsedHeader = JSON.parse(atob(header));
			if ("typ" in parsedHeader && (parsedHeader === null || parsedHeader === void 0 ? void 0 : parsedHeader.typ) !== "JWT") return false;
			if (!parsedHeader.alg) return false;
			if (algorithm && (!("alg" in parsedHeader) || parsedHeader.alg !== algorithm)) return false;
			return true;
		} catch (_unused4) {
			return false;
		}
	}
	var $ZodJWT = /*@__PURE__*/ $constructor("$ZodJWT", (inst, def) => {
		$ZodStringFormat.init(inst, def);
		inst._zod.check = (payload) => {
			if (isValidJWT(payload.value, def.alg)) return;
			payload.issues.push({
				code: "invalid_format",
				format: "jwt",
				input: payload.value,
				inst,
				continue: !def.abort
			});
		};
	});
	var $ZodNumber = /*@__PURE__*/ $constructor("$ZodNumber", (inst, def) => {
		var _inst$_zod$bag$patter2;
		$ZodType.init(inst, def);
		inst._zod.pattern = (_inst$_zod$bag$patter2 = inst._zod.bag.pattern) !== null && _inst$_zod$bag$patter2 !== void 0 ? _inst$_zod$bag$patter2 : number$1;
		inst._zod.parse = (payload, _ctx) => {
			if (def.coerce) try {
				payload.value = Number(payload.value);
			} catch (_) {}
			const input = payload.value;
			if (typeof input === "number" && !Number.isNaN(input) && Number.isFinite(input)) return payload;
			const received = typeof input === "number" ? Number.isNaN(input) ? "NaN" : !Number.isFinite(input) ? "Infinity" : void 0 : void 0;
			payload.issues.push(_objectSpread2({
				expected: "number",
				code: "invalid_type",
				input,
				inst
			}, received ? { received } : {}));
			return payload;
		};
	});
	var $ZodNumberFormat = /*@__PURE__*/ $constructor("$ZodNumber", (inst, def) => {
		$ZodCheckNumberFormat.init(inst, def);
		$ZodNumber.init(inst, def);
	});
	var $ZodBoolean = /*@__PURE__*/ $constructor("$ZodBoolean", (inst, def) => {
		$ZodType.init(inst, def);
		inst._zod.pattern = boolean$1;
		inst._zod.parse = (payload, _ctx) => {
			if (def.coerce) try {
				payload.value = Boolean(payload.value);
			} catch (_) {}
			const input = payload.value;
			if (typeof input === "boolean") return payload;
			payload.issues.push({
				expected: "boolean",
				code: "invalid_type",
				input,
				inst
			});
			return payload;
		};
	});
	var $ZodNull = /*@__PURE__*/ $constructor("$ZodNull", (inst, def) => {
		$ZodType.init(inst, def);
		inst._zod.pattern = _null$2;
		inst._zod.values = /* @__PURE__ */ new Set([null]);
		inst._zod.parse = (payload, _ctx) => {
			const input = payload.value;
			if (input === null) return payload;
			payload.issues.push({
				expected: "null",
				code: "invalid_type",
				input,
				inst
			});
			return payload;
		};
	});
	var $ZodUnknown = /*@__PURE__*/ $constructor("$ZodUnknown", (inst, def) => {
		$ZodType.init(inst, def);
		inst._zod.parse = (payload) => payload;
	});
	var $ZodNever = /*@__PURE__*/ $constructor("$ZodNever", (inst, def) => {
		$ZodType.init(inst, def);
		inst._zod.parse = (payload, _ctx) => {
			payload.issues.push({
				expected: "never",
				code: "invalid_type",
				input: payload.value,
				inst
			});
			return payload;
		};
	});
	function handleArrayResult(result, final, index) {
		if (result.issues.length) final.issues.push(...prefixIssues(index, result.issues));
		final.value[index] = result.value;
	}
	var $ZodArray = /*@__PURE__*/ $constructor("$ZodArray", (inst, def) => {
		$ZodType.init(inst, def);
		inst._zod.parse = (payload, ctx) => {
			const input = payload.value;
			if (!Array.isArray(input)) {
				payload.issues.push({
					expected: "array",
					code: "invalid_type",
					input,
					inst
				});
				return payload;
			}
			payload.value = Array(input.length);
			const proms = [];
			for (let i = 0; i < input.length; i++) {
				const item = input[i];
				const result = def.element._zod.run({
					value: item,
					issues: []
				}, ctx);
				if (result instanceof Promise) proms.push(result.then((result) => handleArrayResult(result, payload, i)));
				else handleArrayResult(result, payload, i);
			}
			if (proms.length) return Promise.all(proms).then(() => payload);
			return payload;
		};
	});
	function handleObjectResult(result, final, key) {
		if (result.issues.length) final.issues.push(...prefixIssues(key, result.issues));
		final.value[key] = result.value;
	}
	function handleOptionalObjectResult(result, final, key, input) {
		if (result.issues.length) if (input[key] === void 0) if (key in input) final.value[key] = void 0;
		else final.value[key] = result.value;
		else final.issues.push(...prefixIssues(key, result.issues));
		else if (result.value === void 0) {
			if (key in input) final.value[key] = void 0;
		} else final.value[key] = result.value;
	}
	var $ZodObject = /*@__PURE__*/ $constructor("$ZodObject", (inst, def) => {
		$ZodType.init(inst, def);
		const _normalized = cached(() => {
			const keys = Object.keys(def.shape);
			for (const k of keys) if (!(def.shape[k] instanceof $ZodType)) throw new Error(`Invalid element at key "${k}": expected a Zod schema`);
			const okeys = optionalKeys(def.shape);
			return {
				shape: def.shape,
				keys,
				keySet: new Set(keys),
				numKeys: keys.length,
				optionalKeys: new Set(okeys)
			};
		});
		defineLazy(inst._zod, "propValues", () => {
			const shape = def.shape;
			const propValues = {};
			for (const key in shape) {
				const field = shape[key]._zod;
				if (field.values) {
					var _propValues$key;
					(_propValues$key = propValues[key]) !== null && _propValues$key !== void 0 || (propValues[key] = /* @__PURE__ */ new Set());
					for (const v of field.values) propValues[key].add(v);
				}
			}
			return propValues;
		});
		const generateFastpass = (shape) => {
			const doc = new Doc([
				"shape",
				"payload",
				"ctx"
			]);
			const normalized = _normalized.value;
			const parseStr = (key) => {
				const k = esc(key);
				return `shape[${k}]._zod.run({ value: input[${k}], issues: [] }, ctx)`;
			};
			doc.write(`const input = payload.value;`);
			const ids = Object.create(null);
			let counter = 0;
			for (const key of normalized.keys) ids[key] = `key_${counter++}`;
			doc.write(`const newResult = {}`);
			for (const key of normalized.keys) if (normalized.optionalKeys.has(key)) {
				const id = ids[key];
				doc.write(`const ${id} = ${parseStr(key)};`);
				const k = esc(key);
				doc.write(`
        if (${id}.issues.length) {
          if (input[${k}] === undefined) {
            if (${k} in input) {
              newResult[${k}] = undefined;
            }
          } else {
            payload.issues = payload.issues.concat(
              ${id}.issues.map((iss) => ({
                ...iss,
                path: iss.path ? [${k}, ...iss.path] : [${k}],
              }))
            );
          }
        } else if (${id}.value === undefined) {
          if (${k} in input) newResult[${k}] = undefined;
        } else {
          newResult[${k}] = ${id}.value;
        }
        `);
			} else {
				const id = ids[key];
				doc.write(`const ${id} = ${parseStr(key)};`);
				doc.write(`
          if (${id}.issues.length) payload.issues = payload.issues.concat(${id}.issues.map(iss => ({
            ...iss,
            path: iss.path ? [${esc(key)}, ...iss.path] : [${esc(key)}]
          })));`);
				doc.write(`newResult[${esc(key)}] = ${id}.value`);
			}
			doc.write(`payload.value = newResult;`);
			doc.write(`return payload;`);
			const fn = doc.compile();
			return (payload, ctx) => fn(shape, payload, ctx);
		};
		let fastpass;
		const isObject$1 = isObject;
		const jit = !globalConfig.jitless;
		const allowsEval$1 = allowsEval;
		const fastEnabled = jit && allowsEval$1.value;
		const catchall = def.catchall;
		let value;
		inst._zod.parse = (payload, ctx) => {
			var _value;
			(_value = value) !== null && _value !== void 0 || (value = _normalized.value);
			const input = payload.value;
			if (!isObject$1(input)) {
				payload.issues.push({
					expected: "object",
					code: "invalid_type",
					input,
					inst
				});
				return payload;
			}
			const proms = [];
			if (jit && fastEnabled && (ctx === null || ctx === void 0 ? void 0 : ctx.async) === false && ctx.jitless !== true) {
				if (!fastpass) fastpass = generateFastpass(def.shape);
				payload = fastpass(payload, ctx);
			} else {
				payload.value = {};
				const shape = value.shape;
				for (const key of value.keys) {
					const el = shape[key];
					const r = el._zod.run({
						value: input[key],
						issues: []
					}, ctx);
					const isOptional = el._zod.optin === "optional" && el._zod.optout === "optional";
					if (r instanceof Promise) proms.push(r.then((r) => isOptional ? handleOptionalObjectResult(r, payload, key, input) : handleObjectResult(r, payload, key)));
					else if (isOptional) handleOptionalObjectResult(r, payload, key, input);
					else handleObjectResult(r, payload, key);
				}
			}
			if (!catchall) return proms.length ? Promise.all(proms).then(() => payload) : payload;
			const unrecognized = [];
			const keySet = value.keySet;
			const _catchall = catchall._zod;
			const t = _catchall.def.type;
			for (const key of Object.keys(input)) {
				if (keySet.has(key)) continue;
				if (t === "never") {
					unrecognized.push(key);
					continue;
				}
				const r = _catchall.run({
					value: input[key],
					issues: []
				}, ctx);
				if (r instanceof Promise) proms.push(r.then((r) => handleObjectResult(r, payload, key)));
				else handleObjectResult(r, payload, key);
			}
			if (unrecognized.length) payload.issues.push({
				code: "unrecognized_keys",
				keys: unrecognized,
				input,
				inst
			});
			if (!proms.length) return payload;
			return Promise.all(proms).then(() => {
				return payload;
			});
		};
	});
	function handleUnionResults(results, final, inst, ctx) {
		for (const result of results) if (result.issues.length === 0) {
			final.value = result.value;
			return final;
		}
		final.issues.push({
			code: "invalid_union",
			input: final.value,
			inst,
			errors: results.map((result) => result.issues.map((iss) => finalizeIssue(iss, ctx, config())))
		});
		return final;
	}
	var $ZodUnion = /*@__PURE__*/ $constructor("$ZodUnion", (inst, def) => {
		$ZodType.init(inst, def);
		defineLazy(inst._zod, "optin", () => def.options.some((o) => o._zod.optin === "optional") ? "optional" : void 0);
		defineLazy(inst._zod, "optout", () => def.options.some((o) => o._zod.optout === "optional") ? "optional" : void 0);
		defineLazy(inst._zod, "values", () => {
			if (def.options.every((o) => o._zod.values)) return new Set(def.options.flatMap((option) => Array.from(option._zod.values)));
		});
		defineLazy(inst._zod, "pattern", () => {
			if (def.options.every((o) => o._zod.pattern)) {
				const patterns = def.options.map((o) => o._zod.pattern);
				return new RegExp(`^(${patterns.map((p) => cleanRegex(p.source)).join("|")})$`);
			}
		});
		inst._zod.parse = (payload, ctx) => {
			let async = false;
			const results = [];
			for (const option of def.options) {
				const result = option._zod.run({
					value: payload.value,
					issues: []
				}, ctx);
				if (result instanceof Promise) {
					results.push(result);
					async = true;
				} else {
					if (result.issues.length === 0) return result;
					results.push(result);
				}
			}
			if (!async) return handleUnionResults(results, payload, inst, ctx);
			return Promise.all(results).then((results) => {
				return handleUnionResults(results, payload, inst, ctx);
			});
		};
	});
	var $ZodDiscriminatedUnion = /*@__PURE__*/ $constructor("$ZodDiscriminatedUnion", (inst, def) => {
		$ZodUnion.init(inst, def);
		const _super = inst._zod.parse;
		defineLazy(inst._zod, "propValues", () => {
			const propValues = {};
			for (const option of def.options) {
				const pv = option._zod.propValues;
				if (!pv || Object.keys(pv).length === 0) throw new Error(`Invalid discriminated union option at index "${def.options.indexOf(option)}"`);
				for (const [k, v] of Object.entries(pv)) {
					if (!propValues[k]) propValues[k] = /* @__PURE__ */ new Set();
					for (const val of v) propValues[k].add(val);
				}
			}
			return propValues;
		});
		const disc = cached(() => {
			const opts = def.options;
			const map = /* @__PURE__ */ new Map();
			for (const o of opts) {
				const values = o._zod.propValues[def.discriminator];
				if (!values || values.size === 0) throw new Error(`Invalid discriminated union option at index "${def.options.indexOf(o)}"`);
				for (const v of values) {
					if (map.has(v)) throw new Error(`Duplicate discriminator value "${String(v)}"`);
					map.set(v, o);
				}
			}
			return map;
		});
		inst._zod.parse = (payload, ctx) => {
			const input = payload.value;
			if (!isObject(input)) {
				payload.issues.push({
					code: "invalid_type",
					expected: "object",
					input,
					inst
				});
				return payload;
			}
			const opt = disc.value.get(input === null || input === void 0 ? void 0 : input[def.discriminator]);
			if (opt) return opt._zod.run(payload, ctx);
			if (def.unionFallback) return _super(payload, ctx);
			payload.issues.push({
				code: "invalid_union",
				errors: [],
				note: "No matching discriminator",
				input,
				path: [def.discriminator],
				inst
			});
			return payload;
		};
	});
	var $ZodIntersection = /*@__PURE__*/ $constructor("$ZodIntersection", (inst, def) => {
		$ZodType.init(inst, def);
		inst._zod.parse = (payload, ctx) => {
			const input = payload.value;
			const left = def.left._zod.run({
				value: input,
				issues: []
			}, ctx);
			const right = def.right._zod.run({
				value: input,
				issues: []
			}, ctx);
			if (left instanceof Promise || right instanceof Promise) return Promise.all([left, right]).then(([left, right]) => {
				return handleIntersectionResults(payload, left, right);
			});
			return handleIntersectionResults(payload, left, right);
		};
	});
	function mergeValues(a, b) {
		if (a === b) return {
			valid: true,
			data: a
		};
		if (a instanceof Date && b instanceof Date && +a === +b) return {
			valid: true,
			data: a
		};
		if (isPlainObject(a) && isPlainObject(b)) {
			const bKeys = Object.keys(b);
			const sharedKeys = Object.keys(a).filter((key) => bKeys.indexOf(key) !== -1);
			const newObj = _objectSpread2(_objectSpread2({}, a), b);
			for (const key of sharedKeys) {
				const sharedValue = mergeValues(a[key], b[key]);
				if (!sharedValue.valid) return {
					valid: false,
					mergeErrorPath: [key, ...sharedValue.mergeErrorPath]
				};
				newObj[key] = sharedValue.data;
			}
			return {
				valid: true,
				data: newObj
			};
		}
		if (Array.isArray(a) && Array.isArray(b)) {
			if (a.length !== b.length) return {
				valid: false,
				mergeErrorPath: []
			};
			const newArray = [];
			for (let index = 0; index < a.length; index++) {
				const itemA = a[index];
				const itemB = b[index];
				const sharedValue = mergeValues(itemA, itemB);
				if (!sharedValue.valid) return {
					valid: false,
					mergeErrorPath: [index, ...sharedValue.mergeErrorPath]
				};
				newArray.push(sharedValue.data);
			}
			return {
				valid: true,
				data: newArray
			};
		}
		return {
			valid: false,
			mergeErrorPath: []
		};
	}
	function handleIntersectionResults(result, left, right) {
		if (left.issues.length) result.issues.push(...left.issues);
		if (right.issues.length) result.issues.push(...right.issues);
		if (aborted(result)) return result;
		const merged = mergeValues(left.value, right.value);
		if (!merged.valid) throw new Error(`Unmergable intersection. Error path: ${JSON.stringify(merged.mergeErrorPath)}`);
		result.value = merged.data;
		return result;
	}
	var $ZodRecord = /*@__PURE__*/ $constructor("$ZodRecord", (inst, def) => {
		$ZodType.init(inst, def);
		inst._zod.parse = (payload, ctx) => {
			const input = payload.value;
			if (!isPlainObject(input)) {
				payload.issues.push({
					expected: "record",
					code: "invalid_type",
					input,
					inst
				});
				return payload;
			}
			const proms = [];
			if (def.keyType._zod.values) {
				const values = def.keyType._zod.values;
				payload.value = {};
				for (const key of values) if (typeof key === "string" || typeof key === "number" || typeof key === "symbol") {
					const result = def.valueType._zod.run({
						value: input[key],
						issues: []
					}, ctx);
					if (result instanceof Promise) proms.push(result.then((result) => {
						if (result.issues.length) payload.issues.push(...prefixIssues(key, result.issues));
						payload.value[key] = result.value;
					}));
					else {
						if (result.issues.length) payload.issues.push(...prefixIssues(key, result.issues));
						payload.value[key] = result.value;
					}
				}
				let unrecognized;
				for (const key in input) if (!values.has(key)) {
					var _unrecognized;
					unrecognized = (_unrecognized = unrecognized) !== null && _unrecognized !== void 0 ? _unrecognized : [];
					unrecognized.push(key);
				}
				if (unrecognized && unrecognized.length > 0) payload.issues.push({
					code: "unrecognized_keys",
					input,
					inst,
					keys: unrecognized
				});
			} else {
				payload.value = {};
				for (const key of Reflect.ownKeys(input)) {
					if (key === "__proto__") continue;
					const keyResult = def.keyType._zod.run({
						value: key,
						issues: []
					}, ctx);
					if (keyResult instanceof Promise) throw new Error("Async schemas not supported in object keys currently");
					if (keyResult.issues.length) {
						payload.issues.push({
							origin: "record",
							code: "invalid_key",
							issues: keyResult.issues.map((iss) => finalizeIssue(iss, ctx, config())),
							input: key,
							path: [key],
							inst
						});
						payload.value[keyResult.value] = keyResult.value;
						continue;
					}
					const result = def.valueType._zod.run({
						value: input[key],
						issues: []
					}, ctx);
					if (result instanceof Promise) proms.push(result.then((result) => {
						if (result.issues.length) payload.issues.push(...prefixIssues(key, result.issues));
						payload.value[keyResult.value] = result.value;
					}));
					else {
						if (result.issues.length) payload.issues.push(...prefixIssues(key, result.issues));
						payload.value[keyResult.value] = result.value;
					}
				}
			}
			if (proms.length) return Promise.all(proms).then(() => payload);
			return payload;
		};
	});
	var $ZodEnum = /*@__PURE__*/ $constructor("$ZodEnum", (inst, def) => {
		$ZodType.init(inst, def);
		const values = getEnumValues(def.entries);
		inst._zod.values = new Set(values);
		inst._zod.pattern = new RegExp(`^(${values.filter((k) => propertyKeyTypes.has(typeof k)).map((o) => typeof o === "string" ? escapeRegex(o) : o.toString()).join("|")})$`);
		inst._zod.parse = (payload, _ctx) => {
			const input = payload.value;
			if (inst._zod.values.has(input)) return payload;
			payload.issues.push({
				code: "invalid_value",
				values,
				input,
				inst
			});
			return payload;
		};
	});
	var $ZodLiteral = /*@__PURE__*/ $constructor("$ZodLiteral", (inst, def) => {
		$ZodType.init(inst, def);
		inst._zod.values = new Set(def.values);
		inst._zod.pattern = new RegExp(`^(${def.values.map((o) => typeof o === "string" ? escapeRegex(o) : o ? o.toString() : String(o)).join("|")})$`);
		inst._zod.parse = (payload, _ctx) => {
			const input = payload.value;
			if (inst._zod.values.has(input)) return payload;
			payload.issues.push({
				code: "invalid_value",
				values: def.values,
				input,
				inst
			});
			return payload;
		};
	});
	var $ZodTransform = /*@__PURE__*/ $constructor("$ZodTransform", (inst, def) => {
		$ZodType.init(inst, def);
		inst._zod.parse = (payload, _ctx) => {
			const _out = def.transform(payload.value, payload);
			if (_ctx.async) return (_out instanceof Promise ? _out : Promise.resolve(_out)).then((output) => {
				payload.value = output;
				return payload;
			});
			if (_out instanceof Promise) throw new $ZodAsyncError();
			payload.value = _out;
			return payload;
		};
	});
	var $ZodOptional = /*@__PURE__*/ $constructor("$ZodOptional", (inst, def) => {
		$ZodType.init(inst, def);
		inst._zod.optin = "optional";
		inst._zod.optout = "optional";
		defineLazy(inst._zod, "values", () => {
			return def.innerType._zod.values ? /* @__PURE__ */ new Set([...def.innerType._zod.values, void 0]) : void 0;
		});
		defineLazy(inst._zod, "pattern", () => {
			const pattern = def.innerType._zod.pattern;
			return pattern ? new RegExp(`^(${cleanRegex(pattern.source)})?$`) : void 0;
		});
		inst._zod.parse = (payload, ctx) => {
			if (def.innerType._zod.optin === "optional") return def.innerType._zod.run(payload, ctx);
			if (payload.value === void 0) return payload;
			return def.innerType._zod.run(payload, ctx);
		};
	});
	var $ZodNullable = /*@__PURE__*/ $constructor("$ZodNullable", (inst, def) => {
		$ZodType.init(inst, def);
		defineLazy(inst._zod, "optin", () => def.innerType._zod.optin);
		defineLazy(inst._zod, "optout", () => def.innerType._zod.optout);
		defineLazy(inst._zod, "pattern", () => {
			const pattern = def.innerType._zod.pattern;
			return pattern ? new RegExp(`^(${cleanRegex(pattern.source)}|null)$`) : void 0;
		});
		defineLazy(inst._zod, "values", () => {
			return def.innerType._zod.values ? /* @__PURE__ */ new Set([...def.innerType._zod.values, null]) : void 0;
		});
		inst._zod.parse = (payload, ctx) => {
			if (payload.value === null) return payload;
			return def.innerType._zod.run(payload, ctx);
		};
	});
	var $ZodDefault = /*@__PURE__*/ $constructor("$ZodDefault", (inst, def) => {
		$ZodType.init(inst, def);
		inst._zod.optin = "optional";
		defineLazy(inst._zod, "values", () => def.innerType._zod.values);
		inst._zod.parse = (payload, ctx) => {
			if (payload.value === void 0) {
				payload.value = def.defaultValue;
				/**
				* $ZodDefault always returns the default value immediately.
				* It doesn't pass the default value into the validator ("prefault"). There's no reason to pass the default value through validation. The validity of the default is enforced by TypeScript statically. Otherwise, it's the responsibility of the user to ensure the default is valid. In the case of pipes with divergent in/out types, you can specify the default on the `in` schema of your ZodPipe to set a "prefault" for the pipe.   */
				return payload;
			}
			const result = def.innerType._zod.run(payload, ctx);
			if (result instanceof Promise) return result.then((result) => handleDefaultResult(result, def));
			return handleDefaultResult(result, def);
		};
	});
	function handleDefaultResult(payload, def) {
		if (payload.value === void 0) payload.value = def.defaultValue;
		return payload;
	}
	var $ZodPrefault = /*@__PURE__*/ $constructor("$ZodPrefault", (inst, def) => {
		$ZodType.init(inst, def);
		inst._zod.optin = "optional";
		defineLazy(inst._zod, "values", () => def.innerType._zod.values);
		inst._zod.parse = (payload, ctx) => {
			if (payload.value === void 0) payload.value = def.defaultValue;
			return def.innerType._zod.run(payload, ctx);
		};
	});
	var $ZodNonOptional = /*@__PURE__*/ $constructor("$ZodNonOptional", (inst, def) => {
		$ZodType.init(inst, def);
		defineLazy(inst._zod, "values", () => {
			const v = def.innerType._zod.values;
			return v ? new Set([...v].filter((x) => x !== void 0)) : void 0;
		});
		inst._zod.parse = (payload, ctx) => {
			const result = def.innerType._zod.run(payload, ctx);
			if (result instanceof Promise) return result.then((result) => handleNonOptionalResult(result, inst));
			return handleNonOptionalResult(result, inst);
		};
	});
	function handleNonOptionalResult(payload, inst) {
		if (!payload.issues.length && payload.value === void 0) payload.issues.push({
			code: "invalid_type",
			expected: "nonoptional",
			input: payload.value,
			inst
		});
		return payload;
	}
	var $ZodCatch = /*@__PURE__*/ $constructor("$ZodCatch", (inst, def) => {
		$ZodType.init(inst, def);
		inst._zod.optin = "optional";
		defineLazy(inst._zod, "optout", () => def.innerType._zod.optout);
		defineLazy(inst._zod, "values", () => def.innerType._zod.values);
		inst._zod.parse = (payload, ctx) => {
			const result = def.innerType._zod.run(payload, ctx);
			if (result instanceof Promise) return result.then((result) => {
				payload.value = result.value;
				if (result.issues.length) {
					payload.value = def.catchValue(_objectSpread2(_objectSpread2({}, payload), {}, {
						error: { issues: result.issues.map((iss) => finalizeIssue(iss, ctx, config())) },
						input: payload.value
					}));
					payload.issues = [];
				}
				return payload;
			});
			payload.value = result.value;
			if (result.issues.length) {
				payload.value = def.catchValue(_objectSpread2(_objectSpread2({}, payload), {}, {
					error: { issues: result.issues.map((iss) => finalizeIssue(iss, ctx, config())) },
					input: payload.value
				}));
				payload.issues = [];
			}
			return payload;
		};
	});
	var $ZodPipe = /*@__PURE__*/ $constructor("$ZodPipe", (inst, def) => {
		$ZodType.init(inst, def);
		defineLazy(inst._zod, "values", () => def.in._zod.values);
		defineLazy(inst._zod, "optin", () => def.in._zod.optin);
		defineLazy(inst._zod, "optout", () => def.out._zod.optout);
		inst._zod.parse = (payload, ctx) => {
			const left = def.in._zod.run(payload, ctx);
			if (left instanceof Promise) return left.then((left) => handlePipeResult(left, def, ctx));
			return handlePipeResult(left, def, ctx);
		};
	});
	function handlePipeResult(left, def, ctx) {
		if (aborted(left)) return left;
		return def.out._zod.run({
			value: left.value,
			issues: left.issues
		}, ctx);
	}
	var $ZodReadonly = /*@__PURE__*/ $constructor("$ZodReadonly", (inst, def) => {
		$ZodType.init(inst, def);
		defineLazy(inst._zod, "propValues", () => def.innerType._zod.propValues);
		defineLazy(inst._zod, "values", () => def.innerType._zod.values);
		defineLazy(inst._zod, "optin", () => def.innerType._zod.optin);
		defineLazy(inst._zod, "optout", () => def.innerType._zod.optout);
		inst._zod.parse = (payload, ctx) => {
			const result = def.innerType._zod.run(payload, ctx);
			if (result instanceof Promise) return result.then(handleReadonlyResult);
			return handleReadonlyResult(result);
		};
	});
	function handleReadonlyResult(payload) {
		payload.value = Object.freeze(payload.value);
		return payload;
	}
	var $ZodCustom = /*@__PURE__*/ $constructor("$ZodCustom", (inst, def) => {
		$ZodCheck.init(inst, def);
		$ZodType.init(inst, def);
		inst._zod.parse = (payload, _) => {
			return payload;
		};
		inst._zod.check = (payload) => {
			const input = payload.value;
			const r = def.fn(input);
			if (r instanceof Promise) return r.then((r) => handleRefineResult(r, payload, input, inst));
			handleRefineResult(r, payload, input, inst);
		};
	});
	function handleRefineResult(result, payload, input, inst) {
		if (!result) {
			var _inst$_zod$def$path;
			const _iss = {
				code: "custom",
				input,
				inst,
				path: [...(_inst$_zod$def$path = inst._zod.def.path) !== null && _inst$_zod$def$path !== void 0 ? _inst$_zod$def$path : []],
				continue: !inst._zod.def.abort
			};
			if (inst._zod.def.params) _iss.params = inst._zod.def.params;
			payload.issues.push(issue(_iss));
		}
	}

//#endregion
//#region node_modules/zod/v4/core/registries.js
	var $ZodRegistry = class {
		constructor() {
			this._map = /* @__PURE__ */ new Map();
			this._idmap = /* @__PURE__ */ new Map();
		}
		add(schema, ..._meta) {
			const meta = _meta[0];
			this._map.set(schema, meta);
			if (meta && typeof meta === "object" && "id" in meta) {
				if (this._idmap.has(meta.id)) throw new Error(`ID ${meta.id} already exists in the registry`);
				this._idmap.set(meta.id, schema);
			}
			return this;
		}
		clear() {
			this._map = /* @__PURE__ */ new Map();
			this._idmap = /* @__PURE__ */ new Map();
			return this;
		}
		remove(schema) {
			const meta = this._map.get(schema);
			if (meta && typeof meta === "object" && "id" in meta) this._idmap.delete(meta.id);
			this._map.delete(schema);
			return this;
		}
		get(schema) {
			const p = schema._zod.parent;
			if (p) {
				var _this$get;
				const pm = _objectSpread2({}, (_this$get = this.get(p)) !== null && _this$get !== void 0 ? _this$get : {});
				delete pm.id;
				return _objectSpread2(_objectSpread2({}, pm), this._map.get(schema));
			}
			return this._map.get(schema);
		}
		has(schema) {
			return this._map.has(schema);
		}
	};
	function registry() {
		return new $ZodRegistry();
	}
	var globalRegistry = /*@__PURE__*/ registry();

//#endregion
//#region node_modules/zod/v4/core/api.js
	function _string(Class, params) {
		return new Class(_objectSpread2({ type: "string" }, normalizeParams(params)));
	}
	function _email(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "email",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _guid(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "guid",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _uuid(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "uuid",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _uuidv4(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "uuid",
			check: "string_format",
			abort: false,
			version: "v4"
		}, normalizeParams(params)));
	}
	function _uuidv6(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "uuid",
			check: "string_format",
			abort: false,
			version: "v6"
		}, normalizeParams(params)));
	}
	function _uuidv7(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "uuid",
			check: "string_format",
			abort: false,
			version: "v7"
		}, normalizeParams(params)));
	}
	function _url(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "url",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _emoji(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "emoji",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _nanoid(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "nanoid",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _cuid(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "cuid",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _cuid2(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "cuid2",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _ulid(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "ulid",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _xid(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "xid",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _ksuid(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "ksuid",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _ipv4(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "ipv4",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _ipv6(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "ipv6",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _cidrv4(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "cidrv4",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _cidrv6(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "cidrv6",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _base64(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "base64",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _base64url(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "base64url",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _e164(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "e164",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _jwt(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "jwt",
			check: "string_format",
			abort: false
		}, normalizeParams(params)));
	}
	function _isoDateTime(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "datetime",
			check: "string_format",
			offset: false,
			local: false,
			precision: null
		}, normalizeParams(params)));
	}
	function _isoDate(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "date",
			check: "string_format"
		}, normalizeParams(params)));
	}
	function _isoTime(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "time",
			check: "string_format",
			precision: null
		}, normalizeParams(params)));
	}
	function _isoDuration(Class, params) {
		return new Class(_objectSpread2({
			type: "string",
			format: "duration",
			check: "string_format"
		}, normalizeParams(params)));
	}
	function _number(Class, params) {
		return new Class(_objectSpread2({
			type: "number",
			checks: []
		}, normalizeParams(params)));
	}
	function _int(Class, params) {
		return new Class(_objectSpread2({
			type: "number",
			check: "number_format",
			abort: false,
			format: "safeint"
		}, normalizeParams(params)));
	}
	function _boolean(Class, params) {
		return new Class(_objectSpread2({ type: "boolean" }, normalizeParams(params)));
	}
	function _null$1(Class, params) {
		return new Class(_objectSpread2({ type: "null" }, normalizeParams(params)));
	}
	__name(_null$1, "_null");
	function _unknown(Class) {
		return new Class({ type: "unknown" });
	}
	function _never(Class, params) {
		return new Class(_objectSpread2({ type: "never" }, normalizeParams(params)));
	}
	function _lt(value, params) {
		return new $ZodCheckLessThan(_objectSpread2(_objectSpread2({ check: "less_than" }, normalizeParams(params)), {}, {
			value,
			inclusive: false
		}));
	}
	function _lte(value, params) {
		return new $ZodCheckLessThan(_objectSpread2(_objectSpread2({ check: "less_than" }, normalizeParams(params)), {}, {
			value,
			inclusive: true
		}));
	}
	function _gt(value, params) {
		return new $ZodCheckGreaterThan(_objectSpread2(_objectSpread2({ check: "greater_than" }, normalizeParams(params)), {}, {
			value,
			inclusive: false
		}));
	}
	function _gte(value, params) {
		return new $ZodCheckGreaterThan(_objectSpread2(_objectSpread2({ check: "greater_than" }, normalizeParams(params)), {}, {
			value,
			inclusive: true
		}));
	}
	function _multipleOf(value, params) {
		return new $ZodCheckMultipleOf(_objectSpread2(_objectSpread2({ check: "multiple_of" }, normalizeParams(params)), {}, { value }));
	}
	function _maxLength(maximum, params) {
		return new $ZodCheckMaxLength(_objectSpread2(_objectSpread2({ check: "max_length" }, normalizeParams(params)), {}, { maximum }));
	}
	function _minLength(minimum, params) {
		return new $ZodCheckMinLength(_objectSpread2(_objectSpread2({ check: "min_length" }, normalizeParams(params)), {}, { minimum }));
	}
	function _length(length, params) {
		return new $ZodCheckLengthEquals(_objectSpread2(_objectSpread2({ check: "length_equals" }, normalizeParams(params)), {}, { length }));
	}
	function _regex(pattern, params) {
		return new $ZodCheckRegex(_objectSpread2(_objectSpread2({
			check: "string_format",
			format: "regex"
		}, normalizeParams(params)), {}, { pattern }));
	}
	function _lowercase(params) {
		return new $ZodCheckLowerCase(_objectSpread2({
			check: "string_format",
			format: "lowercase"
		}, normalizeParams(params)));
	}
	function _uppercase(params) {
		return new $ZodCheckUpperCase(_objectSpread2({
			check: "string_format",
			format: "uppercase"
		}, normalizeParams(params)));
	}
	function _includes(includes, params) {
		return new $ZodCheckIncludes(_objectSpread2(_objectSpread2({
			check: "string_format",
			format: "includes"
		}, normalizeParams(params)), {}, { includes }));
	}
	function _startsWith(prefix, params) {
		return new $ZodCheckStartsWith(_objectSpread2(_objectSpread2({
			check: "string_format",
			format: "starts_with"
		}, normalizeParams(params)), {}, { prefix }));
	}
	function _endsWith(suffix, params) {
		return new $ZodCheckEndsWith(_objectSpread2(_objectSpread2({
			check: "string_format",
			format: "ends_with"
		}, normalizeParams(params)), {}, { suffix }));
	}
	function _overwrite(tx) {
		return new $ZodCheckOverwrite({
			check: "overwrite",
			tx
		});
	}
	function _normalize(form) {
		return _overwrite((input) => input.normalize(form));
	}
	function _trim() {
		return _overwrite((input) => input.trim());
	}
	function _toLowerCase() {
		return _overwrite((input) => input.toLowerCase());
	}
	function _toUpperCase() {
		return _overwrite((input) => input.toUpperCase());
	}
	function _array(Class, element, params) {
		return new Class(_objectSpread2({
			type: "array",
			element
		}, normalizeParams(params)));
	}
	function _custom(Class, fn, _params) {
		var _norm$abort;
		const norm = normalizeParams(_params);
		(_norm$abort = norm.abort) !== null && _norm$abort !== void 0 || (norm.abort = true);
		return new Class(_objectSpread2({
			type: "custom",
			check: "custom",
			fn
		}, norm));
	}
	function _refine(Class, fn, _params) {
		return new Class(_objectSpread2({
			type: "custom",
			check: "custom",
			fn
		}, normalizeParams(_params)));
	}

//#endregion
//#region node_modules/zod/v4/classic/iso.js
	var ZodISODateTime = /*@__PURE__*/ $constructor("ZodISODateTime", (inst, def) => {
		$ZodISODateTime.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	function datetime(params) {
		return _isoDateTime(ZodISODateTime, params);
	}
	var ZodISODate = /*@__PURE__*/ $constructor("ZodISODate", (inst, def) => {
		$ZodISODate.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	function date(params) {
		return _isoDate(ZodISODate, params);
	}
	var ZodISOTime = /*@__PURE__*/ $constructor("ZodISOTime", (inst, def) => {
		$ZodISOTime.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	function time(params) {
		return _isoTime(ZodISOTime, params);
	}
	var ZodISODuration = /*@__PURE__*/ $constructor("ZodISODuration", (inst, def) => {
		$ZodISODuration.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	function duration(params) {
		return _isoDuration(ZodISODuration, params);
	}

//#endregion
//#region node_modules/zod/v4/classic/errors.js
	var initializer = (inst, issues) => {
		$ZodError.init(inst, issues);
		inst.name = "ZodError";
		Object.defineProperties(inst, {
			format: { value: (mapper) => formatError(inst, mapper) },
			flatten: { value: (mapper) => flattenError(inst, mapper) },
			addIssue: { value: (issue) => inst.issues.push(issue) },
			addIssues: { value: (issues) => inst.issues.push(...issues) },
			isEmpty: { get() {
				return inst.issues.length === 0;
			} }
		});
	};
	var ZodError = $constructor("ZodError", initializer);
	var ZodRealError = $constructor("ZodError", initializer, { Parent: Error });

//#endregion
//#region node_modules/zod/v4/classic/parse.js
	var parse = /* @__PURE__ */ _parse(ZodRealError);
	var parseAsync = /* @__PURE__ */ _parseAsync(ZodRealError);
	var safeParse = /* @__PURE__ */ _safeParse(ZodRealError);
	var safeParseAsync = /* @__PURE__ */ _safeParseAsync(ZodRealError);

//#endregion
//#region node_modules/zod/v4/classic/schemas.js
	var ZodType = /*@__PURE__*/ $constructor("ZodType", (inst, def) => {
		$ZodType.init(inst, def);
		inst.def = def;
		Object.defineProperty(inst, "_def", { value: def });
		inst.check = (...checks) => {
			var _def$checks;
			return inst.clone(_objectSpread2(_objectSpread2({}, def), {}, { checks: [...(_def$checks = def.checks) !== null && _def$checks !== void 0 ? _def$checks : [], ...checks.map((ch) => typeof ch === "function" ? { _zod: {
				check: ch,
				def: { check: "custom" },
				onattach: []
			} } : ch)] }));
		};
		inst.clone = (def, params) => clone(inst, def, params);
		inst.brand = () => inst;
		inst.register = ((reg, meta) => {
			reg.add(inst, meta);
			return inst;
		});
		inst.parse = (data, params) => parse(inst, data, params, { callee: inst.parse });
		inst.safeParse = (data, params) => safeParse(inst, data, params);
		inst.parseAsync = function() {
			var _ref = _asyncToGenerator(function* (data, params) {
				return parseAsync(inst, data, params, { callee: inst.parseAsync });
			});
			return function(_x, _x2) {
				return _ref.apply(this, arguments);
			};
		}();
		inst.safeParseAsync = function() {
			var _ref2 = _asyncToGenerator(function* (data, params) {
				return safeParseAsync(inst, data, params);
			});
			return function(_x3, _x4) {
				return _ref2.apply(this, arguments);
			};
		}();
		inst.spa = inst.safeParseAsync;
		inst.refine = (check, params) => inst.check(refine(check, params));
		inst.superRefine = (refinement) => inst.check(superRefine(refinement));
		inst.overwrite = (fn) => inst.check(_overwrite(fn));
		inst.optional = () => optional(inst);
		inst.nullable = () => nullable(inst);
		inst.nullish = () => optional(nullable(inst));
		inst.nonoptional = (params) => nonoptional(inst, params);
		inst.array = () => array(inst);
		inst.or = (arg) => union([inst, arg]);
		inst.and = (arg) => intersection(inst, arg);
		inst.transform = (tx) => pipe(inst, transform(tx));
		inst.default = (def) => _default(inst, def);
		inst.prefault = (def) => prefault(inst, def);
		inst.catch = (params) => _catch(inst, params);
		inst.pipe = (target) => pipe(inst, target);
		inst.readonly = () => readonly(inst);
		inst.describe = (description) => {
			const cl = inst.clone();
			globalRegistry.add(cl, { description });
			return cl;
		};
		Object.defineProperty(inst, "description", {
			get() {
				var _core$globalRegistry$;
				return (_core$globalRegistry$ = globalRegistry.get(inst)) === null || _core$globalRegistry$ === void 0 ? void 0 : _core$globalRegistry$.description;
			},
			configurable: true
		});
		inst.meta = (...args) => {
			if (args.length === 0) return globalRegistry.get(inst);
			const cl = inst.clone();
			globalRegistry.add(cl, args[0]);
			return cl;
		};
		inst.isOptional = () => inst.safeParse(void 0).success;
		inst.isNullable = () => inst.safeParse(null).success;
		return inst;
	});
	/** @internal */
	var _ZodString = /*@__PURE__*/ $constructor("_ZodString", (inst, def) => {
		var _bag$format;
		var _bag$minimum;
		var _bag$maximum;
		$ZodString.init(inst, def);
		ZodType.init(inst, def);
		const bag = inst._zod.bag;
		inst.format = (_bag$format = bag.format) !== null && _bag$format !== void 0 ? _bag$format : null;
		inst.minLength = (_bag$minimum = bag.minimum) !== null && _bag$minimum !== void 0 ? _bag$minimum : null;
		inst.maxLength = (_bag$maximum = bag.maximum) !== null && _bag$maximum !== void 0 ? _bag$maximum : null;
		inst.regex = (...args) => inst.check(_regex(...args));
		inst.includes = (...args) => inst.check(_includes(...args));
		inst.startsWith = (...args) => inst.check(_startsWith(...args));
		inst.endsWith = (...args) => inst.check(_endsWith(...args));
		inst.min = (...args) => inst.check(_minLength(...args));
		inst.max = (...args) => inst.check(_maxLength(...args));
		inst.length = (...args) => inst.check(_length(...args));
		inst.nonempty = (...args) => inst.check(_minLength(1, ...args));
		inst.lowercase = (params) => inst.check(_lowercase(params));
		inst.uppercase = (params) => inst.check(_uppercase(params));
		inst.trim = () => inst.check(_trim());
		inst.normalize = (...args) => inst.check(_normalize(...args));
		inst.toLowerCase = () => inst.check(_toLowerCase());
		inst.toUpperCase = () => inst.check(_toUpperCase());
	});
	var ZodString = /*@__PURE__*/ $constructor("ZodString", (inst, def) => {
		$ZodString.init(inst, def);
		_ZodString.init(inst, def);
		inst.email = (params) => inst.check(_email(ZodEmail, params));
		inst.url = (params) => inst.check(_url(ZodURL, params));
		inst.jwt = (params) => inst.check(_jwt(ZodJWT, params));
		inst.emoji = (params) => inst.check(_emoji(ZodEmoji, params));
		inst.guid = (params) => inst.check(_guid(ZodGUID, params));
		inst.uuid = (params) => inst.check(_uuid(ZodUUID, params));
		inst.uuidv4 = (params) => inst.check(_uuidv4(ZodUUID, params));
		inst.uuidv6 = (params) => inst.check(_uuidv6(ZodUUID, params));
		inst.uuidv7 = (params) => inst.check(_uuidv7(ZodUUID, params));
		inst.nanoid = (params) => inst.check(_nanoid(ZodNanoID, params));
		inst.guid = (params) => inst.check(_guid(ZodGUID, params));
		inst.cuid = (params) => inst.check(_cuid(ZodCUID, params));
		inst.cuid2 = (params) => inst.check(_cuid2(ZodCUID2, params));
		inst.ulid = (params) => inst.check(_ulid(ZodULID, params));
		inst.base64 = (params) => inst.check(_base64(ZodBase64, params));
		inst.base64url = (params) => inst.check(_base64url(ZodBase64URL, params));
		inst.xid = (params) => inst.check(_xid(ZodXID, params));
		inst.ksuid = (params) => inst.check(_ksuid(ZodKSUID, params));
		inst.ipv4 = (params) => inst.check(_ipv4(ZodIPv4, params));
		inst.ipv6 = (params) => inst.check(_ipv6(ZodIPv6, params));
		inst.cidrv4 = (params) => inst.check(_cidrv4(ZodCIDRv4, params));
		inst.cidrv6 = (params) => inst.check(_cidrv6(ZodCIDRv6, params));
		inst.e164 = (params) => inst.check(_e164(ZodE164, params));
		inst.datetime = (params) => inst.check(datetime(params));
		inst.date = (params) => inst.check(date(params));
		inst.time = (params) => inst.check(time(params));
		inst.duration = (params) => inst.check(duration(params));
	});
	function string(params) {
		return _string(ZodString, params);
	}
	var ZodStringFormat = /*@__PURE__*/ $constructor("ZodStringFormat", (inst, def) => {
		$ZodStringFormat.init(inst, def);
		_ZodString.init(inst, def);
	});
	var ZodEmail = /*@__PURE__*/ $constructor("ZodEmail", (inst, def) => {
		$ZodEmail.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodGUID = /*@__PURE__*/ $constructor("ZodGUID", (inst, def) => {
		$ZodGUID.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodUUID = /*@__PURE__*/ $constructor("ZodUUID", (inst, def) => {
		$ZodUUID.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodURL = /*@__PURE__*/ $constructor("ZodURL", (inst, def) => {
		$ZodURL.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodEmoji = /*@__PURE__*/ $constructor("ZodEmoji", (inst, def) => {
		$ZodEmoji.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodNanoID = /*@__PURE__*/ $constructor("ZodNanoID", (inst, def) => {
		$ZodNanoID.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodCUID = /*@__PURE__*/ $constructor("ZodCUID", (inst, def) => {
		$ZodCUID.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodCUID2 = /*@__PURE__*/ $constructor("ZodCUID2", (inst, def) => {
		$ZodCUID2.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodULID = /*@__PURE__*/ $constructor("ZodULID", (inst, def) => {
		$ZodULID.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodXID = /*@__PURE__*/ $constructor("ZodXID", (inst, def) => {
		$ZodXID.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodKSUID = /*@__PURE__*/ $constructor("ZodKSUID", (inst, def) => {
		$ZodKSUID.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodIPv4 = /*@__PURE__*/ $constructor("ZodIPv4", (inst, def) => {
		$ZodIPv4.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodIPv6 = /*@__PURE__*/ $constructor("ZodIPv6", (inst, def) => {
		$ZodIPv6.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodCIDRv4 = /*@__PURE__*/ $constructor("ZodCIDRv4", (inst, def) => {
		$ZodCIDRv4.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodCIDRv6 = /*@__PURE__*/ $constructor("ZodCIDRv6", (inst, def) => {
		$ZodCIDRv6.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodBase64 = /*@__PURE__*/ $constructor("ZodBase64", (inst, def) => {
		$ZodBase64.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodBase64URL = /*@__PURE__*/ $constructor("ZodBase64URL", (inst, def) => {
		$ZodBase64URL.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodE164 = /*@__PURE__*/ $constructor("ZodE164", (inst, def) => {
		$ZodE164.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodJWT = /*@__PURE__*/ $constructor("ZodJWT", (inst, def) => {
		$ZodJWT.init(inst, def);
		ZodStringFormat.init(inst, def);
	});
	var ZodNumber = /*@__PURE__*/ $constructor("ZodNumber", (inst, def) => {
		var _Math$max;
		var _bag$minimum2;
		var _bag$exclusiveMinimum;
		var _Math$min;
		var _bag$maximum2;
		var _bag$exclusiveMaximum;
		var _bag$format2;
		var _bag$multipleOf;
		var _bag$format3;
		$ZodNumber.init(inst, def);
		ZodType.init(inst, def);
		inst.gt = (value, params) => inst.check(_gt(value, params));
		inst.gte = (value, params) => inst.check(_gte(value, params));
		inst.min = (value, params) => inst.check(_gte(value, params));
		inst.lt = (value, params) => inst.check(_lt(value, params));
		inst.lte = (value, params) => inst.check(_lte(value, params));
		inst.max = (value, params) => inst.check(_lte(value, params));
		inst.int = (params) => inst.check(int(params));
		inst.safe = (params) => inst.check(int(params));
		inst.positive = (params) => inst.check(_gt(0, params));
		inst.nonnegative = (params) => inst.check(_gte(0, params));
		inst.negative = (params) => inst.check(_lt(0, params));
		inst.nonpositive = (params) => inst.check(_lte(0, params));
		inst.multipleOf = (value, params) => inst.check(_multipleOf(value, params));
		inst.step = (value, params) => inst.check(_multipleOf(value, params));
		inst.finite = () => inst;
		const bag = inst._zod.bag;
		inst.minValue = (_Math$max = Math.max((_bag$minimum2 = bag.minimum) !== null && _bag$minimum2 !== void 0 ? _bag$minimum2 : Number.NEGATIVE_INFINITY, (_bag$exclusiveMinimum = bag.exclusiveMinimum) !== null && _bag$exclusiveMinimum !== void 0 ? _bag$exclusiveMinimum : Number.NEGATIVE_INFINITY)) !== null && _Math$max !== void 0 ? _Math$max : null;
		inst.maxValue = (_Math$min = Math.min((_bag$maximum2 = bag.maximum) !== null && _bag$maximum2 !== void 0 ? _bag$maximum2 : Number.POSITIVE_INFINITY, (_bag$exclusiveMaximum = bag.exclusiveMaximum) !== null && _bag$exclusiveMaximum !== void 0 ? _bag$exclusiveMaximum : Number.POSITIVE_INFINITY)) !== null && _Math$min !== void 0 ? _Math$min : null;
		inst.isInt = ((_bag$format2 = bag.format) !== null && _bag$format2 !== void 0 ? _bag$format2 : "").includes("int") || Number.isSafeInteger((_bag$multipleOf = bag.multipleOf) !== null && _bag$multipleOf !== void 0 ? _bag$multipleOf : .5);
		inst.isFinite = true;
		inst.format = (_bag$format3 = bag.format) !== null && _bag$format3 !== void 0 ? _bag$format3 : null;
	});
	function number(params) {
		return _number(ZodNumber, params);
	}
	var ZodNumberFormat = /*@__PURE__*/ $constructor("ZodNumberFormat", (inst, def) => {
		$ZodNumberFormat.init(inst, def);
		ZodNumber.init(inst, def);
	});
	function int(params) {
		return _int(ZodNumberFormat, params);
	}
	var ZodBoolean = /*@__PURE__*/ $constructor("ZodBoolean", (inst, def) => {
		$ZodBoolean.init(inst, def);
		ZodType.init(inst, def);
	});
	function boolean(params) {
		return _boolean(ZodBoolean, params);
	}
	var ZodNull = /*@__PURE__*/ $constructor("ZodNull", (inst, def) => {
		$ZodNull.init(inst, def);
		ZodType.init(inst, def);
	});
	function _null(params) {
		return _null$1(ZodNull, params);
	}
	var ZodUnknown = /*@__PURE__*/ $constructor("ZodUnknown", (inst, def) => {
		$ZodUnknown.init(inst, def);
		ZodType.init(inst, def);
	});
	function unknown() {
		return _unknown(ZodUnknown);
	}
	var ZodNever = /*@__PURE__*/ $constructor("ZodNever", (inst, def) => {
		$ZodNever.init(inst, def);
		ZodType.init(inst, def);
	});
	function never(params) {
		return _never(ZodNever, params);
	}
	var ZodArray = /*@__PURE__*/ $constructor("ZodArray", (inst, def) => {
		$ZodArray.init(inst, def);
		ZodType.init(inst, def);
		inst.element = def.element;
		inst.min = (minLength, params) => inst.check(_minLength(minLength, params));
		inst.nonempty = (params) => inst.check(_minLength(1, params));
		inst.max = (maxLength, params) => inst.check(_maxLength(maxLength, params));
		inst.length = (len, params) => inst.check(_length(len, params));
		inst.unwrap = () => inst.element;
	});
	function array(element, params) {
		return _array(ZodArray, element, params);
	}
	var ZodObject = /*@__PURE__*/ $constructor("ZodObject", (inst, def) => {
		$ZodObject.init(inst, def);
		ZodType.init(inst, def);
		defineLazy(inst, "shape", () => def.shape);
		inst.keyof = () => _enum(Object.keys(inst._zod.def.shape));
		inst.catchall = (catchall) => inst.clone(_objectSpread2(_objectSpread2({}, inst._zod.def), {}, { catchall }));
		inst.passthrough = () => inst.clone(_objectSpread2(_objectSpread2({}, inst._zod.def), {}, { catchall: unknown() }));
		inst.loose = () => inst.clone(_objectSpread2(_objectSpread2({}, inst._zod.def), {}, { catchall: unknown() }));
		inst.strict = () => inst.clone(_objectSpread2(_objectSpread2({}, inst._zod.def), {}, { catchall: never() }));
		inst.strip = () => inst.clone(_objectSpread2(_objectSpread2({}, inst._zod.def), {}, { catchall: void 0 }));
		inst.extend = (incoming) => {
			return extend(inst, incoming);
		};
		inst.merge = (other) => merge(inst, other);
		inst.pick = (mask) => pick(inst, mask);
		inst.omit = (mask) => omit(inst, mask);
		inst.partial = (...args) => partial(ZodOptional, inst, args[0]);
		inst.required = (...args) => required(ZodNonOptional, inst, args[0]);
	});
	function object(shape, params) {
		const def = _objectSpread2({
			type: "object",
			get shape() {
				assignProp(this, "shape", _objectSpread2({}, shape));
				return this.shape;
			}
		}, normalizeParams(params));
		return new ZodObject(def);
	}
	function looseObject(shape, params) {
		return new ZodObject(_objectSpread2({
			type: "object",
			get shape() {
				assignProp(this, "shape", _objectSpread2({}, shape));
				return this.shape;
			},
			catchall: unknown()
		}, normalizeParams(params)));
	}
	var ZodUnion = /*@__PURE__*/ $constructor("ZodUnion", (inst, def) => {
		$ZodUnion.init(inst, def);
		ZodType.init(inst, def);
		inst.options = def.options;
	});
	function union(options, params) {
		return new ZodUnion(_objectSpread2({
			type: "union",
			options
		}, normalizeParams(params)));
	}
	var ZodDiscriminatedUnion = /*@__PURE__*/ $constructor("ZodDiscriminatedUnion", (inst, def) => {
		ZodUnion.init(inst, def);
		$ZodDiscriminatedUnion.init(inst, def);
	});
	function discriminatedUnion(discriminator, options, params) {
		return new ZodDiscriminatedUnion(_objectSpread2({
			type: "union",
			options,
			discriminator
		}, normalizeParams(params)));
	}
	var ZodIntersection = /*@__PURE__*/ $constructor("ZodIntersection", (inst, def) => {
		$ZodIntersection.init(inst, def);
		ZodType.init(inst, def);
	});
	function intersection(left, right) {
		return new ZodIntersection({
			type: "intersection",
			left,
			right
		});
	}
	var ZodRecord = /*@__PURE__*/ $constructor("ZodRecord", (inst, def) => {
		$ZodRecord.init(inst, def);
		ZodType.init(inst, def);
		inst.keyType = def.keyType;
		inst.valueType = def.valueType;
	});
	function record(keyType, valueType, params) {
		return new ZodRecord(_objectSpread2({
			type: "record",
			keyType,
			valueType
		}, normalizeParams(params)));
	}
	var ZodEnum = /*@__PURE__*/ $constructor("ZodEnum", (inst, def) => {
		$ZodEnum.init(inst, def);
		ZodType.init(inst, def);
		inst.enum = def.entries;
		inst.options = Object.values(def.entries);
		const keys = new Set(Object.keys(def.entries));
		inst.extract = (values, params) => {
			const newEntries = {};
			for (const value of values) if (keys.has(value)) newEntries[value] = def.entries[value];
			else throw new Error(`Key ${value} not found in enum`);
			return new ZodEnum(_objectSpread2(_objectSpread2(_objectSpread2({}, def), {}, { checks: [] }, normalizeParams(params)), {}, { entries: newEntries }));
		};
		inst.exclude = (values, params) => {
			const newEntries = _objectSpread2({}, def.entries);
			for (const value of values) if (keys.has(value)) delete newEntries[value];
			else throw new Error(`Key ${value} not found in enum`);
			return new ZodEnum(_objectSpread2(_objectSpread2(_objectSpread2({}, def), {}, { checks: [] }, normalizeParams(params)), {}, { entries: newEntries }));
		};
	});
	function _enum(values, params) {
		const entries = Array.isArray(values) ? Object.fromEntries(values.map((v) => [v, v])) : values;
		return new ZodEnum(_objectSpread2({
			type: "enum",
			entries
		}, normalizeParams(params)));
	}
	var ZodLiteral = /*@__PURE__*/ $constructor("ZodLiteral", (inst, def) => {
		$ZodLiteral.init(inst, def);
		ZodType.init(inst, def);
		inst.values = new Set(def.values);
		Object.defineProperty(inst, "value", { get() {
			if (def.values.length > 1) throw new Error("This schema contains multiple valid literal values. Use `.values` instead.");
			return def.values[0];
		} });
	});
	function literal(value, params) {
		return new ZodLiteral(_objectSpread2({
			type: "literal",
			values: Array.isArray(value) ? value : [value]
		}, normalizeParams(params)));
	}
	var ZodTransform = /*@__PURE__*/ $constructor("ZodTransform", (inst, def) => {
		$ZodTransform.init(inst, def);
		ZodType.init(inst, def);
		inst._zod.parse = (payload, _ctx) => {
			payload.addIssue = (issue$1) => {
				if (typeof issue$1 === "string") payload.issues.push(issue(issue$1, payload.value, def));
				else {
					var _issue$code;
					var _issue$input;
					var _issue$inst;
					var _issue$continue;
					const _issue = issue$1;
					if (_issue.fatal) _issue.continue = false;
					(_issue$code = _issue.code) !== null && _issue$code !== void 0 || (_issue.code = "custom");
					(_issue$input = _issue.input) !== null && _issue$input !== void 0 || (_issue.input = payload.value);
					(_issue$inst = _issue.inst) !== null && _issue$inst !== void 0 || (_issue.inst = inst);
					(_issue$continue = _issue.continue) !== null && _issue$continue !== void 0 || (_issue.continue = true);
					payload.issues.push(issue(_issue));
				}
			};
			const output = def.transform(payload.value, payload);
			if (output instanceof Promise) return output.then((output) => {
				payload.value = output;
				return payload;
			});
			payload.value = output;
			return payload;
		};
	});
	function transform(fn) {
		return new ZodTransform({
			type: "transform",
			transform: fn
		});
	}
	var ZodOptional = /*@__PURE__*/ $constructor("ZodOptional", (inst, def) => {
		$ZodOptional.init(inst, def);
		ZodType.init(inst, def);
		inst.unwrap = () => inst._zod.def.innerType;
	});
	function optional(innerType) {
		return new ZodOptional({
			type: "optional",
			innerType
		});
	}
	var ZodNullable = /*@__PURE__*/ $constructor("ZodNullable", (inst, def) => {
		$ZodNullable.init(inst, def);
		ZodType.init(inst, def);
		inst.unwrap = () => inst._zod.def.innerType;
	});
	function nullable(innerType) {
		return new ZodNullable({
			type: "nullable",
			innerType
		});
	}
	var ZodDefault = /*@__PURE__*/ $constructor("ZodDefault", (inst, def) => {
		$ZodDefault.init(inst, def);
		ZodType.init(inst, def);
		inst.unwrap = () => inst._zod.def.innerType;
		inst.removeDefault = inst.unwrap;
	});
	function _default(innerType, defaultValue) {
		return new ZodDefault({
			type: "default",
			innerType,
			get defaultValue() {
				return typeof defaultValue === "function" ? defaultValue() : defaultValue;
			}
		});
	}
	var ZodPrefault = /*@__PURE__*/ $constructor("ZodPrefault", (inst, def) => {
		$ZodPrefault.init(inst, def);
		ZodType.init(inst, def);
		inst.unwrap = () => inst._zod.def.innerType;
	});
	function prefault(innerType, defaultValue) {
		return new ZodPrefault({
			type: "prefault",
			innerType,
			get defaultValue() {
				return typeof defaultValue === "function" ? defaultValue() : defaultValue;
			}
		});
	}
	var ZodNonOptional = /*@__PURE__*/ $constructor("ZodNonOptional", (inst, def) => {
		$ZodNonOptional.init(inst, def);
		ZodType.init(inst, def);
		inst.unwrap = () => inst._zod.def.innerType;
	});
	function nonoptional(innerType, params) {
		return new ZodNonOptional(_objectSpread2({
			type: "nonoptional",
			innerType
		}, normalizeParams(params)));
	}
	var ZodCatch = /*@__PURE__*/ $constructor("ZodCatch", (inst, def) => {
		$ZodCatch.init(inst, def);
		ZodType.init(inst, def);
		inst.unwrap = () => inst._zod.def.innerType;
		inst.removeCatch = inst.unwrap;
	});
	function _catch(innerType, catchValue) {
		return new ZodCatch({
			type: "catch",
			innerType,
			catchValue: typeof catchValue === "function" ? catchValue : () => catchValue
		});
	}
	var ZodPipe = /*@__PURE__*/ $constructor("ZodPipe", (inst, def) => {
		$ZodPipe.init(inst, def);
		ZodType.init(inst, def);
		inst.in = def.in;
		inst.out = def.out;
	});
	function pipe(in_, out) {
		return new ZodPipe({
			type: "pipe",
			in: in_,
			out
		});
	}
	var ZodReadonly = /*@__PURE__*/ $constructor("ZodReadonly", (inst, def) => {
		$ZodReadonly.init(inst, def);
		ZodType.init(inst, def);
	});
	function readonly(innerType) {
		return new ZodReadonly({
			type: "readonly",
			innerType
		});
	}
	var ZodCustom = /*@__PURE__*/ $constructor("ZodCustom", (inst, def) => {
		$ZodCustom.init(inst, def);
		ZodType.init(inst, def);
	});
	function check(fn) {
		const ch = new $ZodCheck({ check: "custom" });
		ch._zod.check = fn;
		return ch;
	}
	function custom(fn, _params) {
		return _custom(ZodCustom, fn !== null && fn !== void 0 ? fn : (() => true), _params);
	}
	function refine(fn, _params = {}) {
		return _refine(ZodCustom, fn, _params);
	}
	function superRefine(fn) {
		const ch = check((payload) => {
			payload.addIssue = (issue$2) => {
				if (typeof issue$2 === "string") payload.issues.push(issue(issue$2, payload.value, ch._zod.def));
				else {
					var _issue$code2;
					var _issue$input2;
					var _issue$inst2;
					var _issue$continue2;
					const _issue = issue$2;
					if (_issue.fatal) _issue.continue = false;
					(_issue$code2 = _issue.code) !== null && _issue$code2 !== void 0 || (_issue.code = "custom");
					(_issue$input2 = _issue.input) !== null && _issue$input2 !== void 0 || (_issue.input = payload.value);
					(_issue$inst2 = _issue.inst) !== null && _issue$inst2 !== void 0 || (_issue.inst = ch);
					(_issue$continue2 = _issue.continue) !== null && _issue$continue2 !== void 0 || (_issue.continue = !ch._zod.def.abort);
					payload.issues.push(issue(_issue));
				}
			};
			return fn(payload.value, payload);
		});
		return ch;
	}
	function preprocess(fn, schema) {
		return pipe(transform(fn), schema);
	}

//#endregion
//#region node_modules/@modelcontextprotocol/sdk/dist/esm/types.js
	var RELATED_TASK_META_KEY = "io.modelcontextprotocol/related-task";
	var JSONRPC_VERSION = "2.0";
	/**
	* Assert 'object' type schema.
	*
	* @internal
	*/
	var AssertObjectSchema = custom((v) => v !== null && (typeof v === "object" || typeof v === "function"));
	/**
	* A progress token, used to associate progress notifications with the original request.
	*/
	var ProgressTokenSchema = union([string(), number().int()]);
	/**
	* An opaque token used to represent a cursor for pagination.
	*/
	var CursorSchema = string();
	/**
	* Task creation parameters, used to ask that the server create a task to represent a request.
	*/
	var TaskCreationParamsSchema = looseObject({
		/**
		* Time in milliseconds to keep task results available after completion.
		* If null, the task has unlimited lifetime until manually cleaned up.
		*/
		ttl: union([number(), _null()]).optional(),
		/**
		* Time in milliseconds to wait between task status requests.
		*/
		pollInterval: number().optional()
	});
	var TaskMetadataSchema = object({ ttl: number().optional() });
	/**
	* Metadata for associating messages with a task.
	* Include this in the `_meta` field under the key `io.modelcontextprotocol/related-task`.
	*/
	var RelatedTaskMetadataSchema = object({ taskId: string() });
	var RequestMetaSchema = looseObject({
		/**
		* If specified, the caller is requesting out-of-band progress notifications for this request (as represented by notifications/progress). The value of this parameter is an opaque token that will be attached to any subsequent notifications. The receiver is not obligated to provide these notifications.
		*/
		progressToken: ProgressTokenSchema.optional(),
		/**
		* If specified, this request is related to the provided task.
		*/
		[RELATED_TASK_META_KEY]: RelatedTaskMetadataSchema.optional()
	});
	/**
	* Common params for any request.
	*/
	var BaseRequestParamsSchema = object({ 
	/**
	* See [General fields: `_meta`](/specification/draft/basic/index#meta) for notes on `_meta` usage.
	*/
_meta: RequestMetaSchema.optional() });
	/**
	* Common params for any task-augmented request.
	*/
	var TaskAugmentedRequestParamsSchema = BaseRequestParamsSchema.extend({ 
	/**
	* If specified, the caller is requesting task-augmented execution for this request.
	* The request will return a CreateTaskResult immediately, and the actual result can be
	* retrieved later via tasks/result.
	*
	* Task augmentation is subject to capability negotiation - receivers MUST declare support
	* for task augmentation of specific request types in their capabilities.
	*/
task: TaskMetadataSchema.optional() });
	var RequestSchema = object({
		method: string(),
		params: BaseRequestParamsSchema.loose().optional()
	});
	var NotificationsParamsSchema = object({ 
	/**
	* See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
	* for notes on _meta usage.
	*/
_meta: RequestMetaSchema.optional() });
	var NotificationSchema = object({
		method: string(),
		params: NotificationsParamsSchema.loose().optional()
	});
	var ResultSchema = looseObject({ 
	/**
	* See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
	* for notes on _meta usage.
	*/
_meta: RequestMetaSchema.optional() });
	/**
	* A uniquely identifying ID for a request in JSON-RPC.
	*/
	var RequestIdSchema = union([string(), number().int()]);
	/**
	* A request that expects a response.
	*/
	var JSONRPCRequestSchema = object(_objectSpread2({
		jsonrpc: literal("2.0"),
		id: RequestIdSchema
	}, RequestSchema.shape)).strict();
	/**
	* A notification which does not expect a response.
	*/
	var JSONRPCNotificationSchema = object(_objectSpread2({ jsonrpc: literal("2.0") }, NotificationSchema.shape)).strict();
	/**
	* A successful (non-error) response to a request.
	*/
	var JSONRPCResultResponseSchema = object({
		jsonrpc: literal("2.0"),
		id: RequestIdSchema,
		result: ResultSchema
	}).strict();
	/**
	* Error codes defined by the JSON-RPC specification.
	*/
	var ErrorCode;
	(function(ErrorCode) {
		ErrorCode[ErrorCode["ConnectionClosed"] = -32e3] = "ConnectionClosed";
		ErrorCode[ErrorCode["RequestTimeout"] = -32001] = "RequestTimeout";
		ErrorCode[ErrorCode["ParseError"] = -32700] = "ParseError";
		ErrorCode[ErrorCode["InvalidRequest"] = -32600] = "InvalidRequest";
		ErrorCode[ErrorCode["MethodNotFound"] = -32601] = "MethodNotFound";
		ErrorCode[ErrorCode["InvalidParams"] = -32602] = "InvalidParams";
		ErrorCode[ErrorCode["InternalError"] = -32603] = "InternalError";
		ErrorCode[ErrorCode["UrlElicitationRequired"] = -32042] = "UrlElicitationRequired";
	})(ErrorCode || (ErrorCode = {}));
	/**
	* A response to a request that indicates an error occurred.
	*/
	var JSONRPCErrorResponseSchema = object({
		jsonrpc: literal("2.0"),
		id: RequestIdSchema.optional(),
		error: object({
			/**
			* The error type that occurred.
			*/
			code: number().int(),
			/**
			* A short description of the error. The message SHOULD be limited to a concise single sentence.
			*/
			message: string(),
			/**
			* Additional information about the error. The value of this member is defined by the sender (e.g. detailed error information, nested errors etc.).
			*/
			data: unknown().optional()
		})
	}).strict();
	var JSONRPCMessageSchema = union([
		JSONRPCRequestSchema,
		JSONRPCNotificationSchema,
		JSONRPCResultResponseSchema,
		JSONRPCErrorResponseSchema
	]);
	var JSONRPCResponseSchema = union([JSONRPCResultResponseSchema, JSONRPCErrorResponseSchema]);
	/**
	* A response that indicates success but carries no data.
	*/
	var EmptyResultSchema = ResultSchema.strict();
	var CancelledNotificationParamsSchema = NotificationsParamsSchema.extend({
		/**
		* The ID of the request to cancel.
		*
		* This MUST correspond to the ID of a request previously issued in the same direction.
		*/
		requestId: RequestIdSchema.optional(),
		/**
		* An optional string describing the reason for the cancellation. This MAY be logged or presented to the user.
		*/
		reason: string().optional()
	});
	/**
	* This notification can be sent by either side to indicate that it is cancelling a previously-issued request.
	*
	* The request SHOULD still be in-flight, but due to communication latency, it is always possible that this notification MAY arrive after the request has already finished.
	*
	* This notification indicates that the result will be unused, so any associated processing SHOULD cease.
	*
	* A client MUST NOT attempt to cancel its `initialize` request.
	*/
	var CancelledNotificationSchema = NotificationSchema.extend({
		method: literal("notifications/cancelled"),
		params: CancelledNotificationParamsSchema
	});
	/**
	* Icon schema for use in tools, prompts, resources, and implementations.
	*/
	var IconSchema = object({
		/**
		* URL or data URI for the icon.
		*/
		src: string(),
		/**
		* Optional MIME type for the icon.
		*/
		mimeType: string().optional(),
		/**
		* Optional array of strings that specify sizes at which the icon can be used.
		* Each string should be in WxH format (e.g., `"48x48"`, `"96x96"`) or `"any"` for scalable formats like SVG.
		*
		* If not provided, the client should assume that the icon can be used at any size.
		*/
		sizes: array(string()).optional(),
		/**
		* Optional specifier for the theme this icon is designed for. `light` indicates
		* the icon is designed to be used with a light background, and `dark` indicates
		* the icon is designed to be used with a dark background.
		*
		* If not provided, the client should assume the icon can be used with any theme.
		*/
		theme: _enum(["light", "dark"]).optional()
	});
	/**
	* Base schema to add `icons` property.
	*
	*/
	var IconsSchema = object({ 
	/**
	* Optional set of sized icons that the client can display in a user interface.
	*
	* Clients that support rendering icons MUST support at least the following MIME types:
	* - `image/png` - PNG images (safe, universal compatibility)
	* - `image/jpeg` (and `image/jpg`) - JPEG images (safe, universal compatibility)
	*
	* Clients that support rendering icons SHOULD also support:
	* - `image/svg+xml` - SVG images (scalable but requires security precautions)
	* - `image/webp` - WebP images (modern, efficient format)
	*/
icons: array(IconSchema).optional() });
	/**
	* Base metadata interface for common properties across resources, tools, prompts, and implementations.
	*/
	var BaseMetadataSchema = object({
		/** Intended for programmatic or logical use, but used as a display name in past specs or fallback */
		name: string(),
		/**
		* Intended for UI and end-user contexts — optimized to be human-readable and easily understood,
		* even by those unfamiliar with domain-specific terminology.
		*
		* If not provided, the name should be used for display (except for Tool,
		* where `annotations.title` should be given precedence over using `name`,
		* if present).
		*/
		title: string().optional()
	});
	/**
	* Describes the name and version of an MCP implementation.
	*/
	var ImplementationSchema = BaseMetadataSchema.extend(_objectSpread2(_objectSpread2(_objectSpread2({}, BaseMetadataSchema.shape), IconsSchema.shape), {}, {
		version: string(),
		/**
		* An optional URL of the website for this implementation.
		*/
		websiteUrl: string().optional(),
		/**
		* An optional human-readable description of what this implementation does.
		*
		* This can be used by clients or servers to provide context about their purpose
		* and capabilities. For example, a server might describe the types of resources
		* or tools it provides, while a client might describe its intended use case.
		*/
		description: string().optional()
	}));
	var FormElicitationCapabilitySchema = intersection(object({ applyDefaults: boolean().optional() }), record(string(), unknown()));
	var ElicitationCapabilitySchema = preprocess((value) => {
		if (value && typeof value === "object" && !Array.isArray(value)) {
			if (Object.keys(value).length === 0) return { form: {} };
		}
		return value;
	}, intersection(object({
		form: FormElicitationCapabilitySchema.optional(),
		url: AssertObjectSchema.optional()
	}), record(string(), unknown()).optional()));
	/**
	* Task capabilities for clients, indicating which request types support task creation.
	*/
	var ClientTasksCapabilitySchema = looseObject({
		/**
		* Present if the client supports listing tasks.
		*/
		list: AssertObjectSchema.optional(),
		/**
		* Present if the client supports cancelling tasks.
		*/
		cancel: AssertObjectSchema.optional(),
		/**
		* Capabilities for task creation on specific request types.
		*/
		requests: looseObject({
			/**
			* Task support for sampling requests.
			*/
			sampling: looseObject({ createMessage: AssertObjectSchema.optional() }).optional(),
			/**
			* Task support for elicitation requests.
			*/
			elicitation: looseObject({ create: AssertObjectSchema.optional() }).optional()
		}).optional()
	});
	/**
	* Task capabilities for servers, indicating which request types support task creation.
	*/
	var ServerTasksCapabilitySchema = looseObject({
		/**
		* Present if the server supports listing tasks.
		*/
		list: AssertObjectSchema.optional(),
		/**
		* Present if the server supports cancelling tasks.
		*/
		cancel: AssertObjectSchema.optional(),
		/**
		* Capabilities for task creation on specific request types.
		*/
		requests: looseObject({ 
		/**
		* Task support for tool requests.
		*/
tools: looseObject({ call: AssertObjectSchema.optional() }).optional() }).optional()
	});
	/**
	* Capabilities a client may support. Known capabilities are defined here, in this schema, but this is not a closed set: any client can define its own, additional capabilities.
	*/
	var ClientCapabilitiesSchema = object({
		/**
		* Experimental, non-standard capabilities that the client supports.
		*/
		experimental: record(string(), AssertObjectSchema).optional(),
		/**
		* Present if the client supports sampling from an LLM.
		*/
		sampling: object({
			/**
			* Present if the client supports context inclusion via includeContext parameter.
			* If not declared, servers SHOULD only use `includeContext: "none"` (or omit it).
			*/
			context: AssertObjectSchema.optional(),
			/**
			* Present if the client supports tool use via tools and toolChoice parameters.
			*/
			tools: AssertObjectSchema.optional()
		}).optional(),
		/**
		* Present if the client supports eliciting user input.
		*/
		elicitation: ElicitationCapabilitySchema.optional(),
		/**
		* Present if the client supports listing roots.
		*/
		roots: object({ 
		/**
		* Whether the client supports issuing notifications for changes to the roots list.
		*/
listChanged: boolean().optional() }).optional(),
		/**
		* Present if the client supports task creation.
		*/
		tasks: ClientTasksCapabilitySchema.optional()
	});
	var InitializeRequestParamsSchema = BaseRequestParamsSchema.extend({
		/**
		* The latest version of the Model Context Protocol that the client supports. The client MAY decide to support older versions as well.
		*/
		protocolVersion: string(),
		capabilities: ClientCapabilitiesSchema,
		clientInfo: ImplementationSchema
	});
	/**
	* This request is sent from the client to the server when it first connects, asking it to begin initialization.
	*/
	var InitializeRequestSchema = RequestSchema.extend({
		method: literal("initialize"),
		params: InitializeRequestParamsSchema
	});
	/**
	* Capabilities that a server may support. Known capabilities are defined here, in this schema, but this is not a closed set: any server can define its own, additional capabilities.
	*/
	var ServerCapabilitiesSchema = object({
		/**
		* Experimental, non-standard capabilities that the server supports.
		*/
		experimental: record(string(), AssertObjectSchema).optional(),
		/**
		* Present if the server supports sending log messages to the client.
		*/
		logging: AssertObjectSchema.optional(),
		/**
		* Present if the server supports sending completions to the client.
		*/
		completions: AssertObjectSchema.optional(),
		/**
		* Present if the server offers any prompt templates.
		*/
		prompts: object({ 
		/**
		* Whether this server supports issuing notifications for changes to the prompt list.
		*/
listChanged: boolean().optional() }).optional(),
		/**
		* Present if the server offers any resources to read.
		*/
		resources: object({
			/**
			* Whether this server supports clients subscribing to resource updates.
			*/
			subscribe: boolean().optional(),
			/**
			* Whether this server supports issuing notifications for changes to the resource list.
			*/
			listChanged: boolean().optional()
		}).optional(),
		/**
		* Present if the server offers any tools to call.
		*/
		tools: object({ 
		/**
		* Whether this server supports issuing notifications for changes to the tool list.
		*/
listChanged: boolean().optional() }).optional(),
		/**
		* Present if the server supports task creation.
		*/
		tasks: ServerTasksCapabilitySchema.optional()
	});
	/**
	* After receiving an initialize request from the client, the server sends this response.
	*/
	var InitializeResultSchema = ResultSchema.extend({
		/**
		* The version of the Model Context Protocol that the server wants to use. This may not match the version that the client requested. If the client cannot support this version, it MUST disconnect.
		*/
		protocolVersion: string(),
		capabilities: ServerCapabilitiesSchema,
		serverInfo: ImplementationSchema,
		/**
		* Instructions describing how to use the server and its features.
		*
		* This can be used by clients to improve the LLM's understanding of available tools, resources, etc. It can be thought of like a "hint" to the model. For example, this information MAY be added to the system prompt.
		*/
		instructions: string().optional()
	});
	/**
	* This notification is sent from the client to the server after initialization has finished.
	*/
	var InitializedNotificationSchema = NotificationSchema.extend({
		method: literal("notifications/initialized"),
		params: NotificationsParamsSchema.optional()
	});
	/**
	* A ping, issued by either the server or the client, to check that the other party is still alive. The receiver must promptly respond, or else may be disconnected.
	*/
	var PingRequestSchema = RequestSchema.extend({
		method: literal("ping"),
		params: BaseRequestParamsSchema.optional()
	});
	var ProgressSchema = object({
		/**
		* The progress thus far. This should increase every time progress is made, even if the total is unknown.
		*/
		progress: number(),
		/**
		* Total number of items to process (or total progress required), if known.
		*/
		total: optional(number()),
		/**
		* An optional message describing the current progress.
		*/
		message: optional(string())
	});
	var ProgressNotificationParamsSchema = object(_objectSpread2(_objectSpread2(_objectSpread2({}, NotificationsParamsSchema.shape), ProgressSchema.shape), {}, { 
	/**
	* The progress token which was given in the initial request, used to associate this notification with the request that is proceeding.
	*/
progressToken: ProgressTokenSchema }));
	/**
	* An out-of-band notification used to inform the receiver of a progress update for a long-running request.
	*
	* @category notifications/progress
	*/
	var ProgressNotificationSchema = NotificationSchema.extend({
		method: literal("notifications/progress"),
		params: ProgressNotificationParamsSchema
	});
	var PaginatedRequestParamsSchema = BaseRequestParamsSchema.extend({ 
	/**
	* An opaque token representing the current pagination position.
	* If provided, the server should return results starting after this cursor.
	*/
cursor: CursorSchema.optional() });
	var PaginatedRequestSchema = RequestSchema.extend({ params: PaginatedRequestParamsSchema.optional() });
	var PaginatedResultSchema = ResultSchema.extend({ 
	/**
	* An opaque token representing the pagination position after the last returned result.
	* If present, there may be more results available.
	*/
nextCursor: CursorSchema.optional() });
	/**
	* The status of a task.
	* */
	var TaskStatusSchema = _enum([
		"working",
		"input_required",
		"completed",
		"failed",
		"cancelled"
	]);
	/**
	* A pollable state object associated with a request.
	*/
	var TaskSchema = object({
		taskId: string(),
		status: TaskStatusSchema,
		/**
		* Time in milliseconds to keep task results available after completion.
		* If null, the task has unlimited lifetime until manually cleaned up.
		*/
		ttl: union([number(), _null()]),
		/**
		* ISO 8601 timestamp when the task was created.
		*/
		createdAt: string(),
		/**
		* ISO 8601 timestamp when the task was last updated.
		*/
		lastUpdatedAt: string(),
		pollInterval: optional(number()),
		/**
		* Optional diagnostic message for failed tasks or other status information.
		*/
		statusMessage: optional(string())
	});
	/**
	* Result returned when a task is created, containing the task data wrapped in a task field.
	*/
	var CreateTaskResultSchema = ResultSchema.extend({ task: TaskSchema });
	/**
	* Parameters for task status notification.
	*/
	var TaskStatusNotificationParamsSchema = NotificationsParamsSchema.merge(TaskSchema);
	/**
	* A notification sent when a task's status changes.
	*/
	var TaskStatusNotificationSchema = NotificationSchema.extend({
		method: literal("notifications/tasks/status"),
		params: TaskStatusNotificationParamsSchema
	});
	/**
	* A request to get the state of a specific task.
	*/
	var GetTaskRequestSchema = RequestSchema.extend({
		method: literal("tasks/get"),
		params: BaseRequestParamsSchema.extend({ taskId: string() })
	});
	/**
	* The response to a tasks/get request.
	*/
	var GetTaskResultSchema = ResultSchema.merge(TaskSchema);
	/**
	* A request to get the result of a specific task.
	*/
	var GetTaskPayloadRequestSchema = RequestSchema.extend({
		method: literal("tasks/result"),
		params: BaseRequestParamsSchema.extend({ taskId: string() })
	});
	/**
	* The response to a tasks/result request.
	* The structure matches the result type of the original request.
	* For example, a tools/call task would return the CallToolResult structure.
	*
	*/
	var GetTaskPayloadResultSchema = ResultSchema.loose();
	/**
	* A request to list tasks.
	*/
	var ListTasksRequestSchema = PaginatedRequestSchema.extend({ method: literal("tasks/list") });
	/**
	* The response to a tasks/list request.
	*/
	var ListTasksResultSchema = PaginatedResultSchema.extend({ tasks: array(TaskSchema) });
	/**
	* A request to cancel a specific task.
	*/
	var CancelTaskRequestSchema = RequestSchema.extend({
		method: literal("tasks/cancel"),
		params: BaseRequestParamsSchema.extend({ taskId: string() })
	});
	/**
	* The response to a tasks/cancel request.
	*/
	var CancelTaskResultSchema = ResultSchema.merge(TaskSchema);
	/**
	* The contents of a specific resource or sub-resource.
	*/
	var ResourceContentsSchema = object({
		/**
		* The URI of this resource.
		*/
		uri: string(),
		/**
		* The MIME type of this resource, if known.
		*/
		mimeType: optional(string()),
		/**
		* See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
		* for notes on _meta usage.
		*/
		_meta: record(string(), unknown()).optional()
	});
	var TextResourceContentsSchema = ResourceContentsSchema.extend({ 
	/**
	* The text of the item. This must only be set if the item can actually be represented as text (not binary data).
	*/
text: string() });
	/**
	* A Zod schema for validating Base64 strings that is more performant and
	* robust for very large inputs than the default regex-based check. It avoids
	* stack overflows by using the native `atob` function for validation.
	*/
	var Base64Schema = string().refine((val) => {
		try {
			atob(val);
			return true;
		} catch (_unused) {
			return false;
		}
	}, { message: "Invalid Base64 string" });
	var BlobResourceContentsSchema = ResourceContentsSchema.extend({ 
	/**
	* A base64-encoded string representing the binary data of the item.
	*/
blob: Base64Schema });
	/**
	* The sender or recipient of messages and data in a conversation.
	*/
	var RoleSchema = _enum(["user", "assistant"]);
	/**
	* Optional annotations providing clients additional context about a resource.
	*/
	var AnnotationsSchema = object({
		/**
		* Intended audience(s) for the resource.
		*/
		audience: array(RoleSchema).optional(),
		/**
		* Importance hint for the resource, from 0 (least) to 1 (most).
		*/
		priority: number().min(0).max(1).optional(),
		/**
		* ISO 8601 timestamp for the most recent modification.
		*/
		lastModified: datetime({ offset: true }).optional()
	});
	/**
	* A known resource that the server is capable of reading.
	*/
	var ResourceSchema = object(_objectSpread2(_objectSpread2(_objectSpread2({}, BaseMetadataSchema.shape), IconsSchema.shape), {}, {
		/**
		* The URI of this resource.
		*/
		uri: string(),
		/**
		* A description of what this resource represents.
		*
		* This can be used by clients to improve the LLM's understanding of available resources. It can be thought of like a "hint" to the model.
		*/
		description: optional(string()),
		/**
		* The MIME type of this resource, if known.
		*/
		mimeType: optional(string()),
		/**
		* Optional annotations for the client.
		*/
		annotations: AnnotationsSchema.optional(),
		/**
		* See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
		* for notes on _meta usage.
		*/
		_meta: optional(looseObject({}))
	}));
	/**
	* A template description for resources available on the server.
	*/
	var ResourceTemplateSchema = object(_objectSpread2(_objectSpread2(_objectSpread2({}, BaseMetadataSchema.shape), IconsSchema.shape), {}, {
		/**
		* A URI template (according to RFC 6570) that can be used to construct resource URIs.
		*/
		uriTemplate: string(),
		/**
		* A description of what this template is for.
		*
		* This can be used by clients to improve the LLM's understanding of available resources. It can be thought of like a "hint" to the model.
		*/
		description: optional(string()),
		/**
		* The MIME type for all resources that match this template. This should only be included if all resources matching this template have the same type.
		*/
		mimeType: optional(string()),
		/**
		* Optional annotations for the client.
		*/
		annotations: AnnotationsSchema.optional(),
		/**
		* See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
		* for notes on _meta usage.
		*/
		_meta: optional(looseObject({}))
	}));
	/**
	* Sent from the client to request a list of resources the server has.
	*/
	var ListResourcesRequestSchema = PaginatedRequestSchema.extend({ method: literal("resources/list") });
	/**
	* The server's response to a resources/list request from the client.
	*/
	var ListResourcesResultSchema = PaginatedResultSchema.extend({ resources: array(ResourceSchema) });
	/**
	* Sent from the client to request a list of resource templates the server has.
	*/
	var ListResourceTemplatesRequestSchema = PaginatedRequestSchema.extend({ method: literal("resources/templates/list") });
	/**
	* The server's response to a resources/templates/list request from the client.
	*/
	var ListResourceTemplatesResultSchema = PaginatedResultSchema.extend({ resourceTemplates: array(ResourceTemplateSchema) });
	var ResourceRequestParamsSchema = BaseRequestParamsSchema.extend({ 
	/**
	* The URI of the resource to read. The URI can use any protocol; it is up to the server how to interpret it.
	*
	* @format uri
	*/
uri: string() });
	/**
	* Parameters for a `resources/read` request.
	*/
	var ReadResourceRequestParamsSchema = ResourceRequestParamsSchema;
	/**
	* Sent from the client to the server, to read a specific resource URI.
	*/
	var ReadResourceRequestSchema = RequestSchema.extend({
		method: literal("resources/read"),
		params: ReadResourceRequestParamsSchema
	});
	/**
	* The server's response to a resources/read request from the client.
	*/
	var ReadResourceResultSchema = ResultSchema.extend({ contents: array(union([TextResourceContentsSchema, BlobResourceContentsSchema])) });
	/**
	* An optional notification from the server to the client, informing it that the list of resources it can read from has changed. This may be issued by servers without any previous subscription from the client.
	*/
	var ResourceListChangedNotificationSchema = NotificationSchema.extend({
		method: literal("notifications/resources/list_changed"),
		params: NotificationsParamsSchema.optional()
	});
	var SubscribeRequestParamsSchema = ResourceRequestParamsSchema;
	/**
	* Sent from the client to request resources/updated notifications from the server whenever a particular resource changes.
	*/
	var SubscribeRequestSchema = RequestSchema.extend({
		method: literal("resources/subscribe"),
		params: SubscribeRequestParamsSchema
	});
	var UnsubscribeRequestParamsSchema = ResourceRequestParamsSchema;
	/**
	* Sent from the client to request cancellation of resources/updated notifications from the server. This should follow a previous resources/subscribe request.
	*/
	var UnsubscribeRequestSchema = RequestSchema.extend({
		method: literal("resources/unsubscribe"),
		params: UnsubscribeRequestParamsSchema
	});
	/**
	* Parameters for a `notifications/resources/updated` notification.
	*/
	var ResourceUpdatedNotificationParamsSchema = NotificationsParamsSchema.extend({ 
	/**
	* The URI of the resource that has been updated. This might be a sub-resource of the one that the client actually subscribed to.
	*/
uri: string() });
	/**
	* A notification from the server to the client, informing it that a resource has changed and may need to be read again. This should only be sent if the client previously sent a resources/subscribe request.
	*/
	var ResourceUpdatedNotificationSchema = NotificationSchema.extend({
		method: literal("notifications/resources/updated"),
		params: ResourceUpdatedNotificationParamsSchema
	});
	/**
	* Describes an argument that a prompt can accept.
	*/
	var PromptArgumentSchema = object({
		/**
		* The name of the argument.
		*/
		name: string(),
		/**
		* A human-readable description of the argument.
		*/
		description: optional(string()),
		/**
		* Whether this argument must be provided.
		*/
		required: optional(boolean())
	});
	/**
	* A prompt or prompt template that the server offers.
	*/
	var PromptSchema = object(_objectSpread2(_objectSpread2(_objectSpread2({}, BaseMetadataSchema.shape), IconsSchema.shape), {}, {
		/**
		* An optional description of what this prompt provides
		*/
		description: optional(string()),
		/**
		* A list of arguments to use for templating the prompt.
		*/
		arguments: optional(array(PromptArgumentSchema)),
		/**
		* See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
		* for notes on _meta usage.
		*/
		_meta: optional(looseObject({}))
	}));
	/**
	* Sent from the client to request a list of prompts and prompt templates the server has.
	*/
	var ListPromptsRequestSchema = PaginatedRequestSchema.extend({ method: literal("prompts/list") });
	/**
	* The server's response to a prompts/list request from the client.
	*/
	var ListPromptsResultSchema = PaginatedResultSchema.extend({ prompts: array(PromptSchema) });
	/**
	* Parameters for a `prompts/get` request.
	*/
	var GetPromptRequestParamsSchema = BaseRequestParamsSchema.extend({
		/**
		* The name of the prompt or prompt template.
		*/
		name: string(),
		/**
		* Arguments to use for templating the prompt.
		*/
		arguments: record(string(), string()).optional()
	});
	/**
	* Used by the client to get a prompt provided by the server.
	*/
	var GetPromptRequestSchema = RequestSchema.extend({
		method: literal("prompts/get"),
		params: GetPromptRequestParamsSchema
	});
	/**
	* Text provided to or from an LLM.
	*/
	var TextContentSchema = object({
		type: literal("text"),
		/**
		* The text content of the message.
		*/
		text: string(),
		/**
		* Optional annotations for the client.
		*/
		annotations: AnnotationsSchema.optional(),
		/**
		* See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
		* for notes on _meta usage.
		*/
		_meta: record(string(), unknown()).optional()
	});
	/**
	* An image provided to or from an LLM.
	*/
	var ImageContentSchema = object({
		type: literal("image"),
		/**
		* The base64-encoded image data.
		*/
		data: Base64Schema,
		/**
		* The MIME type of the image. Different providers may support different image types.
		*/
		mimeType: string(),
		/**
		* Optional annotations for the client.
		*/
		annotations: AnnotationsSchema.optional(),
		/**
		* See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
		* for notes on _meta usage.
		*/
		_meta: record(string(), unknown()).optional()
	});
	/**
	* An Audio provided to or from an LLM.
	*/
	var AudioContentSchema = object({
		type: literal("audio"),
		/**
		* The base64-encoded audio data.
		*/
		data: Base64Schema,
		/**
		* The MIME type of the audio. Different providers may support different audio types.
		*/
		mimeType: string(),
		/**
		* Optional annotations for the client.
		*/
		annotations: AnnotationsSchema.optional(),
		/**
		* See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
		* for notes on _meta usage.
		*/
		_meta: record(string(), unknown()).optional()
	});
	/**
	* A tool call request from an assistant (LLM).
	* Represents the assistant's request to use a tool.
	*/
	var ToolUseContentSchema = object({
		type: literal("tool_use"),
		/**
		* The name of the tool to invoke.
		* Must match a tool name from the request's tools array.
		*/
		name: string(),
		/**
		* Unique identifier for this tool call.
		* Used to correlate with ToolResultContent in subsequent messages.
		*/
		id: string(),
		/**
		* Arguments to pass to the tool.
		* Must conform to the tool's inputSchema.
		*/
		input: record(string(), unknown()),
		/**
		* See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
		* for notes on _meta usage.
		*/
		_meta: record(string(), unknown()).optional()
	});
	/**
	* The contents of a resource, embedded into a prompt or tool call result.
	*/
	var EmbeddedResourceSchema = object({
		type: literal("resource"),
		resource: union([TextResourceContentsSchema, BlobResourceContentsSchema]),
		/**
		* Optional annotations for the client.
		*/
		annotations: AnnotationsSchema.optional(),
		/**
		* See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
		* for notes on _meta usage.
		*/
		_meta: record(string(), unknown()).optional()
	});
	/**
	* A resource that the server is capable of reading, included in a prompt or tool call result.
	*
	* Note: resource links returned by tools are not guaranteed to appear in the results of `resources/list` requests.
	*/
	var ResourceLinkSchema = ResourceSchema.extend({ type: literal("resource_link") });
	/**
	* A content block that can be used in prompts and tool results.
	*/
	var ContentBlockSchema = union([
		TextContentSchema,
		ImageContentSchema,
		AudioContentSchema,
		ResourceLinkSchema,
		EmbeddedResourceSchema
	]);
	/**
	* Describes a message returned as part of a prompt.
	*/
	var PromptMessageSchema = object({
		role: RoleSchema,
		content: ContentBlockSchema
	});
	/**
	* The server's response to a prompts/get request from the client.
	*/
	var GetPromptResultSchema = ResultSchema.extend({
		/**
		* An optional description for the prompt.
		*/
		description: string().optional(),
		messages: array(PromptMessageSchema)
	});
	/**
	* An optional notification from the server to the client, informing it that the list of prompts it offers has changed. This may be issued by servers without any previous subscription from the client.
	*/
	var PromptListChangedNotificationSchema = NotificationSchema.extend({
		method: literal("notifications/prompts/list_changed"),
		params: NotificationsParamsSchema.optional()
	});
	/**
	* Additional properties describing a Tool to clients.
	*
	* NOTE: all properties in ToolAnnotations are **hints**.
	* They are not guaranteed to provide a faithful description of
	* tool behavior (including descriptive properties like `title`).
	*
	* Clients should never make tool use decisions based on ToolAnnotations
	* received from untrusted servers.
	*/
	var ToolAnnotationsSchema = object({
		/**
		* A human-readable title for the tool.
		*/
		title: string().optional(),
		/**
		* If true, the tool does not modify its environment.
		*
		* Default: false
		*/
		readOnlyHint: boolean().optional(),
		/**
		* If true, the tool may perform destructive updates to its environment.
		* If false, the tool performs only additive updates.
		*
		* (This property is meaningful only when `readOnlyHint == false`)
		*
		* Default: true
		*/
		destructiveHint: boolean().optional(),
		/**
		* If true, calling the tool repeatedly with the same arguments
		* will have no additional effect on the its environment.
		*
		* (This property is meaningful only when `readOnlyHint == false`)
		*
		* Default: false
		*/
		idempotentHint: boolean().optional(),
		/**
		* If true, this tool may interact with an "open world" of external
		* entities. If false, the tool's domain of interaction is closed.
		* For example, the world of a web search tool is open, whereas that
		* of a memory tool is not.
		*
		* Default: true
		*/
		openWorldHint: boolean().optional()
	});
	/**
	* Execution-related properties for a tool.
	*/
	var ToolExecutionSchema = object({ 
	/**
	* Indicates the tool's preference for task-augmented execution.
	* - "required": Clients MUST invoke the tool as a task
	* - "optional": Clients MAY invoke the tool as a task or normal request
	* - "forbidden": Clients MUST NOT attempt to invoke the tool as a task
	*
	* If not present, defaults to "forbidden".
	*/
taskSupport: _enum([
		"required",
		"optional",
		"forbidden"
	]).optional() });
	/**
	* Definition for a tool the client can call.
	*/
	var ToolSchema = object(_objectSpread2(_objectSpread2(_objectSpread2({}, BaseMetadataSchema.shape), IconsSchema.shape), {}, {
		/**
		* A human-readable description of the tool.
		*/
		description: string().optional(),
		/**
		* A JSON Schema 2020-12 object defining the expected parameters for the tool.
		* Must have type: 'object' at the root level per MCP spec.
		*/
		inputSchema: object({
			type: literal("object"),
			properties: record(string(), AssertObjectSchema).optional(),
			required: array(string()).optional()
		}).catchall(unknown()),
		/**
		* An optional JSON Schema 2020-12 object defining the structure of the tool's output
		* returned in the structuredContent field of a CallToolResult.
		* Must have type: 'object' at the root level per MCP spec.
		*/
		outputSchema: object({
			type: literal("object"),
			properties: record(string(), AssertObjectSchema).optional(),
			required: array(string()).optional()
		}).catchall(unknown()).optional(),
		/**
		* Optional additional tool information.
		*/
		annotations: ToolAnnotationsSchema.optional(),
		/**
		* Execution-related properties for this tool.
		*/
		execution: ToolExecutionSchema.optional(),
		/**
		* See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
		* for notes on _meta usage.
		*/
		_meta: record(string(), unknown()).optional()
	}));
	/**
	* Sent from the client to request a list of tools the server has.
	*/
	var ListToolsRequestSchema = PaginatedRequestSchema.extend({ method: literal("tools/list") });
	/**
	* The server's response to a tools/list request from the client.
	*/
	var ListToolsResultSchema = PaginatedResultSchema.extend({ tools: array(ToolSchema) });
	/**
	* The server's response to a tool call.
	*/
	var CallToolResultSchema = ResultSchema.extend({
		/**
		* A list of content objects that represent the result of the tool call.
		*
		* If the Tool does not define an outputSchema, this field MUST be present in the result.
		* For backwards compatibility, this field is always present, but it may be empty.
		*/
		content: array(ContentBlockSchema).default([]),
		/**
		* An object containing structured tool output.
		*
		* If the Tool defines an outputSchema, this field MUST be present in the result, and contain a JSON object that matches the schema.
		*/
		structuredContent: record(string(), unknown()).optional(),
		/**
		* Whether the tool call ended in an error.
		*
		* If not set, this is assumed to be false (the call was successful).
		*
		* Any errors that originate from the tool SHOULD be reported inside the result
		* object, with `isError` set to true, _not_ as an MCP protocol-level error
		* response. Otherwise, the LLM would not be able to see that an error occurred
		* and self-correct.
		*
		* However, any errors in _finding_ the tool, an error indicating that the
		* server does not support tool calls, or any other exceptional conditions,
		* should be reported as an MCP error response.
		*/
		isError: boolean().optional()
	});
	/**
	* CallToolResultSchema extended with backwards compatibility to protocol version 2024-10-07.
	*/
	var CompatibilityCallToolResultSchema = CallToolResultSchema.or(ResultSchema.extend({ toolResult: unknown() }));
	/**
	* Parameters for a `tools/call` request.
	*/
	var CallToolRequestParamsSchema = TaskAugmentedRequestParamsSchema.extend({
		/**
		* The name of the tool to call.
		*/
		name: string(),
		/**
		* Arguments to pass to the tool.
		*/
		arguments: record(string(), unknown()).optional()
	});
	/**
	* Used by the client to invoke a tool provided by the server.
	*/
	var CallToolRequestSchema = RequestSchema.extend({
		method: literal("tools/call"),
		params: CallToolRequestParamsSchema
	});
	/**
	* An optional notification from the server to the client, informing it that the list of tools it offers has changed. This may be issued by servers without any previous subscription from the client.
	*/
	var ToolListChangedNotificationSchema = NotificationSchema.extend({
		method: literal("notifications/tools/list_changed"),
		params: NotificationsParamsSchema.optional()
	});
	/**
	* Base schema for list changed subscription options (without callback).
	* Used internally for Zod validation of autoRefresh and debounceMs.
	*/
	var ListChangedOptionsBaseSchema = object({
		/**
		* If true, the list will be refreshed automatically when a list changed notification is received.
		* The callback will be called with the updated list.
		*
		* If false, the callback will be called with null items, allowing manual refresh.
		*
		* @default true
		*/
		autoRefresh: boolean().default(true),
		/**
		* Debounce time in milliseconds for list changed notification processing.
		*
		* Multiple notifications received within this timeframe will only trigger one refresh.
		* Set to 0 to disable debouncing.
		*
		* @default 300
		*/
		debounceMs: number().int().nonnegative().default(300)
	});
	/**
	* The severity of a log message.
	*/
	var LoggingLevelSchema = _enum([
		"debug",
		"info",
		"notice",
		"warning",
		"error",
		"critical",
		"alert",
		"emergency"
	]);
	/**
	* Parameters for a `logging/setLevel` request.
	*/
	var SetLevelRequestParamsSchema = BaseRequestParamsSchema.extend({ 
	/**
	* The level of logging that the client wants to receive from the server. The server should send all logs at this level and higher (i.e., more severe) to the client as notifications/logging/message.
	*/
level: LoggingLevelSchema });
	/**
	* A request from the client to the server, to enable or adjust logging.
	*/
	var SetLevelRequestSchema = RequestSchema.extend({
		method: literal("logging/setLevel"),
		params: SetLevelRequestParamsSchema
	});
	/**
	* Parameters for a `notifications/message` notification.
	*/
	var LoggingMessageNotificationParamsSchema = NotificationsParamsSchema.extend({
		/**
		* The severity of this log message.
		*/
		level: LoggingLevelSchema,
		/**
		* An optional name of the logger issuing this message.
		*/
		logger: string().optional(),
		/**
		* The data to be logged, such as a string message or an object. Any JSON serializable type is allowed here.
		*/
		data: unknown()
	});
	/**
	* Notification of a log message passed from server to client. If no logging/setLevel request has been sent from the client, the server MAY decide which messages to send automatically.
	*/
	var LoggingMessageNotificationSchema = NotificationSchema.extend({
		method: literal("notifications/message"),
		params: LoggingMessageNotificationParamsSchema
	});
	/**
	* Hints to use for model selection.
	*/
	var ModelHintSchema = object({ 
	/**
	* A hint for a model name.
	*/
name: string().optional() });
	/**
	* The server's preferences for model selection, requested of the client during sampling.
	*/
	var ModelPreferencesSchema = object({
		/**
		* Optional hints to use for model selection.
		*/
		hints: array(ModelHintSchema).optional(),
		/**
		* How much to prioritize cost when selecting a model.
		*/
		costPriority: number().min(0).max(1).optional(),
		/**
		* How much to prioritize sampling speed (latency) when selecting a model.
		*/
		speedPriority: number().min(0).max(1).optional(),
		/**
		* How much to prioritize intelligence and capabilities when selecting a model.
		*/
		intelligencePriority: number().min(0).max(1).optional()
	});
	/**
	* Controls tool usage behavior in sampling requests.
	*/
	var ToolChoiceSchema = object({ 
	/**
	* Controls when tools are used:
	* - "auto": Model decides whether to use tools (default)
	* - "required": Model MUST use at least one tool before completing
	* - "none": Model MUST NOT use any tools
	*/
mode: _enum([
		"auto",
		"required",
		"none"
	]).optional() });
	/**
	* The result of a tool execution, provided by the user (server).
	* Represents the outcome of invoking a tool requested via ToolUseContent.
	*/
	var ToolResultContentSchema = object({
		type: literal("tool_result"),
		toolUseId: string().describe("The unique identifier for the corresponding tool call."),
		content: array(ContentBlockSchema).default([]),
		structuredContent: object({}).loose().optional(),
		isError: boolean().optional(),
		/**
		* See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
		* for notes on _meta usage.
		*/
		_meta: record(string(), unknown()).optional()
	});
	/**
	* Basic content types for sampling responses (without tool use).
	* Used for backwards-compatible CreateMessageResult when tools are not used.
	*/
	var SamplingContentSchema = discriminatedUnion("type", [
		TextContentSchema,
		ImageContentSchema,
		AudioContentSchema
	]);
	/**
	* Content block types allowed in sampling messages.
	* This includes text, image, audio, tool use requests, and tool results.
	*/
	var SamplingMessageContentBlockSchema = discriminatedUnion("type", [
		TextContentSchema,
		ImageContentSchema,
		AudioContentSchema,
		ToolUseContentSchema,
		ToolResultContentSchema
	]);
	/**
	* Describes a message issued to or received from an LLM API.
	*/
	var SamplingMessageSchema = object({
		role: RoleSchema,
		content: union([SamplingMessageContentBlockSchema, array(SamplingMessageContentBlockSchema)]),
		/**
		* See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
		* for notes on _meta usage.
		*/
		_meta: record(string(), unknown()).optional()
	});
	/**
	* Parameters for a `sampling/createMessage` request.
	*/
	var CreateMessageRequestParamsSchema = TaskAugmentedRequestParamsSchema.extend({
		messages: array(SamplingMessageSchema),
		/**
		* The server's preferences for which model to select. The client MAY modify or omit this request.
		*/
		modelPreferences: ModelPreferencesSchema.optional(),
		/**
		* An optional system prompt the server wants to use for sampling. The client MAY modify or omit this prompt.
		*/
		systemPrompt: string().optional(),
		/**
		* A request to include context from one or more MCP servers (including the caller), to be attached to the prompt.
		* The client MAY ignore this request.
		*
		* Default is "none". Values "thisServer" and "allServers" are soft-deprecated. Servers SHOULD only use these values if the client
		* declares ClientCapabilities.sampling.context. These values may be removed in future spec releases.
		*/
		includeContext: _enum([
			"none",
			"thisServer",
			"allServers"
		]).optional(),
		temperature: number().optional(),
		/**
		* The requested maximum number of tokens to sample (to prevent runaway completions).
		*
		* The client MAY choose to sample fewer tokens than the requested maximum.
		*/
		maxTokens: number().int(),
		stopSequences: array(string()).optional(),
		/**
		* Optional metadata to pass through to the LLM provider. The format of this metadata is provider-specific.
		*/
		metadata: AssertObjectSchema.optional(),
		/**
		* Tools that the model may use during generation.
		* The client MUST return an error if this field is provided but ClientCapabilities.sampling.tools is not declared.
		*/
		tools: array(ToolSchema).optional(),
		/**
		* Controls how the model uses tools.
		* The client MUST return an error if this field is provided but ClientCapabilities.sampling.tools is not declared.
		* Default is `{ mode: "auto" }`.
		*/
		toolChoice: ToolChoiceSchema.optional()
	});
	/**
	* A request from the server to sample an LLM via the client. The client has full discretion over which model to select. The client should also inform the user before beginning sampling, to allow them to inspect the request (human in the loop) and decide whether to approve it.
	*/
	var CreateMessageRequestSchema = RequestSchema.extend({
		method: literal("sampling/createMessage"),
		params: CreateMessageRequestParamsSchema
	});
	/**
	* The client's response to a sampling/create_message request from the server.
	* This is the backwards-compatible version that returns single content (no arrays).
	* Used when the request does not include tools.
	*/
	var CreateMessageResultSchema = ResultSchema.extend({
		/**
		* The name of the model that generated the message.
		*/
		model: string(),
		/**
		* The reason why sampling stopped, if known.
		*
		* Standard values:
		* - "endTurn": Natural end of the assistant's turn
		* - "stopSequence": A stop sequence was encountered
		* - "maxTokens": Maximum token limit was reached
		*
		* This field is an open string to allow for provider-specific stop reasons.
		*/
		stopReason: optional(_enum([
			"endTurn",
			"stopSequence",
			"maxTokens"
		]).or(string())),
		role: RoleSchema,
		/**
		* Response content. Single content block (text, image, or audio).
		*/
		content: SamplingContentSchema
	});
	/**
	* The client's response to a sampling/create_message request when tools were provided.
	* This version supports array content for tool use flows.
	*/
	var CreateMessageResultWithToolsSchema = ResultSchema.extend({
		/**
		* The name of the model that generated the message.
		*/
		model: string(),
		/**
		* The reason why sampling stopped, if known.
		*
		* Standard values:
		* - "endTurn": Natural end of the assistant's turn
		* - "stopSequence": A stop sequence was encountered
		* - "maxTokens": Maximum token limit was reached
		* - "toolUse": The model wants to use one or more tools
		*
		* This field is an open string to allow for provider-specific stop reasons.
		*/
		stopReason: optional(_enum([
			"endTurn",
			"stopSequence",
			"maxTokens",
			"toolUse"
		]).or(string())),
		role: RoleSchema,
		/**
		* Response content. May be a single block or array. May include ToolUseContent if stopReason is "toolUse".
		*/
		content: union([SamplingMessageContentBlockSchema, array(SamplingMessageContentBlockSchema)])
	});
	/**
	* Primitive schema definition for boolean fields.
	*/
	var BooleanSchemaSchema = object({
		type: literal("boolean"),
		title: string().optional(),
		description: string().optional(),
		default: boolean().optional()
	});
	/**
	* Primitive schema definition for string fields.
	*/
	var StringSchemaSchema = object({
		type: literal("string"),
		title: string().optional(),
		description: string().optional(),
		minLength: number().optional(),
		maxLength: number().optional(),
		format: _enum([
			"email",
			"uri",
			"date",
			"date-time"
		]).optional(),
		default: string().optional()
	});
	/**
	* Primitive schema definition for number fields.
	*/
	var NumberSchemaSchema = object({
		type: _enum(["number", "integer"]),
		title: string().optional(),
		description: string().optional(),
		minimum: number().optional(),
		maximum: number().optional(),
		default: number().optional()
	});
	/**
	* Schema for single-selection enumeration without display titles for options.
	*/
	var UntitledSingleSelectEnumSchemaSchema = object({
		type: literal("string"),
		title: string().optional(),
		description: string().optional(),
		enum: array(string()),
		default: string().optional()
	});
	/**
	* Schema for single-selection enumeration with display titles for each option.
	*/
	var TitledSingleSelectEnumSchemaSchema = object({
		type: literal("string"),
		title: string().optional(),
		description: string().optional(),
		oneOf: array(object({
			const: string(),
			title: string()
		})),
		default: string().optional()
	});
	/**
	* Use TitledSingleSelectEnumSchema instead.
	* This interface will be removed in a future version.
	*/
	var LegacyTitledEnumSchemaSchema = object({
		type: literal("string"),
		title: string().optional(),
		description: string().optional(),
		enum: array(string()),
		enumNames: array(string()).optional(),
		default: string().optional()
	});
	var SingleSelectEnumSchemaSchema = union([UntitledSingleSelectEnumSchemaSchema, TitledSingleSelectEnumSchemaSchema]);
	/**
	* Schema for multiple-selection enumeration without display titles for options.
	*/
	var UntitledMultiSelectEnumSchemaSchema = object({
		type: literal("array"),
		title: string().optional(),
		description: string().optional(),
		minItems: number().optional(),
		maxItems: number().optional(),
		items: object({
			type: literal("string"),
			enum: array(string())
		}),
		default: array(string()).optional()
	});
	/**
	* Schema for multiple-selection enumeration with display titles for each option.
	*/
	var TitledMultiSelectEnumSchemaSchema = object({
		type: literal("array"),
		title: string().optional(),
		description: string().optional(),
		minItems: number().optional(),
		maxItems: number().optional(),
		items: object({ anyOf: array(object({
			const: string(),
			title: string()
		})) }),
		default: array(string()).optional()
	});
	/**
	* Combined schema for multiple-selection enumeration
	*/
	var MultiSelectEnumSchemaSchema = union([UntitledMultiSelectEnumSchemaSchema, TitledMultiSelectEnumSchemaSchema]);
	/**
	* Primitive schema definition for enum fields.
	*/
	var EnumSchemaSchema = union([
		LegacyTitledEnumSchemaSchema,
		SingleSelectEnumSchemaSchema,
		MultiSelectEnumSchemaSchema
	]);
	/**
	* Union of all primitive schema definitions.
	*/
	var PrimitiveSchemaDefinitionSchema = union([
		EnumSchemaSchema,
		BooleanSchemaSchema,
		StringSchemaSchema,
		NumberSchemaSchema
	]);
	/**
	* Parameters for an `elicitation/create` request for form-based elicitation.
	*/
	var ElicitRequestFormParamsSchema = TaskAugmentedRequestParamsSchema.extend({
		/**
		* The elicitation mode.
		*
		* Optional for backward compatibility. Clients MUST treat missing mode as "form".
		*/
		mode: literal("form").optional(),
		/**
		* The message to present to the user describing what information is being requested.
		*/
		message: string(),
		/**
		* A restricted subset of JSON Schema.
		* Only top-level properties are allowed, without nesting.
		*/
		requestedSchema: object({
			type: literal("object"),
			properties: record(string(), PrimitiveSchemaDefinitionSchema),
			required: array(string()).optional()
		})
	});
	/**
	* Parameters for an `elicitation/create` request for URL-based elicitation.
	*/
	var ElicitRequestURLParamsSchema = TaskAugmentedRequestParamsSchema.extend({
		/**
		* The elicitation mode.
		*/
		mode: literal("url"),
		/**
		* The message to present to the user explaining why the interaction is needed.
		*/
		message: string(),
		/**
		* The ID of the elicitation, which must be unique within the context of the server.
		* The client MUST treat this ID as an opaque value.
		*/
		elicitationId: string(),
		/**
		* The URL that the user should navigate to.
		*/
		url: string().url()
	});
	/**
	* The parameters for a request to elicit additional information from the user via the client.
	*/
	var ElicitRequestParamsSchema = union([ElicitRequestFormParamsSchema, ElicitRequestURLParamsSchema]);
	/**
	* A request from the server to elicit user input via the client.
	* The client should present the message and form fields to the user (form mode)
	* or navigate to a URL (URL mode).
	*/
	var ElicitRequestSchema = RequestSchema.extend({
		method: literal("elicitation/create"),
		params: ElicitRequestParamsSchema
	});
	/**
	* Parameters for a `notifications/elicitation/complete` notification.
	*
	* @category notifications/elicitation/complete
	*/
	var ElicitationCompleteNotificationParamsSchema = NotificationsParamsSchema.extend({ 
	/**
	* The ID of the elicitation that completed.
	*/
elicitationId: string() });
	/**
	* A notification from the server to the client, informing it of a completion of an out-of-band elicitation request.
	*
	* @category notifications/elicitation/complete
	*/
	var ElicitationCompleteNotificationSchema = NotificationSchema.extend({
		method: literal("notifications/elicitation/complete"),
		params: ElicitationCompleteNotificationParamsSchema
	});
	/**
	* The client's response to an elicitation/create request from the server.
	*/
	var ElicitResultSchema = ResultSchema.extend({
		/**
		* The user action in response to the elicitation.
		* - "accept": User submitted the form/confirmed the action
		* - "decline": User explicitly decline the action
		* - "cancel": User dismissed without making an explicit choice
		*/
		action: _enum([
			"accept",
			"decline",
			"cancel"
		]),
		/**
		* The submitted form data, only present when action is "accept".
		* Contains values matching the requested schema.
		* Per MCP spec, content is "typically omitted" for decline/cancel actions.
		* We normalize null to undefined for leniency while maintaining type compatibility.
		*/
		content: preprocess((val) => val === null ? void 0 : val, record(string(), union([
			string(),
			number(),
			boolean(),
			array(string())
		])).optional())
	});
	/**
	* A reference to a resource or resource template definition.
	*/
	var ResourceTemplateReferenceSchema = object({
		type: literal("ref/resource"),
		/**
		* The URI or URI template of the resource.
		*/
		uri: string()
	});
	/**
	* Identifies a prompt.
	*/
	var PromptReferenceSchema = object({
		type: literal("ref/prompt"),
		/**
		* The name of the prompt or prompt template
		*/
		name: string()
	});
	/**
	* Parameters for a `completion/complete` request.
	*/
	var CompleteRequestParamsSchema = BaseRequestParamsSchema.extend({
		ref: union([PromptReferenceSchema, ResourceTemplateReferenceSchema]),
		/**
		* The argument's information
		*/
		argument: object({
			/**
			* The name of the argument
			*/
			name: string(),
			/**
			* The value of the argument to use for completion matching.
			*/
			value: string()
		}),
		context: object({ 
		/**
		* Previously-resolved variables in a URI template or prompt.
		*/
arguments: record(string(), string()).optional() }).optional()
	});
	/**
	* A request from the client to the server, to ask for completion options.
	*/
	var CompleteRequestSchema = RequestSchema.extend({
		method: literal("completion/complete"),
		params: CompleteRequestParamsSchema
	});
	/**
	* The server's response to a completion/complete request
	*/
	var CompleteResultSchema = ResultSchema.extend({ completion: looseObject({
		/**
		* An array of completion values. Must not exceed 100 items.
		*/
		values: array(string()).max(100),
		/**
		* The total number of completion options available. This can exceed the number of values actually sent in the response.
		*/
		total: optional(number().int()),
		/**
		* Indicates whether there are additional completion options beyond those provided in the current response, even if the exact total is unknown.
		*/
		hasMore: optional(boolean())
	}) });
	/**
	* Represents a root directory or file that the server can operate on.
	*/
	var RootSchema = object({
		/**
		* The URI identifying the root. This *must* start with file:// for now.
		*/
		uri: string().startsWith("file://"),
		/**
		* An optional name for the root.
		*/
		name: string().optional(),
		/**
		* See [MCP specification](https://github.com/modelcontextprotocol/modelcontextprotocol/blob/47339c03c143bb4ec01a26e721a1b8fe66634ebe/docs/specification/draft/basic/index.mdx#general-fields)
		* for notes on _meta usage.
		*/
		_meta: record(string(), unknown()).optional()
	});
	/**
	* Sent from the server to request a list of root URIs from the client.
	*/
	var ListRootsRequestSchema = RequestSchema.extend({
		method: literal("roots/list"),
		params: BaseRequestParamsSchema.optional()
	});
	/**
	* The client's response to a roots/list request from the server.
	*/
	var ListRootsResultSchema = ResultSchema.extend({ roots: array(RootSchema) });
	/**
	* A notification from the client to the server, informing it that the list of roots has changed.
	*/
	var RootsListChangedNotificationSchema = NotificationSchema.extend({
		method: literal("notifications/roots/list_changed"),
		params: NotificationsParamsSchema.optional()
	});
	var ClientRequestSchema = union([
		PingRequestSchema,
		InitializeRequestSchema,
		CompleteRequestSchema,
		SetLevelRequestSchema,
		GetPromptRequestSchema,
		ListPromptsRequestSchema,
		ListResourcesRequestSchema,
		ListResourceTemplatesRequestSchema,
		ReadResourceRequestSchema,
		SubscribeRequestSchema,
		UnsubscribeRequestSchema,
		CallToolRequestSchema,
		ListToolsRequestSchema,
		GetTaskRequestSchema,
		GetTaskPayloadRequestSchema,
		ListTasksRequestSchema,
		CancelTaskRequestSchema
	]);
	var ClientNotificationSchema = union([
		CancelledNotificationSchema,
		ProgressNotificationSchema,
		InitializedNotificationSchema,
		RootsListChangedNotificationSchema,
		TaskStatusNotificationSchema
	]);
	var ClientResultSchema = union([
		EmptyResultSchema,
		CreateMessageResultSchema,
		CreateMessageResultWithToolsSchema,
		ElicitResultSchema,
		ListRootsResultSchema,
		GetTaskResultSchema,
		ListTasksResultSchema,
		CreateTaskResultSchema
	]);
	var ServerRequestSchema = union([
		PingRequestSchema,
		CreateMessageRequestSchema,
		ElicitRequestSchema,
		ListRootsRequestSchema,
		GetTaskRequestSchema,
		GetTaskPayloadRequestSchema,
		ListTasksRequestSchema,
		CancelTaskRequestSchema
	]);
	var ServerNotificationSchema = union([
		CancelledNotificationSchema,
		ProgressNotificationSchema,
		LoggingMessageNotificationSchema,
		ResourceUpdatedNotificationSchema,
		ResourceListChangedNotificationSchema,
		ToolListChangedNotificationSchema,
		PromptListChangedNotificationSchema,
		TaskStatusNotificationSchema,
		ElicitationCompleteNotificationSchema
	]);
	var ServerResultSchema = union([
		EmptyResultSchema,
		InitializeResultSchema,
		CompleteResultSchema,
		GetPromptResultSchema,
		ListPromptsResultSchema,
		ListResourcesResultSchema,
		ListResourceTemplatesResultSchema,
		ReadResourceResultSchema,
		CallToolResultSchema,
		ListToolsResultSchema,
		GetTaskResultSchema,
		ListTasksResultSchema,
		CreateTaskResultSchema
	]);

//#endregion
//#region \0@oxc-project+runtime@0.140.0/helpers/esm/objectDestructuringEmpty.js
	function _objectDestructuringEmpty(t) {
		if (null == t) throw new TypeError("Cannot destructure " + t);
	}

//#endregion
//#region \0@oxc-project+runtime@0.140.0/helpers/esm/extends.js
	function _extends() {
		return _extends = Object.assign ? Object.assign.bind() : function(n) {
			for (var e = 1; e < arguments.length; e++) {
				var t = arguments[e];
				for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]);
			}
			return n;
		}, _extends.apply(null, arguments);
	}

//#endregion
//#region \0@oxc-project+runtime@0.140.0/helpers/esm/objectWithoutPropertiesLoose.js
	function _objectWithoutPropertiesLoose(r, e) {
		if (null == r) return {};
		var t = {};
		for (var n in r) if ({}.hasOwnProperty.call(r, n)) {
			if (e.includes(n)) continue;
			t[n] = r[n];
		}
		return t;
	}

//#endregion
//#region \0@oxc-project+runtime@0.140.0/helpers/esm/objectWithoutProperties.js
	function _objectWithoutProperties(e, t) {
		if (null == e) return {};
		var o;
		var r;
		var i = _objectWithoutPropertiesLoose(e, t);
		if (Object.getOwnPropertySymbols) {
			var s = Object.getOwnPropertySymbols(e);
			for (r = 0; r < s.length; r++) o = s[r], t.includes(o) || {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]);
		}
		return i;
	}

//#endregion
//#region node_modules/@elementor-external/angie-sdk/dist/index.js
	var _excluded$1 = ["timeoutInSeconds"];
	var _excluded2 = [
		"grant_type",
		"redirect_uri",
		"client_id",
		"client_secret",
		"extraHeaders"
	];
	var _excluded3 = [
		"grant_type",
		"client_id",
		"client_secret",
		"scope"
	];
	var _excluded4 = [
		"grant_type",
		"client_id",
		"client_secret",
		"timeoutInSeconds",
		"extraHeaders"
	];
	var _excluded5 = [
		"url",
		"authority",
		"client_id",
		"redirect_uri",
		"response_type",
		"scope",
		"state_data",
		"response_mode",
		"request_type",
		"client_secret",
		"nonce",
		"url_state",
		"resource",
		"skipUserInfo",
		"extraQueryParams",
		"extraTokenParams",
		"disablePKCE",
		"dpopJkt",
		"omitScopeWhenRequesting"
	];
	var _excluded6 = ["redirectMethod"];
	var _excluded7 = [
		"popupWindowFeatures",
		"popupWindowTarget",
		"popupSignal",
		"popupAbortOnClose"
	];
	var _excluded8 = ["silentRequestTimeoutInSeconds"];
	var _excluded9 = ["silentRequestTimeoutInSeconds"];
	var _excluded10 = ["redirectMethod"];
	var _excluded11 = [
		"popupWindowFeatures",
		"popupWindowTarget",
		"popupSignal"
	];
	var _excluded12 = ["silentRequestTimeoutInSeconds"];
	var t = {
		none: 0,
		error: 1,
		warn: 2,
		info: 3,
		debug: 4
	};
	var i = {
		error: "error",
		warn: "warn",
		info: "info",
		log: "info",
		debug: "debug"
	};
	var n = (e, i) => t[e] <= t[i];
	var s = (e) => "string" == typeof e ? e : JSON.stringify(e);
	var r = (e, t) => `${s(e)} > ${s(t)}`;
	var o = (e, t) => {
		let i = `[${s(e)}]`;
		return typeof window < "u" ? {
			text: `%c${i}`,
			style: `color: ${t.color || "#00bcd4"}; font-weight: bold;`
		} : { text: i };
	};
	var a = (e, t, s, r) => (...a) => {
		if (!n(i[e], r())) return;
		if (!t) return void console[e](...a);
		let { text: c, style: d } = o(t, s);
		d ? console[e](c, d, ...a) : console[e](c, ...a);
	};
	var c = (e, t) => {
		var _t$logLevel;
		let i = (_t$logLevel = t.logLevel) !== null && _t$logLevel !== void 0 ? _t$logLevel : "debug";
		let n = () => i;
		return {
			log: a("log", e, t, n),
			info: a("info", e, t, n),
			warn: a("warn", e, t, n),
			error: a("error", e, t, n),
			debug: a("debug", e, t, n),
			setLogLevel: (e) => {
				i = e;
			},
			extend: (n) => c(e ? r(e, n) : n, _objectSpread2(_objectSpread2({}, t), {}, { logLevel: i }))
		};
	};
	var d = (e, t) => c(e, _objectSpread2({
		color: "#00bcd4",
		logLevel: "debug"
	}, t));
	var l = d("angie-sdk", {
		color: "#00BCD4",
		logLevel: "error"
	});
	var g = (e) => l.extend(e);
	var u;
	var h;
	var p;
	var _;
	var w;
	var m;
	var f;
	var y;
	(function(e) {
		e.POST_MESSAGE = "postMessage";
	})(u || (u = {})), function(e) {
		e.POST_MESSAGE = "postMessage";
	}(h || (h = {})), function(e) {
		e.STREAMABLE_HTTP = "streamableHttp", e.SSE = "sse";
	}(p || (p = {})), function(e) {
		e.LOCAL = "local", e.REMOTE = "remote";
	}(_ || (_ = {})), function(e) {
		e.AGENT = "agent", e.PLAN = "plan", e.ASK = "ask", e.SUPER_ADMIN = "super-admin";
	}(w || (w = {})), function(e) {
		e.SDK_ANGIE_READY_PING = "sdk-angie-ready-ping", e.SDK_ANGIE_REFRESH_PING = "sdk-angie-refresh-ping", e.SDK_ANGIE_ALL_SERVERS_REGISTERED = "sdk-angie-all-servers-registered", e.SDK_REQUEST_CLIENT_CREATION = "sdk-request-client-creation", e.SDK_REQUEST_INIT_SERVER = "sdk-request-init-server", e.SDK_TRIGGER_ANGIE = "sdk-trigger-angie", e.SDK_TRIGGER_ANGIE_RESPONSE = "sdk-trigger-angie-response", e.ANGIE_SIDEBAR_RESIZED = "angie-sidebar-resized", e.ANGIE_SIDEBAR_TOGGLED = "angie-sidebar-toggled", e.ANGIE_CHAT_TOGGLE = "angie-chat-toggle", e.ANGIE_STUDIO_TOGGLE = "angie-studio-toggle", e.ANGIE_NAVIGATE_TO_URL = "angie/navigate-to-url", e.ANGIE_PAGE_RELOAD = "angie/page-reload", e.ANGIE_DISABLE_NAVIGATION_PREVENTION = "angie/disable-navigation-prevention", e.ANGIE_NAVIGATE_AFTER_RESPONSE = "angie/navigate-after-response", e.ANGIE_SET_INTERACTION_MODE = "angie/set-interaction-mode";
	}(m || (m = {})), function(e) {
		e.SET = "ANGIE_SET_LOCALSTORAGE", e.GET = "ANGIE_GET_LOCALSTORAGE";
	}(f || (f = {})), function(e) {
		e.RESET_HASH = "reset-hash", e.HOST_READY = "host/ready", e.ANGIE_LOADED = "angie/loaded", e.ANGIE_READY = "angie/ready";
	}(y || (y = {}));
	var S = g("angie-detector");
	var b = class {
		constructor(e) {
			_defineProperty(this, "isAngieReady", !1);
			_defineProperty(this, "readyPromise", void 0);
			_defineProperty(this, "readyResolve", void 0);
			_defineProperty(this, "getInstanceId", void 0);
			if (this.getInstanceId = e, this.readyPromise = new Promise((e) => {
				this.readyResolve = e;
			}), "undefined" == typeof window) return;
			let t = 0;
			const i = () => {
				var _this$getInstanceId;
				if (this.isAngieReady || t >= 500) return void (!this.isAngieReady && t >= 500 && this.handleDetectionTimeout());
				const e = new MessageChannel();
				e.port1.onmessage = (t) => {
					this.handleAngieReady(t.data), e.port1.close(), e.port2.close();
				};
				const n = {
					type: m.SDK_ANGIE_READY_PING,
					payload: { instanceId: (_this$getInstanceId = this.getInstanceId) === null || _this$getInstanceId === void 0 ? void 0 : _this$getInstanceId.call(this) },
					timestamp: Date.now()
				};
				window.postMessage(n, window.location.origin, [e.port2]), t++, setTimeout(i, 500);
			};
			i();
		}
		handleAngieReady(e) {
			this.isAngieReady = !0;
			const t = {
				isReady: !0,
				version: e.version,
				capabilities: e.capabilities
			};
			this.readyResolve && this.readyResolve(t);
		}
		handleDetectionTimeout() {
			this.readyResolve && this.readyResolve({ isReady: !1 }), S.warn("Detection timeout - Angie may not be available");
		}
		isReady() {
			return this.isAngieReady;
		}
		waitForReady() {
			var _this = this;
			return _asyncToGenerator(function* () {
				return _this.readyPromise;
			})();
		}
		waitUntilReady(e) {
			var _this2 = this;
			return _asyncToGenerator(function* () {
				return _this2.isAngieReady ? _this2.readyPromise : Promise.race([_this2.readyPromise, new Promise((t) => {
					setTimeout(() => t({ isReady: !1 }), e);
				})]);
			})();
		}
	};
	var E = "angie-sidebar-container";
	var k = "angie-iframe";
	var T = () => ({
		open: !1,
		iframe: null,
		iframeUrlObject: null,
		containerId: E,
		instanceId: "",
		layout: "",
		iframeElementId: k
	});
	var R = T();
	var A = class extends Error {};
	A.prototype.name = "InvalidTokenError";
	var C;
	var x;
	var P;
	var O = {
		debug: () => {},
		info: () => {},
		warn: () => {},
		error: () => {}
	};
	var U = ((e) => (e[e.NONE = 0] = "NONE", e[e.ERROR = 1] = "ERROR", e[e.WARN = 2] = "WARN", e[e.INFO = 3] = "INFO", e[e.DEBUG = 4] = "DEBUG", e))(U || {});
	(P = U || (U = {})).reset = function() {
		C = 3, x = O;
	}, P.setLevel = function(e) {
		if (!(0 <= e && e <= 4)) throw new Error("Invalid log level");
		C = e;
	}, P.setLogger = function(e) {
		x = e;
	};
	var L = class e {
		constructor(e) {
			this._name = e;
		}
		debug(...t) {
			C >= 4 && x.debug(e._format(this._name, this._method), ...t);
		}
		info(...t) {
			C >= 3 && x.info(e._format(this._name, this._method), ...t);
		}
		warn(...t) {
			C >= 2 && x.warn(e._format(this._name, this._method), ...t);
		}
		error(...t) {
			C >= 1 && x.error(e._format(this._name, this._method), ...t);
		}
		throw(e) {
			throw this.error(e), e;
		}
		create(e) {
			const t = Object.create(this);
			return t._method = e, t.debug("begin"), t;
		}
		static createStatic(t, i) {
			const n = new e(`${t}.${i}`);
			return n.debug("begin"), n;
		}
		static _format(e, t) {
			const i = `[${e}]`;
			return t ? `${i} ${t}:` : i;
		}
		static debug(t, ...i) {
			C >= 4 && x.debug(e._format(t), ...i);
		}
		static info(t, ...i) {
			C >= 3 && x.info(e._format(t), ...i);
		}
		static warn(t, ...i) {
			C >= 2 && x.warn(e._format(t), ...i);
		}
		static error(t, ...i) {
			C >= 1 && x.error(e._format(t), ...i);
		}
	};
	U.reset();
	var D = class {
		static decode(e) {
			try {
				return function(e, t) {
					if ("string" != typeof e) throw new A("Invalid token specified: must be a string");
					t || (t = {});
					const i = !0 === t.header ? 0 : 1;
					const n = e.split(".")[i];
					if ("string" != typeof n) throw new A(`Invalid token specified: missing part #${i + 1}`);
					let s;
					try {
						s = function(e) {
							let t = e.replace(/-/g, "+").replace(/_/g, "/");
							switch (t.length % 4) {
								case 0: break;
								case 2:
									t += "==";
									break;
								case 3:
									t += "=";
									break;
								default: throw new Error("base64 string is not of the correct length");
							}
							try {
								return function(e) {
									return decodeURIComponent(atob(e).replace(/(.)/g, (e, t) => {
										let i = t.charCodeAt(0).toString(16).toUpperCase();
										return i.length < 2 && (i = "0" + i), "%" + i;
									}));
								}(t);
							} catch (e) {
								return atob(t);
							}
						}(n);
					} catch (e) {
						throw new A(`Invalid token specified: invalid base64 for part #${i + 1} (${e.message})`);
					}
					try {
						return JSON.parse(s);
					} catch (e) {
						throw new A(`Invalid token specified: invalid json for part #${i + 1} (${e.message})`);
					}
				}(e);
			} catch (e) {
				throw L.error("JwtUtils.decode", e), e;
			}
		}
		static generateSignedJwt(e, t, i) {
			return _asyncToGenerator(function* () {
				const n = `${$.encodeBase64Url(new TextEncoder().encode(JSON.stringify(e)))}.${$.encodeBase64Url(new TextEncoder().encode(JSON.stringify(t)))}`;
				const s = yield window.crypto.subtle.sign({
					name: "ECDSA",
					hash: { name: "SHA-256" }
				}, i, new TextEncoder().encode(n));
				return `${n}.${$.encodeBase64Url(new Uint8Array(s))}`;
			})();
		}
		static generateSignedJwtWithHmac(e, t, i) {
			return _asyncToGenerator(function* () {
				const n = `${$.encodeBase64Url(new TextEncoder().encode(JSON.stringify(e)))}.${$.encodeBase64Url(new TextEncoder().encode(JSON.stringify(t)))}`;
				const s = yield window.crypto.subtle.sign("HMAC", i, new TextEncoder().encode(n));
				return `${n}.${$.encodeBase64Url(new Uint8Array(s))}`;
			})();
		}
	};
	var N = (e) => btoa([...new Uint8Array(e)].map((e) => String.fromCharCode(e)).join(""));
	var q = class e {
		static _randomWord() {
			const e = /* @__PURE__ */ new Uint32Array(1);
			return crypto.getRandomValues(e), e[0];
		}
		static generateUUIDv4() {
			return "10000000-1000-4000-8000-100000000000".replace(/[018]/g, (t) => (+t ^ e._randomWord() & 15 >> +t / 4).toString(16)).replace(/-/g, "");
		}
		static generateCodeVerifier() {
			return e.generateUUIDv4() + e.generateUUIDv4() + e.generateUUIDv4();
		}
		static generateCodeChallenge(e) {
			return _asyncToGenerator(function* () {
				if (!crypto.subtle) throw new Error("Crypto.subtle is available only in secure contexts (HTTPS).");
				try {
					const t = new TextEncoder().encode(e);
					return N(yield crypto.subtle.digest("SHA-256", t)).replace(/\+/g, "-").replace(/\//g, "_").replace(/=+$/, "");
				} catch (e) {
					throw L.error("CryptoUtils.generateCodeChallenge", e), e;
				}
			})();
		}
		static generateBasicAuth(e, t) {
			return N(new TextEncoder().encode([e, t].join(":")));
		}
		static hash(e, t) {
			return _asyncToGenerator(function* () {
				const i = new TextEncoder().encode(t);
				const n = yield crypto.subtle.digest(e, i);
				return new Uint8Array(n);
			})();
		}
		static customCalculateJwkThumbprint(t) {
			return _asyncToGenerator(function* () {
				let i;
				switch (t.kty) {
					case "RSA":
						i = {
							e: t.e,
							kty: t.kty,
							n: t.n
						};
						break;
					case "EC":
						i = {
							crv: t.crv,
							kty: t.kty,
							x: t.x,
							y: t.y
						};
						break;
					case "OKP":
						i = {
							crv: t.crv,
							kty: t.kty,
							x: t.x
						};
						break;
					case "oct":
						i = {
							crv: t.k,
							kty: t.kty
						};
						break;
					default: throw new Error("Unknown jwk type");
				}
				const n = yield e.hash("SHA-256", JSON.stringify(i));
				return e.encodeBase64Url(n);
			})();
		}
		static generateDPoPProof({ url: t, accessToken: i, httpMethod: n, keyPair: s, nonce: r }) {
			return _asyncToGenerator(function* () {
				let o;
				let a;
				const c = {
					jti: window.crypto.randomUUID(),
					htm: null != n ? n : "GET",
					htu: t,
					iat: Math.floor(Date.now() / 1e3)
				};
				i && (o = yield e.hash("SHA-256", i), a = e.encodeBase64Url(o), c.ath = a), r && (c.nonce = r);
				try {
					const e = yield crypto.subtle.exportKey("jwk", s.publicKey);
					const t = {
						alg: "ES256",
						typ: "dpop+jwt",
						jwk: {
							crv: e.crv,
							kty: e.kty,
							x: e.x,
							y: e.y
						}
					};
					return yield D.generateSignedJwt(t, c, s.privateKey);
				} catch (e) {
					throw e instanceof TypeError ? /* @__PURE__ */ new Error(`Error exporting dpop public key: ${e.message}`) : e;
				}
			})();
		}
		static generateDPoPJkt(t) {
			return _asyncToGenerator(function* () {
				try {
					const i = yield crypto.subtle.exportKey("jwk", t.publicKey);
					return yield e.customCalculateJwkThumbprint(i);
				} catch (e) {
					throw e instanceof TypeError ? /* @__PURE__ */ new Error(`Could not retrieve dpop keys from storage: ${e.message}`) : e;
				}
			})();
		}
		static generateDPoPKeys() {
			return _asyncToGenerator(function* () {
				return yield window.crypto.subtle.generateKey({
					name: "ECDSA",
					namedCurve: "P-256"
				}, !1, ["sign", "verify"]);
			})();
		}
		static generateClientAssertionJwt(t, i, n, s = "HS256") {
			return _asyncToGenerator(function* () {
				const r = Math.floor(Date.now() / 1e3);
				const o = {
					alg: s,
					typ: "JWT"
				};
				const a = {
					iss: t,
					sub: t,
					aud: n,
					jti: e.generateUUIDv4(),
					exp: r + 300,
					iat: r
				};
				const c = {
					HS256: "SHA-256",
					HS384: "SHA-384",
					HS512: "SHA-512"
				}[s];
				if (!c) throw new Error(`Unsupported algorithm: ${s}. Supported algorithms are: HS256, HS384, HS512`);
				const d = new TextEncoder();
				const l = yield crypto.subtle.importKey("raw", d.encode(i), {
					name: "HMAC",
					hash: c
				}, !1, ["sign"]);
				return yield D.generateSignedJwtWithHmac(o, a, l);
			})();
		}
	};
	q.encodeBase64Url = (e) => N(e).replace(/=/g, "").replace(/\+/g, "-").replace(/\//g, "_");
	var $ = q;
	var M = class {
		constructor(e) {
			this._name = e, this._callbacks = [], this._logger = new L(`Event('${this._name}')`);
		}
		addHandler(e) {
			return this._callbacks.push(e), () => this.removeHandler(e);
		}
		removeHandler(e) {
			const t = this._callbacks.lastIndexOf(e);
			t >= 0 && this._callbacks.splice(t, 1);
		}
		raise(...e) {
			var _this6 = this;
			return _asyncToGenerator(function* () {
				_this6._logger.debug("raise:", ...e);
				for (const t of _this6._callbacks) yield t(...e);
			})();
		}
	};
	var H = class {
		static center(_ref) {
			let e = _extends({}, (_objectDestructuringEmpty(_ref), _ref));
			var t;
			return null == e.width && (e.width = null != (t = [
				800,
				720,
				600,
				480
			].find((e) => e <= window.outerWidth / 1.618)) ? t : 360), null != e.left || (e.left = Math.max(0, Math.round(window.screenX + (window.outerWidth - e.width) / 2))), null != e.height && (null != e.top || (e.top = Math.max(0, Math.round(window.screenY + (window.outerHeight - e.height) / 2)))), e;
		}
		static serialize(e) {
			return Object.entries(e).filter(([, e]) => null != e).map(([e, t]) => `${e}=${"boolean" != typeof t ? t : t ? "yes" : "no"}`).join(",");
		}
	};
	var j = class e extends M {
		constructor() {
			super(...arguments), this._logger = new L(`Timer('${this._name}')`), this._timerHandle = null, this._expiration = 0, this._callback = () => {
				const t = this._expiration - e.getEpochTime();
				this._logger.debug("timer completes in", t), this._expiration <= e.getEpochTime() && (this.cancel(), super.raise());
			};
		}
		static getEpochTime() {
			return Math.floor(Date.now() / 1e3);
		}
		init(t) {
			const i = this._logger.create("init");
			t = Math.max(Math.floor(t), 1);
			const n = e.getEpochTime() + t;
			if (this.expiration === n && this._timerHandle) return void i.debug("skipping since already initialized for expiration at", this.expiration);
			this.cancel(), i.debug("using duration", t), this._expiration = n;
			const s = Math.min(t, 5);
			this._timerHandle = setInterval(this._callback, 1e3 * s);
		}
		get expiration() {
			return this._expiration;
		}
		cancel() {
			this._logger.create("cancel"), this._timerHandle && (clearInterval(this._timerHandle), this._timerHandle = null);
		}
	};
	var G = class {
		static readParams(e, t = "query") {
			if (!e) throw new TypeError("Invalid URL");
			const i = new URL(e, "http://127.0.0.1")["fragment" === t ? "hash" : "search"];
			return new URLSearchParams(i.slice(1));
		}
	};
	var z = ";";
	var B = class extends Error {
		constructor(e, t) {
			var i;
			var n;
			var s;
			if (super(e.error_description || e.error || ""), this.form = t, this.name = "ErrorResponse", !e.error) throw L.error("ErrorResponse", "No error passed"), /* @__PURE__ */ new Error("No error passed");
			this.error = e.error, this.error_description = null != (i = e.error_description) ? i : null, this.error_uri = null != (n = e.error_uri) ? n : null, this.state = e.userState, this.session_state = null != (s = e.session_state) ? s : null, this.url_state = e.url_state;
		}
	};
	var W = class extends Error {
		constructor(e) {
			super(e), this.name = "ErrorTimeout";
		}
	};
	var F = class {
		constructor(e) {
			this._logger = new L("AccessTokenEvents"), this._expiringTimer = new j("Access token expiring"), this._expiredTimer = new j("Access token expired"), this._expiringNotificationTimeInSeconds = e.expiringNotificationTimeInSeconds;
		}
		load(e) {
			var _this7 = this;
			return _asyncToGenerator(function* () {
				const t = _this7._logger.create("load");
				if (e.access_token && void 0 !== e.expires_in) {
					const i = e.expires_in;
					if (t.debug("access token present, remaining duration:", i), i > 0) {
						let e = i - _this7._expiringNotificationTimeInSeconds;
						e <= 0 && (e = 1), t.debug("registering expiring timer, raising in", e, "seconds"), _this7._expiringTimer.init(e);
					} else t.debug("canceling existing expiring timer because we're past expiration."), _this7._expiringTimer.cancel();
					const n = i + 1;
					t.debug("registering expired timer, raising in", n, "seconds"), _this7._expiredTimer.init(n);
				} else _this7._expiringTimer.cancel(), _this7._expiredTimer.cancel();
			})();
		}
		unload() {
			var _this8 = this;
			return _asyncToGenerator(function* () {
				_this8._logger.debug("unload: canceling existing access token timers"), _this8._expiringTimer.cancel(), _this8._expiredTimer.cancel();
			})();
		}
		addAccessTokenExpiring(e) {
			return this._expiringTimer.addHandler(e);
		}
		removeAccessTokenExpiring(e) {
			this._expiringTimer.removeHandler(e);
		}
		addAccessTokenExpired(e) {
			return this._expiredTimer.addHandler(e);
		}
		removeAccessTokenExpired(e) {
			this._expiredTimer.removeHandler(e);
		}
	};
	var K = class {
		constructor(e, t, i, n, s) {
			this._callback = e, this._client_id = t, this._intervalInSeconds = n, this._stopOnError = s, this._logger = new L("CheckSessionIFrame"), this._timer = null, this._session_state = null, this._message = (e) => {
				e.origin === this._frame_origin && e.source === this._frame.contentWindow && ("error" === e.data ? (this._logger.error("error message from check session op iframe"), this._stopOnError && this.stop()) : "changed" === e.data ? (this._logger.debug("changed message from check session op iframe"), this.stop(), this._callback()) : this._logger.debug(e.data + " message from check session op iframe"));
			};
			const r = new URL(i);
			this._frame_origin = r.origin, this._frame = window.document.createElement("iframe"), this._frame.style.visibility = "hidden", this._frame.style.position = "fixed", this._frame.style.left = "-1000px", this._frame.style.top = "0", this._frame.width = "0", this._frame.height = "0", this._frame.src = r.href;
		}
		load() {
			return new Promise((e) => {
				this._frame.onload = () => {
					e();
				}, window.document.body.appendChild(this._frame), window.addEventListener("message", this._message, !1);
			});
		}
		start(e) {
			if (this._session_state === e) return;
			this._logger.create("start"), this.stop(), this._session_state = e;
			const t = () => {
				this._frame.contentWindow && this._session_state && this._frame.contentWindow.postMessage(this._client_id + " " + this._session_state, this._frame_origin);
			};
			t(), this._timer = setInterval(t, 1e3 * this._intervalInSeconds);
		}
		stop() {
			this._logger.create("stop"), this._session_state = null, this._timer && (clearInterval(this._timer), this._timer = null);
		}
	};
	var J = class {
		constructor() {
			this._logger = new L("InMemoryWebStorage"), this._data = {};
		}
		clear() {
			this._logger.create("clear"), this._data = {};
		}
		getItem(e) {
			return this._logger.create(`getItem('${e}')`), this._data[e];
		}
		setItem(e, t) {
			this._logger.create(`setItem('${e}')`), this._data[e] = t;
		}
		removeItem(e) {
			this._logger.create(`removeItem('${e}')`), delete this._data[e];
		}
		get length() {
			return Object.getOwnPropertyNames(this._data).length;
		}
		key(e) {
			return Object.getOwnPropertyNames(this._data)[e];
		}
	};
	var V = class extends Error {
		constructor(e, t) {
			super(t), this.name = "ErrorDPoPNonce", this.nonce = e;
		}
	};
	var Q = class {
		constructor(e = [], t = null, i = {}) {
			this._jwtHandler = t, this._extraHeaders = i, this._logger = new L("JsonService"), this._contentTypes = [], this._contentTypes.push(...e, "application/json"), t && this._contentTypes.push("application/jwt");
		}
		fetchWithTimeout(_x) {
			return _asyncToGenerator(function* (e, t = {}) {
				const { timeoutInSeconds: i } = t, n = _objectWithoutProperties(t, _excluded$1);
				if (!i) return yield fetch(e, n);
				const s = new AbortController();
				const r = setTimeout(() => s.abort(), 1e3 * i);
				try {
					return yield fetch(e, _objectSpread2(_objectSpread2({}, t), {}, { signal: s.signal }));
				} catch (e) {
					if (e instanceof DOMException && "AbortError" === e.name) throw new W("Network timed out");
					throw e;
				} finally {
					clearTimeout(r);
				}
			}).apply(this, arguments);
		}
		getJson(_x2) {
			var _this9 = this;
			return _asyncToGenerator(function* (e, { token: t, credentials: i, timeoutInSeconds: n } = {}) {
				const s = _this9._logger.create("getJson");
				const r = { Accept: _this9._contentTypes.join(", ") };
				let o;
				t && (s.debug("token passed, setting Authorization header"), r.Authorization = "Bearer " + t), _this9._appendExtraHeaders(r);
				try {
					s.debug("url:", e), o = yield _this9.fetchWithTimeout(e, {
						method: "GET",
						headers: r,
						timeoutInSeconds: n,
						credentials: i
					});
				} catch (e) {
					throw s.error("Network Error"), e;
				}
				s.debug("HTTP response received, status", o.status);
				const a = o.headers.get("Content-Type");
				if (a && !_this9._contentTypes.find((e) => a.startsWith(e)) && s.throw(/* @__PURE__ */ new Error(`Invalid response Content-Type: ${null != a ? a : "undefined"}, from URL: ${e}`)), o.ok && _this9._jwtHandler && (null == a ? void 0 : a.startsWith("application/jwt"))) return yield _this9._jwtHandler(yield o.text());
				let c;
				try {
					c = yield o.json();
				} catch (e) {
					if (s.error("Error parsing JSON response", e), o.ok) throw e;
					throw new Error(`${o.statusText} (${o.status})`);
				}
				if (!o.ok) {
					if (s.error("Error from server:", c), c.error) throw new B(c);
					throw new Error(`${o.statusText} (${o.status}): ${JSON.stringify(c)}`);
				}
				return c;
			}).apply(this, arguments);
		}
		postForm(e, { body: t, basicAuth: i, timeoutInSeconds: n, initCredentials: s, extraHeaders: r }) {
			var _this10 = this;
			return _asyncToGenerator(function* () {
				const o = _this10._logger.create("postForm");
				const a = _objectSpread2({
					Accept: _this10._contentTypes.join(", "),
					"Content-Type": "application/x-www-form-urlencoded"
				}, r);
				let c;
				void 0 !== i && (a.Authorization = "Basic " + i), _this10._appendExtraHeaders(a);
				try {
					o.debug("url:", e), c = yield _this10.fetchWithTimeout(e, {
						method: "POST",
						headers: a,
						body: t,
						timeoutInSeconds: n,
						credentials: s
					});
				} catch (e) {
					throw o.error("Network error"), e;
				}
				o.debug("HTTP response received, status", c.status);
				const d = c.headers.get("Content-Type");
				if (d && !_this10._contentTypes.find((e) => d.startsWith(e))) throw new Error(`Invalid response Content-Type: ${null != d ? d : "undefined"}, from URL: ${e}`);
				const l = yield c.text();
				let g = {};
				if (l) try {
					g = JSON.parse(l);
				} catch (e) {
					if (o.error("Error parsing JSON response", e), c.ok) throw e;
					throw new Error(`${c.statusText} (${c.status})`);
				}
				if (!c.ok) {
					if (o.error("Error from server:", g), c.headers.has("dpop-nonce")) throw new V(c.headers.get("dpop-nonce"), `${JSON.stringify(g)}`);
					if (g.error) throw new B(g, t);
					throw new Error(`${c.statusText} (${c.status}): ${JSON.stringify(g)}`);
				}
				return g;
			})();
		}
		_appendExtraHeaders(e) {
			const t = this._logger.create("appendExtraHeaders");
			const i = Object.keys(this._extraHeaders);
			const n = ["accept", "content-type"];
			const s = ["authorization"];
			0 !== i.length && i.forEach((i) => {
				if (n.includes(i.toLocaleLowerCase())) return void t.warn("Protected header could not be set", i, n);
				if (s.includes(i.toLocaleLowerCase()) && Object.keys(e).includes(i)) return void t.warn("Header could not be overridden", i, s);
				const r = "function" == typeof this._extraHeaders[i] ? this._extraHeaders[i]() : this._extraHeaders[i];
				r && "" !== r && (e[i] = r);
			});
		}
	};
	var X = class {
		constructor(e) {
			this._settings = e, this._logger = new L("MetadataService"), this._signingKeys = null, this._metadata = null, this._metadataUrl = this._settings.metadataUrl, this._jsonService = new Q(["application/jwk-set+json"], null, this._settings.extraHeaders), this._settings.signingKeys && (this._logger.debug("using signingKeys from settings"), this._signingKeys = this._settings.signingKeys), this._settings.metadata && (this._logger.debug("using metadata from settings"), this._metadata = this._settings.metadata), this._settings.fetchRequestCredentials && (this._logger.debug("using fetchRequestCredentials from settings"), this._fetchRequestCredentials = this._settings.fetchRequestCredentials);
		}
		resetSigningKeys() {
			this._signingKeys = null;
		}
		getMetadata() {
			var _this11 = this;
			return _asyncToGenerator(function* () {
				const e = _this11._logger.create("getMetadata");
				if (_this11._metadata) return e.debug("using cached values"), _this11._metadata;
				if (!_this11._metadataUrl) throw e.throw(/* @__PURE__ */ new Error("No authority or metadataUrl configured on settings")), null;
				e.debug("getting metadata from", _this11._metadataUrl);
				const t = yield _this11._jsonService.getJson(_this11._metadataUrl, {
					credentials: _this11._fetchRequestCredentials,
					timeoutInSeconds: _this11._settings.requestTimeoutInSeconds
				});
				return e.debug("merging remote JSON with seed metadata"), _this11._metadata = Object.assign({}, t, _this11._settings.metadataSeed), _this11._metadata;
			})();
		}
		getIssuer() {
			return this._getMetadataProperty("issuer");
		}
		getAuthorizationEndpoint() {
			return this._getMetadataProperty("authorization_endpoint");
		}
		getUserInfoEndpoint() {
			return this._getMetadataProperty("userinfo_endpoint");
		}
		getTokenEndpoint(e = !0) {
			return this._getMetadataProperty("token_endpoint", e);
		}
		getCheckSessionIframe() {
			return this._getMetadataProperty("check_session_iframe", !0);
		}
		getEndSessionEndpoint() {
			return this._getMetadataProperty("end_session_endpoint", !0);
		}
		getRevocationEndpoint(e = !0) {
			return this._getMetadataProperty("revocation_endpoint", e);
		}
		getKeysEndpoint(e = !0) {
			return this._getMetadataProperty("jwks_uri", e);
		}
		_getMetadataProperty(_x3) {
			var _this12 = this;
			return _asyncToGenerator(function* (e, t = !1) {
				const i = _this12._logger.create(`_getMetadataProperty('${e}')`);
				const n = yield _this12.getMetadata();
				if (i.debug("resolved"), void 0 === n[e]) {
					if (!0 === t) return void i.warn("Metadata does not contain optional property");
					i.throw(/* @__PURE__ */ new Error("Metadata does not contain property " + e));
				}
				return n[e];
			}).apply(this, arguments);
		}
		getSigningKeys() {
			var _this13 = this;
			return _asyncToGenerator(function* () {
				const e = _this13._logger.create("getSigningKeys");
				if (_this13._signingKeys) return e.debug("returning signingKeys from cache"), _this13._signingKeys;
				const t = yield _this13.getKeysEndpoint(!1);
				e.debug("got jwks_uri", t);
				const i = yield _this13._jsonService.getJson(t, { timeoutInSeconds: _this13._settings.requestTimeoutInSeconds });
				if (e.debug("got key set", i), !Array.isArray(i.keys)) throw e.throw(/* @__PURE__ */ new Error("Missing keys on keyset")), null;
				return _this13._signingKeys = i.keys, _this13._signingKeys;
			})();
		}
	};
	var Y = class {
		constructor({ prefix: e = "oidc.", store: t = localStorage } = {}) {
			this._logger = new L("WebStorageStateStore"), this._store = t, this._prefix = e;
		}
		set(e, t) {
			var _this14 = this;
			return _asyncToGenerator(function* () {
				_this14._logger.create(`set('${e}')`), e = _this14._prefix + e, yield _this14._store.setItem(e, t);
			})();
		}
		get(e) {
			var _this15 = this;
			return _asyncToGenerator(function* () {
				return _this15._logger.create(`get('${e}')`), e = _this15._prefix + e, yield _this15._store.getItem(e);
			})();
		}
		remove(e) {
			var _this16 = this;
			return _asyncToGenerator(function* () {
				_this16._logger.create(`remove('${e}')`), e = _this16._prefix + e;
				const t = yield _this16._store.getItem(e);
				return yield _this16._store.removeItem(e), t;
			})();
		}
		getAllKeys() {
			var _this17 = this;
			return _asyncToGenerator(function* () {
				_this17._logger.create("getAllKeys");
				const e = yield _this17._store.length;
				const t = [];
				for (let i = 0; i < e; i++) {
					const e = yield _this17._store.key(i);
					e && 0 === e.indexOf(_this17._prefix) && t.push(e.substr(_this17._prefix.length));
				}
				return t;
			})();
		}
	};
	var Z = class {
		constructor({ authority: e, metadataUrl: t, metadata: i, signingKeys: n, metadataSeed: s, client_id: r, client_secret: o, response_type: a = "code", scope: c = "openid", redirect_uri: d, post_logout_redirect_uri: l, client_authentication: g = "client_secret_post", token_endpoint_auth_signing_alg: u = "HS256", prompt: h, display: p, max_age: _, ui_locales: w, acr_values: m, resource: f, response_mode: y, filterProtocolClaims: S = !0, loadUserInfo: b = !1, requestTimeoutInSeconds: v, staleStateAgeInSeconds: I = 900, mergeClaimsStrategy: E = { array: "replace" }, disablePKCE: k = !1, stateStore: T, revokeTokenAdditionalContentTypes: R, fetchRequestCredentials: A, refreshTokenAllowedScope: C, extraQueryParams: x = {}, extraTokenParams: P = {}, extraHeaders: O = {}, dpop: U, omitScopeWhenRequesting: L = !1 }) {
			var D;
			if (this.authority = e, t ? this.metadataUrl = t : (this.metadataUrl = e, e && (this.metadataUrl.endsWith("/") || (this.metadataUrl += "/"), this.metadataUrl += ".well-known/openid-configuration")), this.metadata = i, this.metadataSeed = s, this.signingKeys = n, this.client_id = r, this.client_secret = o, this.response_type = a, this.scope = c, this.redirect_uri = d, this.post_logout_redirect_uri = l, this.client_authentication = g, this.token_endpoint_auth_signing_alg = u, this.prompt = h, this.display = p, this.max_age = _, this.ui_locales = w, this.acr_values = m, this.resource = f, this.response_mode = y, this.filterProtocolClaims = null == S || S, this.loadUserInfo = !!b, this.staleStateAgeInSeconds = I, this.mergeClaimsStrategy = E, this.omitScopeWhenRequesting = L, this.disablePKCE = !!k, this.revokeTokenAdditionalContentTypes = R, this.fetchRequestCredentials = A || "same-origin", this.requestTimeoutInSeconds = v, T) this.stateStore = T;
			else {
				const e = "undefined" != typeof window ? window.localStorage : new J();
				this.stateStore = new Y({ store: e });
			}
			if (this.refreshTokenAllowedScope = C, this.extraQueryParams = x, this.extraTokenParams = P, this.extraHeaders = O, this.dpop = U, this.dpop && !(null == (D = this.dpop) ? void 0 : D.store)) throw new Error("A DPoPStore is required when dpop is enabled");
		}
	};
	var ee = class {
		constructor(e, t) {
			var _this18 = this;
			this._settings = e, this._metadataService = t, this._logger = new L("UserInfoService"), this._getClaimsFromJwt = function() {
				var _ref2 = _asyncToGenerator(function* (e) {
					const t = _this18._logger.create("_getClaimsFromJwt");
					try {
						const i = D.decode(e);
						return t.debug("JWT decoding successful"), i;
					} catch (e) {
						throw t.error("Error parsing JWT response"), e;
					}
				});
				return function(_x4) {
					return _ref2.apply(this, arguments);
				};
			}(), this._jsonService = new Q(void 0, this._getClaimsFromJwt, this._settings.extraHeaders);
		}
		getClaims(e) {
			var _this19 = this;
			return _asyncToGenerator(function* () {
				const t = _this19._logger.create("getClaims");
				e || _this19._logger.throw(/* @__PURE__ */ new Error("No token passed"));
				const i = yield _this19._metadataService.getUserInfoEndpoint();
				t.debug("got userinfo url", i);
				const n = yield _this19._jsonService.getJson(i, {
					token: e,
					credentials: _this19._settings.fetchRequestCredentials,
					timeoutInSeconds: _this19._settings.requestTimeoutInSeconds
				});
				return t.debug("got claims", n), n;
			})();
		}
	};
	var te = class {
		constructor(e, t) {
			this._settings = e, this._metadataService = t, this._logger = new L("TokenClient"), this._jsonService = new Q(this._settings.revokeTokenAdditionalContentTypes, null, this._settings.extraHeaders);
		}
		exchangeCode(_ref3) {
			var _this20 = this;
			return _asyncToGenerator(function* () {
				let { grant_type: e = "authorization_code", redirect_uri: t = _this20._settings.redirect_uri, client_id: i = _this20._settings.client_id, client_secret: n = _this20._settings.client_secret, extraHeaders: s } = _ref3, r = _objectWithoutProperties(_ref3, _excluded2);
				const o = _this20._logger.create("exchangeCode");
				i || o.throw(/* @__PURE__ */ new Error("A client_id is required")), t || o.throw(/* @__PURE__ */ new Error("A redirect_uri is required")), r.code || o.throw(/* @__PURE__ */ new Error("A code is required"));
				const a = new URLSearchParams({
					grant_type: e,
					redirect_uri: t
				});
				for (const [e, t] of Object.entries(r)) null != t && a.set(e, t);
				if (("client_secret_basic" === _this20._settings.client_authentication || "client_secret_jwt" === _this20._settings.client_authentication) && null == n) throw o.throw(/* @__PURE__ */ new Error("A client_secret is required")), null;
				let c;
				const d = yield _this20._metadataService.getTokenEndpoint(!1);
				switch (_this20._settings.client_authentication) {
					case "client_secret_basic":
						c = $.generateBasicAuth(i, n);
						break;
					case "client_secret_post":
						a.append("client_id", i), n && a.append("client_secret", n);
						break;
					case "client_secret_jwt": {
						const e = yield $.generateClientAssertionJwt(i, n, d, _this20._settings.token_endpoint_auth_signing_alg);
						a.append("client_id", i), a.append("client_assertion_type", "urn:ietf:params:oauth:client-assertion-type:jwt-bearer"), a.append("client_assertion", e);
						break;
					}
				}
				o.debug("got token endpoint");
				const l = yield _this20._jsonService.postForm(d, {
					body: a,
					basicAuth: c,
					timeoutInSeconds: _this20._settings.requestTimeoutInSeconds,
					initCredentials: _this20._settings.fetchRequestCredentials,
					extraHeaders: s
				});
				return o.debug("got response"), l;
			})();
		}
		exchangeCredentials(_ref4) {
			var _this21 = this;
			return _asyncToGenerator(function* () {
				let { grant_type: e = "password", client_id: t = _this21._settings.client_id, client_secret: i = _this21._settings.client_secret, scope: n = _this21._settings.scope } = _ref4, s = _objectWithoutProperties(_ref4, _excluded3);
				const r = _this21._logger.create("exchangeCredentials");
				t || r.throw(/* @__PURE__ */ new Error("A client_id is required"));
				const o = new URLSearchParams({ grant_type: e });
				_this21._settings.omitScopeWhenRequesting || o.set("scope", n);
				for (const [e, t] of Object.entries(s)) null != t && o.set(e, t);
				if (("client_secret_basic" === _this21._settings.client_authentication || "client_secret_jwt" === _this21._settings.client_authentication) && null == i) throw r.throw(/* @__PURE__ */ new Error("A client_secret is required")), null;
				let a;
				const c = yield _this21._metadataService.getTokenEndpoint(!1);
				switch (_this21._settings.client_authentication) {
					case "client_secret_basic":
						a = $.generateBasicAuth(t, i);
						break;
					case "client_secret_post":
						o.append("client_id", t), i && o.append("client_secret", i);
						break;
					case "client_secret_jwt": {
						const e = yield $.generateClientAssertionJwt(t, i, c, _this21._settings.token_endpoint_auth_signing_alg);
						o.append("client_id", t), o.append("client_assertion_type", "urn:ietf:params:oauth:client-assertion-type:jwt-bearer"), o.append("client_assertion", e);
						break;
					}
				}
				r.debug("got token endpoint");
				const d = yield _this21._jsonService.postForm(c, {
					body: o,
					basicAuth: a,
					timeoutInSeconds: _this21._settings.requestTimeoutInSeconds,
					initCredentials: _this21._settings.fetchRequestCredentials
				});
				return r.debug("got response"), d;
			})();
		}
		exchangeRefreshToken(_ref5) {
			var _this22 = this;
			return _asyncToGenerator(function* () {
				let { grant_type: e = "refresh_token", client_id: t = _this22._settings.client_id, client_secret: i = _this22._settings.client_secret, timeoutInSeconds: n, extraHeaders: s } = _ref5, r = _objectWithoutProperties(_ref5, _excluded4);
				const o = _this22._logger.create("exchangeRefreshToken");
				t || o.throw(/* @__PURE__ */ new Error("A client_id is required")), r.refresh_token || o.throw(/* @__PURE__ */ new Error("A refresh_token is required"));
				const a = new URLSearchParams({ grant_type: e });
				for (const [e, t] of Object.entries(r)) Array.isArray(t) ? t.forEach((t) => a.append(e, t)) : null != t && a.set(e, t);
				if (("client_secret_basic" === _this22._settings.client_authentication || "client_secret_jwt" === _this22._settings.client_authentication) && null == i) throw o.throw(/* @__PURE__ */ new Error("A client_secret is required")), null;
				let c;
				const d = yield _this22._metadataService.getTokenEndpoint(!1);
				switch (_this22._settings.client_authentication) {
					case "client_secret_basic":
						c = $.generateBasicAuth(t, i);
						break;
					case "client_secret_post":
						a.append("client_id", t), i && a.append("client_secret", i);
						break;
					case "client_secret_jwt": {
						const e = yield $.generateClientAssertionJwt(t, i, d, _this22._settings.token_endpoint_auth_signing_alg);
						a.append("client_id", t), a.append("client_assertion_type", "urn:ietf:params:oauth:client-assertion-type:jwt-bearer"), a.append("client_assertion", e);
						break;
					}
				}
				o.debug("got token endpoint");
				const l = yield _this22._jsonService.postForm(d, {
					body: a,
					basicAuth: c,
					timeoutInSeconds: n,
					initCredentials: _this22._settings.fetchRequestCredentials,
					extraHeaders: s
				});
				return o.debug("got response"), l;
			})();
		}
		revoke(e) {
			var _this23 = this;
			return _asyncToGenerator(function* () {
				var t;
				const i = _this23._logger.create("revoke");
				e.token || i.throw(/* @__PURE__ */ new Error("A token is required"));
				const n = yield _this23._metadataService.getRevocationEndpoint(!1);
				i.debug(`got revocation endpoint, revoking ${null != (t = e.token_type_hint) ? t : "default token type"}`);
				const s = new URLSearchParams();
				for (const [t, i] of Object.entries(e)) null != i && s.set(t, i);
				s.set("client_id", _this23._settings.client_id), _this23._settings.client_secret && s.set("client_secret", _this23._settings.client_secret), yield _this23._jsonService.postForm(n, {
					body: s,
					timeoutInSeconds: _this23._settings.requestTimeoutInSeconds
				}), i.debug("got response");
			})();
		}
	};
	var ie = class {
		constructor(e, t, i) {
			this._settings = e, this._metadataService = t, this._claimsService = i, this._logger = new L("ResponseValidator"), this._userInfoService = new ee(this._settings, this._metadataService), this._tokenClient = new te(this._settings, this._metadataService);
		}
		validateSigninResponse(e, t, i) {
			var _this24 = this;
			return _asyncToGenerator(function* () {
				const n = _this24._logger.create("validateSigninResponse");
				_this24._processSigninState(e, t), n.debug("state processed"), yield _this24._processCode(e, t, i), n.debug("code processed"), e.isOpenId && _this24._validateIdTokenAttributes(e), n.debug("tokens validated"), yield _this24._processClaims(e, null == t ? void 0 : t.skipUserInfo, e.isOpenId), n.debug("claims processed");
			})();
		}
		validateCredentialsResponse(e, t) {
			var _this25 = this;
			return _asyncToGenerator(function* () {
				const i = _this25._logger.create("validateCredentialsResponse");
				const n = e.isOpenId && !!e.id_token;
				n && _this25._validateIdTokenAttributes(e), i.debug("tokens validated"), yield _this25._processClaims(e, t, n), i.debug("claims processed");
			})();
		}
		validateRefreshResponse(e, t) {
			var _this26 = this;
			return _asyncToGenerator(function* () {
				const i = _this26._logger.create("validateRefreshResponse");
				e.userState = t.data, null != e.session_state || (e.session_state = t.session_state), null != e.scope || (e.scope = t.scope), e.isOpenId && e.id_token && (_this26._validateIdTokenAttributes(e, t.id_token), i.debug("ID Token validated")), e.id_token || (e.id_token = t.id_token, e.profile = t.profile);
				const n = e.isOpenId && !!e.id_token;
				yield _this26._processClaims(e, !1, n), i.debug("claims processed");
			})();
		}
		validateSignoutResponse(e, t) {
			const i = this._logger.create("validateSignoutResponse");
			if (t.id !== e.state && i.throw(/* @__PURE__ */ new Error("State does not match")), i.debug("state validated"), e.userState = t.data, e.error) throw i.warn("Response was error", e.error), new B(e);
		}
		_processSigninState(e, t) {
			const i = this._logger.create("_processSigninState");
			if (t.id !== e.state && i.throw(/* @__PURE__ */ new Error("State does not match")), t.client_id || i.throw(/* @__PURE__ */ new Error("No client_id on state")), t.authority || i.throw(/* @__PURE__ */ new Error("No authority on state")), this._settings.authority !== t.authority && i.throw(/* @__PURE__ */ new Error("authority mismatch on settings vs. signin state")), this._settings.client_id && this._settings.client_id !== t.client_id && i.throw(/* @__PURE__ */ new Error("client_id mismatch on settings vs. signin state")), i.debug("state validated"), e.userState = t.data, e.url_state = t.url_state, null != e.scope || (e.scope = t.scope), e.error) throw i.warn("Response was error", e.error), new B(e);
			t.code_verifier && !e.code && i.throw(/* @__PURE__ */ new Error("Expected code in response"));
		}
		_processClaims(_x5) {
			var _this27 = this;
			return _asyncToGenerator(function* (e, t = !1, i = !0) {
				const n = _this27._logger.create("_processClaims");
				if (e.profile = _this27._claimsService.filterProtocolClaims(e.profile), t || !_this27._settings.loadUserInfo || !e.access_token) return void n.debug("not loading user info");
				n.debug("loading user info");
				const s = yield _this27._userInfoService.getClaims(e.access_token);
				n.debug("user info claims received from user info endpoint"), i && s.sub !== e.profile.sub && n.throw(/* @__PURE__ */ new Error("subject from UserInfo response does not match subject in ID Token")), e.profile = _this27._claimsService.mergeClaims(e.profile, _this27._claimsService.filterProtocolClaims(s)), n.debug("user info claims received, updated profile:", e.profile);
			}).apply(this, arguments);
		}
		_processCode(e, t, i) {
			var _this28 = this;
			return _asyncToGenerator(function* () {
				const n = _this28._logger.create("_processCode");
				if (e.code) {
					n.debug("Validating code");
					const s = yield _this28._tokenClient.exchangeCode(_objectSpread2({
						client_id: t.client_id,
						client_secret: t.client_secret,
						code: e.code,
						redirect_uri: t.redirect_uri,
						code_verifier: t.code_verifier,
						extraHeaders: i
					}, t.extraTokenParams));
					Object.assign(e, s);
				} else n.debug("No code to process");
			})();
		}
		_validateIdTokenAttributes(e, t) {
			var i;
			const n = this._logger.create("_validateIdTokenAttributes");
			n.debug("decoding ID Token JWT");
			const s = D.decode(null != (i = e.id_token) ? i : "");
			if (s.sub || n.throw(/* @__PURE__ */ new Error("ID Token is missing a subject claim")), t) {
				const e = D.decode(t);
				s.sub !== e.sub && n.throw(/* @__PURE__ */ new Error("sub in id_token does not match current sub")), s.auth_time && s.auth_time !== e.auth_time && n.throw(/* @__PURE__ */ new Error("auth_time in id_token does not match original auth_time")), s.azp && s.azp !== e.azp && n.throw(/* @__PURE__ */ new Error("azp in id_token does not match original azp")), !s.azp && e.azp && n.throw(/* @__PURE__ */ new Error("azp not in id_token, but present in original id_token"));
			}
			e.profile = s;
		}
	};
	var ne = class e {
		constructor(e) {
			this.id = e.id || $.generateUUIDv4(), this.data = e.data, e.created && e.created > 0 ? this.created = e.created : this.created = j.getEpochTime(), this.request_type = e.request_type, this.url_state = e.url_state;
		}
		toStorageString() {
			return new L("State").create("toStorageString"), JSON.stringify({
				id: this.id,
				data: this.data,
				created: this.created,
				request_type: this.request_type,
				url_state: this.url_state
			});
		}
		static fromStorageString(t) {
			return L.createStatic("State", "fromStorageString"), Promise.resolve(new e(JSON.parse(t)));
		}
		static clearStaleState(t, i) {
			return _asyncToGenerator(function* () {
				const n = L.createStatic("State", "clearStaleState");
				const s = j.getEpochTime() - i;
				const r = yield t.getAllKeys();
				n.debug("got keys", r);
				for (let i = 0; i < r.length; i++) {
					const o = r[i];
					const a = yield t.get(o);
					let c = !1;
					if (a) try {
						const t = yield e.fromStorageString(a);
						n.debug("got item from key:", o, t.created), t.created <= s && (c = !0);
					} catch (e) {
						n.error("Error parsing state for key:", o, e), c = !0;
					}
					else n.debug("no item in storage for key:", o), c = !0;
					c && (n.debug("removed item for key:", o), t.remove(o));
				}
			})();
		}
	};
	var se = class e extends ne {
		constructor(e) {
			super(e), this.code_verifier = e.code_verifier, this.code_challenge = e.code_challenge, this.authority = e.authority, this.client_id = e.client_id, this.redirect_uri = e.redirect_uri, this.scope = e.scope, this.client_secret = e.client_secret, this.extraTokenParams = e.extraTokenParams, this.response_mode = e.response_mode, this.skipUserInfo = e.skipUserInfo;
		}
		static create(t) {
			return _asyncToGenerator(function* () {
				const i = !0 === t.code_verifier ? $.generateCodeVerifier() : t.code_verifier || void 0;
				const n = i ? yield $.generateCodeChallenge(i) : void 0;
				return new e(_objectSpread2(_objectSpread2({}, t), {}, {
					code_verifier: i,
					code_challenge: n
				}));
			})();
		}
		toStorageString() {
			return new L("SigninState").create("toStorageString"), JSON.stringify({
				id: this.id,
				data: this.data,
				created: this.created,
				request_type: this.request_type,
				url_state: this.url_state,
				code_verifier: this.code_verifier,
				authority: this.authority,
				client_id: this.client_id,
				redirect_uri: this.redirect_uri,
				scope: this.scope,
				client_secret: this.client_secret,
				extraTokenParams: this.extraTokenParams,
				response_mode: this.response_mode,
				skipUserInfo: this.skipUserInfo
			});
		}
		static fromStorageString(t) {
			L.createStatic("SigninState", "fromStorageString");
			const i = JSON.parse(t);
			return e.create(i);
		}
	};
	var re = class e {
		constructor(e) {
			this.url = e.url, this.state = e.state;
		}
		static create(_ref6) {
			var _this29 = this;
			return _asyncToGenerator(function* () {
				let { url: t, authority: i, client_id: n, redirect_uri: s, response_type: r, scope: o, state_data: a, response_mode: c, request_type: d, client_secret: l, nonce: g, url_state: u, resource: h, skipUserInfo: p, extraQueryParams: _, extraTokenParams: w, disablePKCE: m, dpopJkt: f, omitScopeWhenRequesting: y } = _ref6, S = _objectWithoutProperties(_ref6, _excluded5);
				if (!t) throw _this29._logger.error("create: No url passed"), /* @__PURE__ */ new Error("url");
				if (!n) throw _this29._logger.error("create: No client_id passed"), /* @__PURE__ */ new Error("client_id");
				if (!s) throw _this29._logger.error("create: No redirect_uri passed"), /* @__PURE__ */ new Error("redirect_uri");
				if (!r) throw _this29._logger.error("create: No response_type passed"), /* @__PURE__ */ new Error("response_type");
				if (!o) throw _this29._logger.error("create: No scope passed"), /* @__PURE__ */ new Error("scope");
				if (!i) throw _this29._logger.error("create: No authority passed"), /* @__PURE__ */ new Error("authority");
				const b = yield se.create({
					data: a,
					request_type: d,
					url_state: u,
					code_verifier: !m,
					client_id: n,
					authority: i,
					redirect_uri: s,
					response_mode: c,
					client_secret: l,
					scope: o,
					extraTokenParams: w,
					skipUserInfo: p
				});
				const v = new URL(t);
				v.searchParams.append("client_id", n), v.searchParams.append("redirect_uri", s), v.searchParams.append("response_type", r), y || v.searchParams.append("scope", o), g && v.searchParams.append("nonce", g), f && v.searchParams.append("dpop_jkt", f);
				let I = b.id;
				u && (I = `${I}${z}${u}`), v.searchParams.append("state", I), b.code_challenge && (v.searchParams.append("code_challenge", b.code_challenge), v.searchParams.append("code_challenge_method", "S256")), h && (Array.isArray(h) ? h : [h]).forEach((e) => v.searchParams.append("resource", e));
				for (const [e, t] of Object.entries(_objectSpread2(_objectSpread2({ response_mode: c }, S), _))) null != t && v.searchParams.append(e, t.toString());
				return new e({
					url: v.href,
					state: b
				});
			})();
		}
	};
	re._logger = new L("SigninRequest");
	var oe = re;
	var ae = class {
		constructor(e) {
			if (this.access_token = "", this.token_type = "", this.profile = {}, this.state = e.get("state"), this.session_state = e.get("session_state"), this.state) {
				const e = decodeURIComponent(this.state).split(z);
				this.state = e[0], e.length > 1 && (this.url_state = e.slice(1).join(z));
			}
			this.error = e.get("error"), this.error_description = e.get("error_description"), this.error_uri = e.get("error_uri"), this.code = e.get("code");
		}
		get expires_in() {
			if (void 0 !== this.expires_at) return this.expires_at - j.getEpochTime();
		}
		set expires_in(e) {
			"string" == typeof e && (e = Number(e)), void 0 !== e && e >= 0 && (this.expires_at = Math.floor(e) + j.getEpochTime());
		}
		get isOpenId() {
			var e;
			return (null == (e = this.scope) ? void 0 : e.split(" ").includes("openid")) || !!this.id_token;
		}
	};
	var ce = class {
		constructor({ url: e, state_data: t, id_token_hint: i, post_logout_redirect_uri: n, extraQueryParams: s, request_type: r, client_id: o, url_state: a }) {
			if (this._logger = new L("SignoutRequest"), !e) throw this._logger.error("ctor: No url passed"), /* @__PURE__ */ new Error("url");
			const c = new URL(e);
			if (i && c.searchParams.append("id_token_hint", i), o && c.searchParams.append("client_id", o), n && (c.searchParams.append("post_logout_redirect_uri", n), t || a)) {
				this.state = new ne({
					data: t,
					request_type: r,
					url_state: a
				});
				let e = this.state.id;
				a && (e = `${e}${z}${a}`), c.searchParams.append("state", e);
			}
			for (const [e, t] of Object.entries(_objectSpread2({}, s))) null != t && c.searchParams.append(e, t.toString());
			this.url = c.href;
		}
	};
	var de = class {
		constructor(e) {
			if (this.state = e.get("state"), this.state) {
				const e = decodeURIComponent(this.state).split(z);
				this.state = e[0], e.length > 1 && (this.url_state = e.slice(1).join(z));
			}
			this.error = e.get("error"), this.error_description = e.get("error_description"), this.error_uri = e.get("error_uri");
		}
	};
	var le = [
		"nbf",
		"jti",
		"auth_time",
		"nonce",
		"acr",
		"amr",
		"azp",
		"at_hash"
	];
	var ge = [
		"sub",
		"iss",
		"aud",
		"exp",
		"iat"
	];
	var ue = class {
		constructor(e) {
			this._settings = e, this._logger = new L("ClaimsService");
		}
		filterProtocolClaims(e) {
			const t = _objectSpread2({}, e);
			if (this._settings.filterProtocolClaims) {
				let e;
				e = Array.isArray(this._settings.filterProtocolClaims) ? this._settings.filterProtocolClaims : le;
				for (const i of e) ge.includes(i) || delete t[i];
			}
			return t;
		}
		mergeClaims(e, t) {
			const i = _objectSpread2({}, e);
			for (const [e, n] of Object.entries(t)) if (i[e] !== n) if (Array.isArray(i[e]) || Array.isArray(n)) if ("replace" == this._settings.mergeClaimsStrategy.array) i[e] = n;
			else {
				const t = Array.isArray(i[e]) ? i[e] : [i[e]];
				for (const e of Array.isArray(n) ? n : [n]) t.includes(e) || t.push(e);
				i[e] = t;
			}
			else "object" == typeof i[e] && "object" == typeof n ? i[e] = this.mergeClaims(i[e], n) : i[e] = n;
			return i;
		}
	};
	var he = class {
		constructor(e, t) {
			this.keys = e, this.nonce = t;
		}
	};
	var pe = class {
		constructor(e, t) {
			this._logger = new L("OidcClient"), this.settings = e instanceof Z ? e : new Z(e), this.metadataService = null != t ? t : new X(this.settings), this._claimsService = new ue(this.settings), this._validator = new ie(this.settings, this.metadataService, this._claimsService), this._tokenClient = new te(this.settings, this.metadataService);
		}
		createSigninRequest({ state: e, request: t, request_uri: i, request_type: n, id_token_hint: s, login_hint: r, skipUserInfo: o, nonce: a, url_state: c, response_type: d = this.settings.response_type, scope: l = this.settings.scope, redirect_uri: g = this.settings.redirect_uri, prompt: u = this.settings.prompt, display: h = this.settings.display, max_age: p = this.settings.max_age, ui_locales: _ = this.settings.ui_locales, acr_values: w = this.settings.acr_values, resource: m = this.settings.resource, response_mode: f = this.settings.response_mode, extraQueryParams: y = this.settings.extraQueryParams, extraTokenParams: S = this.settings.extraTokenParams, dpopJkt: b, omitScopeWhenRequesting: v = this.settings.omitScopeWhenRequesting }) {
			var _this30 = this;
			return _asyncToGenerator(function* () {
				const I = _this30._logger.create("createSigninRequest");
				if ("code" !== d) throw new Error("Only the Authorization Code flow (with PKCE) is supported");
				const E = yield _this30.metadataService.getAuthorizationEndpoint();
				I.debug("Received authorization endpoint", E);
				const k = yield oe.create({
					url: E,
					authority: _this30.settings.authority,
					client_id: _this30.settings.client_id,
					redirect_uri: g,
					response_type: d,
					scope: l,
					state_data: e,
					url_state: c,
					prompt: u,
					display: h,
					max_age: p,
					ui_locales: _,
					id_token_hint: s,
					login_hint: r,
					acr_values: w,
					dpopJkt: b,
					resource: m,
					request: t,
					request_uri: i,
					extraQueryParams: y,
					extraTokenParams: S,
					request_type: n,
					response_mode: f,
					client_secret: _this30.settings.client_secret,
					skipUserInfo: o,
					nonce: a,
					disablePKCE: _this30.settings.disablePKCE,
					omitScopeWhenRequesting: v
				});
				yield _this30.clearStaleState();
				const T = k.state;
				return yield _this30.settings.stateStore.set(T.id, T.toStorageString()), k;
			})();
		}
		readSigninResponseState(_x6) {
			var _this31 = this;
			return _asyncToGenerator(function* (e, t = !1) {
				const i = _this31._logger.create("readSigninResponseState");
				const n = new ae(G.readParams(e, _this31.settings.response_mode));
				if (!n.state) throw i.throw(/* @__PURE__ */ new Error("No state in response")), null;
				const s = yield _this31.settings.stateStore[t ? "remove" : "get"](n.state);
				if (!s) throw i.throw(/* @__PURE__ */ new Error("No matching state found in storage")), null;
				return {
					state: yield se.fromStorageString(s),
					response: n
				};
			}).apply(this, arguments);
		}
		processSigninResponse(_x7, _x8) {
			var _this32 = this;
			return _asyncToGenerator(function* (e, t, i = !0) {
				const n = _this32._logger.create("processSigninResponse"), { state: s, response: r } = yield _this32.readSigninResponseState(e, i);
				if (n.debug("received state from storage; validating response"), _this32.settings.dpop && _this32.settings.dpop.store) {
					const e = yield _this32.getDpopProof(_this32.settings.dpop.store);
					t = _objectSpread2(_objectSpread2({}, t), {}, { DPoP: e });
				}
				try {
					yield _this32._validator.validateSigninResponse(r, s, t);
				} catch (e) {
					if (!(e instanceof V && _this32.settings.dpop)) throw e;
					{
						const i = yield _this32.getDpopProof(_this32.settings.dpop.store, e.nonce);
						t.DPoP = i, yield _this32._validator.validateSigninResponse(r, s, t);
					}
				}
				return r;
			}).apply(this, arguments);
		}
		getDpopProof(e, t) {
			var _this33 = this;
			return _asyncToGenerator(function* () {
				let i;
				let n;
				return (yield e.getAllKeys()).includes(_this33.settings.client_id) ? (n = yield e.get(_this33.settings.client_id), n.nonce !== t && t && (n.nonce = t, yield e.set(_this33.settings.client_id, n))) : (i = yield $.generateDPoPKeys(), n = new he(i, t), yield e.set(_this33.settings.client_id, n)), yield $.generateDPoPProof({
					url: yield _this33.metadataService.getTokenEndpoint(!1),
					httpMethod: "POST",
					keyPair: n.keys,
					nonce: n.nonce
				});
			})();
		}
		processResourceOwnerPasswordCredentials({ username: e, password: t, skipUserInfo: i = !1, extraTokenParams: n = {} }) {
			var _this34 = this;
			return _asyncToGenerator(function* () {
				const s = yield _this34._tokenClient.exchangeCredentials(_objectSpread2({
					username: e,
					password: t
				}, n));
				const r = new ae(new URLSearchParams());
				return Object.assign(r, s), yield _this34._validator.validateCredentialsResponse(r, i), r;
			})();
		}
		useRefreshToken({ state: e, redirect_uri: t, resource: i, timeoutInSeconds: n, extraHeaders: s, extraTokenParams: r }) {
			var _this35 = this;
			return _asyncToGenerator(function* () {
				var o;
				const a = _this35._logger.create("useRefreshToken");
				let c;
				let d;
				if (void 0 === _this35.settings.refreshTokenAllowedScope) c = e.scope;
				else {
					const t = _this35.settings.refreshTokenAllowedScope.split(" ");
					c = ((null == (o = e.scope) ? void 0 : o.split(" ")) || []).filter((e) => t.includes(e)).join(" ");
				}
				if (_this35.settings.dpop && _this35.settings.dpop.store) {
					const e = yield _this35.getDpopProof(_this35.settings.dpop.store);
					s = _objectSpread2(_objectSpread2({}, s), {}, { DPoP: e });
				}
				try {
					d = yield _this35._tokenClient.exchangeRefreshToken(_objectSpread2({
						refresh_token: e.refresh_token,
						scope: c,
						redirect_uri: t,
						resource: i,
						timeoutInSeconds: n,
						extraHeaders: s
					}, r));
				} catch (o) {
					if (!(o instanceof V && _this35.settings.dpop)) throw o;
					s.DPoP = yield _this35.getDpopProof(_this35.settings.dpop.store, o.nonce), d = yield _this35._tokenClient.exchangeRefreshToken(_objectSpread2({
						refresh_token: e.refresh_token,
						scope: c,
						redirect_uri: t,
						resource: i,
						timeoutInSeconds: n,
						extraHeaders: s
					}, r));
				}
				const l = new ae(new URLSearchParams());
				return Object.assign(l, d), a.debug("validating response", l), yield _this35._validator.validateRefreshResponse(l, _objectSpread2(_objectSpread2({}, e), {}, { scope: c })), l;
			})();
		}
		createSignoutRequest() {
			var _this36 = this;
			return _asyncToGenerator(function* ({ state: e, id_token_hint: t, client_id: i, request_type: n, url_state: s, post_logout_redirect_uri: r = this.settings.post_logout_redirect_uri, extraQueryParams: o = this.settings.extraQueryParams } = {}) {
				const a = _this36._logger.create("createSignoutRequest");
				const c = yield _this36.metadataService.getEndSessionEndpoint();
				if (!c) throw a.throw(/* @__PURE__ */ new Error("No end session endpoint")), null;
				a.debug("Received end session endpoint", c), i || !r || t || (i = _this36.settings.client_id);
				const d = new ce({
					url: c,
					id_token_hint: t,
					client_id: i,
					post_logout_redirect_uri: r,
					state_data: e,
					extraQueryParams: o,
					request_type: n,
					url_state: s
				});
				yield _this36.clearStaleState();
				const l = d.state;
				return l && (a.debug("Signout request has state to persist"), yield _this36.settings.stateStore.set(l.id, l.toStorageString())), d;
			}).apply(this, arguments);
		}
		readSignoutResponseState(_x9) {
			var _this37 = this;
			return _asyncToGenerator(function* (e, t = !1) {
				const i = _this37._logger.create("readSignoutResponseState");
				const n = new de(G.readParams(e, _this37.settings.response_mode));
				if (!n.state) {
					if (i.debug("No state in response"), n.error) throw i.warn("Response was error:", n.error), new B(n);
					return {
						state: void 0,
						response: n
					};
				}
				const s = yield _this37.settings.stateStore[t ? "remove" : "get"](n.state);
				if (!s) throw i.throw(/* @__PURE__ */ new Error("No matching state found in storage")), null;
				return {
					state: yield ne.fromStorageString(s),
					response: n
				};
			}).apply(this, arguments);
		}
		processSignoutResponse(e) {
			var _this38 = this;
			return _asyncToGenerator(function* () {
				const t = _this38._logger.create("processSignoutResponse"), { state: i, response: n } = yield _this38.readSignoutResponseState(e, !0);
				return i ? (t.debug("Received state from storage; validating response"), _this38._validator.validateSignoutResponse(n, i)) : t.debug("No state from storage; skipping response validation"), n;
			})();
		}
		clearStaleState() {
			return this._logger.create("clearStaleState"), ne.clearStaleState(this.settings.stateStore, this.settings.staleStateAgeInSeconds);
		}
		revokeToken(e, t) {
			var _this39 = this;
			return _asyncToGenerator(function* () {
				return _this39._logger.create("revokeToken"), yield _this39._tokenClient.revoke({
					token: e,
					token_type_hint: t
				});
			})();
		}
	};
	var _e = class {
		constructor(e) {
			var _this40 = this;
			this._userManager = e, this._logger = new L("SessionMonitor"), this._start = function() {
				var _ref7 = _asyncToGenerator(function* (e) {
					const t = e.session_state;
					if (!t) return;
					const i = _this40._logger.create("_start");
					if (e.profile ? (_this40._sub = e.profile.sub, i.debug("session_state", t, ", sub", _this40._sub)) : (_this40._sub = void 0, i.debug("session_state", t, ", anonymous user")), _this40._checkSessionIFrame) _this40._checkSessionIFrame.start(t);
					else try {
						const e = yield _this40._userManager.metadataService.getCheckSessionIframe();
						if (e) {
							i.debug("initializing check session iframe");
							const n = _this40._userManager.settings.client_id;
							const s = _this40._userManager.settings.checkSessionIntervalInSeconds;
							const r = _this40._userManager.settings.stopCheckSessionOnError;
							const o = new K(_this40._callback, n, e, s, r);
							yield o.load(), _this40._checkSessionIFrame = o, o.start(t);
						} else i.warn("no check session iframe found in the metadata");
					} catch (e) {
						i.error("Error from getCheckSessionIframe:", e instanceof Error ? e.message : e);
					}
				});
				return function(_x10) {
					return _ref7.apply(this, arguments);
				};
			}(), this._stop = () => {
				const e = this._logger.create("_stop");
				if (this._sub = void 0, this._checkSessionIFrame && this._checkSessionIFrame.stop(), this._userManager.settings.monitorAnonymousSession) {
					const t = setInterval(_asyncToGenerator(function* () {
						clearInterval(t);
						try {
							const e = yield _this40._userManager.querySessionStatus();
							if (e) {
								const t = {
									session_state: e.session_state,
									profile: e.sub ? { sub: e.sub } : null
								};
								_this40._start(t);
							}
						} catch (t) {
							e.error("error from querySessionStatus", t instanceof Error ? t.message : t);
						}
					}), 1e3);
				}
			}, this._callback = _asyncToGenerator(function* () {
				const e = _this40._logger.create("_callback");
				try {
					const t = yield _this40._userManager.querySessionStatus();
					let i = !0;
					t && _this40._checkSessionIFrame ? t.sub === _this40._sub ? (i = !1, _this40._checkSessionIFrame.start(t.session_state), e.debug("same sub still logged in at OP, session state has changed, restarting check session iframe; session_state", t.session_state), yield _this40._userManager.events._raiseUserSessionChanged()) : e.debug("different subject signed into OP", t.sub) : e.debug("subject no longer signed into OP"), i ? _this40._sub ? yield _this40._userManager.events._raiseUserSignedOut() : yield _this40._userManager.events._raiseUserSignedIn() : e.debug("no change in session detected, no event to raise");
				} catch (t) {
					_this40._sub && (e.debug("Error calling queryCurrentSigninSession; raising signed out event", t), yield _this40._userManager.events._raiseUserSignedOut());
				}
			}), e || this._logger.throw(/* @__PURE__ */ new Error("No user manager passed")), this._userManager.events.addUserLoaded(this._start), this._userManager.events.addUserUnloaded(this._stop), this._init().catch((e) => {
				this._logger.error(e);
			});
		}
		_init() {
			var _this41 = this;
			return _asyncToGenerator(function* () {
				_this41._logger.create("_init");
				const e = yield _this41._userManager.getUser();
				if (e) _this41._start(e);
				else if (_this41._userManager.settings.monitorAnonymousSession) {
					const e = yield _this41._userManager.querySessionStatus();
					if (e) {
						const t = {
							session_state: e.session_state,
							profile: e.sub ? { sub: e.sub } : null
						};
						_this41._start(t);
					}
				}
			})();
		}
	};
	var we = class e {
		constructor(e) {
			var t;
			this.id_token = e.id_token, this.session_state = null != (t = e.session_state) ? t : null, this.access_token = e.access_token, this.refresh_token = e.refresh_token, this.token_type = e.token_type, this.scope = e.scope, this.profile = e.profile, this.expires_at = e.expires_at, this.state = e.userState, this.url_state = e.url_state;
		}
		get expires_in() {
			if (void 0 !== this.expires_at) return this.expires_at - j.getEpochTime();
		}
		set expires_in(e) {
			void 0 !== e && (this.expires_at = Math.floor(e) + j.getEpochTime());
		}
		get expired() {
			const e = this.expires_in;
			if (void 0 !== e) return e <= 0;
		}
		get scopes() {
			var e;
			var t;
			return null != (t = null == (e = this.scope) ? void 0 : e.split(" ")) ? t : [];
		}
		toStorageString() {
			return new L("User").create("toStorageString"), JSON.stringify({
				id_token: this.id_token,
				session_state: this.session_state,
				access_token: this.access_token,
				refresh_token: this.refresh_token,
				token_type: this.token_type,
				scope: this.scope,
				profile: this.profile,
				expires_at: this.expires_at
			});
		}
		static fromStorageString(t) {
			return L.createStatic("User", "fromStorageString"), new e(JSON.parse(t));
		}
	};
	var me = "oidc-client";
	var fe = class {
		constructor() {
			this._abort = new M("Window navigation aborted"), this._disposeHandlers = /* @__PURE__ */ new Set(), this._window = null;
		}
		navigate(e) {
			var _this42 = this;
			return _asyncToGenerator(function* () {
				const t = _this42._logger.create("navigate");
				if (!_this42._window) throw new Error("Attempted to navigate on a disposed window");
				t.debug("setting URL in window"), _this42._window.location.replace(e.url);
				const { url: i, keepOpen: n } = yield new Promise((i, n) => {
					const s = (s) => {
						var r;
						const o = s.data;
						const a = null != (r = e.scriptOrigin) ? r : window.location.origin;
						if (s.origin === a && (null == o ? void 0 : o.source) === me) {
							try {
								const i = G.readParams(o.url, e.response_mode).get("state");
								if (i || t.warn("no state found in response url"), s.source !== _this42._window && i !== e.state) return;
							} catch (_unused) {
								_this42._dispose(), n(/* @__PURE__ */ new Error("Invalid response from window"));
							}
							i(o);
						}
					};
					window.addEventListener("message", s, !1), _this42._disposeHandlers.add(() => window.removeEventListener("message", s, !1));
					const r = new BroadcastChannel(`oidc-client-popup-${e.state}`);
					r.addEventListener("message", s, !1), _this42._disposeHandlers.add(() => r.close()), _this42._disposeHandlers.add(_this42._abort.addHandler((e) => {
						_this42._dispose(), n(e);
					}));
				});
				return t.debug("got response from window"), _this42._dispose(), n || _this42.close(), { url: i };
			})();
		}
		_dispose() {
			this._logger.create("_dispose");
			for (const e of this._disposeHandlers) e();
			this._disposeHandlers.clear();
		}
		static _notifyParent(e, t, i = !1, n = window.location.origin) {
			const s = {
				source: me,
				url: t,
				keepOpen: i
			};
			const r = new L("_notifyParent");
			if (e) r.debug("With parent. Using parent.postMessage."), e.postMessage(s, n);
			else {
				r.debug("No parent. Using BroadcastChannel.");
				const e = new URL(t).searchParams.get("state");
				if (!e) throw new Error("No parent and no state in URL. Can't complete notification.");
				const i = new BroadcastChannel(`oidc-client-popup-${e}`);
				i.postMessage(s), i.close();
			}
		}
	};
	var ye = {
		location: !1,
		toolbar: !1,
		height: 640,
		closePopupWindowAfterInSeconds: -1
	};
	var Se = "_blank";
	var be = 60;
	var ve = 2;
	var Ie = class extends Z {
		constructor(e) {
			const { popup_redirect_uri: t = e.redirect_uri, popup_post_logout_redirect_uri: i = e.post_logout_redirect_uri, popupWindowFeatures: n = ye, popupWindowTarget: s = Se, redirectMethod: r = "assign", redirectTarget: o = "self", iframeNotifyParentOrigin: a = e.iframeNotifyParentOrigin, iframeScriptOrigin: c = e.iframeScriptOrigin, requestTimeoutInSeconds: d, silent_redirect_uri: l = e.redirect_uri, silentRequestTimeoutInSeconds: g, automaticSilentRenew: u = !0, validateSubOnSilentRenew: h = !0, includeIdTokenInSilentRenew: p = !1, monitorSession: _ = !1, monitorAnonymousSession: w = !1, checkSessionIntervalInSeconds: m = ve, query_status_response_type: f = "code", stopCheckSessionOnError: y = !0, revokeTokenTypes: S = ["access_token", "refresh_token"], revokeTokensOnSignout: b = !1, includeIdTokenInSilentSignout: v = !1, accessTokenExpiringNotificationTimeInSeconds: I = be, userStore: E } = e;
			if (super(e), this.popup_redirect_uri = t, this.popup_post_logout_redirect_uri = i, this.popupWindowFeatures = n, this.popupWindowTarget = s, this.redirectMethod = r, this.redirectTarget = o, this.iframeNotifyParentOrigin = a, this.iframeScriptOrigin = c, this.silent_redirect_uri = l, this.silentRequestTimeoutInSeconds = g || d || 10, this.automaticSilentRenew = u, this.validateSubOnSilentRenew = h, this.includeIdTokenInSilentRenew = p, this.monitorSession = _, this.monitorAnonymousSession = w, this.checkSessionIntervalInSeconds = m, this.stopCheckSessionOnError = y, this.query_status_response_type = f, this.revokeTokenTypes = S, this.revokeTokensOnSignout = b, this.includeIdTokenInSilentSignout = v, this.accessTokenExpiringNotificationTimeInSeconds = I, E) this.userStore = E;
			else {
				const e = "undefined" != typeof window ? window.sessionStorage : new J();
				this.userStore = new Y({ store: e });
			}
		}
	};
	var Ee = class e extends fe {
		constructor({ silentRequestTimeoutInSeconds: t = 10 }) {
			super(), this._logger = new L("IFrameWindow"), this._timeoutInSeconds = t, this._frame = e.createHiddenIframe(), this._window = this._frame.contentWindow;
		}
		static createHiddenIframe() {
			const e = window.document.createElement("iframe");
			return e.style.visibility = "hidden", e.style.position = "fixed", e.style.left = "-1000px", e.style.top = "0", e.width = "0", e.height = "0", window.document.body.appendChild(e), e;
		}
		navigate(e) {
			var _superprop_getNavigate = () => super.navigate;
			var _this43 = this;
			return _asyncToGenerator(function* () {
				_this43._logger.debug("navigate: Using timeout of:", _this43._timeoutInSeconds);
				const t = setTimeout(() => {
					_this43._abort.raise(new W("IFrame timed out without a response"));
				}, 1e3 * _this43._timeoutInSeconds);
				return _this43._disposeHandlers.add(() => clearTimeout(t)), yield _superprop_getNavigate().call(_this43, e);
			})();
		}
		close() {
			var e;
			this._frame && (this._frame.parentNode && (this._frame.addEventListener("load", (e) => {
				var t;
				const i = e.target;
				null == (t = i.parentNode) || t.removeChild(i), this._abort.raise(/* @__PURE__ */ new Error("IFrame removed from DOM"));
			}, !0), null == (e = this._frame.contentWindow) || e.location.replace("about:blank")), this._frame = null), this._window = null;
		}
		static notifyParent(e, t) {
			return super._notifyParent(window.parent, e, !1, t);
		}
	};
	var ke = class {
		constructor(e) {
			this._settings = e, this._logger = new L("IFrameNavigator");
		}
		prepare({ silentRequestTimeoutInSeconds: e = this._settings.silentRequestTimeoutInSeconds }) {
			return _asyncToGenerator(function* () {
				return new Ee({ silentRequestTimeoutInSeconds: e });
			})();
		}
		callback(e) {
			var _this44 = this;
			return _asyncToGenerator(function* () {
				_this44._logger.create("callback"), Ee.notifyParent(e, _this44._settings.iframeNotifyParentOrigin);
			})();
		}
	};
	var Te = class extends fe {
		constructor({ popupWindowTarget: e = Se, popupWindowFeatures: t = {}, popupSignal: i, popupAbortOnClose: n }) {
			super(), this._logger = new L("PopupWindow");
			const s = H.center(_objectSpread2(_objectSpread2({}, ye), t));
			this._window = window.open(void 0, e, H.serialize(s)), this.abortOnClose = Boolean(n), i && i.addEventListener("abort", () => {
				var e;
				this._abort.raise(new Error(null != (e = i.reason) ? e : "Popup aborted"));
			}), t.closePopupWindowAfterInSeconds && t.closePopupWindowAfterInSeconds > 0 && setTimeout(() => {
				this._window && "boolean" == typeof this._window.closed && !this._window.closed ? this.close() : this._abort.raise(/* @__PURE__ */ new Error("Popup blocked by user"));
			}, 1e3 * t.closePopupWindowAfterInSeconds);
		}
		navigate(e) {
			var _superprop_getNavigate2 = () => super.navigate;
			var _this45 = this;
			return _asyncToGenerator(function* () {
				var t;
				null == (t = _this45._window) || t.focus();
				const i = setInterval(() => {
					_this45._window && !_this45._window.closed || (_this45._logger.debug("Popup closed by user or isolated by redirect"), n(), _this45._disposeHandlers.delete(n), _this45.abortOnClose && _this45._abort.raise(/* @__PURE__ */ new Error("Popup closed by user")));
				}, 500);
				const n = () => clearInterval(i);
				return _this45._disposeHandlers.add(n), yield _superprop_getNavigate2().call(_this45, e);
			})();
		}
		close() {
			this._window && (this._window.closed || (this._window.close(), this._abort.raise(/* @__PURE__ */ new Error("Popup closed")))), this._window = null;
		}
		static notifyOpener(e, t) {
			super._notifyParent(window.opener, e, t), t || window.opener || window.close();
		}
	};
	var Re = class {
		constructor(e) {
			this._settings = e, this._logger = new L("PopupNavigator");
		}
		prepare({ popupWindowFeatures: e = this._settings.popupWindowFeatures, popupWindowTarget: t = this._settings.popupWindowTarget, popupSignal: i, popupAbortOnClose: n }) {
			return _asyncToGenerator(function* () {
				return new Te({
					popupWindowFeatures: e,
					popupWindowTarget: t,
					popupSignal: i,
					popupAbortOnClose: n
				});
			})();
		}
		callback(e, { keepOpen: t = !1 }) {
			var _this46 = this;
			return _asyncToGenerator(function* () {
				_this46._logger.create("callback"), Te.notifyOpener(e, t);
			})();
		}
	};
	var Ae = class {
		constructor(e) {
			this._settings = e, this._logger = new L("RedirectNavigator");
		}
		prepare({ redirectMethod: e = this._settings.redirectMethod, redirectTarget: t = this._settings.redirectTarget }) {
			var _this47 = this;
			return _asyncToGenerator(function* () {
				var i;
				_this47._logger.create("prepare");
				let n = window.self;
				"top" === t && (n = null != (i = window.top) ? i : window.self);
				const s = n.location[e].bind(n.location);
				let r;
				return {
					navigate: function() {
						var _ref8 = _asyncToGenerator(function* (e) {
							_this47._logger.create("navigate");
							return yield new Promise((t, i) => {
								r = i, window.addEventListener("pageshow", () => t(window.location.href)), s(e.url);
							});
						});
						return function navigate(_x11) {
							return _ref8.apply(this, arguments);
						};
					}(),
					close: () => {
						_this47._logger.create("close"), null == r || r(/* @__PURE__ */ new Error("Redirect aborted")), n.stop();
					}
				};
			})();
		}
		callback() {
			return _asyncToGenerator(function* () {})();
		}
	};
	var Ce = class extends F {
		constructor(e) {
			super({ expiringNotificationTimeInSeconds: e.accessTokenExpiringNotificationTimeInSeconds }), this._logger = new L("UserManagerEvents"), this._userLoaded = new M("User loaded"), this._userUnloaded = new M("User unloaded"), this._silentRenewError = new M("Silent renew error"), this._userSignedIn = new M("User signed in"), this._userSignedOut = new M("User signed out"), this._userSessionChanged = new M("User session changed");
		}
		load(_x12) {
			var _superprop_getLoad = () => super.load;
			var _this48 = this;
			return _asyncToGenerator(function* (e, t = !0) {
				yield _superprop_getLoad().call(_this48, e), t && (yield _this48._userLoaded.raise(e));
			}).apply(this, arguments);
		}
		unload() {
			var _superprop_getUnload = () => super.unload;
			var _this49 = this;
			return _asyncToGenerator(function* () {
				yield _superprop_getUnload().call(_this49), yield _this49._userUnloaded.raise();
			})();
		}
		addUserLoaded(e) {
			return this._userLoaded.addHandler(e);
		}
		removeUserLoaded(e) {
			return this._userLoaded.removeHandler(e);
		}
		addUserUnloaded(e) {
			return this._userUnloaded.addHandler(e);
		}
		removeUserUnloaded(e) {
			return this._userUnloaded.removeHandler(e);
		}
		addSilentRenewError(e) {
			return this._silentRenewError.addHandler(e);
		}
		removeSilentRenewError(e) {
			return this._silentRenewError.removeHandler(e);
		}
		_raiseSilentRenewError(e) {
			var _this50 = this;
			return _asyncToGenerator(function* () {
				yield _this50._silentRenewError.raise(e);
			})();
		}
		addUserSignedIn(e) {
			return this._userSignedIn.addHandler(e);
		}
		removeUserSignedIn(e) {
			this._userSignedIn.removeHandler(e);
		}
		_raiseUserSignedIn() {
			var _this51 = this;
			return _asyncToGenerator(function* () {
				yield _this51._userSignedIn.raise();
			})();
		}
		addUserSignedOut(e) {
			return this._userSignedOut.addHandler(e);
		}
		removeUserSignedOut(e) {
			this._userSignedOut.removeHandler(e);
		}
		_raiseUserSignedOut() {
			var _this52 = this;
			return _asyncToGenerator(function* () {
				yield _this52._userSignedOut.raise();
			})();
		}
		addUserSessionChanged(e) {
			return this._userSessionChanged.addHandler(e);
		}
		removeUserSessionChanged(e) {
			this._userSessionChanged.removeHandler(e);
		}
		_raiseUserSessionChanged() {
			var _this53 = this;
			return _asyncToGenerator(function* () {
				yield _this53._userSessionChanged.raise();
			})();
		}
	};
	var xe = class {
		constructor(e) {
			var _this54 = this;
			this._userManager = e, this._logger = new L("SilentRenewService"), this._isStarted = !1, this._retryTimer = new j("Retry Silent Renew"), this._tokenExpiring = _asyncToGenerator(function* () {
				const e = _this54._logger.create("_tokenExpiring");
				try {
					yield _this54._userManager.signinSilent(), e.debug("silent token renewal successful");
				} catch (t) {
					if (t instanceof W) return e.warn("ErrorTimeout from signinSilent:", t, "retry in 5s"), void _this54._retryTimer.init(5);
					e.error("Error from signinSilent:", t), yield _this54._userManager.events._raiseSilentRenewError(t);
				}
			});
		}
		start() {
			var _this55 = this;
			return _asyncToGenerator(function* () {
				const e = _this55._logger.create("start");
				if (!_this55._isStarted) {
					_this55._isStarted = !0, _this55._userManager.events.addAccessTokenExpiring(_this55._tokenExpiring), _this55._retryTimer.addHandler(_this55._tokenExpiring);
					try {
						yield _this55._userManager.getUser();
					} catch (t) {
						e.error("getUser error", t);
					}
				}
			})();
		}
		stop() {
			this._isStarted && (this._retryTimer.cancel(), this._retryTimer.removeHandler(this._tokenExpiring), this._userManager.events.removeAccessTokenExpiring(this._tokenExpiring), this._isStarted = !1);
		}
	};
	var Pe = class {
		constructor(e) {
			this.refresh_token = e.refresh_token, this.id_token = e.id_token, this.session_state = e.session_state, this.scope = e.scope, this.profile = e.profile, this.data = e.state;
		}
	};
	var Oe = class {
		constructor(e, t, i, n) {
			this._logger = new L("UserManager"), this.settings = new Ie(e), this._client = new pe(e), this._redirectNavigator = null != t ? t : new Ae(this.settings), this._popupNavigator = null != i ? i : new Re(this.settings), this._iframeNavigator = null != n ? n : new ke(this.settings), this._events = new Ce(this.settings), this._silentRenewService = new xe(this), this.settings.automaticSilentRenew && this.startSilentRenew(), this._sessionMonitor = null, this.settings.monitorSession && (this._sessionMonitor = new _e(this));
		}
		get events() {
			return this._events;
		}
		get metadataService() {
			return this._client.metadataService;
		}
		getUser() {
			var _this56 = this;
			return _asyncToGenerator(function* (e = !1) {
				const t = _this56._logger.create("getUser");
				const i = yield _this56._loadUser();
				return i ? (t.info("user loaded"), yield _this56._events.load(i, e), i) : (t.info("user not found in storage"), null);
			}).apply(this, arguments);
		}
		removeUser() {
			var _this57 = this;
			return _asyncToGenerator(function* () {
				const e = _this57._logger.create("removeUser");
				yield _this57.storeUser(null), e.info("user removed from storage"), yield _this57._events.unload();
			})();
		}
		signinRedirect() {
			var _this58 = this;
			return _asyncToGenerator(function* (e = {}) {
				var t;
				_this58._logger.create("signinRedirect");
				const { redirectMethod: i } = e, n = _objectWithoutProperties(e, _excluded6);
				let s;
				null != (t = _this58.settings.dpop) && t.bind_authorization_code && (s = yield _this58.generateDPoPJkt(_this58.settings.dpop));
				const r = yield _this58._redirectNavigator.prepare({ redirectMethod: i });
				yield _this58._signinStart(_objectSpread2({
					request_type: "si:r",
					dpopJkt: s
				}, n), r);
			}).apply(this, arguments);
		}
		signinRedirectCallback() {
			var _this59 = this;
			return _asyncToGenerator(function* (e = window.location.href) {
				const t = _this59._logger.create("signinRedirectCallback");
				const i = yield _this59._signinEnd(e);
				return i.profile && i.profile.sub ? t.info("success, signed in subject", i.profile.sub) : t.info("no subject"), i;
			}).apply(this, arguments);
		}
		signinResourceOwnerCredentials({ username: e, password: t, skipUserInfo: i = !1 }) {
			var _this60 = this;
			return _asyncToGenerator(function* () {
				const n = _this60._logger.create("signinResourceOwnerCredential");
				const s = yield _this60._client.processResourceOwnerPasswordCredentials({
					username: e,
					password: t,
					skipUserInfo: i,
					extraTokenParams: _this60.settings.extraTokenParams
				});
				n.debug("got signin response");
				const r = yield _this60._buildUser(s);
				return r.profile && r.profile.sub ? n.info("success, signed in subject", r.profile.sub) : n.info("no subject"), r;
			})();
		}
		signinPopup() {
			var _this61 = this;
			return _asyncToGenerator(function* (e = {}) {
				var t;
				const i = _this61._logger.create("signinPopup");
				let n;
				null != (t = _this61.settings.dpop) && t.bind_authorization_code && (n = yield _this61.generateDPoPJkt(_this61.settings.dpop));
				const { popupWindowFeatures: s, popupWindowTarget: r, popupSignal: o, popupAbortOnClose: a } = e, c = _objectWithoutProperties(e, _excluded7), d = _this61.settings.popup_redirect_uri;
				d || i.throw(/* @__PURE__ */ new Error("No popup_redirect_uri configured"));
				const l = yield _this61._popupNavigator.prepare({
					popupWindowFeatures: s,
					popupWindowTarget: r,
					popupSignal: o,
					popupAbortOnClose: a
				});
				const g = yield _this61._signin(_objectSpread2({
					request_type: "si:p",
					redirect_uri: d,
					display: "popup",
					dpopJkt: n
				}, c), l);
				return g && (g.profile && g.profile.sub ? i.info("success, signed in subject", g.profile.sub) : i.info("no subject")), g;
			}).apply(this, arguments);
		}
		signinPopupCallback() {
			var _this62 = this;
			return _asyncToGenerator(function* (e = window.location.href, t = !1) {
				const i = _this62._logger.create("signinPopupCallback");
				yield _this62._popupNavigator.callback(e, { keepOpen: t }), i.info("success");
			}).apply(this, arguments);
		}
		signinSilent() {
			var _this63 = this;
			return _asyncToGenerator(function* (e = {}) {
				var t;
				var i;
				const n = _this63._logger.create("signinSilent"), { silentRequestTimeoutInSeconds: s } = e, r = _objectWithoutProperties(e, _excluded8);
				let o;
				let a = yield _this63._loadUser();
				if (!e.forceIframeAuth && (null == a ? void 0 : a.refresh_token)) {
					n.debug("using refresh token");
					const e = new Pe(a);
					return yield _this63._useRefreshToken({
						state: e,
						redirect_uri: r.redirect_uri,
						resource: r.resource,
						extraTokenParams: r.extraTokenParams,
						timeoutInSeconds: s
					});
				}
				null != (t = _this63.settings.dpop) && t.bind_authorization_code && (o = yield _this63.generateDPoPJkt(_this63.settings.dpop));
				const c = _this63.settings.silent_redirect_uri;
				let d;
				c || n.throw(/* @__PURE__ */ new Error("No silent_redirect_uri configured")), a && _this63.settings.validateSubOnSilentRenew && (n.debug("subject prior to silent renew:", a.profile.sub), d = a.profile.sub);
				const l = yield _this63._iframeNavigator.prepare({ silentRequestTimeoutInSeconds: s });
				return a = yield _this63._signin(_objectSpread2({
					request_type: "si:s",
					redirect_uri: c,
					prompt: "none",
					id_token_hint: _this63.settings.includeIdTokenInSilentRenew ? null == a ? void 0 : a.id_token : void 0,
					dpopJkt: o
				}, r), l, d), a && ((null == (i = a.profile) ? void 0 : i.sub) ? n.info("success, signed in subject", a.profile.sub) : n.info("no subject")), a;
			}).apply(this, arguments);
		}
		_useRefreshToken(e) {
			var _this64 = this;
			return _asyncToGenerator(function* () {
				const t = yield _this64._client.useRefreshToken(_objectSpread2({ timeoutInSeconds: _this64.settings.silentRequestTimeoutInSeconds }, e));
				const i = new we(_objectSpread2(_objectSpread2({}, e.state), t));
				return yield _this64.storeUser(i), yield _this64._events.load(i), i;
			})();
		}
		signinSilentCallback() {
			var _this65 = this;
			return _asyncToGenerator(function* (e = window.location.href) {
				const t = _this65._logger.create("signinSilentCallback");
				yield _this65._iframeNavigator.callback(e), t.info("success");
			}).apply(this, arguments);
		}
		signinCallback() {
			var _this66 = this;
			return _asyncToGenerator(function* (e = window.location.href) {
				const { state: t } = yield _this66._client.readSigninResponseState(e);
				switch (t.request_type) {
					case "si:r": return yield _this66.signinRedirectCallback(e);
					case "si:p":
						yield _this66.signinPopupCallback(e);
						break;
					case "si:s":
						yield _this66.signinSilentCallback(e);
						break;
					default: throw new Error("invalid response_type in state");
				}
			}).apply(this, arguments);
		}
		signoutCallback() {
			var _this67 = this;
			return _asyncToGenerator(function* (e = window.location.href, t = !1) {
				const { state: i } = yield _this67._client.readSignoutResponseState(e);
				if (i) switch (i.request_type) {
					case "so:r": return yield _this67.signoutRedirectCallback(e);
					case "so:p":
						yield _this67.signoutPopupCallback(e, t);
						break;
					case "so:s":
						yield _this67.signoutSilentCallback(e);
						break;
					default: throw new Error("invalid response_type in state");
				}
			}).apply(this, arguments);
		}
		querySessionStatus() {
			var _this68 = this;
			return _asyncToGenerator(function* (e = {}) {
				const t = _this68._logger.create("querySessionStatus"), { silentRequestTimeoutInSeconds: i } = e, n = _objectWithoutProperties(e, _excluded9), s = _this68.settings.silent_redirect_uri;
				s || t.throw(/* @__PURE__ */ new Error("No silent_redirect_uri configured"));
				const r = yield _this68._loadUser();
				const o = yield _this68._iframeNavigator.prepare({ silentRequestTimeoutInSeconds: i });
				const a = yield _this68._signinStart(_objectSpread2({
					request_type: "si:s",
					redirect_uri: s,
					prompt: "none",
					id_token_hint: _this68.settings.includeIdTokenInSilentRenew ? null == r ? void 0 : r.id_token : void 0,
					response_type: _this68.settings.query_status_response_type,
					scope: "openid",
					skipUserInfo: !0
				}, n), o);
				try {
					const i = yield _this68._client.processSigninResponse(a.url, {});
					return t.debug("got signin response"), i.session_state && i.profile.sub ? (t.info("success for subject", i.profile.sub), {
						session_state: i.session_state,
						sub: i.profile.sub
					}) : (t.info("success, user not authenticated"), null);
				} catch (e) {
					if (_this68.settings.monitorAnonymousSession && e instanceof B) switch (e.error) {
						case "login_required":
						case "consent_required":
						case "interaction_required":
						case "account_selection_required": return t.info("success for anonymous user"), { session_state: e.session_state };
					}
					throw e;
				}
			}).apply(this, arguments);
		}
		_signin(e, t, i) {
			var _this69 = this;
			return _asyncToGenerator(function* () {
				const n = yield _this69._signinStart(e, t);
				return yield _this69._signinEnd(n.url, i);
			})();
		}
		_signinStart(e, t) {
			var _this70 = this;
			return _asyncToGenerator(function* () {
				const i = _this70._logger.create("_signinStart");
				try {
					const n = yield _this70._client.createSigninRequest(e);
					return i.debug("got signin request"), yield t.navigate({
						url: n.url,
						state: n.state.id,
						response_mode: n.state.response_mode,
						scriptOrigin: _this70.settings.iframeScriptOrigin
					});
				} catch (e) {
					throw i.debug("error after preparing navigator, closing navigator window"), t.close(), e;
				}
			})();
		}
		_signinEnd(e, t) {
			var _this71 = this;
			return _asyncToGenerator(function* () {
				const i = _this71._logger.create("_signinEnd");
				const n = yield _this71._client.processSigninResponse(e, {});
				return i.debug("got signin response"), yield _this71._buildUser(n, t);
			})();
		}
		_buildUser(e, t) {
			var _this72 = this;
			return _asyncToGenerator(function* () {
				const i = _this72._logger.create("_buildUser");
				const n = new we(e);
				if (t) {
					if (t !== n.profile.sub) throw i.debug("current user does not match user returned from signin. sub from signin:", n.profile.sub), new B(_objectSpread2(_objectSpread2({}, e), {}, { error: "login_required" }));
					i.debug("current user matches user returned from signin");
				}
				return yield _this72.storeUser(n), i.debug("user stored"), yield _this72._events.load(n), n;
			})();
		}
		signoutRedirect() {
			var _this73 = this;
			return _asyncToGenerator(function* (e = {}) {
				const t = _this73._logger.create("signoutRedirect"), { redirectMethod: i } = e, n = _objectWithoutProperties(e, _excluded10), s = yield _this73._redirectNavigator.prepare({ redirectMethod: i });
				yield _this73._signoutStart(_objectSpread2({
					request_type: "so:r",
					post_logout_redirect_uri: _this73.settings.post_logout_redirect_uri
				}, n), s), t.info("success");
			}).apply(this, arguments);
		}
		signoutRedirectCallback() {
			var _this74 = this;
			return _asyncToGenerator(function* (e = window.location.href) {
				const t = _this74._logger.create("signoutRedirectCallback");
				const i = yield _this74._signoutEnd(e);
				return t.info("success"), i;
			}).apply(this, arguments);
		}
		signoutPopup() {
			var _this75 = this;
			return _asyncToGenerator(function* (e = {}) {
				const t = _this75._logger.create("signoutPopup"), { popupWindowFeatures: i, popupWindowTarget: n, popupSignal: s } = e, r = _objectWithoutProperties(e, _excluded11), o = _this75.settings.popup_post_logout_redirect_uri, a = yield _this75._popupNavigator.prepare({
					popupWindowFeatures: i,
					popupWindowTarget: n,
					popupSignal: s
				});
				yield _this75._signout(_objectSpread2({
					request_type: "so:p",
					post_logout_redirect_uri: o,
					state: null == o ? void 0 : {}
				}, r), a), t.info("success");
			}).apply(this, arguments);
		}
		signoutPopupCallback() {
			var _this76 = this;
			return _asyncToGenerator(function* (e = window.location.href, t = !1) {
				const i = _this76._logger.create("signoutPopupCallback");
				yield _this76._popupNavigator.callback(e, { keepOpen: t }), i.info("success");
			}).apply(this, arguments);
		}
		_signout(e, t) {
			var _this77 = this;
			return _asyncToGenerator(function* () {
				const i = yield _this77._signoutStart(e, t);
				return yield _this77._signoutEnd(i.url);
			})();
		}
		_signoutStart() {
			var _this78 = this;
			return _asyncToGenerator(function* (e = {}, t) {
				var i;
				const n = _this78._logger.create("_signoutStart");
				try {
					const s = yield _this78._loadUser();
					n.debug("loaded current user from storage"), _this78.settings.revokeTokensOnSignout && (yield _this78._revokeInternal(s));
					const r = e.id_token_hint || s && s.id_token;
					r && (n.debug("setting id_token_hint in signout request"), e.id_token_hint = r), yield _this78.removeUser(), n.debug("user removed, creating signout request");
					const o = yield _this78._client.createSignoutRequest(e);
					return n.debug("got signout request"), yield t.navigate({
						url: o.url,
						state: null == (i = o.state) ? void 0 : i.id,
						scriptOrigin: _this78.settings.iframeScriptOrigin
					});
				} catch (e) {
					throw n.debug("error after preparing navigator, closing navigator window"), t.close(), e;
				}
			}).apply(this, arguments);
		}
		_signoutEnd(e) {
			var _this79 = this;
			return _asyncToGenerator(function* () {
				const t = _this79._logger.create("_signoutEnd");
				const i = yield _this79._client.processSignoutResponse(e);
				return t.debug("got signout response"), i;
			})();
		}
		signoutSilent() {
			var _this80 = this;
			return _asyncToGenerator(function* (e = {}) {
				var t;
				const i = _this80._logger.create("signoutSilent"), { silentRequestTimeoutInSeconds: n } = e, s = _objectWithoutProperties(e, _excluded12), r = _this80.settings.includeIdTokenInSilentSignout ? null == (t = yield _this80._loadUser()) ? void 0 : t.id_token : void 0, o = _this80.settings.popup_post_logout_redirect_uri, a = yield _this80._iframeNavigator.prepare({ silentRequestTimeoutInSeconds: n });
				yield _this80._signout(_objectSpread2({
					request_type: "so:s",
					post_logout_redirect_uri: o,
					id_token_hint: r
				}, s), a), i.info("success");
			}).apply(this, arguments);
		}
		signoutSilentCallback() {
			var _this81 = this;
			return _asyncToGenerator(function* (e = window.location.href) {
				const t = _this81._logger.create("signoutSilentCallback");
				yield _this81._iframeNavigator.callback(e), t.info("success");
			}).apply(this, arguments);
		}
		revokeTokens(e) {
			var _this82 = this;
			return _asyncToGenerator(function* () {
				const t = yield _this82._loadUser();
				yield _this82._revokeInternal(t, e);
			})();
		}
		_revokeInternal(_x13) {
			var _this83 = this;
			return _asyncToGenerator(function* (e, t = this.settings.revokeTokenTypes) {
				const i = _this83._logger.create("_revokeInternal");
				if (!e) return;
				const n = t.filter((t) => "string" == typeof e[t]);
				if (n.length) {
					for (const t of n) yield _this83._client.revokeToken(e[t], t), i.info(`${t} revoked successfully`), "access_token" !== t && (e[t] = null);
					yield _this83.storeUser(e), i.debug("user stored"), yield _this83._events.load(e);
				} else i.debug("no need to revoke due to no token(s)");
			}).apply(this, arguments);
		}
		startSilentRenew() {
			this._logger.create("startSilentRenew"), this._silentRenewService.start();
		}
		stopSilentRenew() {
			this._silentRenewService.stop();
		}
		get _userStoreKey() {
			return `user:${this.settings.authority}:${this.settings.client_id}`;
		}
		_loadUser() {
			var _this84 = this;
			return _asyncToGenerator(function* () {
				const e = _this84._logger.create("_loadUser");
				const t = yield _this84.settings.userStore.get(_this84._userStoreKey);
				return t ? (e.debug("user storageString loaded"), we.fromStorageString(t)) : (e.debug("no user storageString"), null);
			})();
		}
		storeUser(e) {
			var _this85 = this;
			return _asyncToGenerator(function* () {
				const t = _this85._logger.create("storeUser");
				if (e) {
					t.debug("storing user");
					const i = e.toStorageString();
					yield _this85.settings.userStore.set(_this85._userStoreKey, i);
				} else _this85._logger.debug("removing user"), yield _this85.settings.userStore.remove(_this85._userStoreKey), _this85.settings.dpop && (yield _this85.settings.dpop.store.remove(_this85.settings.client_id));
			})();
		}
		clearStaleState() {
			var _this86 = this;
			return _asyncToGenerator(function* () {
				yield _this86._client.clearStaleState();
			})();
		}
		dpopProof(e, t, i, n) {
			var _this87 = this;
			return _asyncToGenerator(function* () {
				var s;
				var r;
				const o = yield null == (r = null == (s = _this87.settings.dpop) ? void 0 : s.store) ? void 0 : r.get(_this87.settings.client_id);
				if (o) return yield $.generateDPoPProof({
					url: e,
					accessToken: null == t ? void 0 : t.access_token,
					httpMethod: i,
					keyPair: o.keys,
					nonce: n
				});
			})();
		}
		generateDPoPJkt(e) {
			var _this88 = this;
			return _asyncToGenerator(function* () {
				let t = yield e.store.get(_this88.settings.client_id);
				if (!t) t = new he(yield $.generateDPoPKeys()), yield e.store.set(_this88.settings.client_id, t);
				return yield $.generateDPoPJkt(t.keys);
			})();
		}
	};
	var Ue = "OAUTH2_LOGIN_FLOW_COMPLETE_EVENT";
	var Le = "OAUTH_GET_TOP_URL";
	var De = "OAUTH_REDIRECT_TOP_WINDOW";
	var Ne = "OAUTH_UPDATE_URL";
	var qe = "OAUTH2_CHECK_PENDING";
	var $e = "oauth2_top_origin";
	var Me = "oauth2_login_success";
	var He = "oauth2_state";
	var je = 60;
	var Ge = Math.max(je - 15, 20);
	var ze = d("oidc-auth", { color: "green" });
	var Be = (e) => ze.extend(e);
	d("oidc-auth-utils");
	var We = () => "undefined" == typeof window ? "" : new URLSearchParams(window.location.search).get("origin") || "";
	var Fe = class Fe {
		constructor() {
			_defineProperty(this, "settings", null);
		}
		static getInstance() {
			return Fe.instance || (Fe.instance = new Fe()), Fe.instance;
		}
		configure(e) {
			this.settings = e;
		}
		isConfigured() {
			return null !== this.settings;
		}
		getSettings() {
			if (!this.settings) throw new Error("OidcAuthConfig not configured. Call configure() or pass settings to OidcAuthClient.initialize().");
			return this.settings;
		}
		getAuthOrigin() {
			const { authOrigin: e, authEndpoint: t } = this.getSettings();
			return e || new URL(t).origin;
		}
		isAccessTokenProactiveRefreshEnabled() {
			var _this$settings$access;
			var _this$settings;
			return (_this$settings$access = (_this$settings = this.settings) === null || _this$settings === void 0 ? void 0 : _this$settings.accessTokenProactiveRefreshEnabled) !== null && _this$settings$access !== void 0 ? _this$settings$access : !0;
		}
		getOidcSettings() {
			const e = "undefined" == typeof window ? "" : window.location.origin, { clientId: t, authEndpoint: i } = this.getSettings(), n = this.getAuthOrigin(), s = "undefined" != typeof window ? new Y({ store: window.localStorage }) : void 0, { accessTokenExpiringNotificationTimeInSeconds: r = je } = this.getSettings();
			return {
				client_id: t,
				authority: n,
				redirect_uri: `${e}/login/oauth-callback`,
				post_logout_redirect_uri: e,
				response_type: "code",
				scope: "openid offline_access",
				automaticSilentRenew: !1,
				accessTokenExpiringNotificationTimeInSeconds: r,
				stateStore: s,
				userStore: s,
				metadata: {
					issuer: n,
					authorization_endpoint: i,
					token_endpoint: `${n}/connect/api/v1/oauth2/token`,
					end_session_endpoint: `${n}/logout/`
				}
			};
		}
		getAccessTokenExpiringNotificationTimeInSeconds() {
			var _this$getSettings$acc;
			return (_this$getSettings$acc = this.getSettings().accessTokenExpiringNotificationTimeInSeconds) !== null && _this$getSettings$acc !== void 0 ? _this$getSettings$acc : je;
		}
		getAccessTokenFreshnessThresholdInSeconds() {
			var _this$getSettings$acc2;
			return (_this$getSettings$acc2 = this.getSettings().accessTokenFreshnessThresholdInSeconds) !== null && _this$getSettings$acc2 !== void 0 ? _this$getSettings$acc2 : Ge;
		}
		getAllowedParentOrigins() {
			var _this$settings2;
			return (_this$settings2 = this.settings) === null || _this$settings2 === void 0 ? void 0 : _this$settings2.allowedParentOrigins;
		}
	};
	_defineProperty(Fe, "instance", null);
	var Ke = Fe.getInstance();
	var Je = Be("oidc-auth:host-api");
	var Ve = function() {
		var _ref9 = _asyncToGenerator(function* (e) {
			return new Promise((t, i) => {
				const n = new MessageChannel();
				let s = !1;
				const r = () => {
					s = !0, n.port1.close();
				};
				const o = setTimeout(() => {
					s || (r(), i(/* @__PURE__ */ new Error(`Host message timeout: ${e.type}`)));
				}, 1e4);
				n.port1.onmessage = (e) => {
					clearTimeout(o), r(), "success" !== e.data.status ? i(e.data.payload) : t(e.data.payload);
				};
				const a = new URLSearchParams(window.location.search).get("origin") || "";
				if (!function(e) {
					if (!e.startsWith("http://") && !e.startsWith("https://")) return !1;
					const t = Ke.getAllowedParentOrigins();
					return !t || 0 === t.length || t.includes(e);
				}(a)) return clearTimeout(o), r(), void i(/* @__PURE__ */ new Error("Origin not allowed"));
				Je.log("posting message to host", e), window.top.postMessage(_objectSpread2({
					type: e.type,
					payload: e.payload
				}, e.data || {}), a, [n.port2]);
			});
		});
		return function Ve(_x14) {
			return _ref9.apply(this, arguments);
		};
	}();
	var Qe = Be("oidc-auth:OidcAuthTimer");
	var Xe = class {
		constructor() {
			_defineProperty(this, "timerHandle", null);
			_defineProperty(this, "expiration", null);
			_defineProperty(this, "initialized", !1);
			_defineProperty(this, "callback", () => {});
			this.timerHandle = null;
		}
		init(e, t, i) {
			const n = e - this.getEpochTime();
			const s = Math.max(n - t, 10);
			this.cancel(), this.expiration = s, this.callback = i, Qe.debug("OIDC: timer - using expiration", s, n, t, e, n - t), this.timerHandle = setTimeout(this.callback, 1e3 * s), this.initialized = !0;
		}
		cancel() {
			this.timerHandle && (clearTimeout(this.timerHandle), this.timerHandle = null), this.expiration = null;
		}
		getEpochTime() {
			return Math.floor(Date.now() / 1e3);
		}
		isInitialized() {
			return this.initialized;
		}
	};
	var Ye = Be("oidc-auth:OidcAuthClient");
	var Ze = class Ze {
		constructor() {
			_defineProperty(this, "userManager", null);
			_defineProperty(this, "initialized", !1);
			_defineProperty(this, "accessTokenExpiringTimer", null);
			_defineProperty(this, "retryTimers", /* @__PURE__ */ new Set());
		}
		static getInstance() {
			return Ze.instance || (Ze.instance = new Ze()), Ze.instance;
		}
		isInitialized() {
			return this.initialized;
		}
		ensureInitialized() {
			if (!this.userManager) throw new Error("OidcAuthClient not initialized. Call initialize() first.");
			return this.userManager;
		}
		initialize(e) {
			if (e && (this.initialized = !1, Ke.configure(e)), this.initialized) Ye.info("OIDC: initialize() - already initialized, skipping");
			else if ("undefined" != typeof window) if (Ke.isConfigured()) try {
				Ye.info("OIDC: initialize() - starting initialization");
				const e = Ke.getOidcSettings();
				this.userManager = new Oe(e), U.setLogger(Ye), U.setLevel(U.ERROR), this.initAccessTokenExpiringTimer(), this.initialized = !0;
			} catch (e) {
				throw Ye.error("OIDC: initialize() - FAILED:", e), e;
			}
			else Ye.warn("OIDC: initialize() - skipped, config not set");
			else Ye.warn("OidcAuthClient cannot initialize on server side");
		}
		initAccessTokenExpiringTimer() {
			var _this89 = this;
			return _asyncToGenerator(function* () {
				Ke.isAccessTokenProactiveRefreshEnabled() ? _this89.getUser().then((e) => {
					const t = e === null || e === void 0 ? void 0 : e.expires_at;
					t && (_this89.accessTokenExpiringTimer || (_this89.accessTokenExpiringTimer = new Xe()), _this89.accessTokenExpiringTimer.init(t, Ke.getAccessTokenExpiringNotificationTimeInSeconds(), _asyncToGenerator(function* () {
						Ye.info("OIDC: timer proactive refresh access token expiring timer fired", t), _this89.proactiveRefreshWithRetry();
					})));
				}).catch((e) => {
					Ye.error("OIDC: initAccessTokenExpiringTimer - FAILED:", e);
				}) : Ye.warn("OIDC: timer - not starting, access token proactive refresh is disabled");
			})();
		}
		getUser() {
			var _this90 = this;
			return _asyncToGenerator(function* () {
				if (!_this90.userManager) return null;
				try {
					return yield _this90.userManager.getUser();
				} catch (e) {
					return Ye.error("OIDC: getUser - FAILED:", e), null;
				}
			})();
		}
		storeUser(e) {
			var _this91 = this;
			return _asyncToGenerator(function* () {
				yield _this91.ensureInitialized().storeUser(e);
			})();
		}
		getAccessToken() {
			var _this92 = this;
			return _asyncToGenerator(function* () {
				const e = yield _this92.getUser();
				if (!e) return Ye.info("OIDC: getAccessToken - no user found"), null;
				if (e.expired) try {
					const e = yield _this92.signinSilent();
					return (e === null || e === void 0 ? void 0 : e.access_token) || null;
				} catch (e) {
					return Ye.error("OIDC: getAccessToken - silent renew failed:", e), null;
				}
				return _this92.isTokenFresh(e) || _this92.signinSilent().catch((e) => {
					Ye.error("OIDC: getAccessToken - background refresh failed:", e);
				}), e.access_token;
			})();
		}
		getUserData() {
			if ("undefined" == typeof window) return null;
			try {
				const e = Ke.getOidcSettings();
				const t = `oidc.user:${e.authority}:${e.client_id}`;
				const i = localStorage.getItem(t);
				if (!i) return null;
				const n = JSON.parse(i);
				const s = n === null || n === void 0 ? void 0 : n.profile;
				return (s === null || s === void 0 ? void 0 : s.sub) ? (Ye.info("OIDC: USER:", { profile: s }), {
					id: s.sub,
					email: s.email || "",
					first_name: s.given_name,
					last_name: s.family_name
				}) : null;
			} catch (e) {
				return Ye.error("OIDC: getUserData - FAILED:", e), null;
			}
		}
		isAuthenticated() {
			var _this93 = this;
			return _asyncToGenerator(function* () {
				const e = yield _this93.getUser();
				return null !== e && !e.expired;
			})();
		}
		signinRedirect(e) {
			var _this94 = this;
			return _asyncToGenerator(function* () {
				yield _this94.ensureInitialized().signinRedirect({
					state: e ? { data: e } : void 0,
					prompt: "login"
				});
			})();
		}
		signinCallback() {
			var _this95 = this;
			return _asyncToGenerator(function* () {
				const t = yield _this95.ensureInitialized().signinCallback();
				if (!t) throw Ye.error("OIDC: signinCallback - FAILED: no user returned"), /* @__PURE__ */ new Error("Signin callback failed: no user returned");
				return t;
			})();
		}
		signinSilent(e) {
			var _this96 = this;
			return _asyncToGenerator(function* () {
				return _this96.ensureInitialized(), "undefined" != typeof navigator && navigator.locks ? navigator.locks.request("oidc-token-refresh", _asyncToGenerator(function* () {
					const t = yield _this96.getUser();
					return t && _this96.isTokenFresh(t, e) ? t : _this96.doSigninSilent();
				})) : (Ye.warn("OIDC: signinSilent - navigator.locks not available, proceeding without lock"), _this96.doSigninSilent());
			})();
		}
		isTokenFresh(e, t) {
			if (!e.expires_at) return !1;
			const i = t !== null && t !== void 0 ? t : Ke.getAccessTokenFreshnessThresholdInSeconds();
			const n = Math.floor(Date.now() / 1e3);
			return e.expires_at - n > i;
		}
		doSigninSilent() {
			var _this97 = this;
			return _asyncToGenerator(function* () {
				const e = _this97.ensureInitialized();
				try {
					return yield e.signinSilent();
				} catch (e) {
					throw Ye.error("OIDC: doSigninSilent - FAILED:", e), e;
				}
			})();
		}
		proactiveRefreshWithRetry(e = 1) {
			if ("undefined" != typeof document && "hidden" === document.visibilityState) {
				Ye.info("OIDC: tab is hidden, deferring proactive refresh until visible");
				const t = () => {
					"visible" === document.visibilityState && (document.removeEventListener("visibilitychange", t), this.proactiveRefreshWithRetry(e));
				};
				document.addEventListener("visibilitychange", t);
				return;
			}
			this.signinSilent(Ke.getAccessTokenExpiringNotificationTimeInSeconds()).then(() => {
				this.initAccessTokenExpiringTimer();
			}).catch((t) => {
				if (Ye.error(`OIDC: proactive refresh failed (attempt ${e}/2):`, t), e < 2) {
					const t = setTimeout(() => {
						this.retryTimers.delete(t), this.proactiveRefreshWithRetry(e + 1);
					}, 3e3);
					this.retryTimers.add(t);
				} else Ye.error("OIDC: proactive refresh exhausted all retries");
			});
		}
		removeUser() {
			var _this98 = this;
			return _asyncToGenerator(function* () {
				var _this$accessTokenExpi;
				const e = _this98.ensureInitialized();
				(_this$accessTokenExpi = _this98.accessTokenExpiringTimer) === null || _this$accessTokenExpi === void 0 || _this$accessTokenExpi.cancel(), _this98.retryTimers.forEach(clearTimeout), _this98.retryTimers.clear(), yield e.removeUser();
			})();
		}
		onUserLoaded(e) {
			this.ensureInitialized().events.addUserLoaded(e);
		}
		offUserLoaded(e) {
			this.ensureInitialized().events.removeUserLoaded(e);
		}
		onUserUnloaded(e) {
			this.ensureInitialized().events.addUserUnloaded(e);
		}
		offUserUnloaded(e) {
			this.ensureInitialized().events.removeUserUnloaded(e);
		}
		onSilentRenewError(e) {
			this.ensureInitialized().events.addSilentRenewError(e);
		}
		offSilentRenewError(e) {
			this.ensureInitialized().events.removeSilentRenewError(e);
		}
		onAccessTokenExpiring(e) {
			this.ensureInitialized().events.addAccessTokenExpiring(e);
		}
		offAccessTokenExpiring(e) {
			this.ensureInitialized().events.removeAccessTokenExpiring(e);
		}
		onAccessTokenExpired(e) {
			this.ensureInitialized().events.addAccessTokenExpired(e);
		}
		offAccessTokenExpired(e) {
			this.ensureInitialized().events.removeAccessTokenExpired(e);
		}
		getLogoutUrl(e, t) {
			const i = new URL(function(e) {
				return `${Ke.getAuthOrigin()}${e.logoutPath}`;
			}(e));
			return t && i.searchParams.set("redirect_to", t), i.toString();
		}
		getWindowOriginParam() {
			const e = new URL(window.location.href).searchParams.get("origin");
			if (!e) throw new Error("iframe origin param is required");
			return e;
		}
		getTopUrl() {
			return _asyncToGenerator(function* () {
				return (yield Ve({ type: Le })).topUrl;
			})();
		}
		isOAuthFlowPending() {
			return _asyncToGenerator(function* () {
				try {
					return (yield Ve({ type: qe })).isPending;
				} catch (e) {
					return Ye.warn("OIDC: isOAuthFlowPending() - failed to check, assuming not pending:", e), !1;
				}
			})();
		}
		triggerLoginFlowViaParent({ loginPath: e, windowPath: t }) {
			var _this99 = this;
			return _asyncToGenerator(function* () {
				Ye.info("OIDC: triggerLoginFlowViaParent() - starting");
				const i = yield _this99.getTopUrl();
				const n = new URL(i).origin;
				const s = `${n}${t}`;
				const r = new URL(`${window.location.origin}${e}`);
				r.searchParams.set($e, n), r.searchParams.set("oauth2_top_wp_url", s), Ye.info("OIDC: triggerLoginFlowViaParent() - redirecting parent to:", r.toString()), yield Ve({
					type: De,
					payload: { url: r.toString() }
				});
			})();
		}
		handleLoginFlowComplete(e, t) {
			var _this100 = this;
			return _asyncToGenerator(function* () {
				var _n$data;
				if (!t) throw new Error("oauthUserState is required");
				const i = _this100.getWindowOriginParam();
				const n = t.state;
				const s = n === null || n === void 0 || (_n$data = n.data) === null || _n$data === void 0 ? void 0 : _n$data[$e];
				if (i !== s) throw Ye.error("OIDC: handleLoginFlowComplete - origin mismatch:", i, "!==", s), /* @__PURE__ */ new Error("Invalid origin in OAuth state");
				try {
					const e = new we(t);
					yield _this100.storeUser(e), _this100.initAccessTokenExpiringTimer(), window.dispatchEvent(new CustomEvent("oidc-auth-completed"));
				} catch (t) {
					Ye.error("OIDC: handleLoginFlowComplete - FAILED to store user:", t), yield _this100.triggerLoginFlowViaParent(e);
				}
			})();
		}
		triggerLogoutViaParent(_x15) {
			var _this101 = this;
			return _asyncToGenerator(function* (e, t = !0) {
				const i = yield _this101.getTopUrl();
				const n = new URL(i).origin;
				const s = t ? `${n}${e.windowPath}` : n;
				yield _this101.removeUser();
				const r = _this101.getLogoutUrl(e, s);
				yield Ve({
					type: De,
					payload: { url: r }
				});
			}).apply(this, arguments);
		}
		cleanOAuthParamsFromUrl() {
			var _this102 = this;
			return _asyncToGenerator(function* () {
				try {
					const e = yield _this102.getTopUrl();
					const t = new URL(e);
					t.searchParams.delete("oauth_code"), t.searchParams.delete("oauth_state"), t.searchParams.delete("start-oauth"), t.searchParams.delete(Me), t.searchParams.delete(He), yield Ve({
						type: Ne,
						payload: { url: t.toString() }
					});
				} catch (e) {
					Ye.warn("Failed to clean OAuth params from URL:", e);
				}
			})();
		}
		setupLoginFlowMessageListener(e) {
			let t = !1;
			const i = (i) => {
				var _i$data;
				if (((_i$data = i.data) === null || _i$data === void 0 ? void 0 : _i$data.type) !== Ue) return;
				if (i.origin !== We()) return void Ye.error("OIDC: origin mismatch - expected:", We(), "received:", i.origin);
				if (t) return void Ye.debug("OIDC: LOGIN_FLOW_COMPLETE already processed, ignoring duplicate");
				const n = i.data.payload;
				(n === null || n === void 0 ? void 0 : n.oauthState) ? (t = !0, this.handleLoginFlowComplete(e, n.oauthState).catch((e) => {
					Ye.error("OIDC: Failed to handle login flow complete:", e), t = !1;
				})) : Ye.warn("OIDC: LOGIN_FLOW_COMPLETE but no oauthState in payload");
			};
			return window.addEventListener("message", i), () => {
				window.removeEventListener("message", i);
			};
		}
		getTokenExpirationInfo() {
			var _this103 = this;
			return _asyncToGenerator(function* () {
				const e = yield _this103.getUser();
				if (!e || !e.expires_at) return {
					expiresAt: null,
					expiresInSeconds: null,
					isExpired: !0
				};
				const t = /* @__PURE__ */ new Date(1e3 * e.expires_at);
				const i = Date.now();
				const n = Math.floor((1e3 * e.expires_at - i) / 1e3);
				return {
					expiresAt: t,
					expiresInSeconds: n,
					isExpired: n <= 0
				};
			})();
		}
		forceTokenRefresh() {
			var _this104 = this;
			return _asyncToGenerator(function* () {
				return Ye.info("OIDC: forceTokenRefresh() - manually triggering token refresh"), _this104.signinSilent();
			})();
		}
	};
	_defineProperty(Ze, "instance", null);
	var et = Ze.getInstance();
	"undefined" != typeof window && (window.oidcAuthClient = et);
	var tt = Be("oidc-auth:oidc-auth-redirect");
	function it(e, t) {
		e.postMessage({
			status: "success",
			payload: t
		});
	}
	function nt(e, t) {
		e.postMessage({
			status: "error",
			payload: t
		});
	}
	function st({ targets: e, onSuccess: t, attempt: i = 1 }) {
		const n = new URLSearchParams(window.location.search);
		if (!n.get(Me)) return void tt.warn("OIDC: No login_success param found, skipping");
		const s = n.get(He);
		if (s) {
			var _e$window;
			if (!((_e$window = e.window) === null || _e$window === void 0 ? void 0 : _e$window.contentWindow) || !e.windowURL) return tt.warn("Cannot forward OIDC state: iframe not available"), void (i < 5 ? setTimeout(() => {
				st({
					targets: e,
					onSuccess: t,
					attempt: i + 1
				});
			}, 500) : tt.error("OIDC: Failed to forward login flow after", 5, "attempts - iframe never became available"));
			try {
				var _i$state;
				const i = JSON.parse(s);
				const n = (_i$state = i.state) === null || _i$state === void 0 || (_i$state = _i$state.data) === null || _i$state === void 0 ? void 0 : _i$state[$e];
				if (n && n !== window.location.origin) return void tt.error("Origin mismatch in OIDC state:", n, "vs", window.location.origin);
				(function(e, t) {
					var _t$window;
					var _t$windowURL;
					const i = (_t$window = t.window) === null || _t$window === void 0 ? void 0 : _t$window.contentWindow;
					const n = (_t$windowURL = t.windowURL) === null || _t$windowURL === void 0 ? void 0 : _t$windowURL.origin;
					i && n ? i.postMessage({
						type: Ue,
						payload: e
					}, n) : tt.warn("Cannot send OIDC state: window or origin not available");
				})({ oauthState: i }, e);
				const r = new URL(window.location.href);
				r.searchParams.delete(Me), r.searchParams.delete(He), history.replaceState({}, "", r.toString()), t === null || t === void 0 || t();
			} catch (e) {
				tt.error("Failed to parse or forward OIDC state:", e);
			}
		} else tt.warn("OIDC login complete but no state found in URL");
	}
	var rt = g("iframe-utils");
	var ot = null;
	var at = (e) => !!e && document.contains(e);
	var ct = (e) => {
		try {
			return new URL(e.src).origin;
		} catch (e) {
			return rt.error("Error parsing iframe URL:", e), null;
		}
	};
	var dt = (e, t, i) => !!(e === null || e === void 0 ? void 0 : e.contentWindow) && (t ? (e.contentWindow.postMessage(i, t), !0) : (rt.error("Could not determine target origin for Angie iframe"), !1));
	var lt = (e) => at(e.iframe) ? e.iframe : null;
	var gt = (e, t) => dt(lt(e), ((e) => {
		if (e.iframeUrlObject) return e.iframeUrlObject.origin;
		const t = lt(e);
		return t ? ct(t) : null;
	})(e), t);
	var ut = () => lt(R) || (at(ot) || (ot = document.querySelector("iframe[src*=\"angie/\"]")), ot);
	var pt = (e, t, i) => {
		const n = document.getElementById(i !== null && i !== void 0 ? i : R.containerId);
		n && n.setAttribute("aria-hidden", t ? "false" : "true"), t ? e.removeAttribute("tabindex") : e.setAttribute("tabindex", "-1");
	};
	var _t = (e, t) => {
		const i = t === null || t === void 0 ? void 0 : t.contentWindow;
		return !i || !e.source || e.source === i;
	};
	var wt = (e, t, i) => e.origin === t && _t(e, i);
	var mt = () => Math.random().toString(36).substring(2, 8);
	var ft = (e, t) => {
		e.postMessage({
			status: "success",
			payload: t
		});
	};
	var St = g("sidebar");
	var bt = !1;
	var vt = "open";
	var It = "closed";
	var Et = "angie_sidebar_state";
	var kt = "angie_sidebar_width";
	function Tt() {
		if ("undefined" == typeof window) return 370;
		try {
			const e = window.localStorage.getItem(kt);
			if (e) {
				const t = parseInt(e, 10);
				if (t >= 350 && t <= 590) return t;
			}
		} catch (e) {
			St.warn("localStorage not available");
		}
		return 370;
	}
	function Rt() {
		return "undefined" == typeof window ? null : localStorage.getItem(Et);
	}
	function At(e) {
		try {
			localStorage.setItem(Et, e);
		} catch (e) {
			St.warn("localStorage not available");
		}
	}
	function Ct(e) {
		try {
			localStorage.setItem(kt, e.toString());
		} catch (e) {
			St.warn("localStorage not available");
		}
	}
	function xt(e) {
		document.documentElement.style.setProperty("--angie-sidebar-width", `${e}px`);
	}
	function Pt(e = vt) {
		!function() {
			if ("undefined" == typeof window) return !1;
			const e = new URLSearchParams(window.location.search);
			return e.has(Me) || e.has(He) || e.has($e);
		}() ? Ot(Rt() || e) : function() {
			Ot(It);
			try {
				localStorage.setItem(Et, It);
			} catch (e) {
				St.warn("localStorage not available");
			}
		}();
	}
	function Ot(e) {
		"undefined" != typeof window && window.toggleAngieSidebar && window.toggleAngieSidebar(e === "open", !0);
	}
	function Ut(e = R) {
		const t = document.getElementById(e.containerId);
		if (!t) return;
		let i = !1;
		let n = 0;
		let s = 0;
		t.addEventListener("mousedown", (e) => {
			const r = t.getBoundingClientRect();
			("rtl" === document.documentElement.dir ? e.clientX <= r.left + 4 : e.clientX >= r.right - 4) && (i = !0, n = e.clientX, s = r.width, t.classList.add("angie-resizing"), document.body.style.cursor = "ew-resize", document.body.style.userSelect = "none", e.preventDefault(), e.stopPropagation());
		}), document.addEventListener("mousemove", (e) => {
			if (!i) return;
			let t;
			t = "rtl" === document.documentElement.dir ? n - e.clientX : e.clientX - n, xt(Math.max(350, Math.min(590, s + t))), e.preventDefault(), e.stopPropagation();
		}), document.addEventListener("mouseup", (n) => {
			if (i) {
				i = !1, t.classList.remove("angie-resizing"), document.body.style.cursor = "", document.body.style.userSelect = "";
				const r = parseInt(getComputedStyle(document.documentElement).getPropertyValue("--angie-sidebar-width"), 10);
				Ct(r), gt(e, {
					type: m.ANGIE_SIDEBAR_RESIZED,
					payload: {
						initialWidth: s,
						width: r
					}
				}), n.preventDefault(), n.stopPropagation();
			}
		}), xt(Tt());
	}
	var Lt = !1;
	function Dt(e) {
		var _e$instance;
		e !== null && e !== void 0 && e.skipDefaultCss || function() {
			if ("undefined" == typeof document || bt) return;
			const e = "angie-sidebar-styles";
			if (document.getElementById(e)) return void (bt = !0);
			const t = document.createElement("style");
			t.id = e, t.textContent = "/* Angie Sidebar - CSS Variables */\n:root {\n    --angie-sidebar-z-index: 1200; /* below MUI popups, elementor popups and media library modal */\n    --angie-sidebar-width: 330px;\n    --angie-sidebar-transition: margin 0.3s ease-in-out, transform 0.3s ease-in-out;\n    /* Direction-aware transform values for sidebar positioning */\n    --angie-sidebar-hide-transform: translateX(-100%); /* LTR: hide to the left */\n    --angie-sidebar-show-transform: translateX(0);\n}\n\n/* RTL-specific transform values */\n[dir=\"rtl\"] {\n    --angie-sidebar-hide-transform: translateX(100%); /* RTL: hide to the right */\n}\n\n/* Respect user's motion preferences */\n@media (prefers-reduced-motion: reduce) {\n    :root {\n        --angie-sidebar-transition: none;\n    }\n}\n\n/* Apply transitions only when user is actively toggling */\nbody.angie-sidebar-transitioning {\n    transition: var(--angie-sidebar-transition) !important;\n}\n\nbody.angie-sidebar-transitioning #angie-sidebar-container {\n    transition: var(--angie-sidebar-transition) !important;\n}\n\n/* Layout (default) - Push content */\n@media (min-width: 768px) {\n    body.angie-sidebar-active {\n        padding-inline-start: var(--angie-sidebar-width) !important;\n    }\n\n    #angie-sidebar-container {\n        position: fixed;\n        top: 0;\n        inset-inline-start: 0;\n        width: var(--angie-sidebar-width);\n        height: 100vh;\n        z-index: var(--angie-sidebar-z-index) !important; /* below elementor popups and media library modal */\n        background: #FCFCFC;\n        transform: var(--angie-sidebar-hide-transform);\n        outline: none;\n        overflow: hidden;\n        /* No default transition - only when transitioning */\n    }\n\n    /* Resize handle */\n    #angie-sidebar-container::after {\n        content: '';\n        position: absolute;\n        top: 0;\n        inset-inline-end: 0;\n        width: 4px;\n        height: 100%;\n        cursor: ew-resize;\n        background: transparent;\n        z-index: 1000001;\n    }\n\n    /* Pink border during resize */\n    #angie-sidebar-container.angie-resizing {\n        border-inline-end-color: #ff69b4 !important;\n        border-inline-end-width: 2px !important;\n    }\n\n    /* Disable iframe pointer events during resize */\n    #angie-sidebar-container.angie-resizing iframe#angie-iframe {\n        pointer-events: none !important;\n    }\n}\n\n/* Active states */\nbody.angie-sidebar-active #angie-sidebar-container {\n    transform: var(--angie-sidebar-show-transform);\n}\n\n/* Studio mode - sidebar takes full width */\n@media (min-width: 768px) {\n    html.angie-studio-active body.angie-sidebar-active #angie-sidebar-container {\n        width: 100%;\n    }\n}\n\n/* High contrast mode support */\n@media (prefers-contrast: high) {\n    #angie-sidebar-container {\n        border-color: #000;\n        box-shadow: none;\n    }\n}\n\n/* Screen reader only class */\n.angie-sr-only {\n    position: absolute;\n    width: 1px;\n    height: 1px;\n    padding: 0;\n    margin: -1px;\n    overflow: hidden;\n    clip: rect(0, 0, 0, 0);\n    white-space: nowrap;\n    border: 0;\n}\n\n/* Plugin conflict resolution */\nbody.angie-sidebar-active {\n    /* Reset common conflicting styles */\n    box-sizing: border-box !important;\n    position: relative !important;\n}\n\n#angie-sidebar-toggle {\n    z-index: 99999 !important;\n}\n";
			const i = document.head || document.getElementsByTagName("head")[0];
			i.insertBefore(t, i.firstChild), bt = !0;
		}();
		const t = (_e$instance = e === null || e === void 0 ? void 0 : e.instance) !== null && _e$instance !== void 0 ? _e$instance : R;
		"undefined" != typeof window && (window.toggleAngieSidebar = function(e, t = R) {
			return function(i, n) {
				const s = document.body;
				const r = document.getElementById(t.containerId);
				if (!r) return void St.warn("Required elements not found!");
				const o = s.classList.contains("angie-sidebar-active");
				const a = void 0 !== i ? i : !o;
				n || (s.classList.add("angie-sidebar-transitioning"), setTimeout(function() {
					s.classList.remove("angie-sidebar-transitioning");
				}, 300)), a ? s.classList.add("angie-sidebar-active") : s.classList.remove("angie-sidebar-active"), t.iframe && pt(t.iframe, a, t.containerId), function(e, t, i = R) {
					e && setTimeout(function() {
						gt(i, { type: "focusInput" });
					}, t);
				}(a, n ? 0 : 300, t), e && e(a, r, n), At(a ? "open" : It);
				const c = new CustomEvent("angieSidebarToggle", { detail: {
					isOpen: a,
					sidebar: r,
					skipTransition: n
				} });
				document.dispatchEvent(c), gt(t, {
					type: m.ANGIE_SIDEBAR_TOGGLED,
					payload: { state: a ? "opened" : "closed" }
				});
			};
		}(e === null || e === void 0 ? void 0 : e.onToggle, t), function(e = R) {
			Lt || (Lt = !0, window.addEventListener("message", function(t) {
				var _t$data;
				var _e$iframeUrlObject;
				var _t$ports;
				if ("toggleAngieSidebar" !== ((_t$data = t.data) === null || _t$data === void 0 ? void 0 : _t$data.type)) return;
				const i = (_e$iframeUrlObject = e.iframeUrlObject) === null || _e$iframeUrlObject === void 0 ? void 0 : _e$iframeUrlObject.origin;
				if (!(i ? wt(t, i, e.iframe) : _t(t, e.iframe))) return;
				const { force: n, skipTransition: s } = t.data.payload || {};
				window.toggleAngieSidebar && window.toggleAngieSidebar(n, s);
				const r = (_t$ports = t.ports) === null || _t$ports === void 0 ? void 0 : _t$ports[0];
				r && ft(r);
			}));
		}(t));
	}
	var Nt = "angie_return_url";
	var qt = g("referrer-redirect");
	function $t(e) {
		try {
			return new URL(e, window.location.origin).origin === window.location.origin;
		} catch (_unused2) {
			return !1;
		}
	}
	function Mt(e, t) {
		if (!$t(e)) return qt.warn("Invalid redirect URL rejected:", e), !1;
		try {
			const i = { url: e };
			return t && (i.prompt = t), localStorage.setItem(Nt, JSON.stringify(i)), !0;
		} catch (e) {
			return qt.warn("localStorage not available"), !1;
		}
	}
	function Ht() {
		try {
			const e = localStorage.getItem(Nt);
			if (!e) return null;
			let t;
			try {
				t = JSON.parse(e);
			} catch (_unused3) {
				return qt.warn("Stored redirect data is not valid JSON, returning null"), null;
			}
			return t.url && "string" == typeof t.url ? $t(t.url) ? t : (qt.warn("Stored redirect URL is invalid, returning null:", t.url), null) : (qt.warn("Stored redirect data missing url field, returning null"), null);
		} catch (e) {
			return qt.warn("localStorage not available"), null;
		}
	}
	function jt() {
		try {
			localStorage.removeItem(Nt);
		} catch (e) {
			qt.warn("localStorage not available");
		}
	}
	function Gt(e, t) {
		return t ? `${e}#angie-prompt=${encodeURIComponent(t)}` : e;
	}
	function zt() {
		const e = Ht();
		return !!e && (jt(), window.location.href = Gt(e.url, e.prompt), !0);
	}
	var Bt = g("oauth");
	var Wt = [];
	var Ft = !1;
	var Kt = !1;
	var Jt = () => {
		(() => {
			try {
				const e = new URL(window.location.href, window.location.origin).searchParams;
				return e.has("start-oauth") && "angie-app" === e.get("page");
			} catch (_unused4) {
				return !1;
			}
		})() && (Bt.log("Post-consent flow detected, checking for referrer redirect"), zt());
	};
	function Vt() {
		const e = Ht();
		if (e) return jt(), void (window.location.href = Gt(e.url, e.prompt));
		try {
			localStorage.setItem(Et, "open");
		} catch (e) {
			Bt.warn("localStorage not available");
		}
		setTimeout(() => {
			window.toggleAngieSidebar(!0);
		}, 500);
	}
	var Qt = (e) => {
		Wt.includes(e) || Wt.push(e);
	};
	var Xt = () => {
		for (const e of Wt.flatMap((e) => e.iframe && e.iframeUrlObject ? [{
			window: e.iframe,
			windowURL: e.iframeUrlObject
		}] : [])) st({
			targets: e,
			onSuccess: Vt
		});
	};
	var Yt = "sidebar";
	var Zt = "floatingChat";
	var ei = [];
	var ti = (e) => {
		var _ei$find;
		return (_ei$find = ei.find((t) => t.instanceId === e)) !== null && _ei$find !== void 0 ? _ei$find : null;
	};
	var ii = () => ei.some((e) => e.layout === Yt);
	var ni = g("sdk");
	var si;
	(si || (si = {})).POST_MESSAGE = "postMessage";
	var ri = /* @__PURE__ */ new Map();
	var oi = [];
	var ai = null;
	var ci = () => {
		0 === oi.length || ai || (ai = (e) => {
			var _e$data2;
			const t = ((e) => {
				var _oi$find;
				var _t$iframeUrlObject;
				if (e.origin === window.location.origin) return (_oi$find = oi.find((t) => {
					var _e$data;
					return ((e, t) => !(!t || t !== e.instanceId) || (!t || !ti(t)) && (() => {
						return ei.find((e) => null !== e.iframe) || (0 === ei.length && R.iframe ? R : null);
					})() === e)(t, e === null || e === void 0 || (_e$data = e.data) === null || _e$data === void 0 || (_e$data = _e$data.payload) === null || _e$data === void 0 ? void 0 : _e$data.instanceId);
				})) !== null && _oi$find !== void 0 ? _oi$find : null;
				const t = oi.find((t) => {
					var _t$iframe;
					return ((_t$iframe = t.iframe) === null || _t$iframe === void 0 ? void 0 : _t$iframe.contentWindow) === e.source;
				});
				return t && wt(e, (_t$iframeUrlObject = t.iframeUrlObject) === null || _t$iframeUrlObject === void 0 ? void 0 : _t$iframeUrlObject.origin, t.iframe) ? t : null;
			})(e);
			if (t) switch (e === null || e === void 0 || (_e$data2 = e.data) === null || _e$data2 === void 0 ? void 0 : _e$data2.type) {
				case m.SDK_ANGIE_ALL_SERVERS_REGISTERED: break;
				case m.SDK_ANGIE_READY_PING: {
					const t = e.ports[0];
					ni.log("Angie is ready", e), ft(t, { message: "Angie is ready" });
					break;
				}
				case m.SDK_REQUEST_CLIENT_CREATION: {
					const i = e.data.payload;
					const n = e.ports[0];
					((e, t) => {
						if (e.iframe) return void t();
						let i = ri.get(e);
						i || (i = [], ri.set(e, i)), i.push(t);
					})(t, () => {
						try {
							var _t$iframe2;
							var _t$iframeUrlObject2;
							const s = new MessageChannel();
							s.port1.onmessage = (e) => {
								n.postMessage({
									success: !0,
									data: e.data
								});
							};
							const r = {
								type: m.SDK_REQUEST_CLIENT_CREATION,
								payload: _objectSpread2(_objectSpread2({ success: !0 }, i), {}, {
									clientId: `dynamic-client-${i.serverName}-${i.serverVersion}`,
									requestId: e.data.payload.requestId
								}),
								timestamp: Date.now()
							};
							(_t$iframe2 = t.iframe) === null || _t$iframe2 === void 0 || (_t$iframe2 = _t$iframe2.contentWindow) === null || _t$iframe2 === void 0 || _t$iframe2.postMessage(r, ((_t$iframeUrlObject2 = t.iframeUrlObject) === null || _t$iframeUrlObject2 === void 0 ? void 0 : _t$iframeUrlObject2.origin) || "", [s.port2]);
						} catch (e) {
							ni.error(`Failed to create client for SDK server "${i.serverName}":`, e);
						}
					});
					break;
				}
				case m.SDK_TRIGGER_ANGIE:
					ni.log("SDK Trigger Angie received", e.data);
					try {
						var _t$iframe$contentWind;
						var _t$iframeUrlObject3;
						const { requestId: i, prompt: n, context: s, options: r } = e.data.payload;
						if (!t.iframe) throw new Error("Iframe not found");
						(_t$iframe$contentWind = t.iframe.contentWindow) === null || _t$iframe$contentWind === void 0 || _t$iframe$contentWind.postMessage({
							type: m.SDK_TRIGGER_ANGIE,
							payload: {
								requestId: i,
								prompt: n,
								context: s,
								options: r
							}
						}, ((_t$iframeUrlObject3 = t.iframeUrlObject) === null || _t$iframeUrlObject3 === void 0 ? void 0 : _t$iframeUrlObject3.origin) || ""), window.postMessage({
							type: m.SDK_TRIGGER_ANGIE_RESPONSE,
							payload: {
								success: !0,
								requestId: i,
								response: "Angie triggered successfully"
							}
						}, window.location.origin);
					} catch (t) {
						var _e$data$payload;
						ni.error("Failed to trigger Angie:", t), window.postMessage({
							type: m.SDK_TRIGGER_ANGIE_RESPONSE,
							payload: {
								success: !1,
								requestId: (_e$data$payload = e.data.payload) === null || _e$data$payload === void 0 ? void 0 : _e$data$payload.requestId,
								error: t instanceof Error ? t.message : "Unknown error"
							}
						}, window.location.origin);
					}
			}
		}, window.addEventListener("message", ai));
	};
	var di = (e) => {
		oi.includes(e) || oi.push(e);
	};
	var li = g("iframe");
	var gi = [];
	var ui = null;
	var hi = (e) => {
		if (e.includes("://") || e.startsWith("//")) return !1;
		try {
			const t = "https://test.com";
			return new URL(e, t).origin === t;
		} catch (_unused5) {
			return !1;
		}
	};
	var pi = function() {
		var _ref10 = _asyncToGenerator(function* (e = R) {
			var _e$iframe;
			if (((_e$iframe = e.iframe) === null || _e$iframe === void 0 ? void 0 : _e$iframe.contentWindow) && e.iframeUrlObject) try {
				li.log("Disabling navigation prevention in Angie iframe"), e.iframe.contentWindow.postMessage({ type: m.ANGIE_DISABLE_NAVIGATION_PREVENTION }, e.iframeUrlObject.origin), yield new Promise((e) => setTimeout(e, 100));
			} catch (e) {
				throw li.error("Failed to disable navigation prevention:", e), e;
			}
			else li.warn("Cannot disable navigation prevention: iframe or origin not available");
		});
		return function pi() {
			return _ref10.apply(this, arguments);
		};
	}();
	var _i = (e) => {
		gi.push(e), ui || (ui = (e) => {
			const t = gi.find((t) => _t(e, t.instance.iframe));
			t && t.trustedOrigins.includes(e.origin) && function() {
				var _ref11 = _asyncToGenerator(function* (e, t) {
					var _e$data3;
					var _e$data4;
					var _e$data5;
					var _e$data6;
					var _e$data7;
					if ((e === null || e === void 0 || (_e$data3 = e.data) === null || _e$data3 === void 0 ? void 0 : _e$data3.type) === m.ANGIE_CHAT_TOGGLE) t.open = e.data.open, t.iframe && pt(t.iframe, t.open, t.containerId);
					else if ((e === null || e === void 0 || (_e$data4 = e.data) === null || _e$data4 === void 0 ? void 0 : _e$data4.type) === m.ANGIE_STUDIO_TOGGLE) {
						const i = e.data.isStudioOpen;
						if (!t.iframe) return;
						if (i) document.documentElement.classList.add("angie-studio-active");
						else {
							const e = Tt();
							document.documentElement.style.setProperty("--angie-sidebar-width", `${e}px`), document.documentElement.classList.remove("angie-studio-active");
						}
					} else if ((e === null || e === void 0 || (_e$data5 = e.data) === null || _e$data5 === void 0 ? void 0 : _e$data5.type) === m.ANGIE_NAVIGATE_TO_URL) {
						const { url: i = "", confirmed: n = !1 } = e.data.payload || {};
						if (!n) return void li.log("Navigation requires user confirmation");
						((e, t = []) => {
							const i = 0 === t.length && "undefined" != typeof window ? [window.location.origin] : t;
							if (!e.startsWith("http")) return !1;
							try {
								const t = new URL(e);
								return i.includes(t.origin);
							} catch (_unused6) {
								return !1;
							}
						})(i) ? (yield pi(t), window.location.assign(i)) : li.error("Navigation blocked: Invalid or unsafe URL", { url: i });
					} else if ((e === null || e === void 0 || (_e$data6 = e.data) === null || _e$data6 === void 0 ? void 0 : _e$data6.type) === m.ANGIE_PAGE_RELOAD) {
						const { confirmed: i = !1 } = e.data.payload || {};
						if (!i) return void li.log("Page reload requires user confirmation");
						li.log("Page reload confirmed - disabling navigation prevention and reloading"), yield pi(t), setTimeout(() => {
							window.location.reload();
						}, 50);
					} else (e === null || e === void 0 || (_e$data7 = e.data) === null || _e$data7 === void 0 ? void 0 : _e$data7.type) === y.RESET_HASH && (window.location.hash = "", ft(e.ports[0], { message: "Hash reset successfully" }));
				});
				return function(_x16, _x17) {
					return _ref11.apply(this, arguments);
				};
			}()(e, t.instance);
		}, window.addEventListener("message", ui));
	};
	var wi = function() {
		var _ref14 = _asyncToGenerator(function* (e, t = R) {
			if (window.screen.availWidth <= 768) return void li.log("Mobile detected, skipping iframe injection");
			const i = t.containerId;
			let n = document.getElementById(i);
			if (!n) {
				const e = performance.now();
				if (li.log("⏱️ Waiting for sidebar container..."), yield new Promise((e) => {
					let t = 0;
					const s = setInterval(() => {
						n = document.getElementById(i), t++, (n || t > 20) && (clearInterval(s), n && e());
					}, 100);
					setTimeout(() => {
						if (clearInterval(s), n) return void e();
						const t = new MutationObserver(() => {
							n = document.getElementById(i), n && (t.disconnect(), e());
						});
						t.observe(document.body, {
							childList: !0,
							subtree: !0
						}), setTimeout(() => {
							t.disconnect(), e();
						}, 8e3);
					}, 2e3);
				}), li.log(`⏱️ Sidebar container detection took: ${(performance.now() - e).toFixed(2)}ms`), !n) return void li.error("Sidebar container not found");
			}
			const { iframe: s, iframeUrlObject: r } = yield function() {
				var _ref13 = _asyncToGenerator(function* (e) {
					const t = e.origin;
					const i = new URL(e.path, t);
					const n = e.instanceId || mt();
					const s = i.pathname.slice(1).replace(/\//, "--") + "-" + n;
					return new Promise((n) => {
						const r = new URL(t);
						r.pathname = i.pathname;
						const o = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
						if (r.searchParams.append("colorScheme", e.uiTheme || o || "light"), r.searchParams.append("sdkVersion", e.sdkVersion), r.searchParams.append("instanceId", s), r.searchParams.append("origin", window.location.origin), e.isRTL && r.searchParams.append("isRTL", e.isRTL ? "true" : "false"), "localhost" === window.location.hostname && window.location.search.includes("debug_error")) {
							const e = new URLSearchParams(window.location.search).get("debug_error");
							e && r.searchParams.append("debug_error", e);
						}
						i.searchParams.forEach((e, t) => {
							r.searchParams.set(t, e);
						}), r.searchParams.set("ver", (/* @__PURE__ */ new Date()).getTime().toString());
						const a = e.parent || document;
						const c = a.createElement("iframe");
						const d = _objectSpread2({
							"background-color": "transparent",
							"color-scheme": "normal"
						}, e.css);
						window.addEventListener("message", function() {
							var _ref12 = _asyncToGenerator(function* (t) {
								if (wt(t, r.origin, c)) switch (t.data.type) {
									case y.ANGIE_READY:
										n({
											iframe: c,
											iframeUrlObject: r
										});
										break;
									case y.ANGIE_LOADED:
										var _c$contentWindow;
										(_c$contentWindow = c.contentWindow) === null || _c$contentWindow === void 0 || _c$contentWindow.postMessage(_objectSpread2({
											type: y.HOST_READY,
											instanceId: s
										}, e.embeddedConfig ? { embedded: e.embeddedConfig } : {}), r.origin);
								}
							});
							return function(_x18) {
								return _ref12.apply(this, arguments);
							};
						}()), c.setAttribute("src", r.href), c.id = e.iframeElementId || "angie-iframe", c.setAttribute("frameborder", "0"), c.setAttribute("scrolling", "no"), c.setAttribute("style", Object.entries(d).map(([e, t]) => `${e}: ${t}`).join("; ")), c.setAttribute("allow", "clipboard-write; clipboard-read"), e.insertCallback ? e.insertCallback(c) : a.body.appendChild(c);
					});
				});
				return function(_x19) {
					return _ref13.apply(this, arguments);
				};
			}()({
				origin: e.origin || "https://angie.elementor.com",
				path: e.path && hi(e.path) ? e.path : "angie/wp-admin",
				insertCallback: (e) => {
					li.log("Injecting Angie iframe into sidebar container"), e.setAttribute("title", "Angie AI Assistant"), e.setAttribute("role", "application"), e.setAttribute("aria-label", "Angie AI Assistant Interface");
					const t = document.getElementById("angie-sidebar-loading");
					t && (t.textContent = ""), n === null || n === void 0 || n.appendChild(e);
				},
				embeddedConfig: e.embeddedConfig,
				css: {
					width: "100%",
					height: "100%",
					border: "none",
					outline: "none"
				},
				uiTheme: e.uiTheme,
				isRTL: e.isRTL,
				sdkVersion: "1.7.1",
				iframeElementId: t.iframeElementId,
				instanceId: t.instanceId
			});
			return t.iframe = s, t.iframeUrlObject = r, ((e) => {
				ci();
				const t = ri.get(e);
				t !== null && t !== void 0 && t.length && (ri.delete(e), t.forEach((e) => e()));
			})(t), di(t), ci(), ((e = R) => {
				var _e$iframeUrlObject$or;
				var _e$iframeUrlObject2;
				Qt(e), Ft || (Ft = !0, function({ trustedOrigin: e, onOAuthParamsCleared: t }) {
					window.addEventListener("message", (i) => {
						var _i$ports;
						if (i.origin !== e) return;
						const n = (_i$ports = i.ports) === null || _i$ports === void 0 ? void 0 : _i$ports[0];
						switch (i.data.type) {
							case Le:
								if (!n) return;
								it(n, { topUrl: window.location.href });
								break;
							case De:
								window.location.href = i.data.payload.url;
								break;
							case Ne: {
								var _history;
								if (!n) return;
								const e = i.data.payload.url;
								if (!((_history = history) === null || _history === void 0 ? void 0 : _history.replaceState)) return void nt(n, { message: "URL update not supported in this browser" });
								try {
									const i = window.location.href;
									history.replaceState({}, "", e), function(e, t) {
										const i = new URL(e).searchParams;
										const n = new URL(t).searchParams;
										const s = [
											Me,
											He,
											$e
										];
										return s.some((e) => i.has(e)) && !s.some((e) => n.has(e));
									}(i, e) && (t === null || t === void 0 || t()), it(n, { message: "URL updated successfully" });
								} catch (e) {
									nt(n, { message: "URL update failed: " + (e instanceof Error ? e.message : "Unknown error") });
								}
								break;
							}
							case qe:
								if (!n) return;
								it(n, { isPending: "true" === new URLSearchParams(window.location.search).get(Me) });
						}
					});
				}({
					trustedOrigin: (_e$iframeUrlObject$or = (_e$iframeUrlObject2 = e.iframeUrlObject) === null || _e$iframeUrlObject2 === void 0 ? void 0 : _e$iframeUrlObject2.origin) !== null && _e$iframeUrlObject$or !== void 0 ? _e$iframeUrlObject$or : "",
					onOAuthParamsCleared: Vt
				}));
			})(t), ((e = R) => {
				Qt(e), Xt(), Kt || (Kt = !0, window.addEventListener("load", () => {
					Bt.log("OIDC: Window load event fired, forwarding OIDC state if present"), Xt();
				}));
			})(t), _i({
				instance: t,
				trustedOrigins: [window.location.origin, e.origin || "https://angie.elementor.com"]
			}), {
				iframe: s,
				iframeOrigin: r.origin
			};
		});
		return function wi(_x20) {
			return _ref14.apply(this, arguments);
		};
	}();
	var mi = g("registration-queue");
	var yi = {
		layout: Yt,
		styleTheme: "",
		persistOpenState: !0,
		resizable: !0,
		chatToggleButtonEnabled: !1
	};
	var Si = "#angie-widget-toggle";
	var bi = {
		boot: { allowInIframe: !1 },
		container: {
			layout: yi.layout,
			styleTheme: yi.styleTheme,
			persistOpenState: yi.persistOpenState,
			resizable: yi.resizable,
			chatToggleButtonSelector: Si
		},
		iframe: {
			origin: "https://angie.elementor.com",
			path: "angie/embedded",
			uiTheme: "light"
		}
	};
	var vi = /* @__PURE__ */ new Map();
	var Ii = !1;
	var Ei = (e, t) => e && t ? `${e}::__angie::${t}` : e;
	var ki = (e, t) => {
		const i = Ei(e, t);
		try {
			const t = window.localStorage.getItem(i);
			return null !== t ? t : i === e ? null : window.localStorage.getItem(e);
		} catch (_unused7) {
			return null;
		}
	};
	var Ti = function() {
		var _ref16 = _asyncToGenerator(function* (e) {
			var _e$data8;
			var _e$ports;
			const t = ((e) => {
				var _t$;
				var _t$find;
				const t = [...vi.values()].filter((t) => t.iframeOrigin === e.origin);
				return t.length <= 1 ? (_t$ = t[0]) !== null && _t$ !== void 0 ? _t$ : null : (_t$find = t.find((t) => {
					var _t$instance$iframe;
					return ((_t$instance$iframe = t.instance.iframe) === null || _t$instance$iframe === void 0 ? void 0 : _t$instance$iframe.contentWindow) === e.source;
				})) !== null && _t$find !== void 0 ? _t$find : null;
			})(e);
			if (!t) return;
			const i = (_e$data8 = e.data) === null || _e$data8 === void 0 ? void 0 : _e$data8.type;
			const n = (_e$ports = e.ports) === null || _e$ports === void 0 ? void 0 : _e$ports[0];
			switch (i) {
				case "GET_EXTERNAL_HEADERS":
					if (!n) return;
					yield function() {
						var _ref15 = _asyncToGenerator(function* (e, t) {
							try {
								const i = t ? yield t() : {};
								ft(e, ((e) => Object.fromEntries(Object.entries(e).filter(([, e]) => void 0 !== e)))(i));
							} catch (t) {
								((e, t) => {
									e.postMessage({
										status: "error",
										payload: t
									});
								})(e, { message: t instanceof Error ? t.message : String(t) });
							}
						});
						return function(_x21, _x22) {
							return _ref15.apply(this, arguments);
						};
					}()(n, t.getExternalHeaders);
					break;
				case "angie/context/get-website-context":
					if (!n) return;
					ft(n, (s = t.host, { payload: _objectSpread2({
						name: document.title,
						tagline: "",
						homeUrl: window.location.origin,
						siteLang: document.documentElement.lang,
						docTitle: document.title,
						platform: "frontend",
						timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
						today: (/* @__PURE__ */ new Date()).toISOString().split("T")[0]
					}, s === null || s === void 0 ? void 0 : s.website) }));
					break;
				case "angie/context/get-analytics-context":
					if (!n) return;
					ft(n, ((e) => ({ payload: _objectSpread2({ screenPath: window.location.pathname }, e === null || e === void 0 ? void 0 : e.analytics) }))(t.host));
					break;
				case f.GET:
					var _t$host;
					if (!n) return;
					n.postMessage({ value: ki(e.data.key, (_t$host = t.host) === null || _t$host === void 0 ? void 0 : _t$host.instanceId) });
					break;
				case f.SET:
					var _t$host2;
					((e, t, i) => {
						try {
							window.localStorage.setItem(Ei(e, i), t);
						} catch (_unused8) {}
					})(e.data.key, e.data.value, (_t$host2 = t.host) === null || _t$host2 === void 0 ? void 0 : _t$host2.instanceId);
			}
			var s;
		});
		return function Ti(_x23) {
			return _ref16.apply(this, arguments);
		};
	}();
	var Ri = "data-angie-toggle-wired";
	var Ai = (e, t) => {
		const i = document.querySelector(e);
		i && (i.setAttribute("aria-expanded", t ? "true" : "false"), i.setAttribute("aria-label", t ? "Close Angie" : "Open Angie"));
	};
	var Ci = (e) => {
		const t = document.querySelector(e.toggleButtonSelector);
		t && "true" !== t.getAttribute(Ri) && (t.setAttribute(Ri, "true"), t.addEventListener("click", e.onClick));
	};
	var xi = /* @__PURE__ */ new Set();
	var Pi = !1;
	var Oi = (e) => {
		for (const t of xi) t(e);
	};
	var Ui = "angie-widget-toggle";
	var Li = "angie-widget-hidden";
	var Di = "angie-widget-fullscreen";
	var Ni = "angie-widget-container";
	var qi = "angie-chat-widget-styles";
	var $i = /^#([^\s#.[:]+)$/;
	var Mi = /^\[([^\]=]+)(?:="([^"]*)")?\]$/;
	var Hi = (e) => document.querySelector(e);
	var ji = /* @__PURE__ */ new Map();
	var Gi = (e) => {
		const t = document.getElementById(e.containerId);
		t && (e.isOpen ? t.classList.remove(Li) : t.classList.add(Li), e.instance.iframe && pt(e.instance.iframe, e.isOpen, e.containerId), Ai(e.toggleButtonSelector, e.isOpen));
	};
	var zi = (e, t) => {
		Gi({
			containerId: e.containerId,
			toggleButtonSelector: e.toggleButtonSelector,
			isOpen: t,
			instance: e.instance
		});
	};
	var Bi = (e, t) => {
		var _e$onClose;
		var _e$onClose2;
		const i = t === null || t === void 0 ? void 0 : t.force;
		if (void 0 !== i) return zi(e, i), void (i || ((_e$onClose = e.onClose) === null || _e$onClose === void 0 ? void 0 : _e$onClose.call(e)));
		const n = ((e) => {
			const t = document.getElementById(e);
			return !!t && !t.classList.contains(Li);
		})(e.containerId);
		zi(e, !n), n && ((_e$onClose2 = e.onClose) === null || _e$onClose2 === void 0 || _e$onClose2.call(e));
	};
	var Wi = (e) => {
		var _ji$get;
		const { instance: t } = e;
		var i;
		(_ji$get = ji.get(t)) === null || _ji$get === void 0 || _ji$get(), ji.set(t, (i = (i) => {
			var _i$ports2;
			if (!wt(i, e.iframeOrigin, t.iframe)) return;
			const n = (_i$ports2 = i.ports) === null || _i$ports2 === void 0 ? void 0 : _i$ports2[0], { type: s, payload: r } = i.data || {};
			switch (s) {
				case m.ANGIE_SIDEBAR_TOGGLED:
				case "toggleAngieSidebar":
					Bi(e, r), n && ft(n);
					break;
				case m.ANGIE_STUDIO_TOGGLE: {
					const t = !!i.data.isStudioOpen;
					((e, t) => {
						const i = document.getElementById(e);
						i && (t ? i.classList.add(Di) : i.classList.remove(Di));
					})(e.containerId, t), t && zi(e, !0), n && ft(n);
					break;
				}
			}
		}, xi.add(i), Pi || (Pi = !0, window.addEventListener("message", Oi)), () => {
			xi.delete(i);
		}));
	};
	var Fi = (e) => {
		const t = ((e) => e === "angie-sidebar-container" ? qi : `${qi}-${e}`)(e);
		if (document.getElementById(t)) return;
		const i = document.createElement("style");
		i.id = t, i.textContent = ((e) => `\n#${e}.${Ni} {\n\t--angie-widget-width: 400px;\n\t--angie-widget-height: 600px;\n\t--angie-widget-z-index: 99999;\n\n\tposition: fixed !important;\n\ttop: auto !important;\n\tbottom: 20px !important;\n\tinset-inline-start: auto !important;\n\tinset-inline-end: 20px !important;\n\twidth: var(--angie-widget-width) !important;\n\theight: var(--angie-widget-height) !important;\n\tmax-height: calc(100vh - 40px) !important;\n\tmax-width: calc(100vw - 40px) !important;\n\tz-index: var(--angie-widget-z-index) !important;\n\ttransform: none !important;\n\tborder-radius: 12px !important;\n\toverflow: hidden !important;\n\tbox-shadow: 0 4px 24px rgba(0, 0, 0, 0.15) !important;\n\ttransition: opacity 0.2s ease, transform 0.2s ease !important;\n}\n\n#${e}.${Ni}.${Li} {\n\tdisplay: none !important;\n}\n\n#${e}.${Ni} iframe {\n\twidth: 100% !important;\n\theight: 100% !important;\n\tborder: none !important;\n\tborder-radius: 12px !important;\n}\n\n\n.${Ui} {\n\t--angie-toggle-size: 56px;\n\t--angie-widget-z-index: 99999;\n\n\tposition: fixed;\n\tbottom: 20px;\n\tinset-inline-end: 20px;\n\twidth: var(--angie-toggle-size);\n\theight: var(--angie-toggle-size);\n\tborder-radius: 50%;\n\tborder: none;\n\tbackground: #EB8EFB;\n\tcolor: white;\n\tcursor: pointer;\n\tz-index: var(--angie-widget-z-index);\n\tdisplay: flex;\n\talign-items: center;\n\tjustify-content: center;\n\tbox-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);\n\ttransition: background 0.2s ease, transform 0.15s ease;\n\tpadding: 0;\n}\n\n.${Ui}:hover {\n\tbackground: #E070F5;\n\ttransform: scale(1.05);\n}\n\n.${Ui}:active {\n\ttransform: scale(0.95);\n}\n\n.${Ui}[aria-expanded="true"] {\n\tdisplay: none;\n}\n\n\n#${e}.${Ni}.${Di} {\n\tbottom: 0 !important;\n\tinset-inline-end: 0 !important;\n\twidth: 100vw !important;\n\theight: 100vh !important;\n\tmax-height: 100vh !important;\n\tmax-width: 100vw !important;\n\tborder-radius: 0 !important;\n}\n\n#${e}.${Ni}.${Di} iframe {\n\tborder-radius: 0 !important;\n}\n\n@media (max-width: 480px) {\n\t#${e}.${Ni} {\n\t\tbottom: 0 !important;\n\t\tinset-inline-end: 0 !important;\n\t\twidth: 100vw !important;\n\t\theight: 100vh !important;\n\t\tmax-height: 100vh !important;\n\t\tmax-width: 100vw !important;\n\t\tborder-radius: 0 !important;\n\t}\n\n\t#${e}.${Ni} iframe {\n\t\tborder-radius: 0 !important;\n\t}\n}\n`)(e), document.head.appendChild(i);
	};
	var Ki = (e) => {
		Fi(e.containerId), ((e) => {
			const t = document.getElementById(e);
			t && (t.classList.add(Ni, Li), t.setAttribute("role", "complementary"), t.setAttribute("aria-label", "Angie"), t.setAttribute("aria-hidden", "true"), t.setAttribute("tabindex", "-1"));
		})(e.containerId), e.injectToggleButton && ((e) => {
			if (Hi(e)) return;
			const t = document.createElement("button");
			((e, t) => {
				var _n$;
				const i = t.match($i);
				if (i) return void (e.id = i[1]);
				const n = t.match(Mi);
				n && e.setAttribute(n[1], (_n$ = n[2]) !== null && _n$ !== void 0 ? _n$ : "");
			})(t, e), t.className = Ui, t.setAttribute("aria-label", "Open Angie"), t.setAttribute("aria-expanded", "false"), t.type = "button", t.innerHTML = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 16 16\" fill=\"none\">\n	<path d=\"M15.0998 8.00414L14.622 8.18282C13.4991 8.60516 12.8142 9.50669 12.4001 10.6519L12.2249 11.1392L12.0497 10.6519C11.6356 9.50669 10.9109 8.60516 9.78801 8.18282L9.31018 8.00414L9.78801 7.82546C10.9109 7.40312 11.6356 6.50159 12.0497 5.3564L12.2249 4.86909L12.4001 5.3564C12.8142 6.50159 13.4991 7.40312 14.622 7.82546L15.0998 8.00414Z\" fill=\"white\"/>\n	<path d=\"M2 8.42721C5.5608 8.42721 8.44479 11.3685 8.44479 15\" stroke=\"white\" stroke-width=\"2.05092\" stroke-miterlimit=\"10\"/>\n	<path d=\"M2 7.57275C5.5608 7.57275 8.44479 4.6315 8.44479 0.999991\" stroke=\"white\" stroke-width=\"2.05092\" stroke-miterlimit=\"10\"/>\n</svg>", document.body.appendChild(t);
		})(e.toggleButtonSelector), ((e) => {
			((e) => {
				Ci({
					toggleButtonSelector: e.toggleButtonSelector,
					onClick: () => {
						var _e$onClose3;
						const t = Hi(e.toggleButtonSelector);
						const i = "true" === (t === null || t === void 0 ? void 0 : t.getAttribute("aria-expanded"));
						zi(e, !i), i && ((_e$onClose3 = e.onClose) === null || _e$onClose3 === void 0 || _e$onClose3.call(e));
					}
				});
			})(e), Wi(e), ii() || (window.toggleAngieSidebar = (t) => {
				Bi(e, { force: t });
			});
		})({
			containerId: e.containerId,
			iframeOrigin: e.iframeOrigin,
			onClose: e.onClose,
			toggleButtonSelector: e.toggleButtonSelector,
			instance: e.instance
		});
	};
	var Ji = !1;
	var Vi = {
		initShell: ({ config: e, instance: t }) => {
			((e, t, i = R) => {
				const n = e.chatToggleButton.enabled ? e.chatToggleButton.selector : void 0;
				Dt({
					instance: i,
					onToggle: (e) => {
						n && Ai(n, e), !e && t.onClose && t.onClose();
					}
				}), ((e) => {
					if ("wordpress" !== e || "undefined" == typeof document) return;
					const t = "angie-sidebar-wordpress-styles";
					if (document.getElementById(t) || (Ji = !1), Ji) return;
					const i = document.createElement("style");
					i.id = t, i.textContent = "body.admin-bar {\n    --angie-sidebar-z-index: 99999;\n}\n\n#angie-body-top-padding {\n    height: 0;\n    transition: height 0.3s ease-in-out;\n}\n\nbody.angie-sidebar-transitioning #wpadminbar {\n    transition: var(--angie-sidebar-transition) !important;\n}\n\n@media (min-width: 768px) {\n    body.angie-sidebar-active #angie-body-top-padding {\n        width: 100%;\n        height: 0;\n    }\n\n    body.angie-sidebar-active #wpadminbar {\n        inset-inline-start: var(--angie-sidebar-width) !important;\n        inset-inline-end: 0 !important;\n        width: calc(100% - var(--angie-sidebar-width)) !important;\n        margin-top: 0;\n    }\n}\n\n@media (max-width: 768px) {\n    body.angie-sidebar-active #wpadminbar {\n        inset-inline-start: 0 !important;\n        inset-inline-end: 0 !important;\n        width: 100% !important;\n    }\n}\n\nbody:not(.wp-admin) #angie-sidebar-container {\n    margin-top: 3px;\n}\n", (document.head || document.getElementsByTagName("head")[0]).appendChild(i), Ji = !0;
				})(e.styleTheme), n && Ci({
					toggleButtonSelector: n,
					onClick: (e) => {
						var _window$toggleAngieSi;
						var _window;
						e.preventDefault(), (_window$toggleAngieSi = (_window = window).toggleAngieSidebar) === null || _window$toggleAngieSi === void 0 || _window$toggleAngieSi.call(_window);
					}
				});
			})(e.container, e.callbacks, t);
		},
		beforeOpenIframe: ({ config: e }) => {
			var t;
			(t = e.container).chatToggleButton.enabled && (t.persistOpenState && Rt() === "open" || Ot(It));
		},
		afterOpenIframe: ({ config: e, instance: t }) => {
			((e, t = R) => {
				e.persistOpenState && Pt(e.chatToggleButton.enabled ? It : "open"), e.resizable && Ut(t);
			})(e.container, t);
		}
	};
	var Qi = { initShell: ({ config: e, instance: t }) => {
		const { chatToggleButton: i } = e.container;
		Ki({
			containerId: e.container.id,
			iframeOrigin: e.iframe.origin,
			onClose: e.callbacks.onClose,
			toggleButtonSelector: i.selector,
			injectToggleButton: i.enabled,
			instance: t
		});
	} };
	var Xi = {
		[Yt]: Vi,
		[Zt]: Qi
	};
	var Yi = {
		layout: Zt,
		styleTheme: "",
		persistOpenState: !1,
		resizable: !1,
		chatToggleButtonEnabled: !0
	};
	var Zi = { closeButton: "collapse" };
	var en = (e, t) => e === "floatingChat" ? _objectSpread2({ closeButton: "close" }, t) : t ? _objectSpread2(_objectSpread2({}, Zi), t) : Zi;
	var tn = function() {
		var _ref18 = _asyncToGenerator(function* (e) {
			var _a$beforeOpenIframe;
			var _a$afterOpenIframe;
			Jt();
			const t = {
				browserUiTheme: "undefined" != typeof window && "function" == typeof window.matchMedia ? window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light" : bi.iframe.uiTheme,
				isInIframe: "undefined" != typeof window && window !== window.top,
				isRTL: "undefined" != typeof document && "rtl" === document.documentElement.dir
			};
			const i = ((e, t) => {
				var _e$boot;
				var _e$container;
				var _e$iframe2;
				var _e$callbacks;
				var _n$layout;
				var _n$chatToggleButton$e;
				var _n$chatToggleButton;
				var _i$allowInIframe;
				var _n$id;
				var _n$styleTheme;
				var _n$persistOpenState;
				var _n$resizable;
				var _n$chatToggleButton2;
				var _s$origin;
				var _s$path;
				var _s$uiTheme;
				var _s$isRTL;
				const i = (_e$boot = e.boot) !== null && _e$boot !== void 0 ? _e$boot : {};
				const n = (_e$container = e.container) !== null && _e$container !== void 0 ? _e$container : {};
				const s = (_e$iframe2 = e.iframe) !== null && _e$iframe2 !== void 0 ? _e$iframe2 : {};
				const r = (_e$callbacks = e.callbacks) !== null && _e$callbacks !== void 0 ? _e$callbacks : {};
				const o = (_n$layout = n.layout) !== null && _n$layout !== void 0 ? _n$layout : bi.container.layout;
				const a = ((e) => e === "floatingChat" ? Yi : yi)(o);
				const c = (_n$chatToggleButton$e = (_n$chatToggleButton = n.chatToggleButton) === null || _n$chatToggleButton === void 0 ? void 0 : _n$chatToggleButton.enabled) !== null && _n$chatToggleButton$e !== void 0 ? _n$chatToggleButton$e : a.chatToggleButtonEnabled;
				return {
					host: {
						appId: e.host.appId,
						instanceId: e.host.instanceId,
						aiContext: e.host.aiContext,
						website: e.host.website,
						analytics: e.host.analytics
					},
					boot: { allowInIframe: (_i$allowInIframe = i.allowInIframe) !== null && _i$allowInIframe !== void 0 ? _i$allowInIframe : bi.boot.allowInIframe },
					container: {
						id: ((_n$id = n.id) === null || _n$id === void 0 ? void 0 : _n$id.trim()) || "angie-sidebar-container",
						layout: o,
						styleTheme: (_n$styleTheme = n.styleTheme) !== null && _n$styleTheme !== void 0 ? _n$styleTheme : a.styleTheme,
						persistOpenState: (_n$persistOpenState = n.persistOpenState) !== null && _n$persistOpenState !== void 0 ? _n$persistOpenState : a.persistOpenState,
						resizable: (_n$resizable = n.resizable) !== null && _n$resizable !== void 0 ? _n$resizable : a.resizable,
						chatToggleButton: {
							enabled: c,
							selector: ((_n$chatToggleButton2 = n.chatToggleButton) === null || _n$chatToggleButton2 === void 0 || (_n$chatToggleButton2 = _n$chatToggleButton2.selector) === null || _n$chatToggleButton2 === void 0 ? void 0 : _n$chatToggleButton2.trim()) || bi.container.chatToggleButtonSelector
						}
					},
					iframe: {
						origin: ((_s$origin = s.origin) === null || _s$origin === void 0 ? void 0 : _s$origin.trim()) || bi.iframe.origin,
						path: ((_s$path = s.path) === null || _s$path === void 0 ? void 0 : _s$path.trim()) || bi.iframe.path,
						uiTheme: (_s$uiTheme = s.uiTheme) !== null && _s$uiTheme !== void 0 ? _s$uiTheme : t.browserUiTheme,
						isRTL: (_s$isRTL = s.isRTL) !== null && _s$isRTL !== void 0 ? _s$isRTL : t.isRTL
					},
					callbacks: {
						onClose: r.onClose,
						getExternalHeaders: r.getExternalHeaders
					},
					widgetConfig: en(o, e.widgetConfig)
				};
			})(e, t);
			if (!((e, t) => !(!e.boot.allowInIframe && t.isInIframe))(i, t)) return;
			if (i.container.layout === "sidebar" && ii()) throw new Error("Angie SDK: only one sidebar layout instance is supported on a page. Use container.layout \"floatingChat\" for the extra instance.");
			if (n = i.container.id, ei.find((e) => e.containerId === n)) throw new Error(`Angie SDK: container id "${i.container.id}" is already used by another Angie instance. Give this instance its own container.id.`);
			var n;
			const s = i.host.instanceId || e.sdkInstanceId || mt();
			if (ti(s)) throw new Error(`Angie SDK: instance id "${s}" is already used by another Angie instance. Give this instance its own host.instanceId.`);
			ei[0] && i.container.chatToggleButton.selector === Si && (i.container.chatToggleButton.selector = `${Si}-${s}`);
			const r = ((e) => {
				const t = 0 === ei.length;
				const i = _objectSpread2(_objectSpread2(_objectSpread2({}, T()), e), {}, { iframeElementId: t ? k : `${k}-${e.instanceId}` });
				const n = t ? Object.assign(R, i) : i;
				return ei.push(n), n;
			})({
				containerId: i.container.id,
				instanceId: s,
				layout: i.container.layout
			});
			var o;
			di(r), ci(), o = {
				iframeOrigin: i.iframe.origin,
				host: i.host,
				getExternalHeaders: i.callbacks.getExternalHeaders,
				instance: r
			}, vi.set(o.instance, o), Ii || (Ii = !0, window.addEventListener("message", (e) => {
				Ti(e);
			})), ((e, t) => {
				if (document.getElementById(e)) return;
				const i = document.createElement("div");
				i.id = e, i.dir = t ? "rtl" : "ltr";
				const n = document.createElement("div");
				n.id = "angie-sidebar-loading", n.setAttribute("aria-live", "polite"), n.className = "angie-sr-only", i.appendChild(n), document.body.appendChild(i);
			})(i.container.id, t.isRTL);
			const a = Xi[i.container.layout];
			const c = {
				config: i,
				env: t,
				instance: r
			};
			a.initShell(c), (_a$beforeOpenIframe = a.beforeOpenIframe) === null || _a$beforeOpenIframe === void 0 || _a$beforeOpenIframe.call(a, c);
			const d = {
				aiContext: (l = i.host).aiContext,
				appId: l.appId,
				configVersion: 2,
				telemetry: { screenPath: window.location.pathname },
				website: _objectSpread2({
					docTitle: document.title,
					homeUrl: window.location.origin,
					name: document.title,
					platform: "frontend",
					siteLang: document.documentElement.lang,
					tagline: ""
				}, l.website)
			};
			var l;
			const g = yield function() {
				var _ref17 = _asyncToGenerator(function* (e) {
					var _e$instance2;
					const t = (_e$instance2 = e.instance) !== null && _e$instance2 !== void 0 ? _e$instance2 : R;
					return !!(yield wi({
						isRTL: e.iframe.isRTL,
						origin: e.iframe.origin,
						path: e.iframe.path,
						uiTheme: e.iframe.uiTheme,
						embeddedConfig: e.embeddedConfig
					}, t)) && (e.container.layout === "floatingChat" && e.container.chatToggleButton.enabled ? (Gi({
						containerId: e.container.id,
						toggleButtonSelector: e.container.chatToggleButton.selector,
						isOpen: !1,
						instance: t
					}), !0) : (t.iframe && pt(t.iframe, !1, t.containerId), e.container.chatToggleButton.enabled && Ai(e.container.chatToggleButton.selector, !1), !0));
				});
				return function(_x24) {
					return _ref17.apply(this, arguments);
				};
			}()({
				container: i.container,
				iframe: i.iframe,
				embeddedConfig: d,
				instance: r
			});
			(_a$afterOpenIframe = a.afterOpenIframe) === null || _a$afterOpenIframe === void 0 || _a$afterOpenIframe.call(a, c), g && (((e, t = R) => {
				gt(t, {
					payload: e,
					type: "sdk-embedded-config"
				});
			})(d, r), i.widgetConfig && ((e, t = R) => {
				gt(t, {
					payload: e,
					type: "sdk-widget-config"
				});
			})(i.widgetConfig, r));
		});
		return function tn(_x25) {
			return _ref18.apply(this, arguments);
		};
	}();
	var an = null;
	var cn = (e) => new Promise((t) => setTimeout(t, e));
	var dn = () => (an || (an = new b()), an);
	var ln = () => {
		var _window$angiePlugin;
		return "undefined" != typeof window && !0 === ((_window$angiePlugin = window.angiePlugin) === null || _window$angiePlugin === void 0 ? void 0 : _window$angiePlugin.available);
	};
	var gn = () => !!ln() && dn().isReady();
	var un = function() {
		var _ref22 = _asyncToGenerator(function* (e = 3e3) {
			return "undefined" != typeof window && function() {
				var _ref21 = _asyncToGenerator(function* (e, t, i = 50) {
					for (; Date.now() < e;) {
						if (t()) return !0;
						yield cn(i);
					}
					return t();
				});
				return function(_x27, _x28) {
					return _ref21.apply(this, arguments);
				};
			}()(Date.now() + e, ln);
		});
		return function un() {
			return _ref22.apply(this, arguments);
		};
	}();
	var hn = function() {
		var _ref23 = _asyncToGenerator(function* (e = 3e3) {
			if ("undefined" == typeof window) return !1;
			const t = Date.now();
			if (!(yield un(e))) return !1;
			const i = Math.max(0, e - (Date.now() - t));
			return (yield dn().waitUntilReady(i)).isReady;
		});
		return function hn() {
			return _ref23.apply(this, arguments);
		};
	}();
	var pn = g("navigation");
	var wn = g("interaction-mode");
	var vn;
	(function(e) {
		e.Inline = "inline", e.EndOfTurn = "end-of-turn";
	})(vn || (vn = {}));

//#endregion
//#region modules/agents/assets/js/admin/scanner/open-angie.js
	var PROMPT_HASH_KEY = "angie-prompt";
	var PENDING_PROMPT_STORAGE_KEY = "elementor_agent_ready_pending_angie_prompt";
	var ANGIE_ACTIVATION_RETURN_PARAM = "open-angie";
	var ANGIE_READY_TIMEOUT_MS = 1e4;
	var canOpenAngie = function canOpenAngie(isAngieActive) {
		return Boolean(isAngieActive) || gn() || Boolean(ut());
	};
	var openAngieWithPrompt = function openAngieWithPrompt(prompt) {
		var iframe = ut();
		if (iframe) pt(iframe, true);
		window.location.hash = "".concat(PROMPT_HASH_KEY, "=").concat(encodeURIComponent(prompt));
	};
	var openAngieAfterInstall = function openAngieAfterInstall(prompt, angieAppUrl) {
		sessionStorage.setItem(PENDING_PROMPT_STORAGE_KEY, prompt);
		Mt(window.location.href, prompt);
		At(vt);
		window.location.assign(angieAppUrl);
	};
	var isReturningFromAngieActivation = function isReturningFromAngieActivation() {
		return !window.location.hash.includes("".concat(PROMPT_HASH_KEY, "=")) && new URLSearchParams(window.location.search).has(ANGIE_ACTIVATION_RETURN_PARAM);
	};
	var deliverPendingAngiePrompt = /*#__PURE__*/ function() {
		var _ref = _asyncToGenerator$1(/*#__PURE__*/ import_regenerator.default.mark(function _callee() {
			var prompt;
			return import_regenerator.default.wrap(function(_context) {
				while (1) switch (_context.prev = _context.next) {
					case 0:
						prompt = sessionStorage.getItem(PENDING_PROMPT_STORAGE_KEY);
						if (!(!prompt || !isReturningFromAngieActivation())) {
							_context.next = 1;
							break;
						}
						return _context.abrupt("return");
					case 1:
						_context.prev = 1;
						_context.next = 2;
						return hn(ANGIE_READY_TIMEOUT_MS);
					case 2:
						if (_context.sent) {
							_context.next = 3;
							break;
						}
						return _context.abrupt("return");
					case 3:
						sessionStorage.removeItem(PENDING_PROMPT_STORAGE_KEY);
						jt();
						openAngieWithPrompt(prompt);
						_context.next = 5;
						break;
					case 4:
						_context.prev = 4;
						_context["catch"](1);
					case 5:
					case "end": return _context.stop();
				}
			}, _callee, null, [[1, 4]]);
		}));
		return function deliverPendingAngiePrompt() {
			return _ref.apply(this, arguments);
		};
	}();

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/angie-install-dialog.js
	var DIALOG_Z_INDEX$1 = 1e5;
	var DIALOG_WIDTH$1 = 889;
	var HERO_WIDTH = 432;
	var DIALOG_HEIGHT = 430;
	var INSTALL_SPINNER_SIZE = 18;
	var ANGIE_TERMS_URL = "https://elementor.com/terms/angie-terms-conditions/";
	var ANGIE_PRIVACY_URL = "https://elementor.com/about/privacy/";
	var getDescription = function getDescription(isAngieInstalled, installFailed) {
		if (installFailed) return (0, _wordpress_i18n.__)("We couldn't install Angie automatically. Install it from the plugin directory instead.", "elementor");
		return isAngieInstalled ? (0, _wordpress_i18n.__)("Angie, agentic AI for WordPress, can fix this issue directly on your site. Angie is already installed, activate it once to get started.", "elementor") : (0, _wordpress_i18n.__)("Angie, agentic AI for WordPress, can fix this issue directly on your site. Install and activate Angie once to get started.", "elementor");
	};
	var getPrimaryLabel = function getPrimaryLabel(isAngieInstalled, isInstalling) {
		if (isAngieInstalled) return isInstalling ? (0, _wordpress_i18n.__)("Activating…", "elementor") : (0, _wordpress_i18n.__)("Activate Angie & fix issue", "elementor");
		return isInstalling ? (0, _wordpress_i18n.__)("Installing…", "elementor") : (0, _wordpress_i18n.__)("Install Angie & fix issue", "elementor");
	};
	var AngieInstallDialog = function AngieInstallDialog(_ref) {
		var check = _ref.check;
		var heroUrl = _ref.heroUrl;
		var angieAppUrl = _ref.angieAppUrl;
		var angiePluginSearchUrl = _ref.angiePluginSearchUrl;
		var _ref$isAngieInstalled = _ref.isAngieInstalled;
		var isAngieInstalled = _ref$isAngieInstalled === void 0 ? false : _ref$isAngieInstalled;
		var onClose = _ref.onClose;
		var onCopyPrompt = _ref.onCopyPrompt;
		var _useState2 = _slicedToArray((0, react.useState)(false), 2);
		var agreedToTerms = _useState2[0];
		var setAgreedToTerms = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)(false), 2);
		var isInstalling = _useState4[0];
		var setIsInstalling = _useState4[1];
		var _useState6 = _slicedToArray((0, react.useState)(false), 2);
		var installFailed = _useState6[0];
		var setInstallFailed = _useState6[1];
		var handleClose = function handleClose() {
			if (isInstalling) return;
			onClose();
		};
		var handleInstall = /*#__PURE__*/ function() {
			var _ref2 = _asyncToGenerator$1(/*#__PURE__*/ import_regenerator.default.mark(function _callee() {
				var result;
				return import_regenerator.default.wrap(function(_context) {
					while (1) switch (_context.prev = _context.next) {
						case 0:
							setIsInstalling(true);
							setInstallFailed(false);
							_context.next = 1;
							return installAngiePlugin();
						case 1:
							result = _context.sent;
							if (result.success) {
								_context.next = 2;
								break;
							}
							setIsInstalling(false);
							setInstallFailed(true);
							return _context.abrupt("return");
						case 2: openAngieAfterInstall(check.fixPrompt, angieAppUrl);
						case 3:
						case "end": return _context.stop();
					}
				}, _callee);
			}));
			return function handleInstall() {
				return _ref2.apply(this, arguments);
			};
		}();
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Dialog.default, {
			open: true,
			onClose: handleClose,
			sx: { zIndex: DIALOG_Z_INDEX$1 },
			PaperProps: { sx: {
				width: DIALOG_WIDTH$1,
				maxWidth: "calc(100% - 32px)",
				m: 0,
				overflow: "hidden"
			} }
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "stretch",
			sx: { minHeight: DIALOG_HEIGHT }
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			component: "img",
			src: heroUrl,
			alt: "",
			width: HERO_WIDTH,
			sx: {
				objectFit: "cover",
				flexShrink: 0,
				display: {
					xs: "none",
					md: "block"
				}
			}
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			flex: 1,
			minWidth: 0
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogHeader.default, {
			onClose: handleClose,
			logo: false
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogContent.default, { sx: { pt: 0 } }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 2,
			alignItems: "flex-start"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "h4",
			component: "h2",
			color: "text.secondary"
		}, (0, _wordpress_i18n.__)("Prepare your site for agents (with Angie)", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "subtitle1",
			color: "text.secondary"
		}, getDescription(isAngieInstalled, installFailed)), !installFailed && /*#__PURE__*/ react.default.createElement(_elementor_ui_FormControlLabel.default, {
			sx: {
				m: 0,
				alignItems: "flex-start",
				gap: 1,
				"& .MuiCheckbox-root": {
					p: 0,
					mt: .25
				},
				"& .MuiFormControlLabel-label": { mt: 0 }
			},
			control: /*#__PURE__*/ react.default.createElement(_elementor_ui_Checkbox.default, {
				size: "small",
				checked: agreedToTerms,
				onChange: function onChange(event) {
					return setAgreedToTerms(event.target.checked);
				}
			}),
			label: /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
				variant: "body2",
				color: "text.secondary",
				component: "span"
			}, (0, _wordpress_i18n.__)("By installing, you agree to our", "elementor"), " ", /*#__PURE__*/ react.default.createElement(_elementor_ui_Link.default, {
				href: ANGIE_TERMS_URL,
				target: "_blank",
				rel: "noreferrer",
				underline: "none",
				color: "info"
			}, (0, _wordpress_i18n.__)("Terms", "elementor")), " & ", /*#__PURE__*/ react.default.createElement(_elementor_ui_Link.default, {
				href: ANGIE_PRIVACY_URL,
				target: "_blank",
				rel: "noreferrer",
				underline: "none",
				color: "info"
			}, (0, _wordpress_i18n.__)("Privacy Policy", "elementor")), ".")
		}))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			justifyContent: "space-between",
			px: 3,
			pb: 3,
			pt: 1
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "secondary",
			onClick: function onClick() {
				return onCopyPrompt(check);
			},
			disabled: isInstalling,
			sx: { textAlign: "left" }
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			alignItems: "flex-start",
			spacing: 0
		}, /*#__PURE__*/ react.default.createElement("span", null, (0, _wordpress_i18n.__)("Not now?", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			component: "span",
			sx: { textDecoration: "underline" }
		}, (0, _wordpress_i18n.__)("Copy the prompt instead", "elementor")))), installFailed ? /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "contained",
			color: "primary",
			href: angiePluginSearchUrl
		}, (0, _wordpress_i18n.__)("Install manually", "elementor")) : /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "contained",
			color: "primary",
			disabled: !agreedToTerms || isInstalling,
			onClick: handleInstall,
			startIcon: isInstalling ? /*#__PURE__*/ react.default.createElement(_elementor_ui_CircularProgress.default, {
				size: INSTALL_SPINNER_SIZE,
				color: "inherit"
			}) : void 0
		}, getPrimaryLabel(isAngieInstalled, isInstalling))))));
	};
	AngieInstallDialog.propTypes = {
		check: import_prop_types.default.object.isRequired,
		heroUrl: import_prop_types.default.string.isRequired,
		angieAppUrl: import_prop_types.default.string.isRequired,
		angiePluginSearchUrl: import_prop_types.default.string.isRequired,
		isAngieInstalled: import_prop_types.default.bool,
		onClose: import_prop_types.default.func.isRequired,
		onCopyPrompt: import_prop_types.default.func.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/rescan-control.js
	var MS_PER_SECOND$1 = 1e3;
	var SECONDS_PER_MINUTE = 60;
	var MINUTES_PER_HOUR = 60;
	var MS_PER_MINUTE = SECONDS_PER_MINUTE * MS_PER_SECOND$1;
	var COOLDOWN_TICK_MS = MS_PER_MINUTE;
	var MINIMUM_REMAINING_MINUTES = 1;
	var RESCAN_LAYOUT_SCORE_ROW = "scoreRow";
	var RESCAN_LAYOUT_ERROR_SUMMARY = "errorSummary";
	var getRetryTimestamp = function getRetryTimestamp(retryAt) {
		var timestamp = retryAt ? Date.parse(retryAt) : NaN;
		return Number.isNaN(timestamp) ? null : timestamp;
	};
	var formatRemaining = function formatRemaining(remainingMs) {
		var totalMinutes = Math.max(MINIMUM_REMAINING_MINUTES, Math.ceil(remainingMs / MS_PER_MINUTE));
		var hours = Math.floor(totalMinutes / MINUTES_PER_HOUR);
		var minutes = totalMinutes % MINUTES_PER_HOUR;
		if (hours > 0 && minutes > 0) return "".concat(hours, "h ").concat(minutes, "m");
		if (hours > 0) return "".concat(hours, "h");
		return "".concat(minutes, "m");
	};
	var getNextScanLabel = function getNextScanLabel(retryAt, now) {
		var retryTimestamp = getRetryTimestamp(retryAt);
		if (null === retryTimestamp || retryTimestamp <= now) return "";
		return (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("Next scan: %s", "elementor"), formatRemaining(retryTimestamp - now));
	};
	var useNextScanLabel = function useNextScanLabel(retryAt) {
		var _useState2 = _slicedToArray((0, react.useState)(function() {
			return Date.now();
		}), 2);
		var now = _useState2[0];
		var setNow = _useState2[1];
		(0, react.useEffect)(function() {
			var retryTimestamp = getRetryTimestamp(retryAt);
			if (null === retryTimestamp || retryTimestamp <= Date.now()) return;
			setNow(Date.now());
			var timerId = setInterval(function() {
				var current = Date.now();
				setNow(current);
				if (retryTimestamp <= current) clearInterval(timerId);
			}, COOLDOWN_TICK_MS);
			return function() {
				return clearInterval(timerId);
			};
		}, [retryAt]);
		return getNextScanLabel(retryAt, now);
	};
	var RESCAN_TOOLTIP = (0, _wordpress_i18n.__)("Rescan", "elementor");
	var RescanTooltip = function RescanTooltip(_ref) {
		var children = _ref.children;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Tooltip.default, { title: RESCAN_TOOLTIP }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			component: "span",
			sx: { display: "inline-flex" }
		}, children));
	};
	RescanTooltip.propTypes = { children: import_prop_types.default.node.isRequired };
	var OutlinedRescanButton = function OutlinedRescanButton(_ref2) {
		var label = _ref2.label;
		var onRescan = _ref2.onRescan;
		var isDisabled = _ref2.isDisabled;
		return /*#__PURE__*/ react.default.createElement(RescanTooltip, null, /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "outlined",
			color: "secondary",
			size: "small",
			onClick: onRescan,
			disabled: isDisabled,
			startIcon: /*#__PURE__*/ react.default.createElement(_elementor_icons.ReloadIcon, null)
		}, label));
	};
	OutlinedRescanButton.propTypes = {
		label: import_prop_types.default.string.isRequired,
		onRescan: import_prop_types.default.func.isRequired,
		isDisabled: import_prop_types.default.bool
	};
	var RescanControl = function RescanControl(_ref3) {
		var retryAt = _ref3.retryAt;
		var onRescan = _ref3.onRescan;
		var _ref3$isBusy = _ref3.isBusy;
		var isBusy = _ref3$isBusy === void 0 ? false : _ref3$isBusy;
		var _ref3$isActivated = _ref3.isActivated;
		var isActivated = _ref3$isActivated === void 0 ? false : _ref3$isActivated;
		var _ref3$layout = _ref3.layout;
		var layout = _ref3$layout === void 0 ? RESCAN_LAYOUT_SCORE_ROW : _ref3$layout;
		var nextScanLabel = useNextScanLabel(retryAt);
		var isCoolingDown = "" !== nextScanLabel;
		var label = isCoolingDown ? nextScanLabel : (0, _wordpress_i18n.__)("Rescan", "elementor");
		var isDisabled = isBusy || isCoolingDown;
		if ("errorSummary" === layout) return /*#__PURE__*/ react.default.createElement(OutlinedRescanButton, {
			label,
			onRescan,
			isDisabled
		});
		var showLabeledButton = isActivated || isCoolingDown;
		return /*#__PURE__*/ react.default.createElement(react.default.Fragment, null, !showLabeledButton && /*#__PURE__*/ react.default.createElement(RescanTooltip, null, /*#__PURE__*/ react.default.createElement(_elementor_ui_IconButton.default, {
			variant: "outlined",
			size: "small",
			color: "secondary",
			onClick: onRescan,
			disabled: isBusy,
			"aria-label": (0, _wordpress_i18n.__)("Rescan", "elementor")
		}, /*#__PURE__*/ react.default.createElement(_elementor_icons.ReloadIcon, null))), showLabeledButton && /*#__PURE__*/ react.default.createElement(OutlinedRescanButton, {
			label,
			onRescan,
			isDisabled
		}));
	};
	RescanControl.propTypes = {
		retryAt: import_prop_types.default.string,
		onRescan: import_prop_types.default.func.isRequired,
		isBusy: import_prop_types.default.bool,
		isActivated: import_prop_types.default.bool,
		layout: import_prop_types.default.oneOf([RESCAN_LAYOUT_SCORE_ROW, RESCAN_LAYOUT_ERROR_SUMMARY])
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/error-report-card.js
	var ALERT_ICON_SIZE = 24;
	var LIST_INDENT = "21px";
	var getUnreachableTitle = function getUnreachableTitle(siteDomain) {
		return (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("We couldn't reach %s", "elementor"), siteDomain);
	};
	var getFailureCopy = function getFailureCopy(reason, siteDomain) {
		if ("local" === reason) return {
			title: (0, _wordpress_i18n.__)("Your site isn't public yet", "elementor"),
			description: (0, _wordpress_i18n.__)("We check a site the way an AI assistant would — by visiting it from the outside. This one only runs on your computer, so those checks will run the day it goes live. Everything below we could read from inside WordPress.", "elementor"),
			steps: [(0, _wordpress_i18n.__)("Switch the basics on now. They'll be in place the moment you publish.", "elementor"), (0, _wordpress_i18n.__)("Come back and run the check once the site is live, to see the outside half.", "elementor")]
		};
		if ("blocked" === reason) return {
			title: getUnreachableTitle(siteDomain),
			description: (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("A firewall or security plugin on %s turned our scanner away, so we couldn't see your site the way an agent does. Your score will appear once we can.", "elementor"), siteDomain),
			steps: [
				(0, _wordpress_i18n.__)("Activate Agent Ready anyway. Everything it sets up runs on your own server.", "elementor"),
				(0, _wordpress_i18n.__)("Ask your host or security plugin to allow the Elementor scanner.", "elementor"),
				(0, _wordpress_i18n.__)("Rescan once access is allowed.", "elementor")
			]
		};
		return {
			title: getUnreachableTitle(siteDomain),
			description: (0, _wordpress_i18n.__)("Something stopped us from loading your site from outside. Your score will appear once we can.", "elementor"),
			steps: [(0, _wordpress_i18n.__)("Activate Agent Ready anyway. Everything it sets up runs on your own server.", "elementor"), (0, _wordpress_i18n.__)("Rescan in a few minutes.", "elementor")]
		};
	};
	var ErrorReportCard = function ErrorReportCard(_ref) {
		var siteDomain = _ref.siteDomain;
		var reason = _ref.reason;
		var retryAt = _ref.retryAt;
		var onRescan = _ref.onRescan;
		var _ref$isActivated = _ref.isActivated;
		var isActivated = _ref$isActivated === void 0 ? false : _ref$isActivated;
		var _getFailureCopy = getFailureCopy(reason, siteDomain);
		var title = _getFailureCopy.title;
		var description = _getFailureCopy.description;
		var steps = _getFailureCopy.steps;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 2.5 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			justifyContent: "space-between",
			alignItems: "flex-start"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 0,
			flex: 1,
			minWidth: 0
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			spacing: 1
		}, /*#__PURE__*/ react.default.createElement(_elementor_icons.AlertCircleIcon, {
			color: "error",
			sx: { fontSize: ALERT_ICON_SIZE }
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "h6",
			component: "h3",
			color: "text.primary"
		}, title)), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, description)), /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, { flexShrink: 0 }, /*#__PURE__*/ react.default.createElement(RescanControl, {
			layout: RESCAN_LAYOUT_ERROR_SUMMARY,
			retryAt,
			onRescan,
			isActivated
		}))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			pt: 1,
			px: 3,
			pb: 3,
			bgcolor: SCANNER_COLORS.paper,
			border: 1,
			borderColor: "divider",
			borderRadius: SCANNER_LAYOUT.cardRadius
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "overline",
			color: "text.tertiary"
		}, (0, _wordpress_i18n.__)("What you can do now", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			component: "ol",
			m: 0,
			pl: LIST_INDENT
		}, steps.map(function(step, index) {
			return /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
				key: step,
				component: "li",
				variant: "body2",
				color: "text.secondary",
				mb: 0 === index ? 1 : 0
			}, step);
		}))));
	};
	ErrorReportCard.propTypes = {
		siteDomain: import_prop_types.default.string.isRequired,
		reason: import_prop_types.default.string,
		retryAt: import_prop_types.default.string,
		onRescan: import_prop_types.default.func.isRequired,
		isActivated: import_prop_types.default.bool
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/scanner-mock-data.js
	function ownKeys$11(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$11, "ownKeys");
	function _objectSpread$11(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$11(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$11(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$11, "_objectSpread");
	var PROGRESS_STEP_DELAYS_MS = [
		920,
		1380,
		1050,
		1720
	];
	var getProgressSteps = function getProgressSteps() {
		return [
			{
				id: "getting-site-ready",
				label: (0, _wordpress_i18n.__)("Getting your site ready to scan", "elementor")
			},
			{
				id: "agents-know-start",
				label: (0, _wordpress_i18n.__)("Checking if agents know where to start", "elementor")
			},
			{
				id: "agents-read-content",
				label: (0, _wordpress_i18n.__)("Checking if agents can read your content", "elementor")
			},
			{
				id: "agents-act-on-site",
				label: (0, _wordpress_i18n.__)("Checking if agents can act on your site", "elementor")
			},
			{
				id: "calculating-score",
				label: (0, _wordpress_i18n.__)("Calculating your score", "elementor")
			}
		];
	};
	var getProgressStepDelaysMs = function getProgressStepDelaysMs() {
		var gateStepCount = getProgressSteps().length - 1;
		if (PROGRESS_STEP_DELAYS_MS.length !== gateStepCount) throw new Error("Progress step delays (".concat(PROGRESS_STEP_DELAYS_MS.length, ") must equal progress steps before gate (").concat(gateStepCount, ")."));
		return PROGRESS_STEP_DELAYS_MS;
	};
	var ERROR_HIDDEN_STEP_ID = "agents-know-start";
	var getFailingChecks = function getFailingChecks() {
		return [
			{
				id: "agent-path",
				title: (0, _wordpress_i18n.__)("Agents can't find a path into your site", "elementor"),
				points: 7,
				tier: TIER_REQUIRED,
				priority: PRIORITY_REQUIRED,
				stage: STAGE_REACH,
				evidence: (0, _wordpress_i18n.__)("No agent discovery files were found under /.well-known/", "elementor"),
				explanation: {
					whatItMeans: (0, _wordpress_i18n.__)("Agents look for a small set of files that tell them what your site offers and where to start. Without them, an agent has to guess.", "elementor"),
					howToFix: (0, _wordpress_i18n.__)("Turn on agent discovery so Elementor publishes the files for you, or add them to /.well-known/ by hand.", "elementor")
				},
				fixWith: FIX_WITH_TOGGLE,
				isLocal: true,
				resolvedByActivation: false
			},
			{
				id: "service-area",
				title: (0, _wordpress_i18n.__)("Your service area isn't stated anywhere an agent can read", "elementor"),
				points: 7,
				tier: TIER_REQUIRED,
				priority: PRIORITY_REQUIRED,
				stage: STAGE_IDENTIFY,
				evidence: (0, _wordpress_i18n.__)("No areaServed value in your structured data", "elementor"),
				explanation: {
					whatItMeans: (0, _wordpress_i18n.__)("Tell agents where you work so they only recommend you to people you can serve.", "elementor"),
					howToFix: (0, _wordpress_i18n.__)("List the cities, regions or countries you serve.", "elementor")
				},
				preferencePlaceholder: (0, _wordpress_i18n.__)("e.g. Greater London and the South East", "elementor"),
				fixWith: FIX_WITH_PREFERENCE,
				isLocal: true,
				resolvedByActivation: false
			},
			{
				id: "opening-hours",
				title: (0, _wordpress_i18n.__)("Agents can't tell when you're open", "elementor"),
				points: 7,
				tier: TIER_REQUIRED,
				priority: PRIORITY_REQUIRED,
				stage: STAGE_IDENTIFY,
				evidence: (0, _wordpress_i18n.__)("No openingHours found on your homepage", "elementor"),
				explanation: {
					whatItMeans: (0, _wordpress_i18n.__)("When someone asks an agent whether you're open, it needs a machine-readable answer.", "elementor"),
					howToFix: (0, _wordpress_i18n.__)("Add your opening hours to your site's structured data.", "elementor")
				},
				fixWith: FIX_WITH_ANGIE,
				isLocal: false,
				resolvedByActivation: false
			},
			{
				id: "contact-action",
				title: (0, _wordpress_i18n.__)("Agents can't find a way to contact you", "elementor"),
				points: 6,
				tier: TIER_RECOMMENDED,
				priority: PRIORITY_RECOMMENDED,
				stage: "act",
				evidence: (0, _wordpress_i18n.__)("No ContactPoint or contact form found", "elementor"),
				explanation: {
					whatItMeans: (0, _wordpress_i18n.__)("Agents can book or ask on a visitor's behalf only if they can find how to reach you.", "elementor"),
					howToFix: (0, _wordpress_i18n.__)("Add a contact point with a phone number or email to your structured data.", "elementor")
				},
				fixWith: FIX_WITH_ANGIE,
				isLocal: false,
				resolvedByActivation: false
			},
			{
				id: "llms-guide",
				title: (0, _wordpress_i18n.__)("Agents don't get a guide to your content", "elementor"),
				points: 6,
				tier: TIER_RECOMMENDED,
				priority: PRIORITY_RECOMMENDED,
				stage: STAGE_REACH,
				evidence: (0, _wordpress_i18n.__)("llms.txt is missing a summary of your key pages", "elementor"),
				explanation: {
					whatItMeans: (0, _wordpress_i18n.__)("llms.txt gives agents a short map of your most useful pages.", "elementor"),
					howToFix: (0, _wordpress_i18n.__)("Choose which content goes into llms.txt in Agent Access.", "elementor")
				},
				fixWith: FIX_WITH_LINK,
				fixUrl: null,
				isLocal: true,
				resolvedByActivation: false
			},
			{
				id: "markdown-pages",
				title: (0, _wordpress_i18n.__)("Agents can't read clean versions of your pages", "elementor"),
				points: 15,
				tier: TIER_REQUIRED,
				priority: PRIORITY_REQUIRED,
				stage: STAGE_REACH,
				evidence: (0, _wordpress_i18n.__)("Requests for .md versions of your pages return 404", "elementor"),
				explanation: {
					whatItMeans: (0, _wordpress_i18n.__)("Agents read faster and more accurately from markdown than from full HTML pages.", "elementor"),
					howToFix: (0, _wordpress_i18n.__)("Activate Agent Ready to serve a markdown version of every page.", "elementor")
				},
				fixWith: FIX_WITH_TOGGLE,
				isLocal: true,
				resolvedByActivation: true
			},
			{
				id: "bot-rules",
				title: (0, _wordpress_i18n.__)("AI agents are blocked by your robots.txt", "elementor"),
				points: 15,
				tier: TIER_REQUIRED,
				priority: PRIORITY_REQUIRED,
				stage: STAGE_REACH,
				evidence: (0, _wordpress_i18n.__)("robots.txt has no rules for AI user agents", "elementor"),
				explanation: {
					whatItMeans: (0, _wordpress_i18n.__)("Without explicit rules, many AI agents treat your site as off limits.", "elementor"),
					howToFix: (0, _wordpress_i18n.__)("Activate Agent Ready to add per-bot rules: search allowed, AI answers allowed, no AI training.", "elementor")
				},
				fixWith: FIX_WITH_TOGGLE,
				isLocal: true,
				resolvedByActivation: true
			}
		];
	};
	var getPassingChecks = function getPassingChecks() {
		return [
			{
				id: "https",
				stage: STAGE_REACH,
				title: (0, _wordpress_i18n.__)("Your site loads over HTTPS", "elementor"),
				description: (0, _wordpress_i18n.__)("Agents only trust and connect to sites served over a secure connection.", "elementor"),
				points: 7
			},
			{
				id: "sitemap",
				stage: STAGE_REACH,
				title: (0, _wordpress_i18n.__)("Agents can read your sitemap", "elementor"),
				description: (0, _wordpress_i18n.__)("A sitemap lists your pages so agents can find them without guessing.", "elementor"),
				points: 7
			},
			{
				id: "site-name",
				stage: STAGE_IDENTIFY,
				title: (0, _wordpress_i18n.__)("Your business name is clear", "elementor"),
				description: (0, _wordpress_i18n.__)("A clear site name lets agents say who you are when they recommend you.", "elementor"),
				points: 8
			},
			{
				id: "page-titles",
				stage: STAGE_IDENTIFY,
				title: (0, _wordpress_i18n.__)("Every page has a descriptive title", "elementor"),
				description: (0, _wordpress_i18n.__)("Descriptive titles help agents tell your pages apart.", "elementor"),
				points: 8
			}
		];
	};
	var getStages = function getStages() {
		return [
			{
				id: STAGE_REACH,
				earned: 2,
				total: 6
			},
			{
				id: STAGE_IDENTIFY,
				earned: 2,
				total: 4
			},
			{
				id: "act",
				earned: 0,
				total: 1
			},
			{
				id: STAGE_COMMERCE,
				earned: 0,
				total: 0,
				isComingSoon: true
			}
		];
	};
	var withFixPrompt = function withFixPrompt(check) {
		return _objectSpread$11(_objectSpread$11({}, check), {}, { fixPrompt: check.explanation.howToFix });
	};
	var getMockScanResult = function getMockScanResult() {
		return {
			score: 30,
			ranAt: (/* @__PURE__ */ new Date()).toISOString(),
			stages: getStages(),
			checks: getFailingChecks().map(withFixPrompt),
			passingChecks: getPassingChecks(),
			outsideScan: {
				status: "ok",
				reason: null
			},
			activation: { scoreAfter: 60 }
		};
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/agent-ready-star-icon.js
	var _excluded = ["sx"];
	function ownKeys$10(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$10, "ownKeys");
	function _objectSpread$10(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$10(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$10(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$10, "_objectSpread");
	var ICON_SIZE = 24;
	var ANGI_STARS_PATH = "M17.7696 11.7602L17.5099 11.7499C14.6724 11.6381 12.3619 9.32784 12.2501 6.49032L12.2398 6.2304C12.2347 6.10176 12.1289 6 12 6C11.8711 6 11.7653 6.10176 11.7602 6.2304L11.7499 6.49032C11.6381 9.32784 9.32784 11.6381 6.49008 11.7499L6.2304 11.7602C6.10176 11.7653 6 11.8711 6 12C6 12.1289 6.10176 12.2347 6.23064 12.2398L6.49032 12.2498C9.32784 12.3619 11.6381 14.6722 11.7499 17.5097L11.7602 17.7696C11.7653 17.8982 11.8711 18 12 18C12.1289 18 12.2347 17.8982 12.2398 17.7696L12.2501 17.5097C12.3619 14.6722 14.6722 12.3619 17.5097 12.2498L17.7694 12.2398C17.8982 12.2347 18 12.1289 18 12C18 11.8711 17.8982 11.7653 17.7696 11.7602Z";
	var AgentReadyStarIcon = function AgentReadyStarIcon(_ref) {
		var sx = _ref.sx;
		var props = _objectWithoutProperties$1(_ref, _excluded);
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_SvgIcon.default, _extends$1({
			viewBox: "0 0 24 24",
			"aria-hidden": true,
			sx: _objectSpread$10({
				width: ICON_SIZE,
				height: ICON_SIZE,
				flexShrink: 0
			}, sx)
		}, props), /*#__PURE__*/ react.default.createElement("path", {
			d: ANGI_STARS_PATH,
			fill: "currentColor"
		}));
	};
	AgentReadyStarIcon.propTypes = { sx: import_prop_types.default.oneOfType([
		import_prop_types.default.object,
		import_prop_types.default.array,
		import_prop_types.default.func
	]) };

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/fix-row-actions.js
	function ownKeys$9(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$9, "ownKeys");
	function _objectSpread$9(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$9(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$9(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$9, "_objectSpread");
	var REASON_MAX_WIDTH = 280;
	var rowActionSx = {
		color: "text.primary",
		fontWeight: 500
	};
	var copyWithSelection = function copyWithSelection(text) {
		var textarea = document.createElement("textarea");
		textarea.value = text;
		textarea.setAttribute("readonly", "");
		textarea.style.position = "fixed";
		textarea.style.opacity = "0";
		document.body.appendChild(textarea);
		textarea.select();
		try {
			return document.execCommand("copy");
		} catch (_unused) {
			return false;
		} finally {
			document.body.removeChild(textarea);
		}
	};
	var copyText = /*#__PURE__*/ function() {
		var _ref = _asyncToGenerator$1(/*#__PURE__*/ import_regenerator.default.mark(function _callee(text) {
			var _navigator$clipboard;
			var normalizedText;
			return import_regenerator.default.wrap(function(_context) {
				while (1) switch (_context.prev = _context.next) {
					case 0:
						if (text) {
							_context.next = 1;
							break;
						}
						return _context.abrupt("return", false);
					case 1:
						normalizedText = text.replace(/\r\n/g, "\n");
						if (!((_navigator$clipboard = navigator.clipboard) !== null && _navigator$clipboard !== void 0 && _navigator$clipboard.writeText)) {
							_context.next = 5;
							break;
						}
						_context.prev = 2;
						_context.next = 3;
						return navigator.clipboard.writeText(normalizedText);
					case 3: return _context.abrupt("return", true);
					case 4:
						_context.prev = 4;
						_context["catch"](2);
						return _context.abrupt("return", copyWithSelection(normalizedText));
					case 5: return _context.abrupt("return", copyWithSelection(normalizedText));
					case 6:
					case "end": return _context.stop();
				}
			}, _callee, null, [[2, 4]]);
		}));
		return function copyText(_x) {
			return _ref.apply(this, arguments);
		};
	}();
	var CopyPromptButton = function CopyPromptButton(_ref2) {
		var onCopy = _ref2.onCopy;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			className: "scanner-copy-fix",
			variant: "text",
			color: "secondary",
			size: "small",
			startIcon: /*#__PURE__*/ react.default.createElement(_elementor_icons.CopyIcon, { fontSize: "tiny" }),
			onClick: onCopy
		}, (0, _wordpress_i18n.__)("Copy prompt", "elementor"));
	};
	CopyPromptButton.propTypes = { onCopy: import_prop_types.default.func.isRequired };
	var copyPromptText = copyText;
	var completedActionSx = {
		minWidth: 0,
		height: SCANNER_LAYOUT.completedActionHeight,
		px: "5px",
		py: "3px"
	};
	var CompletedActions = function CompletedActions(_ref3) {
		var check = _ref3.check;
		var _ref3$isSaving = _ref3.isSaving;
		var isSaving = _ref3$isSaving === void 0 ? false : _ref3$isSaving;
		var onUndo = _ref3.onUndo;
		var onRescan = _ref3.onRescan;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "secondary",
			size: "small",
			disabled: isSaving,
			onClick: function onClick() {
				return onUndo(check);
			},
			sx: completedActionSx
		}, (0, _wordpress_i18n.__)("Undo", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "inherit",
			size: "small",
			disabled: isSaving,
			onClick: onRescan,
			sx: _objectSpread$9(_objectSpread$9({}, completedActionSx), {}, { color: "text.primary" })
		}, (0, _wordpress_i18n.__)("Rescan", "elementor")));
	};
	CompletedActions.propTypes = {
		check: import_prop_types.default.object.isRequired,
		isSaving: import_prop_types.default.bool,
		onUndo: import_prop_types.default.func.isRequired,
		onRescan: import_prop_types.default.func.isRequired
	};
	var CreateForMeButton = function CreateForMeButton(_ref4) {
		var check = _ref4.check;
		var isSaving = _ref4.isSaving;
		var isDisabled = _ref4.isDisabled;
		var disabledReason = _ref4.disabledReason;
		var onSetEnabled = _ref4.onSetEnabled;
		var reasonId = (0, react.useId)();
		var button = /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "inherit",
			size: "small",
			disabled: isSaving || isDisabled,
			"aria-describedby": disabledReason ? reasonId : void 0,
			onClick: function onClick() {
				return onSetEnabled(check, true);
			},
			sx: rowActionSx
		}, (0, _wordpress_i18n.__)("Create for me", "elementor"));
		if (!disabledReason) return button;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Infotip.default, {
			arrow: true,
			placement: "top",
			content: /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
				p: 2,
				maxWidth: REASON_MAX_WIDTH,
				boxSizing: "border-box"
			}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
				variant: "body2",
				color: "text.primary"
			}, disabledReason))
		}, /*#__PURE__*/ react.default.createElement("span", { style: { display: "inline-flex" } }, button, /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			component: "span",
			id: reasonId,
			sx: VISUALLY_HIDDEN_SX
		}, disabledReason)));
	};
	CreateForMeButton.propTypes = {
		check: import_prop_types.default.object.isRequired,
		isSaving: import_prop_types.default.bool,
		isDisabled: import_prop_types.default.bool,
		disabledReason: import_prop_types.default.string,
		onSetEnabled: import_prop_types.default.func.isRequired
	};
	var RowAction = function RowAction(_ref5) {
		var check = _ref5.check;
		var isResolved = _ref5.isResolved;
		var _ref5$isSaving = _ref5.isSaving;
		var isSaving = _ref5$isSaving === void 0 ? false : _ref5$isSaving;
		var _ref5$isToggleDisable = _ref5.isToggleDisabled;
		var isToggleDisabled = _ref5$isToggleDisable === void 0 ? false : _ref5$isToggleDisable;
		var _ref5$toggleDisabledR = _ref5.toggleDisabledReason;
		var toggleDisabledReason = _ref5$toggleDisabledR === void 0 ? null : _ref5$toggleDisabledR;
		var agentAccessUrl = _ref5.agentAccessUrl;
		var seoPluginSearchUrl = _ref5.seoPluginSearchUrl;
		var onOpen = _ref5.onOpen;
		var onFixWithAngie = _ref5.onFixWithAngie;
		var onSetEnabled = _ref5.onSetEnabled;
		var onUndo = _ref5.onUndo;
		var onRescan = _ref5.onRescan;
		if ("toggle" === check.fixWith && !isResolved) return /*#__PURE__*/ react.default.createElement(CreateForMeButton, {
			check,
			isSaving,
			isDisabled: isToggleDisabled,
			disabledReason: toggleDisabledReason,
			onSetEnabled
		});
		if (isResolved) return /*#__PURE__*/ react.default.createElement(CompletedActions, {
			check,
			isSaving,
			onUndo,
			onRescan
		});
		if ("angie" === check.fixWith) return /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "inherit",
			size: "small",
			onClick: function onClick() {
				return onFixWithAngie(check);
			},
			sx: rowActionSx
		}, (0, _wordpress_i18n.__)("Fix with Angie", "elementor"));
		if ("preference" === check.fixWith) return /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "inherit",
			size: "small",
			onClick: function onClick() {
				return onOpen(check.id);
			},
			sx: rowActionSx
		}, (0, _wordpress_i18n.__)("Set your preference", "elementor"));
		if ("link" === check.fixWith) return /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "inherit",
			size: "small",
			href: check.fixUrl || agentAccessUrl,
			endIcon: /*#__PURE__*/ react.default.createElement(_elementor_icons.ArrowUpRightIcon, { fontSize: "tiny" }),
			sx: rowActionSx
		}, (0, _wordpress_i18n.__)("Open settings", "elementor"));
		if ("seo" === check.fixWith) return /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "inherit",
			size: "small",
			href: seoPluginSearchUrl,
			endIcon: /*#__PURE__*/ react.default.createElement(_elementor_icons.ArrowUpRightIcon, { fontSize: "tiny" }),
			sx: rowActionSx
		}, (0, _wordpress_i18n.__)("Browse SEO plugins", "elementor"));
		if ("manual" === check.fixWith) return /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.disabled",
			sx: {
				fontWeight: 500,
				whiteSpace: "nowrap"
			}
		}, (0, _wordpress_i18n.__)("Manual fix", "elementor"));
		return null;
	};
	RowAction.propTypes = {
		check: import_prop_types.default.object.isRequired,
		isResolved: import_prop_types.default.bool,
		isSaving: import_prop_types.default.bool,
		isToggleDisabled: import_prop_types.default.bool,
		toggleDisabledReason: import_prop_types.default.string,
		agentAccessUrl: import_prop_types.default.string.isRequired,
		seoPluginSearchUrl: import_prop_types.default.string.isRequired,
		onOpen: import_prop_types.default.func.isRequired,
		onFixWithAngie: import_prop_types.default.func.isRequired,
		onSetEnabled: import_prop_types.default.func.isRequired,
		onUndo: import_prop_types.default.func.isRequired,
		onRescan: import_prop_types.default.func.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/fix-row-drawer.js
	function ownKeys$8(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$8, "ownKeys");
	function _objectSpread$8(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$8(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$8(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$8, "_objectSpread");
	var drawerSx = _objectSpread$8({
		animation: "scannerDrawerIn ".concat(SCANNER_MOTION.drawerMs, "ms ").concat(SCANNER_MOTION.easeOutExpo),
		"@keyframes scannerDrawerIn": {
			from: {
				opacity: 0,
				transform: "translateY(".concat("6px", ")")
			},
			to: {
				opacity: 1,
				transform: "translateY(0)"
			}
		}
	}, REDUCED_MOTION_SX);
	var ExplanationContent = function ExplanationContent(_ref) {
		var check = _ref.check;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 2 }, check.evidence && /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: .5 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.primary"
		}, (0, _wordpress_i18n.__)("What we found", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, check.evidence)), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: .5 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.primary"
		}, (0, _wordpress_i18n.__)("What it means", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, check.explanation.whatItMeans)), !isPromptBasedFix(check) && /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: .5 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.primary"
		}, (0, _wordpress_i18n.__)("How to fix it manually", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, check.explanation.howToFix)));
	};
	ExplanationContent.propTypes = { check: import_prop_types.default.object.isRequired };
	var PreferenceContent = function PreferenceContent(_ref2) {
		var check = _ref2.check;
		var value = _ref2.value;
		var _onChange = _ref2.onChange;
		var onSave = _ref2.onSave;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 2 }, check.technicalName && /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, check.technicalName), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, check.explanation.whatItMeans), /*#__PURE__*/ react.default.createElement(_elementor_ui_TextField.default, {
			size: "small",
			fullWidth: true,
			multiline: true,
			rows: SCANNER_LAYOUT.preferenceRows,
			value,
			placeholder: check.preferencePlaceholder,
			onChange: function onChange(event) {
				return _onChange(check.id, event.target.value);
			},
			onKeyDown: function handleKeyDown(event) {
				if ("Enter" !== event.key || event.shiftKey) return;
				event.preventDefault();
				onSave(check);
			},
			inputProps: { "aria-label": check.title },
			sx: _objectSpread$8(_objectSpread$8({}, wpAdminInputResetSx), {}, { bgcolor: SCANNER_COLORS.paper })
		}));
	};
	PreferenceContent.propTypes = {
		check: import_prop_types.default.object.isRequired,
		value: import_prop_types.default.string.isRequired,
		onChange: import_prop_types.default.func.isRequired,
		onSave: import_prop_types.default.func.isRequired
	};
	var FixRowDrawer = function FixRowDrawer(_ref3) {
		var check = _ref3.check;
		var preferenceValue = _ref3.preferenceValue;
		var onPreferenceChange = _ref3.onPreferenceChange;
		var onSavePreference = _ref3.onSavePreference;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			mt: 2,
			mx: 2,
			mb: 1.5,
			p: 2,
			bgcolor: SCANNER_COLORS.promptBackground,
			borderRadius: SCANNER_LAYOUT.drawerRadius,
			sx: drawerSx
		}, "preference" === check.fixWith ? /*#__PURE__*/ react.default.createElement(PreferenceContent, {
			check,
			value: preferenceValue,
			onChange: onPreferenceChange,
			onSave: onSavePreference
		}) : /*#__PURE__*/ react.default.createElement(ExplanationContent, { check }));
	};
	FixRowDrawer.propTypes = {
		check: import_prop_types.default.object.isRequired,
		preferenceValue: import_prop_types.default.string.isRequired,
		onPreferenceChange: import_prop_types.default.func.isRequired,
		onSavePreference: import_prop_types.default.func.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/fix-row.js
	function ownKeys$7(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$7, "ownKeys");
	function _objectSpread$7(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$7(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$7(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$7, "_objectSpread");
	var ROW_ACTIVATION_KEYS$1 = ["Enter", " "];
	var stopPropagation = function stopPropagation(event) {
		return event.stopPropagation();
	};
	var PRIORITY_CHIP_COLORS = _defineProperty$1(_defineProperty$1({}, PRIORITY_REQUIRED, "error"), PRIORITY_RECOMMENDED, "success");
	var PriorityChip = function PriorityChip(_ref) {
		var priority = _ref.priority;
		if (!PRIORITY_CHIP_COLORS[priority]) return null;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Chip.default, {
			size: "tiny",
			shape: "rounded",
			variant: "standard",
			color: PRIORITY_CHIP_COLORS[priority],
			label: getCheckPriorityLabel({ priority })
		});
	};
	PriorityChip.propTypes = { priority: import_prop_types.default.string };
	var getDotColor = function getDotColor(check) {
		var _check$status;
		return getCheckStatusDotColor((_check$status = check.status) !== null && _check$status !== void 0 ? _check$status : CHECK_STATUS_FAIL);
	};
	var StatusIndicator = function StatusIndicator(_ref2) {
		var _check$status2;
		var check = _ref2.check;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			width: SCANNER_LAYOUT.statusIconSlot,
			display: "flex",
			justifyContent: "center",
			flexShrink: 0
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			width: SCANNER_LAYOUT.statusDotSize,
			height: SCANNER_LAYOUT.statusDotSize,
			borderRadius: "50%",
			bgcolor: getDotColor(check)
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			component: "span",
			sx: VISUALLY_HIDDEN_SX
		}, getCheckStatusLabel((_check$status2 = check.status) !== null && _check$status2 !== void 0 ? _check$status2 : CHECK_STATUS_FAIL)));
	};
	StatusIndicator.propTypes = { check: import_prop_types.default.object.isRequired };
	var FixRow = function FixRow(_ref3) {
		var check = _ref3.check;
		var isOpen = _ref3.isOpen;
		var isResolved = _ref3.isResolved;
		var _ref3$isSaving = _ref3.isSaving;
		var isSaving = _ref3$isSaving === void 0 ? false : _ref3$isSaving;
		var _ref3$isToggleDisable = _ref3.isToggleDisabled;
		var isToggleDisabled = _ref3$isToggleDisable === void 0 ? false : _ref3$isToggleDisable;
		var _ref3$toggleDisabledR = _ref3.toggleDisabledReason;
		var toggleDisabledReason = _ref3$toggleDisabledR === void 0 ? null : _ref3$toggleDisabledR;
		var isLast = _ref3.isLast;
		var _ref3$preferenceValue = _ref3.preferenceValue;
		var preferenceValue = _ref3$preferenceValue === void 0 ? "" : _ref3$preferenceValue;
		var agentAccessUrl = _ref3.agentAccessUrl;
		var seoPluginSearchUrl = _ref3.seoPluginSearchUrl;
		var onToggle = _ref3.onToggle;
		var onOpen = _ref3.onOpen;
		var onCopyPrompt = _ref3.onCopyPrompt;
		var onFixWithAngie = _ref3.onFixWithAngie;
		var onSetEnabled = _ref3.onSetEnabled;
		var onUndo = _ref3.onUndo;
		var onRescan = _ref3.onRescan;
		var onPreferenceChange = _ref3.onPreferenceChange;
		var onSavePreference = _ref3.onSavePreference;
		var showCopyPrompt = isPromptBasedFix(check) && !isResolved;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			borderBottom: isOpen || isLast ? 0 : 1,
			borderColor: "divider",
			bgcolor: SCANNER_COLORS.paper
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			role: "button",
			tabIndex: 0,
			"aria-expanded": isOpen,
			direction: "row",
			alignItems: "center",
			gap: 3,
			minHeight: SCANNER_LAYOUT.fixRowMinHeight,
			p: 2,
			boxSizing: "border-box",
			onClick: function onClick() {
				return onToggle(check.id);
			},
			onKeyDown: function handleKeyDown(event) {
				if (event.target !== event.currentTarget || !ROW_ACTIVATION_KEYS$1.includes(event.key)) return;
				event.preventDefault();
				onToggle(check.id);
			},
			sx: {
				cursor: "pointer",
				bgcolor: isOpen ? SCANNER_COLORS.paper : void 0,
				"& .scanner-fix-title": { color: isOpen ? "text.primary" : "text.secondary" },
				"&:hover, &:focus-within": { bgcolor: isOpen ? SCANNER_COLORS.paper : SCANNER_COLORS.rowHover },
				"&:hover .scanner-fix-title, &:focus-within .scanner-fix-title": { color: "text.primary" },
				"& .scanner-copy-fix": { display: "none" },
				"&:hover .scanner-copy-fix, &:focus-within .scanner-copy-fix": { display: "inline-flex" },
				"&:focus-visible": {
					outline: 2,
					outlineColor: "primary.main",
					outlineOffset: -2
				}
			}
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			gap: .5,
			flex: 1,
			minWidth: 0
		}, /*#__PURE__*/ react.default.createElement(StatusIndicator, { check }), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			className: "scanner-fix-title",
			variant: "body2"
		}, check.title), /*#__PURE__*/ react.default.createElement(PriorityChip, { priority: check.priority })), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			justifyContent: "flex-end",
			gap: 1,
			minWidth: SCANNER_LAYOUT.actionAreaWidth,
			flexShrink: 0
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			onClick: stopPropagation,
			onKeyDown: stopPropagation
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			gap: 1
		}, showCopyPrompt && /*#__PURE__*/ react.default.createElement(CopyPromptButton, { onCopy: function onCopy() {
			return onCopyPrompt(check);
		} }), /*#__PURE__*/ react.default.createElement(RowAction, {
			check,
			isResolved,
			isSaving,
			isToggleDisabled,
			toggleDisabledReason,
			agentAccessUrl,
			seoPluginSearchUrl,
			onOpen,
			onFixWithAngie,
			onSetEnabled,
			onUndo,
			onRescan
		}))), /*#__PURE__*/ react.default.createElement(_elementor_icons.ChevronDownIcon, { sx: _objectSpread$7({
			fontSize: SCANNER_LAYOUT.chevronSize,
			color: "text.tertiary",
			transform: isOpen ? "rotate(180deg)" : "none",
			transition: "transform ".concat(SCANNER_MOTION.chevronMs, "ms ").concat(SCANNER_MOTION.easeOutExpo)
		}, REDUCED_MOTION_SX) }))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Collapse.default, {
			in: isOpen,
			timeout: SCANNER_MOTION.drawerMs,
			unmountOnExit: true
		}, /*#__PURE__*/ react.default.createElement(FixRowDrawer, {
			check,
			preferenceValue,
			onPreferenceChange,
			onSavePreference
		}), "preference" === check.fixWith && /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			justifyContent: "flex-end",
			px: 2,
			pb: 1.5
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "secondary",
			size: "small",
			onClick: function onClick() {
				return onToggle(check.id);
			}
		}, (0, _wordpress_i18n.__)("Skip", "elementor")))), isOpen && !isLast && /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			borderBottom: 1,
			borderColor: "divider"
		}));
	};
	FixRow.propTypes = {
		check: import_prop_types.default.object.isRequired,
		isOpen: import_prop_types.default.bool,
		isResolved: import_prop_types.default.bool,
		isSaving: import_prop_types.default.bool,
		isToggleDisabled: import_prop_types.default.bool,
		toggleDisabledReason: import_prop_types.default.string,
		isLast: import_prop_types.default.bool,
		preferenceValue: import_prop_types.default.string,
		agentAccessUrl: import_prop_types.default.string.isRequired,
		seoPluginSearchUrl: import_prop_types.default.string.isRequired,
		onToggle: import_prop_types.default.func.isRequired,
		onOpen: import_prop_types.default.func.isRequired,
		onCopyPrompt: import_prop_types.default.func.isRequired,
		onFixWithAngie: import_prop_types.default.func.isRequired,
		onSetEnabled: import_prop_types.default.func.isRequired,
		onUndo: import_prop_types.default.func.isRequired,
		onRescan: import_prop_types.default.func.isRequired,
		onPreferenceChange: import_prop_types.default.func.isRequired,
		onSavePreference: import_prop_types.default.func.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/fix-table.js
	function ownKeys$6(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$6, "ownKeys");
	function _objectSpread$6(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$6(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$6(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$6, "_objectSpread");
	var ROW_RISE = "3px";
	var SKELETON_TITLE_WIDTH = "55%";
	var SKELETON_ACTION_WIDTH = 96;
	var SKELETON_ACTION_HEIGHT = 24;
	var HEADER_FONT_SIZE = "12px";
	var HEADER_LETTER_SPACING = "1px";
	var rowAppearSx = _objectSpread$6({
		animation: "scannerRowIn ".concat(SCANNER_MOTION.rowAppearMs, "ms ease-out"),
		"@keyframes scannerRowIn": {
			from: {
				opacity: 0,
				transform: "translateY(".concat(ROW_RISE, ")")
			},
			to: {
				opacity: 1,
				transform: "translateY(0)"
			}
		}
	}, REDUCED_MOTION_SX);
	var TableCard = function TableCard(_ref) {
		var children = _ref.children;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			bgcolor: SCANNER_COLORS.paper,
			border: 1,
			borderColor: "divider",
			borderRadius: SCANNER_LAYOUT.cardRadius,
			overflow: "hidden"
		}, children);
	};
	TableCard.propTypes = { children: import_prop_types.default.node.isRequired };
	var TableHeaderCell = function TableHeaderCell(_ref2) {
		var children = _ref2.children;
		var width = _ref2.width;
		var _ref2$align = _ref2.align;
		var align = _ref2$align === void 0 ? "left" : _ref2$align;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "overline",
			color: "text.secondary",
			width,
			flex: width ? "none" : 1,
			textAlign: align,
			px: 2,
			boxSizing: "border-box",
			lineHeight: 2,
			sx: {
				fontSize: HEADER_FONT_SIZE,
				letterSpacing: HEADER_LETTER_SPACING
			}
		}, children);
	};
	TableHeaderCell.propTypes = {
		children: import_prop_types.default.node.isRequired,
		width: import_prop_types.default.number,
		align: import_prop_types.default.string
	};
	var CheckTableHeader = function CheckTableHeader() {
		return /*#__PURE__*/ react.default.createElement(TableHeader, null, /*#__PURE__*/ react.default.createElement(TableHeaderCell, null, (0, _wordpress_i18n.__)("Check", "elementor")), /*#__PURE__*/ react.default.createElement(TableHeaderCell, {
			width: SCANNER_LAYOUT.actionColumnWidth,
			align: "right"
		}, (0, _wordpress_i18n.__)("Actions", "elementor")));
	};
	var TableHeader = function TableHeader(_ref3) {
		var children = _ref3.children;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			height: SCANNER_LAYOUT.tableHeaderHeight,
			bgcolor: SCANNER_COLORS.tableHeader
		}, children);
	};
	TableHeader.propTypes = { children: import_prop_types.default.node.isRequired };
	var SkeletonRow = function SkeletonRow() {
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			gap: 3,
			minHeight: SCANNER_LAYOUT.fixRowMinHeight,
			p: 2,
			boxSizing: "border-box",
			borderBottom: 1,
			borderColor: "divider"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Skeleton.default, {
			variant: "circular",
			width: SCANNER_LAYOUT.statusDotSize,
			height: SCANNER_LAYOUT.statusDotSize
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Skeleton.default, {
			variant: "text",
			width: SKELETON_TITLE_WIDTH,
			sx: { bgcolor: "action.hover" }
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, { flex: 1 }), /*#__PURE__*/ react.default.createElement(_elementor_ui_Skeleton.default, {
			variant: "rounded",
			width: SKELETON_ACTION_WIDTH,
			height: SKELETON_ACTION_HEIGHT,
			sx: { bgcolor: "action.hover" }
		}));
	};
	var FixTableRows = function FixTableRows(_ref4) {
		var checks = _ref4.checks;
		var flow = _ref4.flow;
		var agentAccessUrl = _ref4.agentAccessUrl;
		var seoPluginSearchUrl = _ref4.seoPluginSearchUrl;
		var isLoading = _ref4.isLoading;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, { "aria-busy": isLoading }, isLoading ? checks.map(function(check) {
			return /*#__PURE__*/ react.default.createElement(SkeletonRow, { key: check.id });
		}) : checks.map(function(check, index) {
			var _flow$isModuleToggleD;
			var _flow$getToggleDisabl;
			var _flow$getToggleDisabl2;
			var _flow$moduleEnabled;
			var _flow$resolvedIds;
			var _flow$savingCheckIds;
			var _flow$savingModuleKey;
			var moduleKey = "toggle" === check.fixWith ? getToggleModuleKey(check.id) : null;
			var isToggleDisabled = moduleKey && ((_flow$isModuleToggleD = flow.isModuleToggleDisabled) === null || _flow$isModuleToggleD === void 0 ? void 0 : _flow$isModuleToggleD.call(flow, moduleKey));
			var toggleDisabledReason = moduleKey ? (_flow$getToggleDisabl = (_flow$getToggleDisabl2 = flow.getToggleDisabledReason) === null || _flow$getToggleDisabl2 === void 0 ? void 0 : _flow$getToggleDisabl2.call(flow, moduleKey)) !== null && _flow$getToggleDisabl !== void 0 ? _flow$getToggleDisabl : null : null;
			return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
				key: check.id,
				sx: rowAppearSx
			}, /*#__PURE__*/ react.default.createElement(FixRow, {
				check,
				isOpen: !!flow.openIds[check.id],
				isResolved: moduleKey ? !!((_flow$moduleEnabled = flow.moduleEnabled) !== null && _flow$moduleEnabled !== void 0 && _flow$moduleEnabled[moduleKey]) : !!((_flow$resolvedIds = flow.resolvedIds) !== null && _flow$resolvedIds !== void 0 && _flow$resolvedIds[check.id]),
				isSaving: !!((_flow$savingCheckIds = flow.savingCheckIds) !== null && _flow$savingCheckIds !== void 0 && _flow$savingCheckIds[check.id]) || !!(moduleKey && (_flow$savingModuleKey = flow.savingModuleKeys) !== null && _flow$savingModuleKey !== void 0 && _flow$savingModuleKey[moduleKey]),
				isToggleDisabled: !!isToggleDisabled,
				toggleDisabledReason,
				isLast: index === checks.length - 1,
				preferenceValue: flow.preferences[check.id],
				agentAccessUrl,
				seoPluginSearchUrl,
				onToggle: flow.toggleRow,
				onOpen: flow.openRow,
				onCopyPrompt: flow.openHowToFix,
				onFixWithAngie: flow.fixWithAngie,
				onSetEnabled: flow.setCheckEnabled,
				onUndo: flow.undoFix,
				onRescan: flow.startScan,
				onPreferenceChange: flow.setPreference,
				onSavePreference: flow.savePreference
			}));
		}));
	};
	var fixTableRowsPropTypes = {
		checks: import_prop_types.default.arrayOf(import_prop_types.default.object).isRequired,
		flow: import_prop_types.default.shape({
			openIds: import_prop_types.default.object.isRequired,
			moduleEnabled: import_prop_types.default.object,
			resolvedIds: import_prop_types.default.object,
			isModuleToggleDisabled: import_prop_types.default.func,
			getToggleDisabledReason: import_prop_types.default.func,
			savingModuleKeys: import_prop_types.default.object,
			savingCheckIds: import_prop_types.default.object,
			preferences: import_prop_types.default.object.isRequired,
			toggleRow: import_prop_types.default.func.isRequired,
			openRow: import_prop_types.default.func.isRequired,
			openHowToFix: import_prop_types.default.func.isRequired,
			fixWithAngie: import_prop_types.default.func.isRequired,
			setCheckEnabled: import_prop_types.default.func.isRequired,
			undoFix: import_prop_types.default.func.isRequired,
			startScan: import_prop_types.default.func.isRequired,
			setPreference: import_prop_types.default.func.isRequired,
			savePreference: import_prop_types.default.func.isRequired
		}).isRequired,
		agentAccessUrl: import_prop_types.default.string.isRequired,
		seoPluginSearchUrl: import_prop_types.default.string.isRequired,
		isLoading: import_prop_types.default.bool
	};
	FixTableRows.propTypes = fixTableRowsPropTypes;
	var FixTable = function FixTable(props) {
		return /*#__PURE__*/ react.default.createElement(TableCard, null, /*#__PURE__*/ react.default.createElement(CheckTableHeader, null), /*#__PURE__*/ react.default.createElement(FixTableRows, props));
	};
	FixTable.propTypes = fixTableRowsPropTypes;

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/checks-summary.js
	function ownKeys$5(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$5, "ownKeys");
	function _objectSpread$5(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$5(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$5(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$5, "_objectSpread");
	var CHECKS_KIND_FAILED = "failed";
	var CHECKS_KIND_PASSED = "passed";
	var getToggleLabel = function getToggleLabel(kind, isExpanded) {
		if ("passed" === kind) return isExpanded ? (0, _wordpress_i18n.__)("Hide passed checks", "elementor") : (0, _wordpress_i18n.__)("Show passed checks", "elementor");
		return isExpanded ? (0, _wordpress_i18n.__)("Hide failed checks", "elementor") : (0, _wordpress_i18n.__)("Show failed checks", "elementor");
	};
	var CollapsibleCardHeader = function CollapsibleCardHeader(_ref) {
		var _ref$kind = _ref.kind;
		var kind = _ref$kind === void 0 ? CHECKS_KIND_FAILED : _ref$kind;
		var count = _ref.count;
		var title = _ref.title;
		var isExpanded = _ref.isExpanded;
		var isLoading = _ref.isLoading;
		var onToggle = _ref.onToggle;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			justifyContent: "space-between",
			gap: 2,
			p: 2,
			borderBottom: isExpanded ? 1 : 0,
			borderColor: "divider",
			onClick: isLoading ? void 0 : onToggle,
			sx: isLoading ? void 0 : { cursor: "pointer" }
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			gap: 1,
			flex: 1,
			minWidth: 0
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			width: SCANNER_LAYOUT.countBadgeSize,
			height: SCANNER_LAYOUT.countBadgeSize,
			borderRadius: "50%",
			bgcolor: SCANNER_COLORS.countBadge,
			display: "flex",
			alignItems: "center",
			justifyContent: "center",
			flexShrink: 0
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "caption",
			color: "text.secondary"
		}, count)), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "subtitle2",
			color: "text.primary"
		}, title)), /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "secondary",
			size: "small",
			disabled: isLoading,
			"aria-expanded": isExpanded,
			"aria-label": getToggleLabel(kind, isExpanded),
			endIcon: /*#__PURE__*/ react.default.createElement(_elementor_icons.ChevronDownIcon, { sx: _objectSpread$5({
				transform: isExpanded ? "rotate(180deg)" : "none",
				transition: "transform ".concat(SCANNER_MOTION.chevronMs, "ms ").concat(SCANNER_MOTION.easeOutExpo)
			}, REDUCED_MOTION_SX) })
		}, isExpanded ? (0, _wordpress_i18n.__)("Hide checks", "elementor") : (0, _wordpress_i18n.__)("Show checks", "elementor")));
	};
	CollapsibleCardHeader.propTypes = {
		kind: import_prop_types.default.oneOf([CHECKS_KIND_FAILED, CHECKS_KIND_PASSED]),
		count: import_prop_types.default.number.isRequired,
		title: import_prop_types.default.string.isRequired,
		isExpanded: import_prop_types.default.bool,
		isLoading: import_prop_types.default.bool,
		onToggle: import_prop_types.default.func.isRequired
	};
	var ChecksSummary = function ChecksSummary(_ref2) {
		var checks = _ref2.checks;
		var activationFixes = _ref2.activationFixes;
		var isExpanded = _ref2.isExpanded;
		var isLoading = _ref2.isLoading;
		var onToggle = _ref2.onToggle;
		var children = _ref2.children;
		return /*#__PURE__*/ react.default.createElement(TableCard, null, /*#__PURE__*/ react.default.createElement(CollapsibleCardHeader, {
			count: checks.length,
			title: (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("%1$d failed checks to be fixed. Activating agent access settings fixes %2$d of them, the others require Angie or a manual fix.", "elementor"), checks.length, activationFixes),
			isExpanded,
			isLoading,
			onToggle
		}), isExpanded && /*#__PURE__*/ react.default.createElement(CheckTableHeader, null), children);
	};
	ChecksSummary.propTypes = {
		checks: import_prop_types.default.arrayOf(import_prop_types.default.object).isRequired,
		activationFixes: import_prop_types.default.number.isRequired,
		isExpanded: import_prop_types.default.bool,
		isLoading: import_prop_types.default.bool,
		onToggle: import_prop_types.default.func.isRequired,
		children: import_prop_types.default.node
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/fixes-section.js
	function ownKeys$4(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$4, "ownKeys");
	function _objectSpread$4(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$4(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$4(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$4, "_objectSpread");
	var TYPE_ALL = "all";
	var PRIORITY_ALL = "all";
	var FILTER_STAGES = [
		STAGE_REACH,
		STAGE_IDENTIFY,
		"act"
	];
	var SECTION_GAP$1 = "20px";
	var VISIBLE_FIX_COUNT = 5;
	var getTypeOptions = function getTypeOptions() {
		var stageLabels = getStageLabels();
		return [{
			id: TYPE_ALL,
			label: (0, _wordpress_i18n.__)("All", "elementor")
		}].concat(_toConsumableArray(FILTER_STAGES.map(function(stage) {
			return {
				id: stage,
				label: stageLabels[stage]
			};
		})));
	};
	var isFixedByActivation = function isFixedByActivation(check, flow) {
		var _flow$isModuleUnavail;
		var _flow$moduleEnabled;
		if (!check.resolvedByActivation) return false;
		var moduleKey = getToggleModuleKey(check.id);
		if (!moduleKey) return true;
		return !((_flow$isModuleUnavail = flow.isModuleUnavailable) !== null && _flow$isModuleUnavail !== void 0 && _flow$isModuleUnavail.call(flow, moduleKey)) && !((_flow$moduleEnabled = flow.moduleEnabled) !== null && _flow$moduleEnabled !== void 0 && _flow$moduleEnabled[moduleKey]);
	};
	var FixesFilters = function FixesFilters(_ref) {
		var priority = _ref.priority;
		var stage = _ref.stage;
		var onPriorityChange = _ref.onPriorityChange;
		var onStageChange = _ref.onStageChange;
		var isDisabled = _ref.isDisabled;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			gap: 2,
			flexWrap: "wrap"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			gap: 1
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: isDisabled ? "text.disabled" : "text.primary",
			sx: { fontSize: "13px" }
		}, (0, _wordpress_i18n.__)("Priority", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Select.default, {
			size: "tiny",
			value: priority,
			disabled: isDisabled,
			onChange: function onChange(event) {
				return onPriorityChange(event.target.value);
			},
			inputProps: { "aria-label": (0, _wordpress_i18n.__)("Priority", "elementor") },
			sx: _objectSpread$4(_objectSpread$4({}, wpAdminInputResetSx), {}, { width: SCANNER_LAYOUT.prioritySelectWidth })
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_MenuItem.default, { value: PRIORITY_ALL }, (0, _wordpress_i18n.__)("All", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_MenuItem.default, { value: PRIORITY_REQUIRED }, (0, _wordpress_i18n.__)("Required", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_MenuItem.default, { value: PRIORITY_RECOMMENDED }, (0, _wordpress_i18n.__)("Recommended", "elementor")))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Divider.default, {
			orientation: "vertical",
			flexItem: true
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			gap: 1,
			flexWrap: "wrap"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: isDisabled ? "text.disabled" : "text.primary",
			sx: { fontSize: "13px" }
		}, (0, _wordpress_i18n.__)("Layer", "elementor")), getTypeOptions().map(function(option) {
			return /*#__PURE__*/ react.default.createElement(_elementor_ui_Chip.default, {
				key: option.id,
				size: "small",
				shape: "rounded",
				color: stage === option.id ? "primary" : "default",
				label: option.label,
				disabled: isDisabled,
				onClick: function onClick() {
					return onStageChange(option.id);
				},
				"aria-pressed": stage === option.id
			});
		})));
	};
	FixesFilters.propTypes = {
		priority: import_prop_types.default.string.isRequired,
		stage: import_prop_types.default.string.isRequired,
		onPriorityChange: import_prop_types.default.func.isRequired,
		onStageChange: import_prop_types.default.func.isRequired,
		isDisabled: import_prop_types.default.bool
	};
	var FixesSection = function FixesSection(_ref2) {
		var checks = _ref2.checks;
		var flow = _ref2.flow;
		var agentAccessUrl = _ref2.agentAccessUrl;
		var seoPluginSearchUrl = _ref2.seoPluginSearchUrl;
		var isActivated = _ref2.isActivated;
		var isLoading = _ref2.isLoading;
		var _useState2 = _slicedToArray((0, react.useState)(TYPE_ALL), 2);
		var selectedType = _useState2[0];
		var setSelectedType = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)(PRIORITY_ALL), 2);
		var priority = _useState4[0];
		var setPriority = _useState4[1];
		var _useState6 = _slicedToArray((0, react.useState)(false), 2);
		var isShowAllFixes = _useState6[0];
		var setIsShowAllFixes = _useState6[1];
		var isExpanded = flow.areChecksExpanded || isLoading;
		var filteredChecks = checks.filter(function(check) {
			var matchesPriority = PRIORITY_ALL === priority || check.priority === priority;
			var matchesType = TYPE_ALL === selectedType || check.stage === selectedType;
			return matchesPriority && matchesType;
		});
		var shownChecks = isShowAllFixes ? filteredChecks : filteredChecks.slice(0, VISIBLE_FIX_COUNT);
		var hiddenFixCount = Math.max(0, filteredChecks.length - VISIBLE_FIX_COUNT);
		var isFixListVisible = isActivated || isExpanded;
		var activationFixes = checks.filter(function(check) {
			return isFixedByActivation(check, flow);
		}).length;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { gap: SECTION_GAP$1 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			component: "h3",
			color: "text.primary",
			sx: sectionHeadingSx
		}, (0, _wordpress_i18n.__)("Fix these next", "elementor")), /*#__PURE__*/ react.default.createElement(FixesFilters, {
			priority,
			stage: selectedType,
			onPriorityChange: setPriority,
			onStageChange: setSelectedType
		}), !isActivated && /*#__PURE__*/ react.default.createElement(ChecksSummary, {
			checks,
			activationFixes,
			isExpanded,
			isLoading,
			onToggle: flow.toggleChecksExpanded
		}, isExpanded && /*#__PURE__*/ react.default.createElement(FixTableRows, {
			checks: shownChecks,
			flow,
			agentAccessUrl,
			seoPluginSearchUrl,
			isLoading
		})), isActivated && /*#__PURE__*/ react.default.createElement(FixTable, {
			checks: shownChecks,
			flow,
			agentAccessUrl,
			seoPluginSearchUrl,
			isLoading
		}), isFixListVisible && hiddenFixCount > 0 && /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			justifyContent: "flex-end"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "info",
			size: "medium",
			onClick: function onClick() {
				return setIsShowAllFixes(function(current) {
					return !current;
				});
			}
		}, isShowAllFixes ? (0, _wordpress_i18n.__)("Show less", "elementor") : (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("Show all fixes (+%d)", "elementor"), hiddenFixCount))));
	};
	FixesSection.propTypes = {
		checks: import_prop_types.default.arrayOf(import_prop_types.default.object).isRequired,
		flow: import_prop_types.default.object.isRequired,
		agentAccessUrl: import_prop_types.default.string.isRequired,
		seoPluginSearchUrl: import_prop_types.default.string.isRequired,
		isActivated: import_prop_types.default.bool,
		isLoading: import_prop_types.default.bool
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/floating-check-bar.js
	var FLOATING_BAR_Z_INDEX = 10;
	var FLOATING_BAR_SHADOW = "0 4px 16px rgba(0, 0, 0, 0.16)";
	var FloatingCheckBar = function FloatingCheckBar(_ref) {
		var retryAt = _ref.retryAt;
		var onCheck = _ref.onCheck;
		var nextScanLabel = useNextScanLabel(retryAt);
		var isCoolingDown = "" !== nextScanLabel;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			position: "sticky",
			bottom: SCANNER_LAYOUT.floatingBarBottomOffset,
			zIndex: FLOATING_BAR_Z_INDEX,
			display: "flex",
			justifyContent: "center",
			sx: { pointerEvents: "none" }
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			justifyContent: "space-between",
			gap: 2,
			width: SCANNER_LAYOUT.floatingBarWidth,
			maxWidth: "100%",
			height: SCANNER_LAYOUT.floatingBarHeight,
			px: 2,
			boxSizing: "border-box",
			bgcolor: SCANNER_COLORS.paper,
			borderRadius: SCANNER_LAYOUT.cardRadius,
			boxShadow: FLOATING_BAR_SHADOW,
			sx: { pointerEvents: "auto" }
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.primary"
		}, (0, _wordpress_i18n.__)("See what AI says about your business.", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "contained",
			color: "primary",
			size: "small",
			onClick: onCheck,
			disabled: isCoolingDown
		}, isCoolingDown ? nextScanLabel : (0, _wordpress_i18n.__)("Check my site", "elementor"))));
	};
	FloatingCheckBar.propTypes = {
		retryAt: import_prop_types.default.string,
		onCheck: import_prop_types.default.func.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/passing-table.js
	function ownKeys$3(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$3, "ownKeys");
	function _objectSpread$3(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$3(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$3(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$3, "_objectSpread");
	var ROW_ACTIVATION_KEYS = ["Enter", " "];
	var DetailSection = function DetailSection(_ref) {
		var title = _ref.title;
		var body = _ref.body;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: .5 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.primary"
		}, title), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.secondary"
		}, body));
	};
	DetailSection.propTypes = {
		title: import_prop_types.default.string.isRequired,
		body: import_prop_types.default.string.isRequired
	};
	var PassingRow = function PassingRow(_ref2) {
		var check = _ref2.check;
		var stageLabel = _ref2.stageLabel;
		var isLast = _ref2.isLast;
		var isOpen = _ref2.isOpen;
		var onToggle = _ref2.onToggle;
		var isExpandable = Boolean(check.description || check.evidence);
		var interactiveProps = isExpandable ? {
			role: "button",
			tabIndex: 0,
			"aria-expanded": isOpen,
			onClick: function onClick() {
				return onToggle(check.id);
			},
			onKeyDown: function handleKeyDown(event) {
				if (event.target !== event.currentTarget || !ROW_ACTIVATION_KEYS.includes(event.key)) return;
				event.preventDefault();
				onToggle(check.id);
			}
		} : {};
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			borderBottom: isLast ? 0 : 1,
			borderColor: "divider",
			bgcolor: SCANNER_COLORS.paper
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, _extends$1({
			direction: "row",
			alignItems: "center",
			minHeight: SCANNER_LAYOUT.passingRowHeight
		}, interactiveProps, { sx: isExpandable ? {
			cursor: "pointer",
			"&:hover, &:focus-within": { bgcolor: SCANNER_COLORS.rowHover },
			"&:focus-visible": {
				outline: 2,
				outlineColor: "primary.main",
				outlineOffset: -2
			}
		} : void 0 }), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "overline",
			color: "text.secondary",
			width: SCANNER_LAYOUT.passingStageWidth,
			px: 2,
			flexShrink: 0,
			lineHeight: 2
		}, stageLabel), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "subtitle2",
			color: "text.primary",
			flex: 1,
			minWidth: 0,
			px: 2
		}, check.title), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			justifyContent: "flex-end",
			gap: 1,
			px: 2,
			flexShrink: 0,
			boxSizing: "border-box"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			width: SCANNER_LAYOUT.statusDotSize,
			height: SCANNER_LAYOUT.statusDotSize,
			borderRadius: "50%",
			bgcolor: "success.light"
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "caption",
			color: "text.primary"
		}, (0, _wordpress_i18n.__)("Pass", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			width: SCANNER_LAYOUT.chevronSize,
			height: SCANNER_LAYOUT.chevronSize,
			display: "flex",
			alignItems: "center",
			justifyContent: "center"
		}, isExpandable && /*#__PURE__*/ react.default.createElement(_elementor_icons.ChevronDownIcon, { sx: _objectSpread$3({
			fontSize: SCANNER_LAYOUT.chevronSize,
			color: "text.tertiary",
			transform: isOpen ? "rotate(180deg)" : "none",
			transition: "transform ".concat(SCANNER_MOTION.chevronMs, "ms ").concat(SCANNER_MOTION.easeOutExpo)
		}, REDUCED_MOTION_SX) })))), isExpandable && /*#__PURE__*/ react.default.createElement(_elementor_ui_Collapse.default, {
			in: isOpen,
			timeout: SCANNER_MOTION.drawerMs,
			unmountOnExit: true
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			mt: 2,
			mx: 2,
			mb: 1.5,
			p: 2,
			bgcolor: SCANNER_COLORS.promptBackground,
			borderRadius: SCANNER_LAYOUT.drawerRadius
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 2 }, check.description && /*#__PURE__*/ react.default.createElement(DetailSection, {
			title: (0, _wordpress_i18n.__)("What it means", "elementor"),
			body: check.description
		}), check.evidence && /*#__PURE__*/ react.default.createElement(DetailSection, {
			title: (0, _wordpress_i18n.__)("What we found", "elementor"),
			body: check.evidence
		})))));
	};
	PassingRow.propTypes = {
		check: import_prop_types.default.shape({
			id: import_prop_types.default.string.isRequired,
			title: import_prop_types.default.string.isRequired,
			description: import_prop_types.default.string,
			evidence: import_prop_types.default.string
		}).isRequired,
		stageLabel: import_prop_types.default.string.isRequired,
		isLast: import_prop_types.default.bool,
		isOpen: import_prop_types.default.bool,
		onToggle: import_prop_types.default.func.isRequired
	};
	var SourceLine = function SourceLine() {
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.tertiary"
		}, (0, _wordpress_i18n.__)("Source:", "elementor"), " ", /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			component: "a",
			variant: "body2",
			href: SCAN_SOURCE_URL,
			target: "_blank",
			rel: "noreferrer",
			color: "inherit",
			sx: { textDecoration: "underline" }
		}, (0, _wordpress_i18n.__)("Ora API", "elementor")));
	};
	var PassingTable = function PassingTable(_ref3) {
		var checks = _ref3.checks;
		var _useState2 = _slicedToArray((0, react.useState)({}), 2);
		var openIds = _useState2[0];
		var setOpenIds = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)(false), 2);
		var isExpanded = _useState4[0];
		var setIsExpanded = _useState4[1];
		var toggleRow = function toggleRow(checkId) {
			return setOpenIds(function(current) {
				return _objectSpread$3(_objectSpread$3({}, current), {}, _defineProperty$1({}, checkId, !current[checkId]));
			});
		};
		if (!checks.length) return /*#__PURE__*/ react.default.createElement(SourceLine, null);
		var stageLabels = getStageLabels();
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 2 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: .5 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			component: "h3",
			color: "text.primary",
			sx: sectionHeadingSx
		}, (0, _wordpress_i18n.__)("Already working", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.primary"
		}, (0, _wordpress_i18n.__)("These checks passed. No action needed.", "elementor"))), /*#__PURE__*/ react.default.createElement(TableCard, null, /*#__PURE__*/ react.default.createElement(CollapsibleCardHeader, {
			kind: CHECKS_KIND_PASSED,
			count: checks.length,
			title: (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n._n)("%d Check passed", "%d Checks passed", checks.length, "elementor"), checks.length),
			isExpanded,
			onToggle: function onToggle() {
				return setIsExpanded(function(current) {
					return !current;
				});
			}
		}), isExpanded && checks.map(function(check, index) {
			return /*#__PURE__*/ react.default.createElement(PassingRow, {
				key: check.id,
				check,
				stageLabel: stageLabels[check.stage],
				isLast: index === checks.length - 1,
				isOpen: Boolean(openIds[check.id]),
				onToggle: toggleRow
			});
		})), /*#__PURE__*/ react.default.createElement(SourceLine, null));
	};
	PassingTable.propTypes = { checks: import_prop_types.default.arrayOf(import_prop_types.default.object).isRequired };

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/score-ring.js
	function ownKeys$2(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$2, "ownKeys");
	function _objectSpread$2(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$2(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$2(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$2, "_objectSpread");
	var DIMMED_OPACITY = .2;
	var RADIUS = (SCANNER_LAYOUT.ringSize - SCANNER_LAYOUT.ringStroke) / 2;
	var CIRCUMFERENCE = 2 * Math.PI * RADIUS;
	var CENTER = SCANNER_LAYOUT.ringSize / 2;
	var RING_START_ANGLE_DEG = -90;
	var ScoreRing = function ScoreRing(_ref) {
		var score = _ref.score;
		var durationMs = _ref.durationMs;
		var isDimmed = _ref.isDimmed;
		var gradientId = (0, react.useId)();
		var normalizedScore = normalizeReadinessScore(score);
		var hasScore = null !== normalizedScore;
		var targetScore = hasScore ? normalizedScore : 0;
		var _useState2 = _slicedToArray((0, react.useState)(0), 2);
		var drawnScore = _useState2[0];
		var setDrawnScore = _useState2[1];
		(0, react.useEffect)(function() {
			var frame = window.requestAnimationFrame(function() {
				return setDrawnScore(targetScore);
			});
			return function() {
				return window.cancelAnimationFrame(frame);
			};
		}, [targetScore]);
		var gradient = SCORE_RING_GRADIENTS[getScoreTier(targetScore)];
		var dashOffset = CIRCUMFERENCE * (1 - drawnScore / 100);
		var label = hasScore ? (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("Agent readiness score: %d out of 100", "elementor"), targetScore) : (0, _wordpress_i18n.__)("Agent readiness score unavailable", "elementor");
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			position: "relative",
			width: SCANNER_LAYOUT.ringSize,
			height: SCANNER_LAYOUT.ringSize,
			flexShrink: 0,
			role: "img",
			"aria-label": label
		}, /*#__PURE__*/ react.default.createElement("svg", {
			width: SCANNER_LAYOUT.ringSize,
			height: SCANNER_LAYOUT.ringSize,
			viewBox: "0 0 ".concat(SCANNER_LAYOUT.ringSize, " ").concat(SCANNER_LAYOUT.ringSize),
			"aria-hidden": "true"
		}, /*#__PURE__*/ react.default.createElement("defs", null, /*#__PURE__*/ react.default.createElement("linearGradient", {
			id: gradientId,
			gradientUnits: "userSpaceOnUse",
			gradientTransform: "rotate(".concat(-RING_START_ANGLE_DEG, " ").concat(CENTER, " ").concat(CENTER, ")"),
			x1: gradient.x1,
			y1: gradient.y1,
			x2: gradient.x2,
			y2: gradient.y2
		}, /*#__PURE__*/ react.default.createElement("stop", {
			offset: "0%",
			stopColor: gradient.from
		}), /*#__PURE__*/ react.default.createElement("stop", {
			offset: "100%",
			stopColor: gradient.to
		}))), /*#__PURE__*/ react.default.createElement("circle", {
			cx: CENTER,
			cy: CENTER,
			r: RADIUS,
			fill: "none",
			stroke: SCANNER_COLORS.ringTrack,
			strokeWidth: SCANNER_LAYOUT.ringStroke
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			component: "circle",
			cx: CENTER,
			cy: CENTER,
			r: RADIUS,
			fill: "none",
			stroke: "url(#".concat(gradientId, ")"),
			strokeWidth: SCANNER_LAYOUT.ringStroke,
			strokeLinecap: "round",
			strokeDasharray: CIRCUMFERENCE,
			strokeDashoffset: dashOffset,
			transform: "rotate(".concat(RING_START_ANGLE_DEG, " ").concat(CENTER, " ").concat(CENTER, ")"),
			sx: _objectSpread$2({ transition: "stroke-dashoffset ".concat(durationMs, "ms ").concat(SCANNER_MOTION.easeOutCubic) }, REDUCED_MOTION_SX)
		})), /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			position: "absolute",
			top: 0,
			left: 0,
			right: 0,
			bottom: 0,
			display: "flex",
			alignItems: "center",
			justifyContent: "center",
			"aria-hidden": "true",
			sx: {
				opacity: isDimmed ? DIMMED_OPACITY : 1,
				transition: "opacity ".concat(SCANNER_MOTION.drawerMs, "ms ease-out")
			}
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			display: "flex",
			flexDirection: "column",
			alignItems: "center"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "h6",
			component: "span",
			color: "text.primary",
			lineHeight: "32px"
		}, hasScore ? targetScore : "--"), hasScore && /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "caption",
			component: "span",
			color: "text.disabled",
			lineHeight: "20px"
		}, "/".concat(100)))));
	};
	ScoreRing.propTypes = {
		score: import_prop_types.default.number,
		durationMs: import_prop_types.default.number.isRequired,
		isDimmed: import_prop_types.default.bool
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/score-summary-block.js
	var WHERE_YOU_STAND_MAX = 39;
	var GETTING_THERE_MAX = 69;
	var IN_GOOD_SHAPE_MAX = 89;
	var CLOCK_PAD = 2;
	var HOURS_IN_HALF_DAY = 12;
	var BANNER_BLOCK_GAP = "20px";
	var PLAIN_BLOCK_GAP = "32px";
	var BANNER_BLOCK_EXTRA_BOTTOM_SPACE = "".concat(SCANNER_LAYOUT.summaryBlockBottomSpace - SCANNER_LAYOUT.pageGap, "px");
	var SCORE_TEXT_GAP = "32px";
	var METADATA_ROW_GAP = 1;
	var getScoreCopy = function getScoreCopy(score) {
		if ("number" !== typeof score) return {
			heading: (0, _wordpress_i18n.__)("Rescan to get your score", "elementor"),
			description: (0, _wordpress_i18n.__)("Run a scan to see how ready your site is for agents.", "elementor")
		};
		if (score <= WHERE_YOU_STAND_MAX) return {
			heading: (0, _wordpress_i18n.__)("Where you stand", "elementor"),
			description: (0, _wordpress_i18n.__)("Some of what agents need is already in place, but they can't yet read your prices or use your forms. Below is what to fix, with a prompt you can paste into any AI tool.", "elementor")
		};
		if (score <= GETTING_THERE_MAX) return {
			heading: (0, _wordpress_i18n.__)("Getting there", "elementor"),
			description: (0, _wordpress_i18n.__)("Agents can use parts of your site, but several gaps still keep them from acting for your visitors. Below is what to fix, with a prompt you can paste into any AI tool.", "elementor")
		};
		if (score <= IN_GOOD_SHAPE_MAX) return {
			heading: (0, _wordpress_i18n.__)("In good shape", "elementor"),
			description: (0, _wordpress_i18n.__)("Most of what agents need is in place. A few gaps still stop them from using everything your site offers. Below is what to fix, with a prompt you can paste into any AI tool.", "elementor")
		};
		return {
			heading: (0, _wordpress_i18n.__)("Leading the way", "elementor"),
			description: (0, _wordpress_i18n.__)("Agents can find, understand, and act on your site. Only small gaps remain. Below is what to fix, with a prompt you can paste into any AI tool.", "elementor")
		};
	};
	var formatClock = function formatClock(date) {
		var hours = date.getHours();
		var minutes = String(date.getMinutes()).padStart(CLOCK_PAD, "0");
		var suffix = hours >= HOURS_IN_HALF_DAY ? "pm" : "am";
		var hour12 = hours % HOURS_IN_HALF_DAY || HOURS_IN_HALF_DAY;
		return "".concat(hour12, ":").concat(minutes, " ").concat(suffix);
	};
	var formatCheckedAt = function formatCheckedAt(ranAt) {
		var date = new Date(ranAt);
		if (Number.isNaN(date.getTime())) return "";
		var time = formatClock(date);
		if (date.toDateString() === (/* @__PURE__ */ new Date()).toDateString()) return (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("Checked today at %s", "elementor"), time);
		return (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("Checked on %s", "elementor"), date.toLocaleDateString());
	};
	var ScoreSummaryBlock = function ScoreSummaryBlock(_ref) {
		var children = _ref.children;
		var banner = _ref.banner;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			gap: banner ? BANNER_BLOCK_GAP : PLAIN_BLOCK_GAP,
			mb: banner ? BANNER_BLOCK_EXTRA_BOTTOM_SPACE : 0
		}, children, banner);
	};
	ScoreSummaryBlock.propTypes = {
		children: import_prop_types.default.node.isRequired,
		banner: import_prop_types.default.node
	};
	var ScoreCopySkeleton = function ScoreCopySkeleton() {
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 1,
			"aria-hidden": "true"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Skeleton.default, {
			variant: "text",
			width: SCANNER_LAYOUT.skeletonHeadingWidth,
			height: SCANNER_LAYOUT.skeletonHeadingHeight
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Skeleton.default, {
			variant: "text",
			width: "100%"
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Skeleton.default, {
			variant: "text",
			width: SCANNER_LAYOUT.skeletonMetaWidth
		}));
	};
	var ScoreRow = function ScoreRow(_ref2) {
		var score = _ref2.score;
		var ringDurationMs = _ref2.ringDurationMs;
		var isDimmed = _ref2.isDimmed;
		var isActivated = _ref2.isActivated;
		var isBusy = _ref2.isBusy;
		var needsRescanForScore = _ref2.needsRescanForScore;
		var siteDomain = _ref2.siteDomain;
		var ranAt = _ref2.ranAt;
		var agentAccessUrl = _ref2.agentAccessUrl;
		var retryAt = _ref2.retryAt;
		var onRescan = _ref2.onRescan;
		var _getScoreCopy = getScoreCopy(score);
		var heading = _getScoreCopy.heading;
		var description = _getScoreCopy.description;
		var checkedAt = ranAt ? formatCheckedAt(ranAt) : "";
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "flex-start",
			gap: SCORE_TEXT_GAP
		}, /*#__PURE__*/ react.default.createElement(ScoreRing, {
			score,
			durationMs: ringDurationMs,
			isDimmed
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 2,
			flex: 1,
			minWidth: 0
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 0 }, isBusy ? /*#__PURE__*/ react.default.createElement(ScoreCopySkeleton, null) : /*#__PURE__*/ react.default.createElement(react.default.Fragment, null, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "h5",
			component: "h3",
			color: "text.primary"
		}, heading), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body1",
			color: "text.secondary"
		}, description)), needsRescanForScore && /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "caption",
			color: "text.tertiary"
		}, (0, _wordpress_i18n.__)("Rescan to update your score.", "elementor"))), checkedAt && /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			spacing: METADATA_ROW_GAP
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "caption",
			color: "text.tertiary"
		}, "".concat(siteDomain, " · ").concat(checkedAt)), /*#__PURE__*/ react.default.createElement(ScoreStandardInfotip, null)), isActivated && /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			spacing: .5
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "info.main"
		}, (0, _wordpress_i18n.__)("Agent Ready is on", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "primary",
			size: "small",
			href: agentAccessUrl,
			endIcon: /*#__PURE__*/ react.default.createElement(_elementor_icons.ExternalLinkIcon, { fontSize: "tiny" })
		}, (0, _wordpress_i18n.__)("Settings", "elementor")))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			flexShrink: 0
		}, /*#__PURE__*/ react.default.createElement(RescanControl, {
			layout: RESCAN_LAYOUT_SCORE_ROW,
			retryAt,
			onRescan,
			isBusy,
			isActivated
		})));
	};
	ScoreRow.propTypes = {
		score: import_prop_types.default.number,
		ringDurationMs: import_prop_types.default.number.isRequired,
		isDimmed: import_prop_types.default.bool,
		isActivated: import_prop_types.default.bool,
		isBusy: import_prop_types.default.bool,
		needsRescanForScore: import_prop_types.default.bool,
		siteDomain: import_prop_types.default.string.isRequired,
		ranAt: import_prop_types.default.string,
		agentAccessUrl: import_prop_types.default.string.isRequired,
		retryAt: import_prop_types.default.string,
		onRescan: import_prop_types.default.func.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/stage-bars.js
	function ownKeys$1(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	__name(ownKeys$1, "ownKeys");
	function _objectSpread$1(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys$1(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys$1(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	__name(_objectSpread$1, "_objectSpread");
	var BAR_HEIGHT = 4;
	var BAR_RADIUS = "8px";
	var COUNT_ROW_GAP = 1;
	var getFillRatio = function getFillRatio(stage) {
		return stage.total > 0 ? stage.earned / stage.total : 0;
	};
	var StageBar = function StageBar(_ref) {
		var _healthColors$fill;
		var stage = _ref.stage;
		var label = _ref.label;
		var question = _ref.question;
		var healthTier = getStageHealthTier(stage);
		var healthColors = healthTier ? STAGE_HEALTH_COLORS[healthTier] : null;
		var healthLabel = healthTier ? getStageHealthLabels()[healthTier] : "";
		var fillColor = (_healthColors$fill = healthColors === null || healthColors === void 0 ? void 0 : healthColors.fill) !== null && _healthColors$fill !== void 0 ? _healthColors$fill : STAGE_COMING_SOON_FILL;
		var countLabel = stage.isComingSoon ? (0, _wordpress_i18n.__)("Coming soon", "elementor") : (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("%1$d / %2$d", "elementor"), stage.earned, stage.total);
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 1,
			flex: 1,
			minWidth: 0,
			pt: 1,
			px: 1,
			pb: 2
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "overline",
			color: "text.secondary",
			lineHeight: 2
		}, label), /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			position: "relative",
			height: BAR_HEIGHT,
			borderRadius: BAR_RADIUS,
			overflow: "hidden"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			position: "absolute",
			top: 0,
			left: 0,
			right: 0,
			bottom: 0,
			bgcolor: fillColor,
			sx: { opacity: SCANNER_LAYOUT.progressTrackWash }
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			position: "relative",
			height: "100%",
			width: "".concat(getFillRatio(stage) * 100, "%"),
			bgcolor: fillColor,
			borderRadius: BAR_RADIUS,
			sx: _objectSpread$1({ transition: "width ".concat(SCANNER_MOTION.ringArriveMs, "ms ").concat(SCANNER_MOTION.easeOutCubic) }, REDUCED_MOTION_SX)
		})), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			spacing: COUNT_ROW_GAP
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "caption",
			color: "text.disabled"
		}, countLabel), healthLabel && /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "caption",
			color: healthColors.text
		}, healthLabel)), question && /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.tertiary"
		}, question));
	};
	StageBar.propTypes = {
		stage: import_prop_types.default.shape({
			id: import_prop_types.default.string.isRequired,
			earned: import_prop_types.default.number.isRequired,
			total: import_prop_types.default.number.isRequired,
			isComingSoon: import_prop_types.default.bool
		}).isRequired,
		label: import_prop_types.default.string.isRequired,
		question: import_prop_types.default.string
	};
	var StageBars = function StageBars(_ref2) {
		var stages = _ref2.stages;
		var labels = getStageLabels();
		var questions = getStageQuestions();
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			display: "flex",
			flexWrap: "wrap"
		}, stages.map(function(stage) {
			return /*#__PURE__*/ react.default.createElement(StageBar, {
				key: stage.id,
				stage,
				label: labels[stage.id],
				question: questions[stage.id]
			});
		}));
	};
	StageBars.propTypes = { stages: import_prop_types.default.arrayOf(import_prop_types.default.object).isRequired };

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/first-run-view.js
	var HERO_COPY_MAX_WIDTH = 608;
	var HERO_SECTION_GAP = 6;
	var FEATURE_CARD_GAP = .5;
	var PREVIEW_BLUR = "5px";
	var PREVIEW_OPACITY = .5;
	var PREVIEW_TOP_SPACE = "56px";
	var PREVIEW_RING_DURATION_MS = 0;
	var PAGE_INLINE_PADDING = "32px";
	var noop$1 = function noop() {};
	var PREVIEW_FLOW = {
		openIds: {},
		resolvedIds: {},
		preferences: {},
		toggleRow: noop$1,
		openRow: noop$1,
		openHowToFix: noop$1,
		fixWithAngie: noop$1,
		setCheckEnabled: noop$1,
		undoFix: noop$1,
		startScan: noop$1,
		setPreference: noop$1,
		savePreference: noop$1,
		areChecksExpanded: false,
		toggleChecksExpanded: noop$1
	};
	var getFeatureCards = function getFeatureCards() {
		return [
			{
				title: (0, _wordpress_i18n.__)("Find the gaps", "elementor"),
				body: (0, _wordpress_i18n.__)("Learn what makes your site hard for AI agents to find, read and take action on.", "elementor")
			},
			{
				title: (0, _wordpress_i18n.__)("Know exactly what to fix", "elementor"),
				body: (0, _wordpress_i18n.__)("Get a clear fix for each gap and find out how to fix it.", "elementor")
			},
			{
				title: (0, _wordpress_i18n.__)("Rescan when you're done", "elementor"),
				body: (0, _wordpress_i18n.__)("Rescan once you've made changes to see your site's revised score.", "elementor")
			}
		];
	};
	var FeatureCard = function FeatureCard(_ref) {
		var title = _ref.title;
		var body = _ref.body;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: FEATURE_CARD_GAP,
			flex: 1,
			p: 3,
			bgcolor: SCANNER_COLORS.paper,
			border: 1,
			borderColor: "divider",
			borderRadius: SCANNER_LAYOUT.featureCardRadius,
			textAlign: "left"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "center",
			spacing: FEATURE_CARD_GAP
		}, /*#__PURE__*/ react.default.createElement(AgentReadyStarIcon, { sx: { color: SCANNER_COLORS.accent } }), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "subtitle1",
			color: "text.secondary"
		}, title)), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.tertiary"
		}, body));
	};
	FeatureCard.propTypes = {
		title: import_prop_types.default.string.isRequired,
		body: import_prop_types.default.string.isRequired
	};
	var ScannerPreview = function ScannerPreview(_ref2) {
		var agentAccessUrl = _ref2.agentAccessUrl;
		var seoPluginSearchUrl = _ref2.seoPluginSearchUrl;
		var siteDomain = _ref2.siteDomain;
		var scan = getMockScanResult();
		var score = getBaseScore(scan);
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			"aria-hidden": "true",
			inert: "",
			pt: PREVIEW_TOP_SPACE,
			sx: {
				width: "calc(100% + (".concat(PAGE_INLINE_PADDING, " * 2))"),
				paddingInline: PAGE_INLINE_PADDING,
				boxSizing: "border-box",
				filter: "blur(".concat(PREVIEW_BLUR, ")"),
				opacity: PREVIEW_OPACITY,
				pointerEvents: "none",
				userSelect: "none"
			}
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { gap: "".concat(SCANNER_LAYOUT.pageGap, "px") }, /*#__PURE__*/ react.default.createElement(ScoreSummaryBlock, null, /*#__PURE__*/ react.default.createElement(ScoreRow, {
			score,
			ringDurationMs: PREVIEW_RING_DURATION_MS,
			isActivated: true,
			siteDomain,
			ranAt: scan.ranAt,
			agentAccessUrl,
			onRescan: noop$1
		})), /*#__PURE__*/ react.default.createElement(StageBars, { stages: scan.stages }), /*#__PURE__*/ react.default.createElement(FixesSection, {
			checks: getVisibleChecks(scan),
			flow: PREVIEW_FLOW,
			agentAccessUrl,
			seoPluginSearchUrl,
			isActivated: true
		}), /*#__PURE__*/ react.default.createElement(PassingTable, { checks: scan.passingChecks })));
	};
	ScannerPreview.propTypes = {
		agentAccessUrl: import_prop_types.default.string.isRequired,
		seoPluginSearchUrl: import_prop_types.default.string.isRequired,
		siteDomain: import_prop_types.default.string.isRequired
	};
	var FirstRunView = function FirstRunView(_ref3) {
		var agentAccessUrl = _ref3.agentAccessUrl;
		var seoPluginSearchUrl = _ref3.seoPluginSearchUrl;
		var siteDomain = _ref3.siteDomain;
		var onStart = _ref3.onStart;
		var _ref3$retryAt = _ref3.retryAt;
		var retryAt = _ref3$retryAt === void 0 ? null : _ref3$retryAt;
		var nextScanLabel = useNextScanLabel(retryAt);
		var isCoolingDown = "" !== nextScanLabel;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			alignItems: "center",
			width: "100%"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			alignItems: "center",
			spacing: HERO_SECTION_GAP,
			textAlign: "center",
			width: "100%"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			alignItems: "center",
			spacing: 1
		}, /*#__PURE__*/ react.default.createElement(BetaChip, null), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "h4",
			component: "h2",
			color: "text.primary"
		}, (0, _wordpress_i18n.__)("Is your site ready for agents?", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body1",
			color: "text.secondary",
			maxWidth: HERO_COPY_MAX_WIDTH
		}, (0, _wordpress_i18n.__)("AI agents like those by ChatGPT and Claude now visit websites on behalf of people. Check how easily agents can find, read and take action on your site.", "elementor"))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "contained",
			size: "large",
			onClick: onStart,
			disabled: isCoolingDown
		}, isCoolingDown ? nextScanLabel : (0, _wordpress_i18n.__)("Check my site", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			spacing: 2,
			width: "100%",
			alignItems: "stretch"
		}, getFeatureCards().map(function(card) {
			return /*#__PURE__*/ react.default.createElement(FeatureCard, {
				key: card.title,
				title: card.title,
				body: card.body
			});
		}))), /*#__PURE__*/ react.default.createElement(ScannerPreview, {
			agentAccessUrl,
			seoPluginSearchUrl,
			siteDomain
		}), /*#__PURE__*/ react.default.createElement(FloatingCheckBar, {
			retryAt,
			onCheck: onStart
		}));
	};
	FirstRunView.propTypes = {
		agentAccessUrl: import_prop_types.default.string.isRequired,
		seoPluginSearchUrl: import_prop_types.default.string.isRequired,
		siteDomain: import_prop_types.default.string.isRequired,
		onStart: import_prop_types.default.func.isRequired,
		retryAt: import_prop_types.default.string
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/how-to-fix-dialog.js
	var DIALOG_Z_INDEX = 1e5;
	var DIALOG_WIDTH = 647;
	var DIALOG_RADIUS = 16;
	var PROMPT_RADIUS = 12;
	var STEP_BADGE_SIZE = 24;
	var PROMPT_MIN_HEIGHT = 120;
	var PROMPT_MAX_HEIGHT = 280;
	var PROMPT_LINE_HEIGHT = "20px";
	var COPIED_FEEDBACK_MS = 2e3;
	var OVERLINE_TOP_SPACE = 24;
	var getPromptChecks = function getPromptChecks(checks) {
		return checks.filter(isPromptBasedFix);
	};
	var StepBadge = function StepBadge(_ref) {
		var number = _ref.number;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			width: STEP_BADGE_SIZE,
			height: STEP_BADGE_SIZE,
			borderRadius: "50%",
			bgcolor: SCANNER_COLORS.stepBadge,
			display: "flex",
			alignItems: "center",
			justifyContent: "center",
			flexShrink: 0
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "caption",
			color: "text.secondary"
		}, number));
	};
	StepBadge.propTypes = { number: import_prop_types.default.number.isRequired };
	var CopyPromptAction = function CopyPromptAction(_ref2) {
		var prompt = _ref2.prompt;
		var _useState2 = _slicedToArray((0, react.useState)(false), 2);
		var isCopied = _useState2[0];
		var setIsCopied = _useState2[1];
		var resetTimer = (0, react.useRef)(null);
		(0, react.useEffect)(function() {
			return function() {
				return clearTimeout(resetTimer.current);
			};
		}, []);
		var handleCopy = /*#__PURE__*/ function() {
			var _ref3 = _asyncToGenerator$1(/*#__PURE__*/ import_regenerator.default.mark(function _callee() {
				var copied;
				return import_regenerator.default.wrap(function(_context) {
					while (1) switch (_context.prev = _context.next) {
						case 0:
							_context.next = 1;
							return copyPromptText(prompt);
						case 1:
							copied = _context.sent;
							if (copied) {
								_context.next = 2;
								break;
							}
							return _context.abrupt("return");
						case 2:
							setIsCopied(true);
							clearTimeout(resetTimer.current);
							resetTimer.current = setTimeout(function() {
								return setIsCopied(false);
							}, COPIED_FEEDBACK_MS);
						case 3:
						case "end": return _context.stop();
					}
				}, _callee);
			}));
			return function handleCopy() {
				return _ref3.apply(this, arguments);
			};
		}();
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "info",
			size: "small",
			startIcon: /*#__PURE__*/ react.default.createElement(_elementor_icons.CopyIcon, { fontSize: "tiny" }),
			onClick: handleCopy
		}, isCopied ? (0, _wordpress_i18n.__)("Copied", "elementor") : (0, _wordpress_i18n.__)("Copy prompt", "elementor"));
	};
	CopyPromptAction.propTypes = { prompt: import_prop_types.default.string.isRequired };
	var HowToFixDialog = function HowToFixDialog(_ref4) {
		var checks = _ref4.checks;
		var checkId = _ref4.checkId;
		var onClose = _ref4.onClose;
		var onShowCheck = _ref4.onShowCheck;
		var promptChecks = getPromptChecks(checks);
		var index = promptChecks.findIndex(function(check) {
			return check.id === checkId;
		});
		var check = promptChecks[index];
		if (!check) return null;
		var isLast = index === promptChecks.length - 1;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Dialog.default, {
			open: true,
			onClose,
			sx: { zIndex: DIALOG_Z_INDEX },
			PaperProps: { sx: {
				width: DIALOG_WIDTH,
				maxWidth: "calc(100% - 32px)",
				m: 0,
				borderRadius: "".concat(DIALOG_RADIUS, "px")
			} }
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogHeader.default, {
			onClose,
			logo: false
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 0,
			minWidth: 0
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "overline",
			color: "text.tertiary",
			mt: "".concat(OVERLINE_TOP_SPACE, "px")
		}, (0, _wordpress_i18n.__)("How to fix", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogTitle.default, {
			component: "h2",
			sx: { typography: "h5" }
		}, check.title))), /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogContent.default, null, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 2 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: 1 }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			spacing: 1,
			alignItems: "center"
		}, /*#__PURE__*/ react.default.createElement(StepBadge, { number: 1 }), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.primary"
		}, (0, _wordpress_i18n.__)("Copy the prompt", "elementor"))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 1,
			bgcolor: SCANNER_COLORS.promptBackground,
			borderRadius: "".concat(PROMPT_RADIUS, "px"),
			px: 2,
			pt: 2,
			pb: 1,
			minHeight: PROMPT_MIN_HEIGHT,
			justifyContent: "flex-end"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.tertiary",
			sx: {
				fontFamily: "\"Roboto Mono\", monospace",
				fontSize: 12,
				lineHeight: PROMPT_LINE_HEIGHT,
				letterSpacing: "0.15px",
				whiteSpace: "pre-wrap",
				maxHeight: PROMPT_MAX_HEIGHT,
				overflowY: "auto"
			}
		}, check.fixPrompt), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			justifyContent: "flex-end"
		}, /*#__PURE__*/ react.default.createElement(CopyPromptAction, {
			key: check.id,
			prompt: check.fixPrompt
		})))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			spacing: 1,
			alignItems: "flex-start"
		}, /*#__PURE__*/ react.default.createElement(StepBadge, { number: 2 }), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.primary"
		}, (0, _wordpress_i18n.__)("Paste it into your AI assistant. If it's connected to your site, it can make the fix. If not, it will walk you through it.", "elementor"))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			spacing: 1,
			alignItems: "center"
		}, /*#__PURE__*/ react.default.createElement(StepBadge, { number: 3 }), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.primary"
		}, (0, _wordpress_i18n.__)("Scan again when you're done", "elementor"))))), /*#__PURE__*/ react.default.createElement(_elementor_ui_DialogActions.default, { sx: {
			px: 3,
			py: 2,
			justifyContent: "space-between",
			borderTop: 1,
			borderColor: "divider"
		} }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "secondary",
			onClick: onClose
		}, (0, _wordpress_i18n.__)("Cancel", "elementor")), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			spacing: 2,
			alignItems: "center"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			variant: "body2",
			color: "text.disabled"
		}, (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("%1$d of %2$d", "elementor"), index + 1, promptChecks.length)), /*#__PURE__*/ react.default.createElement(_elementor_ui_Button.default, {
			variant: "text",
			color: "inherit",
			endIcon: /*#__PURE__*/ react.default.createElement(_elementor_icons.ArrowRightIcon, { fontSize: "tiny" }),
			disabled: isLast,
			sx: {
				color: "text.primary",
				"&.Mui-disabled": { color: "text.disabled" }
			},
			onClick: function onClick() {
				var nextCheck = promptChecks[index + 1];
				if (nextCheck) onShowCheck(nextCheck.id);
			}
		}, (0, _wordpress_i18n.__)("Next issue", "elementor")))));
	};
	HowToFixDialog.propTypes = {
		checks: import_prop_types.default.arrayOf(import_prop_types.default.object).isRequired,
		checkId: import_prop_types.default.string,
		onClose: import_prop_types.default.func.isRequired,
		onShowCheck: import_prop_types.default.func.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/dotted-background.js
	var hashCell = function hashCell(column, row) {
		var value = Math.sin(column * DOT_FIELD.hashColumn + row * DOT_FIELD.hashRow) * DOT_FIELD.hashScale;
		return value - Math.floor(value);
	};
	var opacityFor = function opacityFor(column, row) {
		var biased = Math.pow(hashCell(column, row), DOT_FIELD.opacityBias);
		var bucket = Math.min(DOT_FIELD.opacitySteps - 1, Math.floor(biased * DOT_FIELD.opacitySteps));
		return DOT_FIELD.opacityMin + bucket * DOT_FIELD.opacityStep;
	};
	var resolveGridSpacing = function resolveGridSpacing(width, height) {
		var baseGrid = DOT_FIELD.grid;
		var dotCount = Math.ceil(width / baseGrid) * Math.ceil(height / baseGrid);
		if (dotCount <= DOT_FIELD.maxDots) return baseGrid;
		return baseGrid * Math.sqrt(dotCount / DOT_FIELD.maxDots);
	};
	var buildDots = function buildDots(width, height) {
		var gridSpacing = resolveGridSpacing(width, height);
		var columns = Math.ceil(width / gridSpacing);
		var rows = Math.ceil(height / gridSpacing);
		var dots = [];
		for (var row = 0; row < rows; row++) for (var column = 0; column < columns; column++) {
			var x = column * gridSpacing;
			var y = row * gridSpacing;
			var hashColumn = Math.floor(x / DOT_FIELD.grid);
			var hashRow = Math.floor(y / DOT_FIELD.grid);
			dots.push({
				x,
				y,
				base: opacityFor(hashColumn, hashRow),
				phase: hashColumn * DOT_FIELD.driftScaleX + hashRow * DOT_FIELD.driftScaleY,
				phaseAlt: hashRow * DOT_FIELD.driftScaleY - hashColumn * DOT_FIELD.driftScaleX
			});
		}
		return dots;
	};
	var shimmerOpacity = function shimmerOpacity(dot, cycle) {
		var wave = (Math.sin(cycle + dot.phase) + Math.sin(cycle * DOT_FIELD.secondaryDrift + dot.phaseAlt)) / 2;
		var lift = DOT_FIELD.opacityFloor + (1 - DOT_FIELD.opacityFloor) * ((wave + 1) / 2);
		return dot.base * lift;
	};
	var DottedBackground = function DottedBackground() {
		var canvasRef = (0, react.useRef)(null);
		(0, react.useEffect)(function() {
			var _canvas$getContext;
			var canvas = canvasRef.current;
			var context = canvas === null || canvas === void 0 || (_canvas$getContext = canvas.getContext) === null || _canvas$getContext === void 0 ? void 0 : _canvas$getContext.call(canvas, "2d");
			if (!canvas || !context) return;
			var motionQuery = window.matchMedia(REDUCED_MOTION_MEDIA);
			var dots = [];
			var width = 0;
			var height = 0;
			var frameId = 0;
			var rebuild = function rebuild() {
				var nextWidth = canvas.clientWidth;
				var nextHeight = canvas.clientHeight;
				if (!nextWidth || !nextHeight || nextWidth === width && nextHeight === height) return;
				width = nextWidth;
				height = nextHeight;
				var ratio = Math.min(window.devicePixelRatio || 1, DOT_FIELD.maxDevicePixelRatio);
				canvas.width = Math.floor(width * ratio);
				canvas.height = Math.floor(height * ratio);
				context.setTransform(ratio, 0, 0, ratio, 0, 0);
				dots = buildDots(width, height);
			};
			var paint = function paint(elapsedMs) {
				rebuild();
				context.clearRect(0, 0, width, height);
				var reduced = motionQuery.matches;
				var cycle = reduced ? 0 : elapsedMs / SCANNER_MOTION.dotShimmerMs * Math.PI * 2;
				dots.forEach(function(dot) {
					var opacity = reduced ? dot.base : shimmerOpacity(dot, cycle);
					context.fillStyle = "rgba(".concat(DOT_FIELD.rgb, ", ").concat(opacity, ")");
					context.fillRect(dot.x, dot.y, DOT_FIELD.size, DOT_FIELD.size);
				});
			};
			var _loop = function loop(now) {
				paint(now);
				frameId = window.requestAnimationFrame(_loop);
			};
			var start = function start() {
				window.cancelAnimationFrame(frameId);
				if (motionQuery.matches) {
					paint(0);
					return;
				}
				frameId = window.requestAnimationFrame(_loop);
			};
			var observer = "undefined" === typeof ResizeObserver ? null : new ResizeObserver(function() {
				if (width === canvas.clientWidth && height === canvas.clientHeight) return;
				start();
			});
			observer === null || observer === void 0 || observer.observe(canvas);
			motionQuery.addEventListener("change", start);
			start();
			return function() {
				window.cancelAnimationFrame(frameId);
				observer === null || observer === void 0 || observer.disconnect();
				motionQuery.removeEventListener("change", start);
			};
		}, []);
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			component: "canvas",
			ref: canvasRef,
			"aria-hidden": "true",
			sx: {
				position: "absolute",
				inset: 0,
				width: "100%",
				height: "100%",
				pointerEvents: "none"
			}
		});
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/progress-view.js
	var STEP_ICON_SIZE = 16;
	var PENDING_DOT_SIZE = 6;
	var SECTION_GAP = 4;
	var CARD_ROW_GAP = 1;
	var ICON_LABEL_GAP = .5;
	var getStepStatus = function getStepStatus(index, completedStepsCount, isFailed) {
		if (isFailed) return 0 === index ? PROGRESS_STEP_FAILED : PROGRESS_STEP_DONE;
		if (index < completedStepsCount) return PROGRESS_STEP_DONE;
		return index === completedStepsCount ? PROGRESS_STEP_ACTIVE : PROGRESS_STEP_PENDING;
	};
	var StepIcon = function StepIcon(_ref) {
		var status = _ref.status;
		if ("done" === status) return /*#__PURE__*/ react.default.createElement(_elementor_icons.CircleCheckFilledIcon, {
			color: "success",
			sx: { fontSize: STEP_ICON_SIZE }
		});
		if ("failed" === status) return /*#__PURE__*/ react.default.createElement(_elementor_icons.CircleXFilledIcon, {
			color: "error",
			sx: { fontSize: STEP_ICON_SIZE }
		});
		if ("active" === status) return /*#__PURE__*/ react.default.createElement(_elementor_ui_CircularProgress.default, {
			size: STEP_ICON_SIZE,
			thickness: 5,
			sx: { color: "text.primary" }
		});
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			width: STEP_ICON_SIZE,
			display: "flex",
			alignItems: "center",
			justifyContent: "center"
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			width: PENDING_DOT_SIZE,
			height: PENDING_DOT_SIZE,
			borderRadius: "50%",
			bgcolor: "text.tertiary"
		}));
	};
	StepIcon.propTypes = { status: import_prop_types.default.oneOf([
		PROGRESS_STEP_ACTIVE,
		PROGRESS_STEP_DONE,
		PROGRESS_STEP_FAILED,
		PROGRESS_STEP_PENDING
	]).isRequired };
	var ProgressView = function ProgressView(_ref2) {
		var _activeStep$label;
		var siteDomain = _ref2.siteDomain;
		var steps = _ref2.steps;
		var completedStepsCount = _ref2.completedStepsCount;
		var isFailed = _ref2.isFailed;
		var visibleSteps = isFailed ? steps.filter(function(step) {
			return ERROR_HIDDEN_STEP_ID !== step.id;
		}) : steps;
		var activeStep = steps[completedStepsCount];
		var liveMessage = isFailed ? (0, _wordpress_i18n.__)("We couldn't reach your site from outside.", "elementor") : (_activeStep$label = activeStep === null || activeStep === void 0 ? void 0 : activeStep.label) !== null && _activeStep$label !== void 0 ? _activeStep$label : (0, _wordpress_i18n.__)("Scan complete.", "elementor");
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			position: "relative",
			display: "flex",
			alignItems: "center",
			justifyContent: "center",
			width: "100%",
			flex: 1,
			minHeight: "100%"
		}, /*#__PURE__*/ react.default.createElement(DottedBackground, null), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			alignItems: "center",
			spacing: SECTION_GAP,
			position: "relative",
			zIndex: 1,
			width: "100%",
			px: 2
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { alignItems: "center" }, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			component: "p",
			m: 0,
			color: "text.primary",
			sx: {
				fontSize: SCANNER_LAYOUT.checkingFontSize,
				lineHeight: SCANNER_LAYOUT.checkingLineHeight,
				letterSpacing: SCANNER_LAYOUT.checkingLetterSpacing
			}
		}, (0, _wordpress_i18n.sprintf)((0, _wordpress_i18n.__)("Checking %s", "elementor"), siteDomain))), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			component: "ul",
			spacing: CARD_ROW_GAP,
			width: SCANNER_LAYOUT.progressCardWidth,
			maxWidth: "100%",
			m: 0,
			p: 2,
			bgcolor: SCANNER_COLORS.paper,
			border: 1,
			borderColor: "divider",
			borderRadius: SCANNER_LAYOUT.cardRadius,
			sx: { listStyle: "none" }
		}, visibleSteps.map(function(step, index) {
			var status = getStepStatus(index, completedStepsCount, isFailed);
			return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
				key: step.id,
				component: "li",
				direction: "row",
				alignItems: "center",
				spacing: ICON_LABEL_GAP,
				m: 0
			}, /*#__PURE__*/ react.default.createElement(StepIcon, { status }), /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
				variant: "body2",
				color: "pending" === status ? "text.tertiary" : "text.secondary"
			}, step.label));
		})), /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			role: "progressbar",
			"aria-label": (0, _wordpress_i18n.__)("Scan progress", "elementor"),
			"aria-valuemin": 0,
			"aria-valuemax": steps.length,
			"aria-valuenow": Math.min(completedStepsCount, steps.length),
			sx: VISUALLY_HIDDEN_SX
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			"aria-live": "polite",
			sx: VISUALLY_HIDDEN_SX
		}, liveMessage)));
	};
	ProgressView.propTypes = {
		siteDomain: import_prop_types.default.string.isRequired,
		steps: import_prop_types.default.arrayOf(import_prop_types.default.shape({
			id: import_prop_types.default.string.isRequired,
			label: import_prop_types.default.string.isRequired
		})).isRequired,
		completedStepsCount: import_prop_types.default.number.isRequired,
		isFailed: import_prop_types.default.bool
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/components/rescan-skeleton-view.js
	var FILTER_ALL = "all";
	var SCORE_GAP = "32px";
	var noop = function noop() {};
	var BlockSkeleton = function BlockSkeleton(_ref) {
		var width = _ref.width;
		var height = _ref.height;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Skeleton.default, {
			variant: "rounded",
			width,
			height,
			animation: "wave",
			sx: { bgcolor: "action.hover" }
		});
	};
	BlockSkeleton.propTypes = {
		width: import_prop_types.default.oneOfType([import_prop_types.default.number, import_prop_types.default.string]),
		height: import_prop_types.default.number.isRequired
	};
	var RingSkeleton = function RingSkeleton() {
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Skeleton.default, {
			variant: "circular",
			width: SCANNER_LAYOUT.ringSize,
			height: SCANNER_LAYOUT.ringSize,
			animation: "wave",
			sx: {
				bgcolor: "action.hover",
				flexShrink: 0
			}
		});
	};
	var RescanSkeletonView = function RescanSkeletonView(_ref2) {
		var showBanner = _ref2.showBanner;
		var agentAccessUrl = _ref2.agentAccessUrl;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			gap: "".concat(SCANNER_LAYOUT.pageGap, "px"),
			width: "100%",
			"aria-busy": "true",
			"aria-live": "polite",
			role: "status",
			"aria-label": (0, _wordpress_i18n.__)("Scanning again", "elementor")
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			alignItems: "flex-start",
			gap: SCORE_GAP
		}, /*#__PURE__*/ react.default.createElement(RingSkeleton, null), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 2,
			flex: 1,
			minWidth: 0,
			pt: .5
		}, /*#__PURE__*/ react.default.createElement(BlockSkeleton, {
			width: SCANNER_LAYOUT.skeletonHeadingWidth,
			height: SCANNER_LAYOUT.skeletonHeadingHeight
		}), /*#__PURE__*/ react.default.createElement(BlockSkeleton, {
			width: "100%",
			height: SCANNER_LAYOUT.skeletonBlockHeight
		}), /*#__PURE__*/ react.default.createElement(BlockSkeleton, {
			width: SCANNER_LAYOUT.skeletonMetaWidth,
			height: SCANNER_LAYOUT.skeletonBlockHeight
		}))), showBanner && /*#__PURE__*/ react.default.createElement(ActivateBanner, {
			isActivating: true,
			onActivate: noop,
			agentAccessUrl
		}), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			direction: "row",
			gap: 2
		}, Array.from({ length: SCANNER_LAYOUT.skeletonRowCount }, function(_, index) {
			return /*#__PURE__*/ react.default.createElement(_elementor_ui_Skeleton.default, {
				key: index,
				variant: "rounded",
				animation: "wave",
				height: SCANNER_LAYOUT.skeletonStageHeight,
				sx: {
					flex: 1,
					borderRadius: SCANNER_LAYOUT.cardRadius,
					bgcolor: "action.hover"
				}
			});
		})), /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			spacing: 2,
			pt: 2
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, { spacing: .5 }, /*#__PURE__*/ react.default.createElement(BlockSkeleton, {
			width: SCANNER_LAYOUT.skeletonSectionHeadingWidth,
			height: SCANNER_LAYOUT.skeletonBlockHeight
		}), /*#__PURE__*/ react.default.createElement(BlockSkeleton, {
			width: "100%",
			height: SCANNER_LAYOUT.skeletonBlockHeight
		})), /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, { sx: { pointerEvents: "none" } }, /*#__PURE__*/ react.default.createElement(FixesFilters, {
			priority: FILTER_ALL,
			stage: FILTER_ALL,
			onPriorityChange: noop,
			onStageChange: noop,
			isDisabled: true
		})), Array.from({ length: SCANNER_LAYOUT.skeletonRowCount }, function(_, index) {
			return /*#__PURE__*/ react.default.createElement(_elementor_ui_Skeleton.default, {
				key: index,
				variant: "rounded",
				animation: "wave",
				height: SCANNER_LAYOUT.skeletonRowHeight,
				sx: {
					borderRadius: SCANNER_LAYOUT.cardRadius,
					bgcolor: "action.hover"
				}
			});
		})));
	};
	RescanSkeletonView.propTypes = {
		showBanner: import_prop_types.default.bool,
		agentAccessUrl: import_prop_types.default.string.isRequired
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/use-scanner-flow.js
	function ownKeys(e, r) {
		var t = Object.keys(e);
		if (Object.getOwnPropertySymbols) {
			var o = Object.getOwnPropertySymbols(e);
			r && (o = o.filter(function(r) {
				return Object.getOwnPropertyDescriptor(e, r).enumerable;
			})), t.push.apply(t, o);
		}
		return t;
	}
	function _objectSpread(e) {
		for (var r = 1; r < arguments.length; r++) {
			var t = null != arguments[r] ? arguments[r] : {};
			r % 2 ? ownKeys(Object(t), !0).forEach(function(r) {
				_defineProperty$1(e, r, t[r]);
			}) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function(r) {
				Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
			});
		}
		return e;
	}
	function _toPropertyKey(t) {
		var i = _toPrimitive(t, "string");
		return "symbol" == _typeof$1(i) ? i : i + "";
	}
	function _toPrimitive(t, r) {
		if ("object" != _typeof$1(t) || !t) return t;
		var e = t[Symbol.toPrimitive];
		if (void 0 !== e) {
			var i = e.call(t, r || "default");
			if ("object" != _typeof$1(i)) return i;
			throw new TypeError("@@toPrimitive must return a primitive value.");
		}
		return ("string" === r ? String : Number)(t);
	}
	var MS_PER_SECOND = 1e3;
	var NOT_ACTIVATED_REASON = (0, _wordpress_i18n.__)("Activate Agent Ready to turn this on.", "elementor");
	var wait = function wait(delayMs) {
		return new Promise(function(resolve) {
			return setTimeout(resolve, delayMs);
		});
	};
	var getFallbackRetryAt = function getFallbackRetryAt() {
		return new Date(Date.now() + 300 * MS_PER_SECOND).toISOString();
	};
	var getThrottledRetryAt = function getThrottledRetryAt(error) {
		return (error === null || error === void 0 ? void 0 : error.retryAt) || getFallbackRetryAt();
	};
	var withoutKey = function withoutKey(object, key) {
		object[key];
		return _objectWithoutProperties$1(object, [key].map(_toPropertyKey));
	};
	var getReportView = function getReportView(isActivated) {
		return isActivated ? SCANNER_STATE_FIXES : SCANNER_STATE_FIRST_CHECK;
	};
	var isThrottled = function isThrottled(error) {
		return 429 === (error === null || error === void 0 ? void 0 : error.status);
	};
	var isRetryAhead = function isRetryAhead(retryAt) {
		var retryTimestamp = retryAt ? Date.parse(retryAt) : NaN;
		return !Number.isNaN(retryTimestamp) && retryTimestamp > Date.now();
	};
	var withFailedOutsideScan = function withFailedOutsideScan(scan, reason) {
		return _objectSpread(_objectSpread({}, scan !== null && scan !== void 0 ? scan : toFailedScanResult()), {}, { outsideScan: {
			status: OUTSIDE_SCAN_STATUS_FAILED,
			reason
		} });
	};
	var withModuleFlag = function withModuleFlag(moduleEnabled, moduleKey, isEnabled) {
		return _objectSpread(_objectSpread({}, moduleEnabled), {}, _defineProperty$1({}, moduleKey, isEnabled));
	};
	var withDirtyModuleKey = function withDirtyModuleKey(dirtyModuleKeys, moduleKey, isEnabled, initialModuleEnabled) {
		return Boolean(initialModuleEnabled[moduleKey]) === isEnabled ? withoutKey(dirtyModuleKeys, moduleKey) : _objectSpread(_objectSpread({}, dirtyModuleKeys), {}, _defineProperty$1({}, moduleKey, true));
	};
	var useScannerFlow = function useScannerFlow() {
		var _ref = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : {};
		var _ref$initialScan = _ref.initialScan;
		var initialScan = _ref$initialScan === void 0 ? null : _ref$initialScan;
		var _ref$initialRetryAt = _ref.initialRetryAt;
		var initialRetryAt = _ref$initialRetryAt === void 0 ? null : _ref$initialRetryAt;
		var _ref$isActivated = _ref.isActivated;
		var initialIsActivated = _ref$isActivated === void 0 ? false : _ref$isActivated;
		var _ref$isAngieActive = _ref.isAngieActive;
		var isAngieActive = _ref$isAngieActive === void 0 ? false : _ref$isAngieActive;
		var llmsConfig = _ref.llmsConfig;
		var markdownConfig = _ref.markdownConfig;
		var botAccessConfig = _ref.botAccessConfig;
		var agentDiscoveryConfig = _ref.agentDiscoveryConfig;
		var browserToolsConfig = _ref.browserToolsConfig;
		var moduleConfigs = (0, react.useMemo)(function() {
			return {
				llmsConfig,
				markdownConfig,
				botAccessConfig,
				agentDiscoveryConfig,
				browserToolsConfig
			};
		}, [
			llmsConfig,
			markdownConfig,
			botAccessConfig,
			agentDiscoveryConfig,
			browserToolsConfig
		]);
		var progressSteps = (0, react.useMemo)(getProgressSteps, []);
		var isModuleToggleDisabled = (0, react.useCallback)(function(moduleKey) {
			return isAgentReadyModuleToggleDisabled(moduleKey, moduleConfigs);
		}, [moduleConfigs]);
		var isMountedRef = (0, react.useRef)(true);
		var initialModuleEnabledRef = (0, react.useRef)(getInitialModuleEnabled({
			llmsConfig,
			markdownConfig,
			botAccessConfig,
			agentDiscoveryConfig,
			browserToolsConfig
		}));
		var moduleEnabledRef = (0, react.useRef)(initialModuleEnabledRef.current);
		var _useState2 = _slicedToArray((0, react.useState)(initialScan ? getReportView(initialIsActivated) : SCANNER_STATE_FIRST_RUN), 2);
		var view = _useState2[0];
		var setView = _useState2[1];
		var _useState4 = _slicedToArray((0, react.useState)(0), 2);
		var completedStepsCount = _useState4[0];
		var setCompletedStepsCount = _useState4[1];
		var _useState6 = _slicedToArray((0, react.useState)(initialScan), 2);
		var scan = _useState6[0];
		var setScan = _useState6[1];
		var _useState8 = _slicedToArray((0, react.useState)(initialRetryAt), 2);
		var cooldownRetryAt = _useState8[0];
		var setCooldownRetryAt = _useState8[1];
		var _useState0 = _slicedToArray((0, react.useState)(initialIsActivated), 2);
		var isActivated = _useState0[0];
		var setIsActivated = _useState0[1];
		var _useState10 = _slicedToArray((0, react.useState)(SCANNER_MOTION.ringArriveMs), 2);
		var ringDurationMs = _useState10[0];
		var setRingDurationMs = _useState10[1];
		var _useState12 = _slicedToArray((0, react.useState)({}), 2);
		var resolvedIds = _useState12[0];
		var setResolvedIds = _useState12[1];
		var _useState14 = _slicedToArray((0, react.useState)(initialModuleEnabledRef.current), 2);
		var moduleEnabled = _useState14[0];
		var setModuleEnabled = _useState14[1];
		var _useState16 = _slicedToArray((0, react.useState)({}), 2);
		var dirtyModuleKeys = _useState16[0];
		var setDirtyModuleKeys = _useState16[1];
		var _useState18 = _slicedToArray((0, react.useState)({}), 2);
		var savingModuleKeys = _useState18[0];
		var setSavingModuleKeys = _useState18[1];
		var _useState20 = _slicedToArray((0, react.useState)({}), 2);
		var openIds = _useState20[0];
		var setOpenIds = _useState20[1];
		var _useState22 = _slicedToArray((0, react.useState)({}), 2);
		var preferences = _useState22[0];
		var setPreferences = _useState22[1];
		var _useState24 = _slicedToArray((0, react.useState)(false), 2);
		var areChecksExpanded = _useState24[0];
		var setAreChecksExpanded = _useState24[1];
		var _useState26 = _slicedToArray((0, react.useState)(""), 2);
		var actionError = _useState26[0];
		var setActionError = _useState26[1];
		var _useState28 = _slicedToArray((0, react.useState)(null), 2);
		var installCheck = _useState28[0];
		var setInstallCheck = _useState28[1];
		var _useState30 = _slicedToArray((0, react.useState)(null), 2);
		var howToFixId = _useState30[0];
		var setHowToFixId = _useState30[1];
		var _useState32 = _slicedToArray((0, react.useState)({}), 2);
		var savingCheckIds = _useState32[0];
		var setSavingCheckIds = _useState32[1];
		(0, react.useEffect)(function() {
			isMountedRef.current = true;
			return function() {
				isMountedRef.current = false;
			};
		}, []);
		var playProgressStepsUntilScanGate = (0, react.useCallback)(/*#__PURE__*/ _asyncToGenerator$1(/*#__PURE__*/ import_regenerator.default.mark(function _callee() {
			var gateAt;
			var progressStepDelaysMs;
			var step;
			return import_regenerator.default.wrap(function(_context) {
				while (1) switch (_context.prev = _context.next) {
					case 0:
						gateAt = progressSteps.length - 1;
						progressStepDelaysMs = getProgressStepDelaysMs();
						step = 1;
					case 1:
						if (!(step <= gateAt)) {
							_context.next = 5;
							break;
						}
						_context.next = 2;
						return wait(progressStepDelaysMs[step - 1]);
					case 2:
						if (isMountedRef.current) {
							_context.next = 3;
							break;
						}
						return _context.abrupt("return");
					case 3: setCompletedStepsCount(step);
					case 4:
						step++;
						_context.next = 1;
						break;
					case 5:
					case "end": return _context.stop();
				}
			}, _callee);
		})), [progressSteps.length]);
		var showScanFailure = (0, react.useCallback)(/*#__PURE__*/ _asyncToGenerator$1(/*#__PURE__*/ import_regenerator.default.mark(function _callee2() {
			return import_regenerator.default.wrap(function(_context2) {
				while (1) switch (_context2.prev = _context2.next) {
					case 0:
						setView(SCANNER_STATE_ERROR_PROGRESS);
						_context2.next = 1;
						return wait(SCANNER_MOTION.errorProgressPauseMs);
					case 1: if (isMountedRef.current) setView(SCANNER_STATE_ERROR_REPORT);
					case 2:
					case "end": return _context2.stop();
				}
			}, _callee2);
		})), []);
		var retryAt = getLaterRetryAt(scan === null || scan === void 0 ? void 0 : scan.retryAt, cooldownRetryAt);
		var startScan = (0, react.useCallback)(/*#__PURE__*/ _asyncToGenerator$1(/*#__PURE__*/ import_regenerator.default.mark(function _callee3() {
			var _result$outsideScan;
			var isRescan;
			var scanError;
			var scanPromise;
			var _yield$Promise$all;
			var _yield$Promise$all2;
			var result;
			var throttledRetryAt;
			var _scan$outsideScan;
			var hasFailedOutsideScan;
			var _scanError$reason;
			var _scanError;
			return import_regenerator.default.wrap(function(_context3) {
				while (1) switch (_context3.prev = _context3.next) {
					case 0:
						if (!isRetryAhead(retryAt)) {
							_context3.next = 1;
							break;
						}
						return _context3.abrupt("return");
					case 1:
						setActionError("");
						isRescan = Boolean(scan);
						if (isRescan) setView(SCANNER_STATE_RESCANNING);
						else {
							setCompletedStepsCount(0);
							setView(SCANNER_STATE_PROGRESS);
						}
						scanError = null;
						scanPromise = runSiteScan().catch(function(error) {
							scanError = error;
							return null;
						});
						_context3.next = 2;
						return Promise.all([scanPromise, isRescan ? wait(SCANNER_MOTION.rescanMinMs) : playProgressStepsUntilScanGate()]);
					case 2:
						_yield$Promise$all = _context3.sent;
						_yield$Promise$all2 = _slicedToArray(_yield$Promise$all, 1);
						result = _yield$Promise$all2[0];
						if (isMountedRef.current) {
							_context3.next = 3;
							break;
						}
						return _context3.abrupt("return");
					case 3:
						if (!isThrottled(scanError)) {
							_context3.next = 5;
							break;
						}
						throttledRetryAt = getThrottledRetryAt(scanError);
						setCooldownRetryAt(throttledRetryAt);
						if (!scan) {
							_context3.next = 4;
							break;
						}
						hasFailedOutsideScan = OUTSIDE_SCAN_STATUS_FAILED === ((_scan$outsideScan = scan.outsideScan) === null || _scan$outsideScan === void 0 ? void 0 : _scan$outsideScan.status);
						setScan(_objectSpread(_objectSpread({}, scan), {}, { retryAt: throttledRetryAt }));
						setView(hasFailedOutsideScan ? SCANNER_STATE_ERROR_REPORT : getReportView(isActivated));
						return _context3.abrupt("return");
					case 4:
						setView(SCANNER_STATE_FIRST_RUN);
						return _context3.abrupt("return");
					case 5:
						if (!(!result || "failed" === ((_result$outsideScan = result.outsideScan) === null || _result$outsideScan === void 0 ? void 0 : _result$outsideScan.status))) {
							_context3.next = 8;
							break;
						}
						setScan(result !== null && result !== void 0 ? result : withFailedOutsideScan(scan, (_scanError$reason = (_scanError = scanError) === null || _scanError === void 0 ? void 0 : _scanError.reason) !== null && _scanError$reason !== void 0 ? _scanError$reason : null));
						if (!isRescan) {
							_context3.next = 6;
							break;
						}
						setView(SCANNER_STATE_ERROR_REPORT);
						return _context3.abrupt("return");
					case 6:
						_context3.next = 7;
						return showScanFailure();
					case 7: return _context3.abrupt("return");
					case 8:
						setCompletedStepsCount(progressSteps.length);
						setScan(result);
						setResolvedIds({});
						initialModuleEnabledRef.current = moduleEnabledRef.current;
						setDirtyModuleKeys({});
						setOpenIds({});
						setRingDurationMs(SCANNER_MOTION.ringArriveMs);
						setView(getReportView(isActivated));
					case 9:
					case "end": return _context3.stop();
				}
			}, _callee3);
		})), [
			isActivated,
			playProgressStepsUntilScanGate,
			progressSteps.length,
			retryAt,
			showScanFailure,
			scan
		]);
		var activate = (0, react.useCallback)(/*#__PURE__*/ _asyncToGenerator$1(/*#__PURE__*/ import_regenerator.default.mark(function _callee4() {
			var previousView;
			var nextModuleEnabled;
			var _t;
			return import_regenerator.default.wrap(function(_context4) {
				while (1) switch (_context4.prev = _context4.next) {
					case 0:
						previousView = view;
						setActionError("");
						setRingDurationMs(SCANNER_MOTION.ringActivatingMs);
						setView(SCANNER_STATE_ACTIVATING);
						_context4.prev = 1;
						_context4.next = 2;
						return Promise.all([activateAgentsReady(), wait(SCANNER_MOTION.ringActivatingMs)]);
					case 2:
						_context4.next = 4;
						break;
					case 3:
						_context4.prev = 3;
						_t = _context4["catch"](1);
						if (isMountedRef.current) {
							setActionError(getSaveErrorMessage(_t));
							setView(previousView);
						}
						return _context4.abrupt("return");
					case 4:
						if (isMountedRef.current) {
							_context4.next = 5;
							break;
						}
						return _context4.abrupt("return");
					case 5:
						nextModuleEnabled = getInitialModuleEnabled(moduleConfigs);
						Object.keys(nextModuleEnabled).forEach(function(moduleKey) {
							if (!isModuleToggleDisabled(moduleKey)) nextModuleEnabled[moduleKey] = true;
						});
						moduleEnabledRef.current = nextModuleEnabled;
						setModuleEnabled(nextModuleEnabled);
						setIsActivated(true);
						setOpenIds({});
						setRingDurationMs(SCANNER_MOTION.ringLandMs);
						setView(SCANNER_STATE_FIXES);
					case 6:
					case "end": return _context4.stop();
				}
			}, _callee4, null, [[1, 3]]);
		})), [
			isModuleToggleDisabled,
			moduleConfigs,
			view
		]);
		var toggleRow = (0, react.useCallback)(function(checkId) {
			setOpenIds(function(current) {
				return current[checkId] ? withoutKey(current, checkId) : _objectSpread(_objectSpread({}, current), {}, _defineProperty$1({}, checkId, true));
			});
		}, []);
		var openRow = (0, react.useCallback)(function(checkId) {
			setOpenIds(function(current) {
				return _objectSpread(_objectSpread({}, current), {}, _defineProperty$1({}, checkId, true));
			});
		}, []);
		var markResolved = (0, react.useCallback)(function(check, payload) {
			setActionError("");
			setResolvedIds(function(current) {
				return _objectSpread(_objectSpread({}, current), {}, _defineProperty$1({}, check.id, payload));
			});
			setSavingCheckIds(function(current) {
				return _objectSpread(_objectSpread({}, current), {}, _defineProperty$1({}, check.id, true));
			});
			applyCheckFix(check.id, payload).catch(function(error) {
				if (isMountedRef.current) {
					setResolvedIds(function(current) {
						return withoutKey(current, check.id);
					});
					setActionError(getSaveErrorMessage(error));
				}
			}).finally(function() {
				if (isMountedRef.current) setSavingCheckIds(function(current) {
					return withoutKey(current, check.id);
				});
			});
		}, []);
		var fixWithAngie = (0, react.useCallback)(function(check) {
			if (canOpenAngie(isAngieActive)) {
				openAngieWithPrompt(check.fixPrompt);
				return;
			}
			setInstallCheck(check);
		}, [isAngieActive]);
		var openHowToFix = (0, react.useCallback)(function(check) {
			setInstallCheck(null);
			setHowToFixId(check.id);
		}, []);
		var closeHowToFix = (0, react.useCallback)(function() {
			setHowToFixId(null);
		}, []);
		var showHowToFix = (0, react.useCallback)(function(checkId) {
			setHowToFixId(checkId);
		}, []);
		var closeInstallDialog = (0, react.useCallback)(function() {
			setInstallCheck(null);
		}, []);
		var setModuleFlag = (0, react.useCallback)(function(moduleKey, isEnabled) {
			var nextModuleEnabled = withModuleFlag(moduleEnabledRef.current, moduleKey, isEnabled);
			moduleEnabledRef.current = nextModuleEnabled;
			setModuleEnabled(nextModuleEnabled);
			setDirtyModuleKeys(function(current) {
				return withDirtyModuleKey(current, moduleKey, isEnabled, initialModuleEnabledRef.current);
			});
		}, []);
		var setCheckEnabled = (0, react.useCallback)(function(check, isEnabled) {
			var moduleKey = getToggleModuleKey(check.id);
			if (!moduleKey) return;
			var previousEnabled = Boolean(moduleEnabledRef.current[moduleKey]);
			if (previousEnabled === isEnabled) return;
			setActionError("");
			setModuleFlag(moduleKey, isEnabled);
			setSavingModuleKeys(function(current) {
				return _objectSpread(_objectSpread({}, current), {}, _defineProperty$1({}, moduleKey, true));
			});
			(isEnabled ? applyCheckFix(check.id, { enabled: true }) : undoCheckFix(check.id)).catch(function(error) {
				if (isMountedRef.current) {
					setModuleFlag(moduleKey, previousEnabled);
					setActionError(getSaveErrorMessage(error));
				}
			}).finally(function() {
				if (isMountedRef.current) setSavingModuleKeys(function(current) {
					return withoutKey(current, moduleKey);
				});
			});
		}, [setModuleFlag]);
		var undoFix = (0, react.useCallback)(function(check) {
			if (getToggleModuleKey(check.id)) {
				setCheckEnabled(check, false);
				return;
			}
			var previousPayload = resolvedIds[check.id];
			setActionError("");
			setResolvedIds(function(current) {
				return withoutKey(current, check.id);
			});
			setSavingCheckIds(function(current) {
				return _objectSpread(_objectSpread({}, current), {}, _defineProperty$1({}, check.id, true));
			});
			undoCheckFix(check.id).catch(function(error) {
				if (isMountedRef.current) {
					setResolvedIds(function(current) {
						return _objectSpread(_objectSpread({}, current), {}, _defineProperty$1({}, check.id, previousPayload));
					});
					setActionError(getSaveErrorMessage(error));
				}
			}).finally(function() {
				if (isMountedRef.current) setSavingCheckIds(function(current) {
					return withoutKey(current, check.id);
				});
			});
		}, [resolvedIds, setCheckEnabled]);
		var setPreference = (0, react.useCallback)(function(checkId, value) {
			setPreferences(function(current) {
				return _objectSpread(_objectSpread({}, current), {}, _defineProperty$1({}, checkId, value));
			});
		}, []);
		var savePreference = (0, react.useCallback)(function(check) {
			var _preferences$check$id;
			var value = ((_preferences$check$id = preferences[check.id]) !== null && _preferences$check$id !== void 0 ? _preferences$check$id : "").trim();
			if (!value) return;
			markResolved(check, { value });
			setOpenIds(function(current) {
				return withoutKey(current, check.id);
			});
		}, [preferences, markResolved]);
		var toggleChecksExpanded = (0, react.useCallback)(function() {
			setAreChecksExpanded(function(current) {
				return !current;
			});
		}, []);
		var getToggleDisabledReason = (0, react.useCallback)(function(moduleKey) {
			var unavailableReason = getModuleUnavailableReason(moduleKey, moduleConfigs);
			if (unavailableReason) return unavailableReason.message || null;
			return isActivated ? null : NOT_ACTIVATED_REASON;
		}, [isActivated, moduleConfigs]);
		var isCheckToggleDisabled = (0, react.useCallback)(function(moduleKey) {
			return !isActivated || isModuleToggleDisabled(moduleKey);
		}, [isActivated, isModuleToggleDisabled]);
		var visibleChecks = getVisibleChecks(scan);
		return {
			view,
			progressSteps,
			completedStepsCount,
			scan,
			isActivated,
			visibleChecks,
			ringScore: getBaseScore(scan),
			ringDurationMs,
			needsRescanForScore: hasPendingFixes(visibleChecks, resolvedIds) || hasDirtyModuleChecks(visibleChecks, dirtyModuleKeys),
			resolvedIds,
			moduleEnabled,
			isModuleToggleDisabled: isCheckToggleDisabled,
			isModuleUnavailable: isModuleToggleDisabled,
			getToggleDisabledReason,
			savingModuleKeys,
			openIds,
			preferences,
			areChecksExpanded,
			actionError,
			installCheck,
			howToFixId,
			savingCheckIds,
			retryAt,
			startScan,
			activate,
			toggleRow,
			openRow,
			openHowToFix,
			closeHowToFix,
			showHowToFix,
			closeInstallDialog,
			fixWithAngie,
			undoFix,
			setCheckEnabled,
			setPreference,
			savePreference,
			toggleChecksExpanded
		};
	};

//#endregion
//#region modules/agents/assets/js/admin/scanner/scanner-app.js
	var ScannerShell = function ScannerShell(_ref) {
		var children = _ref.children;
		var hideOverline = _ref.hideOverline;
		var fillViewport = _ref.fillViewport;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, {
			boxSizing: "border-box",
			sx: fillViewport ? {
				backgroundColor: SCANNER_COLORS.paper,
				display: "flex",
				flexDirection: "column",
				width: "calc(100% + ".concat(SCANNER_LAYOUT.wrapInlinePadding * 2, "px)"),
				marginInline: "-".concat(SCANNER_LAYOUT.wrapInlinePadding, "px"),
				marginTop: "-".concat(SCANNER_LAYOUT.wrapBlockStart, "px"),
				minHeight: SCANNER_LAYOUT.progressPaneMinHeight
			} : {
				width: "100%",
				py: 3
			}
		}, /*#__PURE__*/ react.default.createElement(_elementor_ui_Stack.default, {
			gap: hideOverline ? 0 : "".concat(SCANNER_LAYOUT.pageGap, "px"),
			width: "100%",
			flex: fillViewport ? 1 : void 0,
			minHeight: fillViewport ? "100%" : void 0
		}, !hideOverline && /*#__PURE__*/ react.default.createElement(_elementor_ui_Box.default, null, /*#__PURE__*/ react.default.createElement(_elementor_ui_Typography.default, {
			component: "h2",
			m: 0,
			sx: VISUALLY_HIDDEN_SX
		}, (0, _wordpress_i18n.__)("Agent Ready", "elementor")), /*#__PURE__*/ react.default.createElement(BetaChip, null)), children));
	};
	ScannerShell.propTypes = {
		children: import_prop_types.default.node.isRequired,
		hideOverline: import_prop_types.default.bool,
		fillViewport: import_prop_types.default.bool
	};
	var ScannerView = function ScannerView(_ref2) {
		var flow = _ref2.flow;
		var siteDomain = _ref2.siteDomain;
		var agentAccessUrl = _ref2.agentAccessUrl;
		var seoPluginSearchUrl = _ref2.seoPluginSearchUrl;
		var view = flow.view;
		var scan = flow.scan;
		var isActivating = SCANNER_STATE_ACTIVATING === view;
		var renderActivateBanner = function renderActivateBanner() {
			return /*#__PURE__*/ react.default.createElement(ActivateBanner, {
				isActivating,
				onActivate: flow.activate,
				agentAccessUrl
			});
		};
		var renderScoreRow = function renderScoreRow() {
			return /*#__PURE__*/ react.default.createElement(ScoreRow, {
				score: flow.ringScore,
				ringDurationMs: flow.ringDurationMs,
				isDimmed: isActivating,
				isActivated: flow.isActivated,
				isBusy: isActivating,
				needsRescanForScore: flow.needsRescanForScore,
				siteDomain,
				ranAt: scan === null || scan === void 0 ? void 0 : scan.ranAt,
				agentAccessUrl,
				retryAt: scan === null || scan === void 0 ? void 0 : scan.retryAt,
				onRescan: flow.startScan
			});
		};
		var renderReport = function renderReport(_ref3) {
			var _scan$stages;
			var _scan$passingChecks;
			var withBanner = _ref3.withBanner;
			return /*#__PURE__*/ react.default.createElement(react.default.Fragment, null, /*#__PURE__*/ react.default.createElement(ScoreSummaryBlock, { banner: withBanner ? renderActivateBanner() : null }, renderScoreRow()), ((_scan$stages = scan === null || scan === void 0 ? void 0 : scan.stages) !== null && _scan$stages !== void 0 ? _scan$stages : []).length > 0 && /*#__PURE__*/ react.default.createElement(StageBars, { stages: scan.stages }), flow.actionError && /*#__PURE__*/ react.default.createElement(_elementor_ui_Alert.default, { severity: "error" }, flow.actionError), /*#__PURE__*/ react.default.createElement(FixesSection, {
				checks: flow.visibleChecks,
				flow,
				agentAccessUrl,
				seoPluginSearchUrl,
				isActivated: flow.isActivated,
				isLoading: isActivating
			}), /*#__PURE__*/ react.default.createElement(PassingTable, { checks: (_scan$passingChecks = scan === null || scan === void 0 ? void 0 : scan.passingChecks) !== null && _scan$passingChecks !== void 0 ? _scan$passingChecks : [] }));
		};
		if ("first_run" === view) return /*#__PURE__*/ react.default.createElement(FirstRunView, {
			agentAccessUrl,
			seoPluginSearchUrl,
			siteDomain,
			onStart: flow.startScan,
			retryAt: flow.retryAt
		});
		if ("progress" === view || "error_progress" === view) return /*#__PURE__*/ react.default.createElement(ProgressView, {
			siteDomain,
			steps: flow.progressSteps,
			completedStepsCount: flow.completedStepsCount,
			isFailed: SCANNER_STATE_ERROR_PROGRESS === view
		});
		if ("rescanning" === view) return /*#__PURE__*/ react.default.createElement(RescanSkeletonView, {
			showBanner: !flow.isActivated,
			agentAccessUrl
		});
		if ("error_report" === view) {
			var _scan$outsideScan;
			return /*#__PURE__*/ react.default.createElement(ScoreSummaryBlock, { banner: flow.isActivated ? null : renderActivateBanner() }, /*#__PURE__*/ react.default.createElement(ErrorReportCard, {
				siteDomain,
				reason: scan === null || scan === void 0 || (_scan$outsideScan = scan.outsideScan) === null || _scan$outsideScan === void 0 ? void 0 : _scan$outsideScan.reason,
				retryAt: scan === null || scan === void 0 ? void 0 : scan.retryAt,
				onRescan: flow.startScan,
				isActivated: flow.isActivated
			}));
		}
		if ("fixes" === view) return renderReport({ withBanner: false });
		if ("first_check" === view || isActivating) return renderReport({ withBanner: !flow.isActivated });
		return null;
	};
	var FixDialogs = function FixDialogs(_ref4) {
		var flow = _ref4.flow;
		var angieInstallHeroUrl = _ref4.angieInstallHeroUrl;
		var angieAppUrl = _ref4.angieAppUrl;
		var angiePluginSearchUrl = _ref4.angiePluginSearchUrl;
		var isAngieInstalled = _ref4.isAngieInstalled;
		return /*#__PURE__*/ react.default.createElement(react.default.Fragment, null, flow.installCheck && /*#__PURE__*/ react.default.createElement(AngieInstallDialog, {
			check: flow.installCheck,
			isAngieInstalled,
			heroUrl: angieInstallHeroUrl,
			angieAppUrl,
			angiePluginSearchUrl,
			onClose: flow.closeInstallDialog,
			onCopyPrompt: flow.openHowToFix
		}), flow.howToFixId && /*#__PURE__*/ react.default.createElement(HowToFixDialog, {
			checks: flow.visibleChecks,
			checkId: flow.howToFixId,
			onClose: flow.closeHowToFix,
			onShowCheck: flow.showHowToFix
		}));
	};
	FixDialogs.propTypes = {
		flow: import_prop_types.default.object.isRequired,
		angieInstallHeroUrl: import_prop_types.default.string.isRequired,
		angieAppUrl: import_prop_types.default.string.isRequired,
		angiePluginSearchUrl: import_prop_types.default.string.isRequired,
		isAngieInstalled: import_prop_types.default.bool.isRequired
	};
	ScannerView.propTypes = {
		flow: import_prop_types.default.object.isRequired,
		siteDomain: import_prop_types.default.string.isRequired,
		agentAccessUrl: import_prop_types.default.string.isRequired,
		seoPluginSearchUrl: import_prop_types.default.string.isRequired
	};
	var ScannerScreen = function ScannerScreen(_ref5) {
		var siteDomain = _ref5.siteDomain;
		var agentAccessUrl = _ref5.agentAccessUrl;
		var seoPluginSearchUrl = _ref5.seoPluginSearchUrl;
		var angieInstallHeroUrl = _ref5.angieInstallHeroUrl;
		var angieAppUrl = _ref5.angieAppUrl;
		var angiePluginSearchUrl = _ref5.angiePluginSearchUrl;
		var isActivated = _ref5.isActivated;
		var isAngieActive = _ref5.isAngieActive;
		var isAngieInstalled = _ref5.isAngieInstalled;
		var lastReport = _ref5.lastReport;
		var _ref5$retryAt = _ref5.retryAt;
		var retryAt = _ref5$retryAt === void 0 ? null : _ref5$retryAt;
		var llmsConfig = _ref5.llmsConfig;
		var markdownConfig = _ref5.markdownConfig;
		var botAccessConfig = _ref5.botAccessConfig;
		var agentDiscoveryConfig = _ref5.agentDiscoveryConfig;
		var browserToolsConfig = _ref5.browserToolsConfig;
		var flow = useScannerFlow({
			initialScan: (0, react.useMemo)(function() {
				return lastReport ? toScanResult(lastReport, getLaterRetryAt(lastReport.retryAt, retryAt)) : null;
			}, [lastReport, retryAt]),
			initialRetryAt: lastReport ? null : retryAt,
			isActivated,
			isAngieActive,
			llmsConfig,
			markdownConfig,
			botAccessConfig,
			agentDiscoveryConfig,
			browserToolsConfig
		});
		(0, react.useEffect)(function() {
			deliverPendingAngiePrompt();
		}, []);
		var isFirstRun = SCANNER_STATE_FIRST_RUN === flow.view;
		var isScanProgress = "progress" === flow.view || "error_progress" === flow.view;
		return /*#__PURE__*/ react.default.createElement(ScannerShell, {
			hideOverline: isFirstRun || isScanProgress,
			fillViewport: isScanProgress
		}, /*#__PURE__*/ react.default.createElement(ScannerView, {
			flow,
			siteDomain,
			agentAccessUrl,
			seoPluginSearchUrl
		}), /*#__PURE__*/ react.default.createElement(FixDialogs, {
			flow,
			angieInstallHeroUrl,
			angieAppUrl,
			angiePluginSearchUrl,
			isAngieInstalled
		}));
	};
	ScannerScreen.propTypes = {
		siteDomain: import_prop_types.default.string.isRequired,
		agentAccessUrl: import_prop_types.default.string.isRequired,
		seoPluginSearchUrl: import_prop_types.default.string.isRequired,
		angieInstallHeroUrl: import_prop_types.default.string.isRequired,
		angieAppUrl: import_prop_types.default.string.isRequired,
		angiePluginSearchUrl: import_prop_types.default.string.isRequired,
		isActivated: import_prop_types.default.bool,
		isAngieActive: import_prop_types.default.bool,
		isAngieInstalled: import_prop_types.default.bool,
		lastReport: import_prop_types.default.object,
		retryAt: import_prop_types.default.string,
		llmsConfig: import_prop_types.default.object,
		markdownConfig: import_prop_types.default.object,
		botAccessConfig: import_prop_types.default.object,
		agentDiscoveryConfig: import_prop_types.default.object,
		browserToolsConfig: import_prop_types.default.object
	};
	var ScannerApp = function ScannerApp(_ref6) {
		var isRTL = _ref6.isRTL;
		var siteDomain = _ref6.siteDomain;
		var agentAccessUrl = _ref6.agentAccessUrl;
		var _ref6$seoPluginSearch = _ref6.seoPluginSearchUrl;
		var seoPluginSearchUrl = _ref6$seoPluginSearch === void 0 ? "" : _ref6$seoPluginSearch;
		var _ref6$angieInstallHer = _ref6.angieInstallHeroUrl;
		var angieInstallHeroUrl = _ref6$angieInstallHer === void 0 ? "" : _ref6$angieInstallHer;
		var _ref6$angieAppUrl = _ref6.angieAppUrl;
		var angieAppUrl = _ref6$angieAppUrl === void 0 ? "" : _ref6$angieAppUrl;
		var _ref6$angiePluginSear = _ref6.angiePluginSearchUrl;
		var angiePluginSearchUrl = _ref6$angiePluginSear === void 0 ? "" : _ref6$angiePluginSear;
		var _ref6$isActivated = _ref6.isActivated;
		var isActivated = _ref6$isActivated === void 0 ? false : _ref6$isActivated;
		var _ref6$isAngieActive = _ref6.isAngieActive;
		var isAngieActive = _ref6$isAngieActive === void 0 ? false : _ref6$isAngieActive;
		var _ref6$isAngieInstalle = _ref6.isAngieInstalled;
		var isAngieInstalled = _ref6$isAngieInstalle === void 0 ? false : _ref6$isAngieInstalle;
		var lastReport = _ref6.lastReport;
		var _ref6$retryAt = _ref6.retryAt;
		var retryAt = _ref6$retryAt === void 0 ? null : _ref6$retryAt;
		var llmsConfig = _ref6.llmsConfig;
		var markdownConfig = _ref6.markdownConfig;
		var botAccessConfig = _ref6.botAccessConfig;
		var agentDiscoveryConfig = _ref6.agentDiscoveryConfig;
		var browserToolsConfig = _ref6.browserToolsConfig;
		return /*#__PURE__*/ react.default.createElement(_elementor_ui.DirectionProvider, { rtl: isRTL }, /*#__PURE__*/ react.default.createElement(_elementor_ui.LocalizationProvider, null, /*#__PURE__*/ react.default.createElement(_elementor_ui.ThemeProvider, {
			colorScheme: "light",
			palette: "argon-beta"
		}, /*#__PURE__*/ react.default.createElement(ScannerScreen, {
			siteDomain,
			agentAccessUrl,
			seoPluginSearchUrl,
			angieInstallHeroUrl,
			angieAppUrl,
			angiePluginSearchUrl,
			isActivated,
			isAngieActive,
			isAngieInstalled,
			lastReport,
			retryAt,
			llmsConfig,
			markdownConfig,
			botAccessConfig,
			agentDiscoveryConfig,
			browserToolsConfig
		}))));
	};
	ScannerApp.propTypes = {
		isRTL: import_prop_types.default.bool,
		siteDomain: import_prop_types.default.string.isRequired,
		agentAccessUrl: import_prop_types.default.string.isRequired,
		seoPluginSearchUrl: import_prop_types.default.string,
		angieInstallHeroUrl: import_prop_types.default.string,
		angieAppUrl: import_prop_types.default.string,
		angiePluginSearchUrl: import_prop_types.default.string,
		isActivated: import_prop_types.default.bool,
		isAngieActive: import_prop_types.default.bool,
		isAngieInstalled: import_prop_types.default.bool,
		lastReport: import_prop_types.default.object,
		retryAt: import_prop_types.default.string,
		llmsConfig: import_prop_types.default.object,
		markdownConfig: import_prop_types.default.object,
		botAccessConfig: import_prop_types.default.object,
		agentDiscoveryConfig: import_prop_types.default.object,
		browserToolsConfig: import_prop_types.default.object
	};

//#endregion
//#region modules/agents/assets/js/admin/agents-ready.js
	var MOUNT_ID = "e-agents-ready";
	var SCANNER_MOUNT_ID = "e-agents-ready-scanner";
	var mountAgentAccess = function mountAgentAccess(rootElement, config, isRTL) {
		react_default.render(/*#__PURE__*/ react.default.createElement(App, {
			isRTL,
			isActivated: !!config.isActivated,
			readinessScoreUrl: config.readinessScoreUrl || "",
			agentDiscoveryConfig: config.agentDiscovery,
			botAccessConfig: config.botAccess,
			browserToolsConfig: config.browserTools,
			llmsConfig: config.llms,
			markdownConfig: config.markdown
		}), rootElement);
	};
	var mountScanner = function mountScanner(rootElement, config, isRTL) {
		var scannerConfig = config.scanner || {};
		react_default.render(/*#__PURE__*/ react.default.createElement(ScannerApp, {
			isRTL,
			isActivated: !!config.isActivated,
			siteDomain: scannerConfig.siteDomain || window.location.hostname,
			agentAccessUrl: scannerConfig.agentAccessUrl || "",
			seoPluginSearchUrl: scannerConfig.seoPluginSearchUrl || "",
			angiePluginSearchUrl: scannerConfig.angiePluginSearchUrl || "",
			angieAppUrl: scannerConfig.angieAppUrl || "",
			angieInstallHeroUrl: scannerConfig.angieInstallHeroUrl || "",
			isAngieActive: !!scannerConfig.isAngieActive,
			isAngieInstalled: !!scannerConfig.isAngieInstalled,
			lastReport: scannerConfig.lastReport || null,
			retryAt: scannerConfig.retryAt || null,
			llmsConfig: config.llms,
			markdownConfig: config.markdown,
			botAccessConfig: config.botAccess,
			agentDiscoveryConfig: config.agentDiscovery,
			browserToolsConfig: config.browserTools
		}), rootElement);
	};
	(function init() {
		var config = window.elementorAgentsReadyConfig || {};
		var isRTL = !!elementorCommon.config.isRTL;
		var scannerRoot = document.querySelector("#".concat(SCANNER_MOUNT_ID));
		var agentAccessRoot = document.querySelector("#".concat(MOUNT_ID));
		if (scannerRoot) mountScanner(scannerRoot, config, isRTL);
		if (agentAccessRoot) mountAgentAccess(agentAccessRoot, config, isRTL);
	})();

//#endregion
})(React, ReactDOM, elementorV2.ui, elementorV2.icons, elementorV2.ui['Alert'], elementorV2.ui['Button'], elementorV2.ui['Container'], elementorV2.ui['Divider'], elementorV2.ui['Stack'], wp.i18n, wp.apiFetch, elementorV2.ui['Chip'], elementorV2.ui['Typography'], elementorV2.ui['Dialog'], elementorV2.ui['DialogActions'], elementorV2.ui['DialogContent'], elementorV2.ui['DialogContentText'], elementorV2.ui['DialogTitle'], elementorV2.ui['DialogHeader'], elementorV2.ui['CloseButton'], elementorV2.ui['IconButton'], elementorV2.ui['InputAdornment'], elementorV2.ui['MenuItem'], elementorV2.ui['MenuList'], elementorV2.ui['Popover'], elementorV2.ui['TextField'], elementorV2.ui['Box'], elementorV2.ui['Switch'], elementorV2.ui['Avatar'], elementorV2.ui['Collapse'], elementorV2.ui['Link'], elementorV2.ui['Tooltip'], elementorV2.ui['CircularProgress'], elementorV2.ui['Autocomplete'], elementorV2.ui['Accordion'], elementorV2.ui['AccordionDetails'], elementorV2.ui['AccordionSummary'], elementorV2.ui['Infotip'], elementorV2.ui['SvgIcon'], elementorV2.ui['Checkbox'], elementorV2.ui['FormControlLabel'], elementorV2.ui['Select'], elementorV2.ui['Skeleton']);
//# sourceMappingURL=agents-ready.js.map