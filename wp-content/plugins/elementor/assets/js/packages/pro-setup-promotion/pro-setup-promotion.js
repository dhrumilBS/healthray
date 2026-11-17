(function(react, _elementor_editor_v1_adapters, _elementor_editor_notifications, _elementor_editor_ui, _elementor_http_client, _elementor_ui, _wordpress_i18n, _elementor_events, _elementor_icons) {

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
	var __exportAll = (all, no_symbols) => {
		let target = {};
		for (var name in all) {
			__defProp(target, name, {
				get: all[name],
				enumerable: true
			});
		}
		if (!no_symbols) {
			__defProp(target, Symbol.toStringTag, { value: "Module" });
		}
		return target;
	};
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

//#region node_modules/react-dom/client.js
	var require_client = /* @__PURE__ */ __commonJSMin(((exports) => {
		var m = (globalThis.ReactDOM);
		var i = m.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;
		exports.createRoot = function(c, o) {
			i.usingClientEntryPoint = true;
			try {
				return m.createRoot(c, o);
			} finally {
				i.usingClientEntryPoint = false;
			}
		};
	}));

//#endregion
//#region packages/apps/pro-setup-promotion/src/analytics/constants.ts
var import_client = require_client();
	var TRACKING_WINDOW_NAME = "pro_setup";
	var TRACKING_TARGET_LOCATION = "widget_panel";
	var TRACKING_LOCATION_L1 = "pro_setup_panel";
	var PANEL_VIEWED_EVENT = "pro_setup_panel_viewed";
	var POPOVER_OPENED_EVENT = "pro_setup_popover_opened";
	var POPOVER_CLOSED_EVENT = "pro_setup_popover_closed";
	var PANEL_DISMISSED_EVENT = "pro_setup_panel_dismissed";
	var CTA_CLICKED_EVENT = "pro_setup_cta_clicked";
	var STEP_COMPLETED_EVENT = "pro_setup_step_completed";
	var CTA_TARGET_NAMES = {
		install: "install_and_activate",
		activate: "activate_pro",
		connect: "connect_and_activate"
	};

//#endregion
//#region packages/apps/pro-setup-promotion/src/analytics/panel-view-session.ts
	var panelViewedStatesThisSession = /* @__PURE__ */ new Set();
	function shouldTrackPanelViewForState(state) {
		if (panelViewedStatesThisSession.has(state)) return false;
		panelViewedStatesThisSession.add(state);
		return true;
	}

//#endregion
//#region packages/apps/pro-setup-promotion/src/analytics.ts
	var dispatch = (eventName, payload) => {
		(0, _elementor_events.getMixpanel)().dispatchEvent?.(eventName, {
			window_name: TRACKING_WINDOW_NAME,
			target_location: TRACKING_TARGET_LOCATION,
			location_l1: TRACKING_LOCATION_L1,
			state: payload.state,
			...payload
		});
	};
	var withState = (state, payload) => ({
		state,
		...payload
	});
	function trackProSetupPromotionImpression(state) {
		if (!shouldTrackPanelViewForState(state)) return;
		dispatch(PANEL_VIEWED_EVENT, withState(state, {
			interaction_type: "view",
			target_type: "banner",
			target_name: "pro_setup_panel",
			interaction_result: "panel_viewed"
		}));
	}
	function trackProSetupPromotionBannerClick(state) {
		dispatch(POPOVER_OPENED_EVENT, withState(state, {
			interaction_type: "click",
			target_type: "banner",
			target_name: "pro_setup_panel",
			interaction_result: "popover_opened"
		}));
	}
	function trackProSetupPromotionPopoverClose(state, targetName) {
		dispatch(POPOVER_CLOSED_EVENT, withState(state, {
			interaction_type: "click",
			target_type: "button",
			target_name: targetName,
			interaction_result: "popover_closed"
		}));
	}
	function trackProSetupPromotionDismiss(state) {
		dispatch(PANEL_DISMISSED_EVENT, withState(state, {
			interaction_type: "click",
			target_type: "button",
			target_name: "dismiss",
			interaction_result: "panel_dismissed"
		}));
	}
	function trackProSetupPromotionCtaClick(state) {
		dispatch(CTA_CLICKED_EVENT, withState(state, {
			interaction_type: "click",
			target_type: "button",
			target_name: CTA_TARGET_NAMES[state],
			interaction_result: "cta_clicked"
		}));
	}
	function trackProSetupPromotionResult(state, result, action, errorMessage) {
		dispatch(STEP_COMPLETED_EVENT, withState(state, {
			interaction_type: "view",
			target_type: "workflow",
			target_name: "pro_setup",
			target_value: action,
			interaction_result: "success" === result ? "step_completed" : "step_failed",
			...errorMessage ? { metadata: { error: errorMessage } } : {}
		}));
	}
	function trackProSetupPromotionConnectResult(result, errorMessage) {
		trackProSetupPromotionResult("connect", result, "connect", errorMessage);
	}

//#endregion
//#region packages/apps/pro-setup-promotion/src/hooks/use-elementor-connect-popup.ts
	var POPUP_WIDTH = 600;
	var POPUP_HEIGHT = 700;
	var POPUP_TOP = 200;
	var POPUP_LEFT = 0;
	var POPUP_CLOSED_POLL_INTERVAL_MS = 400;
	var SETUP_VERIFY_POLL_INTERVAL_MS = 1500;
	var POPUP_CLOSED_VERIFY_ATTEMPTS = 10;
	var POPUP_CLOSED_VERIFY_DELAY_MS = 800;
	var callbackCounter = 0;
	function delay$1(ms) {
		return new Promise((resolve) => {
			setTimeout(resolve, ms);
		});
	}
	__name(delay$1, "delay");
	function getConnectWindow() {
		return window.elementorCommon?.elements?.$window;
	}
	function getConnectBody() {
		return window.elementorCommon?.elements?.$body;
	}
	function buildPopupUrl(connectUrl, callbackId) {
		const separator = connectUrl.includes("?") ? "&" : "?";
		const modeParam = connectUrl.includes("mode=popup") ? "" : "&mode=popup";
		return `${connectUrl}${separator}callback_id=${encodeURIComponent(callbackId)}${modeParam}`;
	}
	function closePopupWindow(popup) {
		if (!popup || popup.closed) return;
		try {
			popup.close();
		} catch {}
	}
	function useElementorConnectPopup({ connectUrl, onSuccess, onError, onPopupBlocked, verifyConnectComplete }) {
		const onSuccessRef = (0, react.useRef)(onSuccess);
		const onErrorRef = (0, react.useRef)(onError);
		const onPopupBlockedRef = (0, react.useRef)(onPopupBlocked);
		const verifyConnectCompleteRef = (0, react.useRef)(verifyConnectComplete);
		const activeCallbackRef = (0, react.useRef)(null);
		const successHandlerRef = (0, react.useRef)(null);
		const errorHandlerRef = (0, react.useRef)(null);
		const popupClosedPollRef = (0, react.useRef)(null);
		const setupVerifyPollRef = (0, react.useRef)(null);
		const popupUrlRef = (0, react.useRef)("");
		const popupWindowRef = (0, react.useRef)(null);
		const connectFlowCompletedRef = (0, react.useRef)(false);
		const verifyInFlightRef = (0, react.useRef)(false);
		const flowGenerationRef = (0, react.useRef)(0);
		onSuccessRef.current = onSuccess;
		onErrorRef.current = onError;
		onPopupBlockedRef.current = onPopupBlocked;
		verifyConnectCompleteRef.current = verifyConnectComplete;
		const clearPopupClosedPoll = (0, react.useCallback)(() => {
			if (popupClosedPollRef.current) {
				clearInterval(popupClosedPollRef.current);
				popupClosedPollRef.current = null;
			}
		}, []);
		const clearSetupVerifyPoll = (0, react.useCallback)(() => {
			if (setupVerifyPollRef.current) {
				clearInterval(setupVerifyPollRef.current);
				setupVerifyPollRef.current = null;
			}
		}, []);
		const detachListeners = (0, react.useCallback)(() => {
			const callbackId = activeCallbackRef.current;
			const $window = getConnectWindow();
			const $body = getConnectBody();
			clearPopupClosedPoll();
			clearSetupVerifyPoll();
			if (callbackId) {
				if ($window) {
					if (successHandlerRef.current) $window.off(`elementor/connect/success/${callbackId}`, successHandlerRef.current);
					if (errorHandlerRef.current) $window.off(`elementor/connect/error/${callbackId}`, errorHandlerRef.current);
				}
				if ($body) {
					if (successHandlerRef.current) $body.off(`elementor/connect/success/${callbackId}`, successHandlerRef.current);
					if (errorHandlerRef.current) $body.off(`elementor/connect/error/${callbackId}`, errorHandlerRef.current);
				}
			}
			activeCallbackRef.current = null;
			successHandlerRef.current = null;
			errorHandlerRef.current = null;
			popupUrlRef.current = "";
			popupWindowRef.current = null;
			verifyInFlightRef.current = false;
		}, [clearPopupClosedPoll, clearSetupVerifyPoll]);
		(0, react.useEffect)(() => () => {
			flowGenerationRef.current += 1;
			connectFlowCompletedRef.current = true;
			detachListeners();
		}, [detachListeners]);
		const isFlowStillActive = (0, react.useCallback)((flowGeneration, callbackId) => {
			return !connectFlowCompletedRef.current && flowGeneration === flowGenerationRef.current && callbackId === activeCallbackRef.current;
		}, []);
		const verifySetupComplete = (0, react.useCallback)(async () => {
			const verify = verifyConnectCompleteRef.current;
			if (!verify) return true;
			return verify();
		}, []);
		const finishConnectFlow = (0, react.useCallback)((outcome) => {
			if (connectFlowCompletedRef.current) return;
			connectFlowCompletedRef.current = true;
			closePopupWindow(popupWindowRef.current);
			detachListeners();
			if ("success" === outcome) {
				onSuccessRef.current?.();
				return;
			}
			onErrorRef.current?.();
		}, [detachListeners]);
		const tryCompleteConnectFlow = (0, react.useCallback)(async () => {
			if (connectFlowCompletedRef.current || !activeCallbackRef.current || verifyInFlightRef.current) return;
			if (!verifyConnectCompleteRef.current) {
				finishConnectFlow("success");
				return;
			}
			verifyInFlightRef.current = true;
			try {
				if (await verifySetupComplete()) finishConnectFlow("success");
			} catch {} finally {
				verifyInFlightRef.current = false;
			}
		}, [finishConnectFlow, verifySetupComplete]);
		const handlePopupClosed = (0, react.useCallback)(() => {
			if (connectFlowCompletedRef.current || !activeCallbackRef.current) return;
			const callbackId = activeCallbackRef.current;
			const flowGeneration = flowGenerationRef.current;
			clearPopupClosedPoll();
			clearSetupVerifyPoll();
			(async () => {
				if (verifyConnectCompleteRef.current) for (let attempt = 0; attempt < POPUP_CLOSED_VERIFY_ATTEMPTS; attempt++) {
					if (!isFlowStillActive(flowGeneration, callbackId)) return;
					try {
						if (await verifySetupComplete()) {
							finishConnectFlow("success");
							return;
						}
					} catch {}
					if (attempt < POPUP_CLOSED_VERIFY_ATTEMPTS - 1) {
						await delay$1(POPUP_CLOSED_VERIFY_DELAY_MS);
						if (!isFlowStillActive(flowGeneration, callbackId)) return;
					}
				}
				if (!isFlowStillActive(flowGeneration, callbackId)) return;
				finishConnectFlow("error");
			})();
		}, [
			clearPopupClosedPoll,
			clearSetupVerifyPoll,
			finishConnectFlow,
			isFlowStillActive,
			verifySetupComplete
		]);
		const startSetupVerifyPoll = (0, react.useCallback)(() => {
			clearSetupVerifyPoll();
			setupVerifyPollRef.current = setInterval(() => {
				const popup = popupWindowRef.current;
				if (connectFlowCompletedRef.current || !popup || popup.closed) return;
				tryCompleteConnectFlow();
			}, SETUP_VERIFY_POLL_INTERVAL_MS);
		}, [clearSetupVerifyPoll, tryCompleteConnectFlow]);
		const tryOpenPopupWindow = (0, react.useCallback)(() => {
			const popupUrl = popupUrlRef.current;
			if (!popupUrl) return false;
			const features = `toolbar=no,menubar=no,width=${POPUP_WIDTH},height=${POPUP_HEIGHT},top=${POPUP_TOP},left=${POPUP_LEFT}`;
			const popup = window.open(popupUrl, "elementorConnect", features);
			if (!popup) return false;
			popupWindowRef.current = popup;
			startSetupVerifyPoll();
			popupClosedPollRef.current = setInterval(() => {
				if (connectFlowCompletedRef.current) {
					clearPopupClosedPoll();
					return;
				}
				if (!popup.closed) return;
				handlePopupClosed();
			}, POPUP_CLOSED_POLL_INTERVAL_MS);
			return true;
		}, [
			clearPopupClosedPoll,
			handlePopupClosed,
			startSetupVerifyPoll
		]);
		return (0, react.useCallback)((connectUrlOverride) => {
			const activeConnectUrl = connectUrlOverride ?? connectUrl;
			if (!activeConnectUrl) {
				detachListeners();
				return;
			}
			connectFlowCompletedRef.current = false;
			detachListeners();
			flowGenerationRef.current += 1;
			const callbackId = `proSetupPromo${++callbackCounter}`;
			activeCallbackRef.current = callbackId;
			const successHandler = () => {
				tryCompleteConnectFlow();
			};
			const errorHandler = () => {
				finishConnectFlow("error");
			};
			successHandlerRef.current = successHandler;
			errorHandlerRef.current = errorHandler;
			const $window = getConnectWindow();
			const $body = getConnectBody();
			if (!$window && !$body) {
				detachListeners();
				onErrorRef.current?.();
				return;
			}
			const eventSuffix = callbackId;
			if ($window) {
				$window.on(`elementor/connect/success/${eventSuffix}`, successHandler);
				$window.on(`elementor/connect/error/${eventSuffix}`, errorHandler);
			}
			if ($body) {
				$body.on(`elementor/connect/success/${eventSuffix}`, successHandler);
				$body.on(`elementor/connect/error/${eventSuffix}`, errorHandler);
			}
			popupUrlRef.current = buildPopupUrl(activeConnectUrl, callbackId);
			if (tryOpenPopupWindow()) return;
			const retryOpen = () => {
				if (!activeCallbackRef.current || !popupUrlRef.current) return;
				if (tryOpenPopupWindow()) return;
				onPopupBlockedRef.current?.(retryOpen);
			};
			onPopupBlockedRef.current?.(retryOpen);
		}, [
			connectUrl,
			detachListeners,
			finishConnectFlow,
			tryCompleteConnectFlow,
			tryOpenPopupWindow
		]);
	}

//#endregion
//#region packages/apps/pro-setup-promotion/src/utils/fetch-promotion-config-after-connect.ts
	var SETUP_STATE_RETRY_ATTEMPTS = 12;
	var SETUP_STATE_RETRY_DELAY_MS = 800;
	function delay(ms) {
		return new Promise((resolve) => {
			setTimeout(resolve, ms);
		});
	}
	async function fetchPromotionConfigAfterConnect(fetchConfig) {
		let lastConfig = null;
		for (let attempt = 0; attempt < SETUP_STATE_RETRY_ATTEMPTS; attempt++) {
			let config;
			try {
				config = await fetchConfig();
			} catch (error) {
				if (attempt >= SETUP_STATE_RETRY_ATTEMPTS - 1) throw error;
				await delay(SETUP_STATE_RETRY_DELAY_MS);
				continue;
			}
			if (!config?.state) return config;
			if ("connect" !== config.state) return config;
			lastConfig = config;
			if (attempt < SETUP_STATE_RETRY_ATTEMPTS - 1) await delay(SETUP_STATE_RETRY_DELAY_MS);
		}
		return lastConfig;
	}

//#endregion
//#region packages/apps/pro-setup-promotion/src/utils/get-setup-request-error-message.ts
	function getSetupRequestErrorMessage(error) {
		if (error instanceof _elementor_http_client.AxiosError) {
			const data = error.response?.data;
			if (data?.message) return data.message;
			if (data?.data?.message) return data.data.message;
			if (500 === error.response?.status) return (0, _wordpress_i18n.__)("We couldn't complete Pro setup. Please try again.", "elementor");
		}
		if (error instanceof Error && error.message && !error.message.includes("status code")) return error.message;
		return (0, _wordpress_i18n.__)("We couldn't complete Pro setup. Please try again.", "elementor");
	}

//#endregion
//#region packages/apps/pro-setup-promotion/src/utils/pro-setup-query-params.ts
	var PRO_SETUP_CONNECT_RELOAD_QUERY_PARAM = "elementor_pro_setup_connected";
	var PRO_SETUP_OPEN_CONNECT_QUERY_PARAM = "elementor_pro_setup_open_connect";
	var PRO_SETUP_CONNECT_RELOAD_ATTEMPT_STORAGE_KEY = "elementor_pro_setup_connect_reload_attempted";

//#endregion
//#region packages/apps/pro-setup-promotion/src/utils/pro-setup-connect-reload-guard.ts
	var CONNECT_RELOAD_ATTEMPT_TTL_MS = 300 * 1e3;
	function isOpenConnectQueryParamInUrl() {
		try {
			return "1" === new URL(window.location.href).searchParams.get(PRO_SETUP_OPEN_CONNECT_QUERY_PARAM);
		} catch {
			return false;
		}
	}
	function readStoredValue(key) {
		let sessionValue = null;
		try {
			sessionValue = sessionStorage.getItem(key);
		} catch {
			sessionValue = null;
		}
		if (null !== sessionValue) return sessionValue;
		try {
			return localStorage.getItem(key);
		} catch {
			return null;
		}
	}
	function writeStoredValue(key, value) {
		try {
			sessionStorage.setItem(key, value);
			return;
		} catch {}
		try {
			localStorage.setItem(key, value);
		} catch {}
	}
	function removeStoredValue(key) {
		try {
			sessionStorage.removeItem(key);
		} catch {}
		try {
			localStorage.removeItem(key);
		} catch {}
	}
	function readConnectReloadAttemptedAt() {
		const raw = readStoredValue(PRO_SETUP_CONNECT_RELOAD_ATTEMPT_STORAGE_KEY);
		if (!raw) return null;
		const timestamp = Number(raw);
		return Number.isFinite(timestamp) ? timestamp : null;
	}
	function hasConnectReloadBeenAttempted() {
		const timestamp = readConnectReloadAttemptedAt();
		if (null === timestamp) return false;
		if (Date.now() - timestamp > CONNECT_RELOAD_ATTEMPT_TTL_MS) {
			clearConnectReloadAttempted();
			return false;
		}
		return true;
	}
	function markConnectReloadAttempted() {
		writeStoredValue(PRO_SETUP_CONNECT_RELOAD_ATTEMPT_STORAGE_KEY, String(Date.now()));
	}
	function clearConnectReloadAttempted() {
		removeStoredValue(PRO_SETUP_CONNECT_RELOAD_ATTEMPT_STORAGE_KEY);
	}
	function stripOpenConnectQueryParamFromUrl() {
		try {
			const url = new URL(window.location.href);
			if ("1" !== url.searchParams.get("elementor_pro_setup_open_connect")) return;
			url.searchParams.delete(PRO_SETUP_OPEN_CONNECT_QUERY_PARAM);
			window.history.replaceState(null, "", url.toString());
		} catch {}
	}

//#endregion
//#region packages/apps/pro-setup-promotion/src/utils/pro-setup-editor-refresh.ts
	function isConnectPromotionMissingUrl(config) {
		return "connect" === config?.state && !config?.connectUrl;
	}
	function isReloadDueToMissingConnectUrl(result) {
		if ("missing_connect_url" === result.reloadReason) return true;
		return isConnectPromotionMissingUrl(result.proSetupPromotion);
	}
	function shouldReloadEditorAfterSetupStep(result) {
		if (result.reload) return true;
		return isConnectPromotionMissingUrl(result.proSetupPromotion);
	}
	function appendSetupRefreshQueryParams(url, options = {}) {
		url.searchParams.set(PRO_SETUP_CONNECT_RELOAD_QUERY_PARAM, String(Date.now()));
		if (options.openConnectPopover) url.searchParams.set(PRO_SETUP_OPEN_CONNECT_QUERY_PARAM, "1");
	}

//#endregion
//#region packages/apps/pro-setup-promotion/src/utils/post-reload-connect-success-notification.ts
	var SESSION_STORAGE_KEY = "elementor_pro_setup_promotion_connect_success_pending";
	function markConnectSuccessNotificationForAfterReload() {
		try {
			sessionStorage.setItem(SESSION_STORAGE_KEY, "1");
		} catch {}
	}
	function showConnectSuccessNotificationAfterReloadIfPending() {
		try {
			if ("1" !== sessionStorage.getItem(SESSION_STORAGE_KEY)) return;
			sessionStorage.removeItem(SESSION_STORAGE_KEY);
			showConnectSuccessNotification();
		} catch {}
	}
	function showConnectSuccessNotification() {
		(0, _elementor_editor_notifications.notify)({
			type: "success",
			message: (0, _wordpress_i18n.__)("Elementor Pro is connected and ready to use", "elementor"),
			id: "pro-setup-promotion-connect-success"
		});
	}

//#endregion
//#region packages/apps/pro-setup-promotion/src/utils/reload-editor-after-connect-success.ts
	function reloadEditorAfterConnectSuccess() {
		markConnectSuccessNotificationForAfterReload();
		reloadEditorWithCacheBust();
	}
	function reloadEditorForSetupRefresh(options = {}) {
		reloadEditorWithCacheBust(options);
	}
	function reloadEditorWithCacheBust(options = {}) {
		try {
			const url = new URL(window.location.href);
			appendSetupRefreshQueryParams(url, options);
			window.location.assign(url.toString());
		} catch {
			window.location.reload();
		}
	}

//#endregion
//#region packages/apps/pro-setup-promotion/src/components/pro-setup-banner-card.tsx
	function getInfoBannerSurface(theme) {
		const isDark = "dark" === theme.palette.mode;
		const infoMain = theme.palette.info.main;
		return {
			bgcolor: (0, _elementor_ui.alpha)(infoMain, isDark ? .18 : .12),
			borderColor: (0, _elementor_ui.alpha)(infoMain, isDark ? .38 : .22)
		};
	}
	function getDownloadTileSurface(theme) {
		if ("dark" === theme.palette.mode) return {
			bgcolor: (0, _elementor_ui.alpha)(theme.palette.common.white, .08),
			borderColor: (0, _elementor_ui.alpha)(theme.palette.common.white, .16),
			color: theme.palette.text.primary
		};
		return {
			bgcolor: theme.palette.background.paper,
			borderColor: theme.palette.divider,
			color: theme.palette.text.secondary
		};
	}
	function ProSetupBannerCard({ title, subtitle }) {
		return /* @__PURE__ */ react.createElement(_elementor_ui.Stack, {
			direction: "row",
			alignItems: "center",
			gap: 1.25,
			sx: (theme) => {
				const surface = getInfoBannerSurface(theme);
				return {
					border: 1,
					borderColor: surface.borderColor,
					borderRadius: 1.5,
					bgcolor: surface.bgcolor,
					px: 1.25,
					py: 1,
					transition: theme.transitions.create(["border-color", "background-color"], { duration: 150 })
				};
			}
		}, /* @__PURE__ */ react.createElement(_elementor_ui.Stack, {
			flex: 1,
			minWidth: 0,
			gap: .25,
			sx: { textAlign: "start" }
		}, /* @__PURE__ */ react.createElement(_elementor_ui.Typography, {
			variant: "subtitle2",
			color: "text.primary",
			sx: (theme) => ({
				lineHeight: 1.35,
				fontWeight: theme.typography.fontWeightBold,
				display: "-webkit-box",
				WebkitLineClamp: 2,
				WebkitBoxOrient: "vertical",
				overflow: "hidden"
			})
		}, title), /* @__PURE__ */ react.createElement(_elementor_ui.Typography, {
			variant: "caption",
			color: "text.secondary",
			sx: (theme) => ({
				lineHeight: 1.35,
				fontWeight: theme.typography.fontWeightRegular,
				display: "-webkit-box",
				WebkitLineClamp: 2,
				WebkitBoxOrient: "vertical",
				overflow: "hidden"
			})
		}, subtitle)), /* @__PURE__ */ react.createElement(_elementor_ui.Box, {
			"aria-hidden": true,
			"data-testid": "pro-setup-banner-decorative-tile",
			sx: (theme) => {
				const tile = getDownloadTileSurface(theme);
				return {
					display: "flex",
					alignItems: "center",
					justifyContent: "center",
					flexShrink: 0,
					marginInlineStart: "auto",
					width: 36,
					height: 36,
					borderRadius: 1,
					border: 1,
					borderColor: tile.borderColor,
					bgcolor: tile.bgcolor,
					color: tile.color
				};
			}
		}, /* @__PURE__ */ react.createElement(_elementor_icons.DownloadIcon, {
			"aria-hidden": true,
			sx: { fontSize: 18 }
		})));
	}

//#endregion
//#region packages/apps/pro-setup-promotion/src/components/pro-setup-panel-promo.tsx
	var POPOVER_WIDTH = 296;
	var POPOVER_OFFSET_LTR = [-24, 12];
	var POPOVER_OFFSET_RTL = [24, 12];
	var POPOVER_DESCRIPTION_MIN_HEIGHT = 72;
	var POPOVER_HEADER_MIN_HEIGHT = 48;
	var REST_BASE = "elementor/v1/pro-setup-promotion";
	var SetupInfotip = _elementor_ui.Infotip;
	function getConfig() {
		const config = window.elementor?.config?.proSetupPromotion;
		if (!config?.state) return null;
		return config;
	}
	var isEventInside = (target, anchor) => {
		if (!(target instanceof Element)) return false;
		return !!anchor?.contains(target) || !!target.closest("[data-pro-setup-popover], .MuiTooltip-popper");
	};
	function isEditorRtl() {
		return "rtl" === document.dir;
	}
	function getPopoverPlacement(isRtl) {
		return isRtl ? "left-start" : "right-start";
	}
	function getPopoverOffset(isRtl) {
		return isRtl ? POPOVER_OFFSET_RTL : POPOVER_OFFSET_LTR;
	}
	async function postDismiss() {
		await (0, _elementor_http_client.httpService)().post(`${REST_BASE}/dismiss`);
	}
	async function postCompleteSetup() {
		return (await (0, _elementor_http_client.httpService)().post(`${REST_BASE}/complete-setup`)).data?.data ?? {};
	}
	async function fetchPromotionConfig() {
		const config = (await (0, _elementor_http_client.httpService)().get(`${REST_BASE}/setup-state`)).data?.data?.proSetupPromotion;
		if (!config?.state) return null;
		return config;
	}
	function syncWindowPromotionConfig(config) {
		if (!window.elementor?.config) return;
		if (config) {
			window.elementor.config.proSetupPromotion = config;
			return;
		}
		delete window.elementor.config.proSetupPromotion;
	}
	function ProSetupPanelPromo() {
		const [promotionConfig, setPromotionConfig] = (0, react.useState)(() => getConfig());
		const [dismissed, setDismissed] = (0, react.useState)(false);
		const [open, setOpen] = (0, react.useState)(false);
		const [isProcessing, setIsProcessing] = (0, react.useState)(false);
		const anchorRef = (0, react.useRef)(null);
		const popoverRef = (0, react.useRef)(null);
		const isProcessingRef = (0, react.useRef)(false);
		const isRtl = isEditorRtl();
		const placement = getPopoverPlacement(isRtl);
		const popoverOffset = getPopoverOffset(isRtl);
		const state = promotionConfig?.state;
		const closePopover = (0, react.useCallback)((targetName) => {
			if (state) trackProSetupPromotionPopoverClose(state, targetName);
			setOpen(false);
		}, [state]);
		const applyPromotionConfig = (next) => {
			if (void 0 === next) return;
			syncWindowPromotionConfig(next);
			setPromotionConfig(next);
			if (!next) {
				setDismissed(true);
				setOpen(false);
			}
		};
		const handleConnectSuccess = async () => {
			setOpen(false);
			try {
				const config = await fetchPromotionConfigAfterConnect(fetchPromotionConfig);
				if (config && "connect" === config.state) {
					applyPromotionConfig(config);
					trackProSetupPromotionConnectResult("error", (0, _wordpress_i18n.__)("License was not activated on this site.", "elementor"));
					(0, _elementor_editor_notifications.notify)({
						type: "error",
						message: (0, _wordpress_i18n.__)("Your Pro license is not active on this site yet. Reload the editor or connect again from the setup banner.", "elementor"),
						id: "pro-setup-promotion-connect-incomplete",
						additionalActionProps: [{
							children: (0, _wordpress_i18n.__)("Reload editor", "elementor"),
							onClick: () => reloadEditorForSetupRefresh()
						}]
					});
					return;
				}
				applyPromotionConfig(null);
				trackProSetupPromotionConnectResult("success");
				reloadEditorAfterConnectSuccess();
			} catch {
				(0, _elementor_editor_notifications.notify)({
					type: "error",
					message: (0, _wordpress_i18n.__)("We couldn't confirm your Pro license. Please try again.", "elementor"),
					id: "pro-setup-promotion-connect-verify-error"
				});
				trackProSetupPromotionConnectResult("error", (0, _wordpress_i18n.__)("We couldn't confirm your Pro license. Please try again.", "elementor"));
			} finally {
				isProcessingRef.current = false;
				setIsProcessing(false);
			}
		};
		const handleConnectError = () => {
			isProcessingRef.current = false;
			setIsProcessing(false);
			trackProSetupPromotionConnectResult("error", (0, _wordpress_i18n.__)("Connect window closed.", "elementor"));
		};
		const handleConnectPopupBlocked = (retryOpen) => {
			isProcessingRef.current = false;
			setIsProcessing(false);
			(0, _elementor_editor_notifications.notify)({
				type: "info",
				message: (0, _wordpress_i18n.__)("Your browser blocked the connect window. Allow pop-ups for this site, or open connect manually.", "elementor"),
				id: "pro-setup-promotion-connect-popup-blocked",
				autoHideDuration: 3e4,
				additionalActionProps: [{
					children: (0, _wordpress_i18n.__)("Open connect window", "elementor"),
					onClick: () => {
						isProcessingRef.current = true;
						setIsProcessing(true);
						retryOpen();
					}
				}]
			});
		};
		const isProSetupConnectComplete = async () => {
			const config = await fetchPromotionConfig();
			return !config || "connect" !== config.state;
		};
		const openConnectPopup = useElementorConnectPopup({
			connectUrl: promotionConfig?.connectUrl ?? "",
			onSuccess: handleConnectSuccess,
			onError: handleConnectError,
			onPopupBlocked: handleConnectPopupBlocked,
			verifyConnectComplete: isProSetupConnectComplete
		});
		const notifyConnectUnavailable = (0, react.useCallback)(() => {
			isProcessingRef.current = false;
			setIsProcessing(false);
			(0, _elementor_editor_notifications.notify)({
				type: "error",
				message: (0, _wordpress_i18n.__)("Connect isn't available right now. Reload the editor and try again.", "elementor"),
				id: "pro-setup-promotion-connect-url-missing"
			});
		}, []);
		const reloadEditorForMissingConnectUrl = (0, react.useCallback)(() => {
			if (hasConnectReloadBeenAttempted()) {
				stripOpenConnectQueryParamFromUrl();
				notifyConnectUnavailable();
				return;
			}
			markConnectReloadAttempted();
			reloadEditorForSetupRefresh({ openConnectPopover: true });
		}, [notifyConnectUnavailable]);
		(0, react.useEffect)(() => {
			if (!state) return;
			trackProSetupPromotionImpression(state);
		}, [state]);
		(0, react.useEffect)(() => {
			let landedWithOpenConnect = false;
			try {
				landedWithOpenConnect = isOpenConnectQueryParamInUrl();
				if (landedWithOpenConnect) stripOpenConnectQueryParamFromUrl();
			} catch {
				return;
			}
			if (!landedWithOpenConnect) return;
			if ("connect" !== state || !promotionConfig?.connectUrl) {
				if (hasConnectReloadBeenAttempted()) notifyConnectUnavailable();
				return;
			}
			clearConnectReloadAttempted();
			setOpen(true);
		}, [
			notifyConnectUnavailable,
			promotionConfig?.connectUrl,
			state
		]);
		(0, react.useEffect)(() => {
			if (!open) return;
			const closeOnPointerDown = (event) => {
				if (isProcessingRef.current) return;
				if (isEventInside(event.target, anchorRef.current)) return;
				closePopover("outside_click");
			};
			const closeOnEscape = (event) => {
				if ("Escape" !== event.key) return;
				if (isProcessingRef.current) return;
				const target = event.target;
				if (!(target instanceof Node)) return;
				if (!(popoverRef.current?.contains(target) || anchorRef.current?.contains(target))) return;
				event.preventDefault();
				event.stopPropagation();
				closePopover("escape_key");
			};
			const iframeDocument = window.elementor?.$preview?.[0]?.contentDocument;
			document.addEventListener("mousedown", closeOnPointerDown, true);
			document.addEventListener("keydown", closeOnEscape, true);
			iframeDocument?.addEventListener("mousedown", closeOnPointerDown, true);
			return () => {
				document.removeEventListener("mousedown", closeOnPointerDown, true);
				document.removeEventListener("keydown", closeOnEscape, true);
				iframeDocument?.removeEventListener("mousedown", closeOnPointerDown, true);
			};
		}, [
			closePopover,
			open,
			state
		]);
		if (dismissed || !promotionConfig || !state) return null;
		const dismissForCooldownPeriod = async () => {
			trackProSetupPromotionDismiss(state);
			setDismissed(true);
			setOpen(false);
			try {
				await postDismiss();
			} catch {
				(0, _elementor_editor_notifications.notify)({
					type: "error",
					message: (0, _wordpress_i18n.__)("Couldn't dismiss this reminder. Please try again.", "elementor"),
					id: "pro-setup-promotion-dismiss-error"
				});
				setDismissed(false);
			}
		};
		const runServerSetup = async () => {
			setIsProcessing(true);
			isProcessingRef.current = true;
			trackProSetupPromotionCtaClick(state);
			try {
				const result = await postCompleteSetup();
				trackProSetupPromotionResult(state, "success", state === "install" ? "install" : "activate");
				if (shouldReloadEditorAfterSetupStep(result)) {
					if (isReloadDueToMissingConnectUrl(result)) {
						reloadEditorForMissingConnectUrl();
						return;
					}
					reloadEditorForSetupRefresh();
					return;
				}
				applyPromotionConfig(result.proSetupPromotion ?? null);
				setOpen(true);
			} catch (error) {
				const message = getSetupRequestErrorMessage(error);
				trackProSetupPromotionResult(state, "error", state === "install" ? "install" : "activate", message);
				(0, _elementor_editor_notifications.notify)({
					type: "error",
					message,
					id: "pro-setup-promotion-setup-error"
				});
			} finally {
				isProcessingRef.current = false;
				setIsProcessing(false);
			}
		};
		const onPrimaryClick = () => {
			if ("connect" === state) {
				trackProSetupPromotionCtaClick(state);
				isProcessingRef.current = true;
				setIsProcessing(true);
				const connectUrlForPopup = promotionConfig?.connectUrl ?? "";
				if (!connectUrlForPopup) {
					reloadEditorForMissingConnectUrl();
					return;
				}
				clearConnectReloadAttempted();
				openConnectPopup(connectUrlForPopup);
				(async () => {
					try {
						const latest = await fetchPromotionConfig();
						if (latest) applyPromotionConfig(latest);
					} catch {}
				})();
				return;
			}
			runServerSetup();
		};
		const openPopover = () => {
			trackProSetupPromotionBannerClick(state);
			setOpen(true);
		};
		return /* @__PURE__ */ react.createElement(_elementor_ui.DirectionProvider, { rtl: isRtl }, /* @__PURE__ */ react.createElement(_elementor_editor_ui.ThemeProvider, null, /* @__PURE__ */ react.createElement(SetupInfotip, {
			arrow: true,
			disableFocusListener: true,
			disableHoverListener: true,
			disableTouchListener: true,
			open,
			placement,
			PopperProps: { modifiers: [{
				name: "offset",
				options: { offset: popoverOffset }
			}] },
			content: /* @__PURE__ */ react.createElement(_elementor_ui.Box, {
				ref: popoverRef,
				className: "dialog-widget",
				"data-pro-setup-popover": "",
				tabIndex: -1,
				onKeyDown: (event) => {
					if ("Escape" === event.key) {
						event.preventDefault();
						event.stopPropagation();
						closePopover("escape_key");
					}
				},
				sx: {
					width: POPOVER_WIDTH,
					border: 1,
					borderColor: "divider",
					borderRadius: 1.5,
					boxShadow: 2
				}
			}, /* @__PURE__ */ react.createElement(_elementor_ui.Stack, {
				direction: "row",
				alignItems: "center",
				gap: 1,
				py: 1,
				px: 2,
				sx: { minHeight: POPOVER_HEADER_MIN_HEIGHT }
			}, /* @__PURE__ */ react.createElement(_elementor_ui.Typography, {
				variant: "subtitle2",
				sx: {
					flex: 1,
					minWidth: 0
				}
			}, promotionConfig.popover.title), /* @__PURE__ */ react.createElement(_elementor_ui.CloseButton, {
				edge: "end",
				sx: {
					marginInlineStart: "auto",
					flexShrink: 0
				},
				slotProps: { icon: { fontSize: "small" } },
				onClick: () => closePopover("close_button")
			})), /* @__PURE__ */ react.createElement(_elementor_ui.Stack, {
				px: 2,
				sx: { minHeight: POPOVER_DESCRIPTION_MIN_HEIGHT }
			}, /* @__PURE__ */ react.createElement(_elementor_ui.Typography, {
				variant: "body2",
				color: "secondary",
				sx: {
					pt: .5,
					pb: 1
				}
			}, promotionConfig.popover.description)), /* @__PURE__ */ react.createElement(_elementor_ui.Stack, {
				direction: "row",
				alignItems: "center",
				pt: 1,
				pb: 1.5,
				px: 2
			}, /* @__PURE__ */ react.createElement(_elementor_ui.Button, {
				variant: "text",
				size: "small",
				color: "secondary",
				onClick: dismissForCooldownPeriod,
				sx: { marginInlineStart: -1 }
			}, (0, _wordpress_i18n.__)("Dismiss", "elementor")), /* @__PURE__ */ react.createElement(_elementor_ui.Button, {
				variant: "contained",
				size: "small",
				color: "info",
				disabled: isProcessing,
				onClick: (event) => {
					event.stopPropagation();
					onPrimaryClick();
				},
				sx: { marginInlineStart: "auto" }
			}, isProcessing ? /* @__PURE__ */ react.createElement(_elementor_ui.CircularProgress, {
				size: 16,
				color: "inherit"
			}) : promotionConfig.popover.ctaLabel)))
		}, /* @__PURE__ */ react.createElement(_elementor_ui.Box, {
			ref: anchorRef,
			role: "button",
			tabIndex: 0,
			"aria-expanded": open,
			onClick: openPopover,
			onKeyDown: (event) => {
				if ("Enter" === event.key || " " === event.key) {
					event.preventDefault();
					openPopover();
					return;
				}
				if (open && "Escape" === event.key) {
					event.preventDefault();
					event.stopPropagation();
					closePopover("escape_key");
				}
			},
			sx: (theme) => ({
				mx: 1.5,
				my: 1,
				cursor: "pointer",
				"&:hover > *": { filter: "dark" === theme.palette.mode ? "brightness(1.06)" : "brightness(0.97)" },
				"&:focus-visible": {
					outline: "2px solid",
					outlineColor: "primary.main",
					outlineOffset: 2,
					borderRadius: 1.5
				}
			})
		}, /* @__PURE__ */ react.createElement(ProSetupBannerCard, {
			title: promotionConfig.banner.title,
			subtitle: promotionConfig.banner.description
		})))));
	}

//#endregion
//#region packages/apps/pro-setup-promotion/src/init.ts
	var NOTICE_AREA_ID = "elementor-panel-elements-notice-area";
	var ELEMENTS_PANEL_ROUTE_PREFIX = "panel/elements/";
	var WIDGETS_PANEL_ROUTE = "panel/elements/categories";
	function isWidgetsPanelActive() {
		const panelRoute = window.$e?.routes?.getCurrent?.()?.panel;
		return Boolean(panelRoute?.startsWith(WIDGETS_PANEL_ROUTE));
	}
	var reactRoot = null;
	var mountedContainer = null;
	function unmountNotice() {
		reactRoot?.unmount();
		reactRoot = null;
		mountedContainer = null;
	}
	function mountNotice() {
		if (!isWidgetsPanelActive() || !window.elementor?.config?.proSetupPromotion) {
			unmountNotice();
			return;
		}
		const container = document.getElementById(NOTICE_AREA_ID);
		if (!container) return;
		if (mountedContainer !== container) {
			unmountNotice();
			reactRoot = (0, import_client.createRoot)(container);
			mountedContainer = container;
		}
		reactRoot?.render(react.createElement(ProSetupPanelPromo));
	}
	function syncNoticeWithActiveRoute() {
		if (isWidgetsPanelActive()) mountNotice();
		else unmountNotice();
	}
	function stripConnectReloadQueryParam() {
		try {
			const url = new URL(window.location.href);
			if (!url.searchParams.has("elementor_pro_setup_connected")) return;
			url.searchParams.delete(PRO_SETUP_CONNECT_RELOAD_QUERY_PARAM);
			window.history.replaceState(null, "", url.toString());
		} catch {}
	}
	function init() {
		(0, _elementor_editor_v1_adapters.__privateListenTo)((0, _elementor_editor_v1_adapters.v1ReadyEvent)(), () => {
			stripConnectReloadQueryParam();
			showConnectSuccessNotificationAfterReloadIfPending();
			syncNoticeWithActiveRoute();
		});
		(0, _elementor_editor_v1_adapters.__privateListenTo)([(0, _elementor_editor_v1_adapters.routeOpenEvent)(ELEMENTS_PANEL_ROUTE_PREFIX), (0, _elementor_editor_v1_adapters.routeCloseEvent)(ELEMENTS_PANEL_ROUTE_PREFIX)], syncNoticeWithActiveRoute);
	}

//#endregion
//#region packages/apps/pro-setup-promotion/src/index.ts
	var src_exports = /* @__PURE__ */ __exportAll({
		ProSetupPanelPromo: () => ProSetupPanelPromo,
		init: () => init
	});

//#endregion
//#region \0elementor-package-library-entry
	(window.elementorV2 = window.elementorV2 || {}).proSetupPromotion = src_exports;

//#endregion
})(React, elementorV2.editorV1Adapters, elementorV2.editorNotifications, elementorV2.editorUi, elementorV2.httpClient, elementorV2.ui, wp.i18n, elementorV2.events, elementorV2.icons);
window.elementorV2.proSetupPromotion?.init?.();
//# sourceMappingURL=pro-setup-promotion.js.map