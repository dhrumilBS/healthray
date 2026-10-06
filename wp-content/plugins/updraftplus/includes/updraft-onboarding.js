/**
 * Initialize global object for onboarding actions if it doesn't exist
 */
if (!window.pluginOnboardingActions) {
	window.pluginOnboardingActions = {};
}

/**
 * Initialize global object for filters if it doesn't exist
 */
if (!window.updraftOnboardingFilters) {
	window.updraftOnboardingFilters = {};
}

/**
 * Adds a filter function to a specific tag.
 *
 * @param {string} tag The name of the filter.
 * @param {function} callback The function to be called when the filter is applied.
 */
window.pluginOnboardingActions.addFilter = function(tag, callback) {
	if (!window.updraftOnboardingFilters[tag]) {
		window.updraftOnboardingFilters[tag] = [];
	}
	window.updraftOnboardingFilters[tag].push(callback);
};

/**
 * Applies all filter functions registered for a specific tag to a value.
 *
 * @param {string} tag The name of the filter.
 * @param {any} value The value to filter.
 * @returns {any} The filtered value.
 */
window.pluginOnboardingActions.applyFilters = function(tag, value) {
	const args = Array.prototype.slice.call(arguments, 2);

	if (window.updraftOnboardingFilters[tag]) {
		window.updraftOnboardingFilters[tag].forEach(function(callback) {
			value = callback.apply(null, [value].concat(args));
		});
	}

	return value;
};

/**
 * Helper function to get a setting value from the settings array.
 *
 * @param {Array<object>} settings The entire form settings array from Zustand.
 * @param {string} id The ID of the setting to retrieve.
 * @param {any} defaultValue The default value if the setting is not found.
 * @returns {any} The value of the setting or the default value.
 */
const updraftGetSettingValue = function(settings, id, defaultValue) {
	if (typeof defaultValue === 'undefined') {
		defaultValue = '';
	}

	const found = settings.find(function(s) {
		return s.id === id;
	});

	if (found && typeof found.value !== 'undefined') {
		return found.value;
	}

	return defaultValue;
};

/**
 * Simple email validation function.
 *
 * @param {string} email The email string to validate.
 * @returns {boolean} True if the email is valid, false otherwise.
 */
const updraftIsValidEmail = function(email) {
	const trimmed = (email || "").trim();
	return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmed);
};

/**
 * Creates callback objects (success and error) for storage connection testing.
 * This helps keep the code DRY when there are many connection testing functions.
 *
 * @param {object} options Options to configure the callbacks.
 * @param {string} options.groupId The group ID (e.g., 'backblaze', 'azure').
 * @param {string} options.methodLabel A user-friendly label for the storage method (e.g., 'Backblaze', 'Azure').
 * @param {function} options.setAlertState Function to update connection status in Zustand store.
 * @param {function} options.setValue Function to update a field's value in Zustand for completion status.
 * @returns {{successCallback: Function, errorCallback: Function}} An object containing the callback functions.
 */
window.pluginOnboardingActions.createTestConnectionCallbacks = function({groupId, methodLabel, setAlertState, setValue}) {
	const successCallback = function(response, status) {
		if (response && response.data && response.data.success) {
			let message = wp.i18n.sprintf(updraftplus_onboarding.connected, updraftplus_onboarding.remote_storages[methodLabel]);

			setAlertState(groupId, {
				responseMessage: message,
				responseSuccess: true,
				responseCode: 'success',
				isUpdating: false,
			});
			if (setValue) {
				setValue(`${groupId}_completed`, true);
			}
		} else {
			response.output = response.output.replaceAll('&quot;', '"');
			setAlertState(groupId, {
				responseMessage: wp.i18n.sprintf(updraftlion.settings_test_result, updraftplus_onboarding.remote_storages[methodLabel]) + ' ' + response.output,
				responseSuccess: false,
				responseCode: 'danger',
				isUpdating: false,
			});
			if (setValue) {
				setValue(`${groupId}_completed`, false);
			}
		}
	};

	const errorCallback = function(response, status, error_code, resp) {
		let errorMessage = wp.i18n.sprintf(updraftlion.settings_test_result, updraftplus_onboarding.remote_storages[methodLabel]);

		if (typeof resp !== 'undefined' && resp.hasOwnProperty('fatal_error')) {
			errorMessage = resp.fatal_error_message;
			console.error(resp.fatal_error_message);
		} else if (response && response.output) {
			errorMessage = response.output.replaceAll('&quot;', '"');
		} else {
			errorMessage = `updraft_send_command: error: ${status} (${error_code})`;
		}
		setAlertState(groupId, {
			responseMessage: errorMessage,
			responseSuccess: false,
			responseCode: 'danger',
			isUpdating: false,
		});
		if (setValue) {
			setValue(`${groupId}_completed`, false);
		}
	};

	return { successCallback, errorCallback };
};

