// @ts-check

import apiFetch from '@wordpress/api-fetch';

import '../css/entries.scss';

function initExportButtons() {
	const headingElement = document.querySelector(
		'.wrap:has(.cf7r-meta-filter)  .wp-heading-inline'
	);

	if ( ! headingElement ) {
		return;
	}

	const button = document.createElement( 'button' );
	button.className = 'button button-primary cf7r-btn-export';
	button.textContent = window.cf7rData?.labels.export;

	let filename = 'cf7r-export.csv';

	button.onclick = () => {
		const urlParams = new URLSearchParams( window.location.search );
		let exportPath = window.cf7rData.endpoints.export;

		// Add current filters.
		if ( urlParams.toString() ) {
			exportPath += '?' + urlParams.toString();
		}

		apiFetch( {
			path: exportPath,
			method: 'GET',
			parse: false,
		} )
			.then( ( response ) => {
				const contentDisposition = response.headers.get(
					'Content-Disposition'
				);
				filename = contentDisposition
					? contentDisposition
							.split( 'filename=' )[ 1 ]
							.replace( /"/g, '' )
					: filename;

				return response.blob();
			} )
			.then( ( blob ) => {
				const url = window.URL.createObjectURL( blob );
				const a = document.createElement( 'a' );
				a.style.display = 'none';
				a.href = url;

				a.download = filename;
				document.body.appendChild( a );
				a.click();

				// Clean up
				window.URL.revokeObjectURL( url );
				document.body.removeChild( a );
			} )
			.catch( ( error ) => {
				console.error( 'Export failed:', error );
			} );
	};

	headingElement.insertAdjacentElement( 'afterend', button );
}

/**
 * Add copy to clipboard action.
 *
 * @param {HTMLLabelElement} root
 */
function createCopyToClipboardBtn( root ) {
	const copyButton = document.createElement( 'button' );
	copyButton.className = 'rcf-7-copy-field button button-link';
	copyButton.type = 'button';
	copyButton.title = window.cf7rData?.labels.copy;

	// Create icon element
	const icon = document.createElement( 'span' );
	icon.className = 'dashicons dashicons-clipboard';
	copyButton.appendChild( icon );

	copyButton.onclick = function ( e ) {
		e.preventDefault();
		const fieldWrap = this.closest( '.field-wrap' );
		if ( ! fieldWrap ) {
			return;
		}

		const fieldValue =
			fieldWrap.querySelector( 'input, textarea, select' )?.value || '';
		navigator.clipboard
			.writeText( fieldValue )
			.then( () => {
				icon.className = 'dashicons dashicons-yes';
				setTimeout( () => {
					icon.className = 'dashicons dashicons-clipboard';
				}, 1000 );
			} )
			.catch( ( err ) => {
				console.error( 'Failed to copy text: ', err );
			} );
	};
	root.appendChild( copyButton );
}

/**
 * Initialize the action for Entry fields display.
 */
function initEntryFieldActions() {
	// NOTE: Omit file fields.
	document
		.querySelectorAll(
			'#wpcf7r_leads .field-wrap:not(:has(.rcf7-file-download-container)) label'
		)
		.forEach( ( labelElem ) => {
			createCopyToClipboardBtn( labelElem );
		} );
}

/**
 * Displays an error message within the designated error container for a field.
 *
 * @param {HTMLElement} element An element within the field wrap (e.g., the button).
 * @param {string} message The error message to display.
 */
function displayErrorInContainer( element, message ) {
	const fieldWrap = element.closest( '.field-wrap' );
	if ( ! fieldWrap ) return;

	const errorContainer = fieldWrap.querySelector(
		'.rcf7-file-error-container'
	);
	if ( ! errorContainer ) return;

	// Clear previous content and wrap the new message in a <p> tag
	errorContainer.innerHTML = message ? `<p>${ message }</p>` : '';
	errorContainer.style.display = message ? 'block' : 'none'; // Show/hide based on message

	// Clear the error after 5 seconds if a message was set
	if ( message ) {
		setTimeout( () => {
			errorContainer.innerHTML = '';
			errorContainer.style.display = 'none';
		}, 5000 );
	}
}

/**
 * Fetches a file blob and its filename from the API.
 *
 * @param {string} fileKey The key of the file to fetch.
 * @param {string} entryPostId The ID of the entry post.
 * @returns {Promise<{blob: Blob}>} A promise that resolves with the blob and filename.
 */
async function fetchFileBlob( fileKey, entryPostId ) {
	const path = `${ window.cf7rData.endpoints.downloadFile }?file_key=${ fileKey }&entry_post_id=${ entryPostId }`;

	const response = await apiFetch( {
		path: path,
		method: 'GET',
		parse: false,
	} );

	// Check for API errors indicated by response status or specific headers/body content if applicable
	if ( ! response.ok ) {
		// Try to parse error message from response body if it's JSON
		let errorMessage = `HTTP error! status: ${ response.status }`;
		try {
			const errorData = await response.json();
			errorMessage = errorData.message || errorMessage;
		} catch ( e ) {
			// If response is not JSON or parsing fails, use the status text
			errorMessage = response.statusText || errorMessage;
		}
		throw new Error( errorMessage );
	}

	const blob = await response.blob();
	return { blob };
}

/**
 * Handles errors from file download or preview actions.
 * Logs the error, extracts a user-friendly message, and displays it.
 *
 * @param {Error|Response|string} error The error object or message.
 * @param {HTMLElement} buttonElement The button that triggered the action.
 * @param {string} actionType A string indicating the action type (e.g., 'Download', 'Preview') for logging.
 */
async function handleFileActionError( error, buttonElement, actionType ) {
	console.error( `${ actionType } failed:`, error ); // Log the original error

	let errorMessage =
		error?.message ||
		window.cf7rData?.labels?.error ||
		'An unknown error occurred';

	// Attempt to get a more specific message if the error is a Response object
	if ( error instanceof Response ) {
		try {
			// Clone response to avoid consuming body if needed elsewhere or if parsing fails
			const errorData = await error.clone().json();
			errorMessage = errorData.message || errorMessage; // Use message from JSON if available
		} catch ( parseError ) {
			console.error( 'Failed to parse error response body:', parseError );
			// Fallback to status text if JSON parsing fails or response isn't JSON
			errorMessage = error.statusText || errorMessage;
		}
	} else if ( typeof error === 'string' ) {
		// If the error is just a string, use it directly
		errorMessage = error;
	}

	// Construct the final message to display
	const displayMessage = `${
		window.cf7rData?.labels?.error || 'Error'
	}: ${ errorMessage }`;

	// Display the extracted or default error message
	displayErrorInContainer( buttonElement, displayMessage );
}

function initDownloadButtons() {
	const entryPostId = document.querySelector( '#post_ID' )?.value;
	if ( ! entryPostId ) {
		return;
	}

	document
		.querySelectorAll( 'button.rcf7-download-btn' )
		.forEach( ( btn ) => {
			const fileKey = btn.dataset.fileKey;
			const fileType = btn.dataset.fileType;
			const fileName = btn.dataset.fileName;

			// Add preview button for image files
			const imageTypes = [
				'png',
				'jpeg',
				'jpg',
				'gif',
				'webp',
				'svg',
				'bmp',
			];

			if ( fileType && imageTypes.includes( fileType.toLowerCase() ) ) {
				const previewBtn = document.createElement( 'button' );
				previewBtn.className =
					'rcf7-preview-btn button button-secondary';
				previewBtn.textContent =
					window.cf7rData?.labels?.preview || 'Preview';
				previewBtn.style.marginLeft = '5px';

				previewBtn.addEventListener( 'click', async ( e ) => {
					e.preventDefault();
					displayErrorInContainer( previewBtn, '' ); // Clear previous errors

					const filePreviewContainer = btn
						.closest( '.field-wrap' )
						?.querySelector( '.rcf7-file-preview-container' );
					if ( ! filePreviewContainer ) return;

					// Check if preview is already open
					const existingPreview = filePreviewContainer.querySelector(
						'.rcf7-image-preview'
					);
					if ( existingPreview ) {
						// Toggle visibility (remove it)
						existingPreview.remove();
						previewBtn.textContent =
							window.cf7rData?.labels?.preview || 'Preview'; // Reset text
						return;
					}

					// Show loading state
					previewBtn.textContent = `${
						window.cf7rData?.labels?.closePreview ?? 'Loading'
					}...`;
					previewBtn.disabled = true;

					try {
						const { blob } = await fetchFileBlob(
							fileKey,
							entryPostId
						);

						// Create image preview
						const previewContainer =
							document.createElement( 'div' );
						previewContainer.className = 'rcf7-image-preview';
						previewContainer.style.marginTop = '10px';

						const img = document.createElement( 'img' );
						img.src = URL.createObjectURL( blob );
						img.style.maxWidth = '100%';
						img.style.maxHeight = '300px';
						img.onload = () => URL.revokeObjectURL( img.src ); // Clean up object URL when image loads.

						previewContainer.appendChild( img );

						// Clear previous previews and add new one.
						filePreviewContainer.innerHTML = '';
						filePreviewContainer.appendChild( previewContainer );

						previewBtn.textContent =
							window.cf7rData?.labels?.closePreview ||
							'Close Preview';
					} catch ( error ) {
						await handleFileActionError(
							error,
							previewBtn,
							'Preview'
						);
						// Reset button text specifically for preview on error
						previewBtn.textContent =
							window.cf7rData?.labels?.preview || 'Preview';
					} finally {
						previewBtn.disabled = false; // Re-enable button
					}
				} );

				// Insert the preview button after the download button
				btn.insertAdjacentElement( 'afterend', previewBtn );
			}

			if ( fileType && fileKey ) {
				btn.addEventListener( 'click', async ( e ) => {
					e.preventDefault();
					displayErrorInContainer( btn, '' ); // Clear previous errors

					// Add loading state maybe?
					const originalText = btn.textContent;
					btn.textContent = 'Downloading...';
					btn.disabled = true;

					try {
						const { blob } = await fetchFileBlob(
							fileKey,
							entryPostId
						);

						const url = window.URL.createObjectURL( blob );
						const a = document.createElement( 'a' );
						a.style.display = 'none';
						a.href = url;
						a.download = fileName;

						document.body.appendChild( a );
						a.click();

						// Clean up
						window.URL.revokeObjectURL( url );
						document.body.removeChild( a );
					} catch ( error ) {
						await handleFileActionError( error, btn, 'Download' );
					} finally {
						// Restore button state
						btn.textContent = originalText;
						btn.disabled = false;
					}
				} );
			}
		} );
}

document.addEventListener( 'DOMContentLoaded', function () {
	initExportButtons();
	initEntryFieldActions();
	initDownloadButtons();
} );
