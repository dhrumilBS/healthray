"use strict";

(function() {

//#region \0rolldown/runtime.js
	var __defProp = Object.defineProperty;
	var __name = (target, value) => __defProp(target, "name", {
		value,
		configurable: true
	});
	var __esmMin = (fn, res, err) => () => {
		if (err) throw err[0];
		try {
			return fn && (res = fn(fn = 0)), res;
		} catch (e) {
			throw err = [e], e;
		}
	};
	var __commonJSMin = (cb, mod) => () => (mod || (cb((mod = { exports: {} }).exports, mod), cb = null), mod.exports);

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
	var init_asyncToGenerator = __esmMin((() => {}));

//#endregion
//#region modules/agents/assets/js/frontend/model-context.js
	var require_model_context = /* @__PURE__ */ __commonJSMin(((exports) => {
		Object.defineProperty(exports, "__esModule", { value: true });
		exports.getModelContext = void 0;
		var getModelContext = (doc, nav) => {
			var _doc$modelContext;
			return (_doc$modelContext = doc === null || doc === void 0 ? void 0 : doc.modelContext) !== null && _doc$modelContext !== void 0 ? _doc$modelContext : nav === null || nav === void 0 ? void 0 : nav.modelContext;
		};
		exports.getModelContext = getModelContext;
	}));

//#endregion
//#region modules/agents/assets/js/frontend/webmcp-tools.js
	var require_webmcp_tools = /* @__PURE__ */ __commonJSMin(((exports) => {
		init_asyncToGenerator();
		Object.defineProperty(exports, "__esModule", { value: true });
		exports.runTool = exports.registerTools = void 0;
		var _modelContext = require_model_context();
		var toTextResult = (text, isError) => ({
			content: [{
				type: "text",
				text
			}],
			isError
		});
		var runTool = function() {
			var _ref = _asyncToGenerator(function* (runUrl, name, input) {
				try {
					const response = yield fetch(runUrl, {
						method: "POST",
						credentials: "omit",
						headers: { "Content-Type": "application/json" },
						body: JSON.stringify({
							tool: name,
							input
						})
					});
					const payload = yield response.json();
					return toTextResult(JSON.stringify(payload), !response.ok);
				} catch (error) {
					return toTextResult(`Tool "${name}" failed: ${error.message}`, true);
				}
			});
			return function runTool(_x, _x2, _x3) {
				return _ref.apply(this, arguments);
			};
		}();
		exports.runTool = runTool;
		var registerTools = (modelContext, config) => {
			if (!(modelContext === null || modelContext === void 0 ? void 0 : modelContext.registerTool) || !Array.isArray(config === null || config === void 0 ? void 0 : config.tools)) return;
			for (const { name, description, inputSchema } of config.tools) try {
				modelContext.registerTool({
					name,
					description,
					inputSchema,
					annotations: { readOnlyHint: true },
					execute: (input) => runTool(config.runUrl, name, input)
				});
			} catch (_unused) {}
		};
		exports.registerTools = registerTools;
		registerTools((0, _modelContext.getModelContext)(window.document, window.navigator), window.elementorAgentsWebmcpConfig);
	}));

//#endregion
return require_webmcp_tools();

})();
//# sourceMappingURL=agents-webmcp-tools.js.map