/**
 * Generic function to test connections to various remote storage destinations.
 *
 * @param {object} field The definition object of the clicked button field.
 * @param {function} setAlertState Function to update connection status in Zustand store.
 * @param {string} methodLabel A user-friendly label for the storage method (e.g., 'Backblaze', 'Azure').
 * @param {object} dataPayload The complete data payload to send to the backend.
 * @param {function} setValue Function to update a field's value in Zustand for completion status.
 */
window.pluginOnboardingActions.testRemoteStorageConnection = async function(
	field,
	setAlertState,
	methodLabel,
	dataPayload,
	setValue
) {
	// Use field.group_id if available, otherwise fallback to field.id
	const groupId = field.group_id || field.id;
	const message = wp.i18n.sprintf(updraftplus_onboarding.testing_remote_storage, updraftplus_onboarding.remote_storages[methodLabel]);
	setAlertState(groupId, {
		isUpdating: true,
		responseSuccess: false,
		responseCode: 'loading',
		responseMessage: message,
	});

	// Use the utility function to create callbacks
	const { successCallback, errorCallback } = window.pluginOnboardingActions.createTestConnectionCallbacks({
		groupId,
		methodLabel: methodLabel,
		setAlertState,
		setValue
	});

	window.updraft_send_command(
		'test_storage_settings',
		dataPayload,
		successCallback,
		{ error_callback: errorCallback }
	);
};

/**
 * Function to transform data for WebDAV connection.
 * Combines individual fields into a single 'url' parameter.
 *
 * @param {object} data The raw form data for WebDAV.
 * @returns {object} The transformed data with a single 'url' field.
 */
function updraftDataForWebdav(data) {
	const protocol = data.webdav || 'webdav://';
	const user = data.user || '';
	const pass = data.pass || '';
	const host = data.host || '';
	const port = data.port; // Can be 0, empty string, or a number
	const path = data.path || '';
	data.enable_chunk = data.enable_chunk ? 1 : 0;

	let credentials = '';
	if (user && pass) {
		credentials = `${user}:${pass}@`;
	} else if (user) {
		credentials = `${user}@`;
	}

	let portString = '';
	if (port) {
		const defaultWebdavPort = (protocol === 'webdavs://') ? 443 : 80;
		if (parseInt(port, 10) !== defaultWebdavPort) {
			portString = `:${port}`;
		}
	}

	// Ensure path starts with a slash if it's not empty
	const formattedPath = path.startsWith('/') || !path ? path : `/${path}`;

	data.url = `${protocol}${credentials}${host}${portString}${formattedPath}`;

	// Return an object with the constructed URL and other relevant fields
	return data;
}

// Register dataForWebdav as a filter
window.pluginOnboardingActions.addFilter('dataForWebdav', updraftDataForWebdav);

/**
 * Generic function to test connections to various remote storage destinations.
 * Gathers data dynamically and then calls the generic `testRemoteStorageConnection` function.
 *
 * @param {object} field The definition object of the clicked button field.
 * @param {Array<object>} settings The entire form settings array from Zustand.
 * @param {function} setAlertState Function to update connection status in Zustand store.
 * @param {function} setValue Function to update a field's value in Zustand for completion status.
 */
window.pluginOnboardingActions.testConnection = async function(
	field,
	settings,
	setAlertState,
	setValue
) {
	// Use field.group_id if available, otherwise fallback to field.id
	const groupId = field.group_id || field.id;
	const methodLabel = field.method_label || groupId;

	let formData = {};
	// Collect relevant settings for the current destination
	settings.forEach(function(setting) {
		if (setting.id.indexOf(groupId + '_') === 0) {
			const key = setting.id.substring((groupId + '_').length);
			formData[key] = (setting && typeof setting.value !== 'undefined') ? setting.value : '';
		}
	});

	// Apply specific data transformations based on groupId using filters
	const filterTag = `dataFor${groupId.charAt(0).toUpperCase() + groupId.slice(1)}`;
	formData = window.pluginOnboardingActions.applyFilters(filterTag, formData);

	// Prepare payload
	const dataForUpdraftCommand = Object.assign({}, formData, {
		useservercerts: 0,
		disableverify: 0,
		nossl: 0,
		method: groupId
	});

	await window.pluginOnboardingActions.testRemoteStorageConnection(
		field,
		setAlertState,
		methodLabel,
		dataForUpdraftCommand,
		setValue
	);
};

/**
 * Function to handle OAuth connections for remote storage.
 * This will trigger a click on the existing authentication button for the specified destination.
 *
 * @param {object} field The definition object of the clicked button field.
 * @param {Array<object>} settings The entire form settings array from Zustand.
 * @param {function} setAlertState Function to update connection status in Zustand store.
 * @param {function} setValue Function to update a field's value in Zustand.
 * @param {function} updateStepSettings Function to update step in Zustand.
 */
window.pluginOnboardingActions.oauth = async function(
	field,
	settings,
	setAlertState,
	setValue,
	updateStepSettings
) {
	// Use field.group_id if available, otherwise fallback to field.id
	const groupId = field.group_id || field.id;
	const methodLabel = field.method_label || groupId;
	let message = wp.i18n.sprintf(
		updraftplus_onboarding.oauth_pre_connection,
		updraftplus_onboarding.remote_storages[methodLabel]
	);

	setAlertState(groupId, {
		isUpdating: true,
		responseSuccess: true,
		responseCode: 'loading',
		responseMessage: message,
	});

	await updateStepSettings(settings);

	// Redirect to OAuth page
	window.location.href = jQuery(`.updraftplusmethod.${groupId} .updraft_authlink[data-remote_method="${groupId}"]`)?.attr('href');
};

/**
 * Promise-based wrapper for window.updraft_send_command.
 *
 * @param {string} command - The command name sent to Updraft.
 * @param {Object} payload - The payload data sent with the command.
 * @returns {Promise<{
 *   success: boolean,
 *   response?: Object,
 *   status?: string,
 *   error_code?: string
 * }>} The command execution result.
 */
const updraftSendCommandWithPromise = async function(command, payload) {
	return new Promise(function (resolve) {
		window.updraft_send_command(
			command,
			payload,
			function (response) {
				resolve({ success: true, response });
			},
			{
				error_callback: function (response, status, error_code) {
					resolve({
						success: false,
						response,
						status,
						error_code,
					});
				},
			}
		);
	});
}

/**
 * Promise-based wrapper for window.updraft_send_command with timeout.
 * Prevents the promise from hanging forever if the AJAX callback is never invoked.
 *
 * @param {string} command - The command name sent to Updraft.
 * @param {Object} payload - The payload data sent with the command.
 * @param {number} timeoutMs - Timeout in milliseconds (default 15000).
 * @returns {Promise<{
 *   success: boolean,
 *   response?: Object,
 *   status?: string,
 *   error_code?: string
 * }>} The command execution result.
 */
const updraftSendCommandWithTimeout = async function(command, payload, timeoutMs) {
	if (typeof timeoutMs === 'undefined') {
		timeoutMs = 15000;
	}

	const timeoutPromise = new Promise(function (_, reject) {
		setTimeout(function () {
			reject(new Error('updraft_send_command timed out after ' + timeoutMs + 'ms'));
		}, timeoutMs);
	});

	return Promise.race([updraftSendCommandWithPromise(command, payload), timeoutPromise]);
};

/**
 * Handles UpdraftVault connection or quota refresh,
 * including UI state updates, form value updates, and onboarding global state.
 *
 * @param {string} command - The command name sent to Updraft.
 * @param {Object} payload - The payload data for the command.
 * @param {string} groupId - Alert/state group ID to be updated.
 * @param {string} methodLabel - The method label (e.g., "UpdraftVault").
 * @param {Function} setAlertState - Setter for the alert UI state.
 * @param {Function} setValue - Setter for form values.
 * @param {boolean} isConnect - Indicates whether this is an initial connection or a quota refresh.
 * @returns {Promise<{
 *   success: boolean,
 *   message: string
 * }>} The final status of the process.
 */
async function handleUpdraftVaultConnection(
	command,
	payload,
	groupId,
	methodLabel,
	setAlertState,
	setValue,
	isConnect
) {
	let message = isConnect ? updraftplus_onboarding.connecting : updraftplus_onboarding.refreshing;
	message = wp.i18n.sprintf(message, 'UpdraftVault');

	if (typeof setAlertState === 'function') {
		setAlertState(groupId, {
			isUpdating: true,
			responseSuccess: false,
			responseCode: 'loading',
			responseMessage: message,
		});
	}

	let result;
	try {
		result = await updraftSendCommandWithTimeout(command, payload);
	} catch (error) {
		// Timeout or network failure - ensure spinner is cleared
		const errorMessage = wp.i18n.sprintf(
			updraftplus_onboarding.connection_error || __('Error connecting to %s.', 'updraftplus'),
			'UpdraftVault'
		);
		if (typeof setAlertState === 'function') {
			setAlertState(groupId, {
				isUpdating: false,
				responseSuccess: false,
				responseCode: 'danger',
				responseMessage: errorMessage,
			});
		}
		return { success: false, message: errorMessage };
	}

	// For disconnect: the server returns connected:false after successful
	// disconnect, which is the desired outcome. Don't treat it as failure.
	const isDisconnect = 'vault_disconnect' === command;
	const isConnected = result.success && result.response && result.response.connected;

	if ((isDisconnect && result.success) || isConnected) {
		const response = result.response;
		let emailDisplay = '';
		let quotaDisplay = '';

		if (response && typeof response === 'object') {
			emailDisplay = payload.email;
			if (response.quota) {
				quotaDisplay = response.quota;
			}
		}

		if (emailDisplay) {
			setValue('updraftvault_email_display', emailDisplay);
		}
		if (quotaDisplay) {
			setValue('updraftvault_quota_display', quotaDisplay);
		}
		if (isConnect) {
			setValue('updraftvault_completed', true);
		}

		if (typeof setAlertState === 'function') {
			setAlertState(groupId, {
				isUpdating: false,
				responseSuccess: true,
				responseCode: false,
				responseMessage: '',
			});
		}

		return { success: true, message: '' };
	}

	if (result.success) {
		if (isConnect) {
			message = updraftplus_onboarding.not_connected;
		} else {
			message = updraftplus_onboarding.cannot_refresh_updraftvault;
		}
	} else {
		if (isConnect) {
			message = updraftplus_onboarding.connection_error;
		} else {
			message = updraftplus_onboarding.refresh_error;
		}

		message += result.status + ' (' + result.error_code + ')';
	}

	message = wp.i18n.sprintf(message, 'UpdraftVault');

	if (typeof setAlertState === 'function') {
		setAlertState(groupId, {
			isUpdating: false,
			responseSuccess: false,
			responseCode: 'danger',
			responseMessage: message,
		});
	}

	if (isConnect) {
		setValue('updraftvault_completed', false);
	}

	return { success: false };
}

/**
 * Handles the connect to UpdraftVault from an onboarding step.
 *
 * @param {object} field The current step object.
 * @param {Array<object>} settings The entire form settings array from Zustand.
 * @param {function} setAlertState Function to update connection status in Zustand store.
 * @param {function} setValue Function to update a field's value in Zustand for completion status.
 * @param {function} updateStepSettings Function to update step in Zustand.
 * @returns {Promise<{success: boolean, message: string}>} Result of the operation.
 */
window.pluginOnboardingActions.connectUpdraftVault = async function(
	field,
	settings,
	setAlertState,
	setValue,
	updateStepSettings
) {
	const groupId = field.group_id || field.id || 'updraftvault';
	const methodLabel = field.method_label || 'UpdraftVault';

	const email = updraftGetSettingValue(settings, 'updraftvault_email', '');
	const password = updraftGetSettingValue(settings, 'updraftvault_password', '');

	if (!email || !updraftIsValidEmail(email)) {
		setAlertState(groupId, {
			isUpdating: false,
			responseSuccess: false,
			responseCode: 'danger',
			responseMessage: updraftplus_onboarding.email_not_valid,
		});
		setValue('updraftvault_completed', false);
		return { success: false };
	}

	if (!password) {
		setAlertState(groupId, {
			isUpdating: false,
			responseSuccess: false,
			responseCode: 'danger',
			responseMessage: updraftplus_onboarding.password_cannot_empty,
		});
		setValue('updraftvault_completed', false);
		return { success: false };
	}

	const result = handleUpdraftVaultConnection(
		'vault_connect',
		{
			email: email,
			pass: password,
			return_data_only: true
		},
		groupId,
		methodLabel,
		setAlertState,
		setValue,
		true
	);

	await updateStepSettings(settings);

	return result;
};

/**
 * Handles the recount quota for UpdraftVault from an onboarding step.
 *
 * @param {object} field The current step object.
 * @param {Array<object>} settings The entire form settings array from Zustand.
 * @param {function} setAlertState Function to update connection status in Zustand store.
 * @param {function} setValue Function to update a field's value in Zustand for completion status.
 * @returns {Promise<{success: boolean, message: string}>} Result of the operation.
 */
window.pluginOnboardingActions.recountQuotaUpdraftVault = async function(
	field,
	settings,
	setAlertState,
	setValue
) {
	const groupId = 'updraftvault_connected';
	const methodLabel = field.method_label || 'UpdraftVault';

	return handleUpdraftVaultConnection(
		'vault_recountquota',
		{
			return_data_only: true
		},
		groupId,
		methodLabel,
		setAlertState,
		setValue,
		false
	);
};

/**
 * Handles the disconnect to UpdraftVault from an onboarding step.
 *
 * @param {object} field The current step object.
 * @param {Array<object>} settings The entire form settings array from Zustand.
 * @param {function} setAlertState Function to update connection status in Zustand store.
 * @param {function} setValue Function to update a field's value in Zustand for completion status.
 * @returns {Promise<{success: boolean, message: string}>} Result of the operation.
 */
window.pluginOnboardingActions.disconnectUpdraftVault = async function(
	field,
	settings,
	setAlertState,
	setValue
) {
	const groupId = 'updraftvault_connected';
	const methodLabel = field.method_label || 'UpdraftVault';

	try {
		const result = await handleUpdraftVaultConnection(
			'vault_disconnect',
			{
				return_data_only: true
			},
			groupId,
			methodLabel,
			setAlertState,
			setValue,
			false
		);

		// Mark as disconnected AFTER the server confirms success.
		// On failure (e.g. timeout), still set to false so the user
		// can retry from the login form. handleUpdraftVaultConnection
		// has already cleared the spinner in all code paths.
		setValue('updraftvault_completed', false);

		return result;
	} catch (e) {
		// If handleUpdraftVaultConnection throws (should not happen after
		// the try/catch added there), ensure spinner is cleared and user
		// can retry.
		console.error('Error in disconnectUpdraftVault:', e);
		setValue('updraftvault_completed', false);
		return { success: false };
	}
};

/**
 * Clean up the URL after an UpdraftPlus remote storage authentication flow.
 *
 * Only runs when the `action` query parameter starts with
 * `updraftmethod-`, indicating that the page was reached from a
 * remote storage authentication callback.
 */
document.addEventListener('DOMContentLoaded', function () {
	const url = new URL(window.location.href);
	const action = url.searchParams.get('action');

	if (!action || !action.startsWith('updraftmethod-')) {
		return;
	}

	window.history.replaceState(
		{},
		document.title,
		url.pathname + '?page=updraftplus'
	);
});
