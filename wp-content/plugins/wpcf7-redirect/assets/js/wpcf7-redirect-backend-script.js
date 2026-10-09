// @ts-check

import '../css/wpcf-redirect-backend-style.scss';

import './wpcf7-redirect-extensions.js';
import { Wpcf7_admin_validations } from './wpcf7-redirect-validation.js';

let wpcf7_redirect_admin;

(function ( /** @type {import("@types/jquery")} */ $) {
	let addNewActionAutoOpenTimeout = null;

	/**
	 * Get the default setting for TinyMCE editor.
	 * 
	 * @see wp_enqueue_editor()
	 * 
	 * @returns
	 */
	function getDefaultTinyMCEConfig() {
		return {
			plugins: "charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern,wpview",
			toolbar1: "formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,fullscreen,wp_adv",
			toolbar2: "strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help",
			wpautop: true,
			entity_encoding: "raw",
			convert_urls: false,
			branding: false,
			menubar: false,
			fix_list_elements: true,
			entities: "38,amp,60,lt,62,gt",
			end_container_on_empty_block: true,
			relative_urls: false,
			resize: "vertical",
			remove_script_host: false,
			wpeditimage_html5_captions: true,
			theme: "modern",
			keep_styles: false,
			formats: {
				alignleft: [
					{selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: {textAlign:"left"}},
					{selector: "img,table,dl.wp-caption", classes: "alignleft"}
				],
				aligncenter: [
					{selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: {textAlign:"center"}},
					{selector: "img,table,dl.wp-caption", classes: "aligncenter"}
				],
				alignright: [
					{selector: "p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li", styles: {textAlign:"right"}},
					{selector: "img,table,dl.wp-caption", classes: "alignright"}
				],
				strikethrough: {inline: "del"}
			}
		}
	}

	/**
	 * Get the configuration of TinyMCE.
	 * 
	 * Return an existing configuration, otherwise get a default one. 
	 * 
	 * @returns 
	 */
	function getTinyMCEConfig() {
		
		if (
			typeof tinyMCEPreInit !== 'undefined' && 
			typeof tinyMCEPreInit.mceInit === 'object' && 
			Object.keys(tinyMCEPreInit.mceInit).length > 0
		) {
			
			const editorKeys = Object.keys(tinyMCEPreInit.mceInit).filter(key => key.startsWith('editor-'));
			
			if (editorKeys.length > 0) {
				return tinyMCEPreInit.mceInit[editorKeys[0]];
			}
		}
		
		return getDefaultTinyMCEConfig();
	}

	/**
	 * Get the default setting for TinyMCE quick tags editor.
	 * 
	 * @see wp_enqueue_editor()
	 * 
	 * @returns
	 */
	function getDefaultQuickTagsConfig() {
		return {
			buttons: "strong,em,link,block,del,ins,img,ul,ol,li,code,more,close"
		}
	}

	/**
	 * Main admin class for Contact Form 7 Redirection plugin
	 * 
	 * Handles all admin interface functionality including action management, 
	 * form display, field validations, AJAX operations, and UI interactions.
	 * 
	 * @class
	 * @constructor
	 * @requires jQuery
	 * @requires wp.media
	 * @requires tinymce
	 * @requires quicktags
	 */
	function Wpcf7_redirect_admin() {

		/**
		 * Initialize the class and set up the admin functionality
		 * 
		 * Sets up all necessary parameters, hooks, and initial UI state
		 * for the Contact Form 7 Redirection plugin admin interface.
		 * 
		 * @return {void}
		 */
		this.init = function () {
			this.setparams();
			//set hooks for handling the redirect settings tab
			this.admin_field_handlers();
			//set hooks
			this.register_action_hooks();
			//hide select options
			this.hide_select_options();
			//init drag and drop features
			this.init_draggable();
			this.renumber_rows();

			this.admin_validations = new Wpcf7_admin_validations($);

			this.init_select2();

			this.init_media_field();
			this.init_colorpickers();
			this.mark_default_select_fields();
		};

		/**
		 * Avoid alert while trying to leave page by setting default select values
		 * 
		 * Ensures that all select fields in action containers have a proper default value selected
		 * to prevent unwanted "unsaved changes" alerts when navigating away from the page.
		 * 
		 * @return {void}
		 */
		this.mark_default_select_fields = function () {
			$('.action-container select').each(function () {
				if ($(this).val() === 0 || !$(this).val()) {
					$(this).find('option:first-child').prop('selected', 'selected')
				}
			})
		}

		/**
		 * Initialize TinyMCE editor instances for rich text fields
		 * 
		 * @param {jQuery} editorActionWrap - The jQuery element wrapping the editor
		 * @return {void}
		 */
		this.init_editors = function (editorActionWrap) {
			if ( 'undefined' === typeof tinymce ) {
				return;
			}

			// Get all editor IDs from textareas within the editor action wrap.
			const editorIds = editorActionWrap.find('textarea').map(function() {
				return this.id ? this.id : null;
			}).get().filter(Boolean);
	
			/*
			 * Because we are sending the new actions via API equests,
			 * the TinyMCE editor is not initialized since the default initialization by WP is done only at page loading.
			 * We need to manually initialized the newly added instances.
			 * 
			 * @see https://github.com/WordPress/wordpress-develop/blob/7aae2ea200f73bee61bf095d3e1dcdaec8cc91cf/src/wp-includes/class-wp-editor.php#L1675-L1730
			*/
			editorIds.forEach( id => {
				const tinymceConfig = getTinyMCEConfig();
				tinymceConfig.selector = `#${id}`;

				const tinymceQuickTagsConfig = getDefaultQuickTagsConfig();
				tinymceQuickTagsConfig.id = id;

				window.tinymce.init( tinymceConfig );
				window.quicktags( tinymceQuickTagsConfig );
			});
		}
		
		/**
		 * Initialize WordPress color picker on all colorpicker elements
		 * 
		 * @return {void}
		 */
		this.init_colorpickers = function () {
			$('input.colorpicker').addClass('rendered').wpColorPicker();
		}

		/**
		 * Set parameters and selectors used throughout the admin interface
		 * 
		 * Defines all jQuery selectors and counters used by the admin functionality.
		 * 
		 * @return {void}
		 */
		this.setparams = function () {
			/**
			 * Define jquery selectors
			 * @type {String}
			 */
			this.banner_selector = '.wpcfr-banner-holder';
			this.add_and_selector = '.add-condition';
			this.row_template_selector = '.row-template';
			this.remove_and_selector = '.qs-condition-actions .rcf7-delete-conditional-rule';
			this.add_group_button_selector = '.wpcfr-add-group';
			this.rule_group_selector = '.wpcfr-rule-group';
			this.edit_block_title_selector = '.conditional-group-titles .dashicons-edit';
			this.cancel_block_title_selector = '.conditional-group-titles .dashicons-no';
			this.save_block_title_edit_selector = '.conditional-group-titles .dashicons-yes';
			this.tab_title_all_selector = '.block-title';
			this.tab_title_selector = '.block-title:not(.edit)';
			this.tab_title_active_selector = '.block-title.active';
			this.active_tab_selector = '.conditional-group-block.active';
			this.tab_inner_title = '.conditional-group-block-title';
			this.new_block_counter = 1;
			this.add_block_button_selector = '.wpcf7r-add-block';
			this.blocks_container_selector = '.conditional-group-blocks';
			this.remove_block_button_selector = '.remove-block';
			this.group_row_value_select_selector = '.group_row_value_select';
			this.group_select_field_selector = '.wpcf7r-fields';
			this.open_tab_selector = '#redirect-panel h3[data-tab-target]';
			this.show_action_selector = '.actions-list .edit a';
			this.move_action_to_trash_selector = '.actions-list .row-actions .trash a';
			this.dupicate_action_selector = '.actions-list .row-actions .duplicate a';
			this.move_lead_to_trash_selector = '.leads-list .trash a';
			this.custom_checkbox_selector = '.wpcf7r-checkbox input';
			this.action_title_field = '.wpcf7-redirect-post-title-fields';
			this.migrate_from_cf7_api_selector = '.migrate-from-send-to-api';
			this.migrate_from_cf7_redirect_selector = '.migrate-from-redirection';
			this.json_textarea_selector = '.json-container';
			this.butify_button_selector = '.wpcf7-redirect-butify';
			this.add_repeater_field_selector = '.qs-repeater-action .dashicons-plus';
			this.remove_repeater_field_selector = '.qs-repeater-action .dashicons-minus';
			this.api_test_button_selector = '.wpcf7-redirect-test_button-fields';
			this.toggler_handler_selector = '.actions-list [data-toggle] input';
			this.select_toggler_selector = '[data-toggler-name]';
			this.select_action_selector = '[name="new-action-selector"]';
			this.mailchimp_get_lists = '.wpcf7-redirect-get_mailchimp_lists-fields';
			this.mailchimp_create_list = '.wpcf7-redirect-create_list-fields';
			this.mailchimp_list_selector = '.field-wrap-mailchimp_list_id select';
			this.tab_actions_selector = '[href="#redirect-panel"]';
			this.pro_banner_submit_btn_selector = '.btn-rp-submit';
			this.pro_banner_user_email_selecttor = '[name="rp_user_email"]';
			this.new_group_counter = 0;
			this.reset_all_button = '.cf7-redirect-reset';
			this.new_row_counter = 0;
			this.mail_tags_toggle = '.mail-tags-title';
			this.validate_salesforce_app_details = '.wpcf7-redirect-validate_connection-fields';
			this.debug_send_button_selector = '.send-debug-info';
			this.close_popup_button_selector = '.wpcfr-close-popup';

		}

		/**
		 * Initialize Select2 fields throughout the admin interface
		 * 
		 * Applies the Select2 jQuery plugin to all uninitialized select fields
		 * with the select2-field class to enhance the user experience.
		 * 
		 * @return {void}
		 */
		this.init_select2 = function (e) {
			$('.select2-field select:not(.rendered)').each(function () {
				const options = {
					width: 'resolve'
				};
				$('.select2-field select:not(.rendered)').select2(options).addClass('rendered');
			});
		}

		/**
		 * Initialize WordPress media uploader for image/file fields
		 * 
		 * Sets up the WordPress media library integration for all media upload fields,
		 * allowing users to select or upload files via the media library.
		 * 
		 * @return {void}
		 */
		this.init_media_field = function () {
			let imgContainer, imgIdInput = '';

			if (typeof wp.media == 'undefined') {
				console.log('no media support');
				return;
			}
			
			const file_frame = wp.media.frames.file_frame = wp.media({
				frame: 'post',
				state: 'insert',
				multiple: false
			});

			file_frame.on('insert', function (e) {
				// Get media attachment details from the frame state
				const attachment = file_frame.state().get('selection').first().toJSON();

				if (imgContainer.hasClass('file-container')) {
					imgContainer.find('.file-url').val(attachment.url);
				} else {
					imgContainer.find('.popup-image').remove();
					// Send the attachment URL to our custom image input field.
					imgContainer.prepend('<img src="' + attachment.url + '" alt="" style="max-width:100px;" class="popup-image"/>');
				}

				// Send the attachment id to our hidden input
				imgIdInput.val(attachment.url).change();
			});

			$(document.body).on('click', '.image-uploader-btn', function () {
				imgIdInput = $(this).parent().find('input[type=hidden]');
				imgContainer = $(this).parent();
				file_frame.open();
			});

			$(document.body).on('click', '.image-remove-btn, .input-remove-btn', function () {
				imgIdInput = $(this).parent().find('input[type=hidden]');
				imgContainer = $(this).parent();

				if (imgContainer.hasClass('file-container')) {
					imgContainer.find('.file-url').val('');
				} else {
					imgContainer.find('img').remove();
				}

				imgIdInput.val('');
			});
		}

		/**
		 * Beautify the user input JSON or XML for better readability
		 * 
		 * Formats JSON or XML code in textareas to improve readability
		 * by adding proper indentation and structure.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.beutify_json_and_css = function (e) {
			e.preventDefault();
			const button = $(e.currentTarget);
			const hiddenActionParent = button.parents('.hidden-action');
			const record_type = hiddenActionParent.find('.field-wrap-record_type select').val();
			this.remove_errors();
			const textareaElement = $('textarea', hiddenActionParent);
			let string = textareaElement.val();
			try {
				if (record_type == 'json') {
					var json_object = jQuery.parseJSON(string);
					if (json_object) {
						string = JSON.stringify(json_object, null, "\t");
						if (string) {
							textareaElement.val(string);
						}
					}
				} else if (record_type == 'xml') {
					var xml_object = jQuery.parseXML(string);
					if (xml_object) {
						var xmlString = (new XMLSerializer()).serializeToString(xml_object);
						textareaElement.val(xmlString);
					}
				}
			} catch (err) {
				this.add_error(textareaElement, err);
			}
		}

		/**
		 * Initialize sortable elements for drag-and-drop functionality
		 * 
		 * Sets up jQuery UI sortable on action lists to allow reordering
		 * actions via drag and drop.
		 * 
		 * @return {void}
		 */
		this.init_draggable = function () {
			var _this = this;

			const fixHelper = function (e, ui) {
				ui.children().children().each(function () {
					$(this).width($(this).width());
				});
				return ui;
			};

			$('#the_list').sortable({
				'items': '.drag',
				'axis': 'y',
				'helper': fixHelper,
				'update': function (e, ui) {
					const params = {
						'order': $('#the_list').sortable('serialize')
					};
					_this.make_ajax_call('wpcf7r_set_action_menu_order', params, 'after_ajax_call');

					const actionId = $(ui.item).data('actionid');

					$(ui.item).after($('.action-container[data-actionid=' + actionId + ']'));
					_this.renumber_rows();
				}
			});

		}

		/**
		 * Replace or add query parameter to URL
		 * 
		 * Modifies a URL by either replacing the value of an existing parameter
		 * or adding a new parameter if it doesn't exist.
		 * 
		 * @param {string} uri - The URL to modify
		 * @param {string} key - The parameter name
		 * @param {string} value - The parameter value
		 * @return {string} - The modified URL
		 */
		this.replace_query_var = function (uri, key, value) {
			const re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
			const separator = uri.indexOf('?') !== -1 ? "&" : "?";
			if (uri.match(re)) {
				return uri.replace(re, '$1' + key + "=" + value + '$2');
			}
			else {
				return uri + separator + key + "=" + value;
			}
		}

		/**
		 * Renumber rows in the actions table
		 * 
		 * Updates the displayed sequence numbers after sorting
		 * or adding/removing actions.
		 * 
		 * @return {void}
		 */
		this.renumber_rows = function () {
			let numbering = 1;
			$('#the_list tr .num:visible').each(function () {
				$(this).html(numbering);
				numbering++;
			});
		}

		/**
		 * Register all the event handlers for the admin interface
		 * 
		 * Sets up jQuery event listeners for all interactive elements
		 * in the admin interface.
		 * 
		 * @return {void}
		 */
		this.register_action_hooks = function () {

			//add and rule
			$(document.body).on('click', this.add_and_selector, this.add_and_row.bind(this));
			//remove rule
			$(document.body).on('click', this.remove_and_selector, this.remove_and_row.bind(this));
			//add group
			$(document.body).on('click', this.add_group_button_selector, this.add_new_group.bind(this));
			//edit group block title
			$(document.body).on('click', this.edit_block_title_selector, this.edit_block_title.bind(this));
			//cancel group block title change
			$(document.body).on('click', this.cancel_block_title_selector, this.cancel_block_title_edit.bind(this));
			//save group block title change
			$(document.body).on('click', this.save_block_title_edit_selector, this.save_block_title_edit.bind(this));
			//change tab
			$(document.body).on('click', this.tab_title_selector, this.switch_tab.bind(this));
			//add a new rule block
			$(document.body).on('click', this.add_block_button_selector, this.add_new_block.bind(this));
			//remove block button
			$(document.body).on('click', this.remove_block_button_selector, this.remove_block.bind(this));
			//set select value
			$(document.body).on('change', this.group_row_value_select_selector, this.set_select_value.bind(this));
			//set select value
			$(document.body).on('change', this.group_select_field_selector, this.show_field_options.bind(this));
			//show/hide tabs
			$(document.body).on('click', this.open_tab_selector, this.show_hide_tab.bind(this));
			//show action
			$(document.body).on('click', this.show_action_selector, this.show_hide_action.bind(this));
			//move action to trash
			$(document.body).on('click', this.move_action_to_trash_selector, this.move_post_to_trash.bind(this));
			//move lead to trash
			$(document.body).on('click', this.move_lead_to_trash_selector, this.move_post_to_trash.bind(this));
			//after ajax call handler
			$(document.body).on('wpcf7r_after_ajax_call', this.after_ajax_call.bind(this));
			//checkbox change event
			$(document.body).on('change', this.custom_checkbox_selector, this.checkbox_changed.bind(this));
			//title change
			$(document.body).on('keyup', this.action_title_field, this.action_title_field_changed.bind(this));
			//migrate from wp7 api
			$(document.body).on('click', this.migrate_from_cf7_api_selector, this.migrate_from_cf7_api.bind(this));
			//migrate from wp7 redirect
			$(document.body).on('click', this.migrate_from_cf7_redirect_selector, this.migrate_from_cf7_api.bind(this));
			//butify json and xml
			$(document.body).on('click', this.butify_button_selector, this.beutify_json_and_css.bind(this));
			//add repeater field
			$(document.body).on('click', this.add_repeater_field_selector, this.add_repeating_field.bind(this));
			//remove repeater field
			$(document.body).on('click', this.remove_repeater_field_selector, this.remove_repeating_field.bind(this));
			//make API test
			$(document.body).on('click', this.api_test_button_selector, this.make_api_test_call.bind(this));
			//data toggler function
			$(document.body).on('change', this.toggler_handler_selector, this.data_toggler.bind(this));
			//display content according to select field
			$(document.body).on('change', this.select_toggler_selector, this.select_toggler.bind(this));
			//get mailchimp lists
			$(document.body).on('click', this.mailchimp_get_lists, this.mailchimp_get_lists_handler.bind(this));
			//create mailchimp list
			$(document.body).on('click', this.mailchimp_create_list, this.mailchimp_create_list_handler.bind(this));
			//change the selected list calback
			$(document.body).on('change', this.mailchimp_list_selector, this.mailchimp_list_select_handler.bind(this));
			//reset all plugin settings
			$(document.body).on('click', this.reset_all_button, this.reset_all_settings.bind(this));
			//toggle mail tags
			$(document.body).on('click', this.mail_tags_toggle, this.toggle_mail_tags.bind(this));
			//duplicate action
			$(document.body).on('click', this.dupicate_action_selector, this.duplicate_action.bind(this));
			//close general admin popups
			$(document.body).on('click' , this.close_popup_button_selector, this.close_popup.bind(this));
			//change selected action
			$(document.body).on('change', this.select_action_selector, this.select_action.bind(this));
			$(document.body).on('change', '.cf7r-rule-status', toggleActionStatus);
		}

		/**
		 * Close open popups
		 * 
		 * Removes popup containers from the DOM when dismissed.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.close_popup = function(e){
			$('.wpcfr-popup-wrap').remove();
		}
		/**
		 * Toggle the mail tags section visibility
		 * 
		 * Shows or hides the mail tags reference section when clicked.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.toggle_mail_tags = function (e) {
			const button = $(e.currentTarget);
			button.next().slideToggle('fast');
		}

		/**
		 * Handler for creating a new MailChimp list
		 * 
		 * Prompts the user for a list name and calls the list creation API.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void|boolean} - Returns false if validation fails
		 */
		this.mailchimp_create_list_handler = function (e) {
			const list_name = prompt("Please enter list name");
			if (list_name != null) {
				this.mailchimp_get_lists_handler(e, list_name);
			}
		}

		/**
		 * Set available MailChimp tags based on selected list
		 * 
		 * Updates the merge fields dropdowns based on the selected MailChimp list.
		 * 
		 * @param {Event} e - The change event object
		 * @return {void}
		 */
		this.mailchimp_list_select_handler = function (e) {
			const changedElement = $(e.currentTarget);
			const parentAction = changedElement.parents('.action-container').first();
			var lists = parentAction.find('.field-wrap-mailchimp_settings').data('lists');
			const selected_list = parentAction.find(this.mailchimp_list_selector).val();
			lists = this.maybe_parse_json(lists);
			
			const list_fields = lists[selected_list].list_fields;
			const merge_tags_selects = parentAction.find('.field-wrap-mailchimp_key select');

			$.each(merge_tags_selects, function (key, select) {
				$(select).html('');
				$(select).append(`<option value="">${window.rcf7Data.labels.selectField}</option>`);
				$.each(list_fields, (k, v) => {
					$(select).append('<option value="' + k + '">' + v + '</option>');
				});
			});
		}

		/**
		 * Parse JSON string or return already parsed object
		 * 
		 * Safely attempts to parse a string as JSON and returns either
		 * the parsed object or the original input if parsing fails.
		 * 
		 * @param {string|object} string - The string to parse or object to return
		 * @return {object} - The parsed object or the original input
		 */
		this.maybe_parse_json = function (string) {
			let a = '';
			try {
				a = JSON.parse(string);
			} catch (e) {
				a = string;
			}
			return a;
		}

		/**
		 * Handler for retrieving MailChimp lists
		 * 
		 * Makes an AJAX request to get lists from MailChimp API and updates
		 * the lists dropdown with the results.
		 * 
		 * @param {Event} e - The click event object
		 * @param {string} [list_name] - Optional list name for creating a new list
		 */
		this.mailchimp_get_lists_handler = function (e, list_name) {
			const button = $(e.currentTarget);
			const parentAction = button.parents('.action-container').first();
			this.remove_errors();

			if (!$('.wpcf7-redirect-mailchimp_api_key-fields').val()) {
				this.add_error('.wpcf7-redirect-mailchimp_api_key-fields', 'Add your key and save the form');
				return false;
			}

			this.show_action_loader(button);

			const params = {
				'action_id': this.get_block_action_id(button),
				'mailchimp_api_key': parentAction.find('.wpcf7-redirect-mailchimp_api_key-fields').val(),
				'list_name': list_name
			};

			this.make_ajax_call('wpcf7r_get_mailchimp_lists', params, 'after_ajax_call');
		}

		/**
		 * Display loading animation on an action container
		 * 
		 * Shows a loading spinner within the specified action container.
		 * 
		 * @param {jQuery} $inner_element - Element within the action container
		 * @return {void}
		 */
		this.show_action_loader = function ($inner_element) {
			var $action_wrap = $inner_element.parents('.field-wrap-test_section').first();
			this.show_loader($inner_element.parents('.hidden-action').first());
		}

		/**
		 * Handle toggling display based on select field changes
		 * 
		 * Shows or hides elements based on the selected value of a dropdown.
		 * 
		 * @param {Event} e - The change event object
		 * @return {void}
		 */
		this.select_toggler = function (e) {
			const selectElement = $(e.currentTarget);

			const togglerName = selectElement.data('toggler-name');
			const selectedValue = selectElement.val();

			$('.' + togglerName).hide();

			if (selectedValue) {
				$('.' + togglerName + '_' + selectedValue).show();
			}
		}

		/**
		 * Handle action type selection changes
		 * 
		 * Updates the "Add Action" button text based on whether the
		 * selected action requires purchase.
		 * 
		 * @param {Event} e - The change event object
		 * @return {void}
		 */
		this.select_action = function (e) {
			const selectElement = $(e.currentTarget);
			const action = selectElement.find(':selected').attr('data-action');
			if ('purchase' === action) {
				$('a.wpcf7-add-new-action').text( 'Get Addon' );
				return;
			}
			$('a.wpcf7-add-new-action').text( 'Add Action' );
		}

		/**
		 * Toggle visibility of elements based on checkbox state
		 * 
		 * Shows or hides target elements when a toggle checkbox changes state.
		 * 
		 * @param {Event} e - The change event object
		 * @return {void}
		 */
		this.data_toggler = function (e) {
			//prevent checkbox input from firing duplicated event but keep its original functionality
			const button = $(e.currentTarget);
			const parentAction = button.parents('.action-container').first();
			const toggleTarget = button.parents('[data-toggle]').data('toggle');
			if (toggleTarget) {
				parentAction.find(toggleTarget).slideToggle('fast');
			}
		}

		/**
		 * Execute an API test call
		 * 
		 * Makes an AJAX request to test an API connection with current settings.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.make_api_test_call = function (e) {
			e.preventDefault();
			const button = $(e.currentTarget);
			const actionWrapElement = button.parents('.field-wrap-test_section').first();
			this.show_loader(button.parents('.hidden-action').first());

			const params = {
				'action_id': button.data('action_id'),
				'cf7_id': button.data('cf7_id'),
				'rule_id': button.data('ruleid'),
				'data': actionWrapElement.find('input').serialize()
			};

			this.make_ajax_call('wpcf7r_make_api_test', params, 'after_ajax_call');
		}

		/**
		 * Remove a repeating field row
		 * 
		 * Deletes a row from a repeater field group.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.remove_repeating_field = function (e) {
			e.preventDefault();
			const button = $(e.currentTarget);
			button.parents('.qs-repeater-row').remove();
		}

		/**
		 * Add a new repeating field row
		 * 
		 * Creates a new row in a repeater field group based on a template.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.add_repeating_field = function (e) {
			e.preventDefault();
			const button = $(e.currentTarget);
			const parentElement = button.parents('[data-repeater-template]');
			let newRowCount = parentElement.find('[data-repeater-row-count]').last().data('repeater-row-count');
			newRowCount++;
			let template;
			const templateString = parentElement.data('repeater-template');
			try {
				template = JSON.parse(templateString);
			} catch (error) {
				console.error('Failed to parse repeater template JSON:', error, 'Template string:', templateString);
				return;
			}
			const template_html = this.replaceAll(template.template, 'new_row', newRowCount);
			parentElement.append(template_html);
			$(document.body).trigger('added-repeating-row', [parentElement]);
		}

		/**
		 * Migrate settings from other Contact Form 7 extension plugins
		 * 
		 * Imports settings from CF7 API or CF7 Redirect plugins.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.migrate_from_cf7_api = function (e) {
			e.preventDefault();
			const button = $(e.currentTarget);
			this.show_loader(button.parents('.actions-list'));
			const params = {
				'post_id': button.data('id'),
				'rule_id': button.data('ruleid'),
				'action_type': button.data('migration-type'),
			};

			this.make_ajax_call('wpcf7r_add_action', params, 'after_ajax_call');

			button.fadeOut(function () {
				$(this).remove();
			});
		}

		/**
		 * Update action title in list when field value changes
		 * 
		 * Synchronizes the displayed action title with the input field value.
		 * 
		 * @param {Event} e - The keyup event object
		 * @return {void}
		 */
		this.action_title_field_changed = function (e) {
			e.preventDefault();
			const changedTitle = $(e.currentTarget);
			const actionId = this.get_block_action_id(changedTitle);
			$('.primary[data-actionid="' + actionId + '"] .column-post-title').html(changedTitle.val());
		}

		/**
		 * Handle checkbox state changes and update related elements
		 * 
		 * Updates toggle-dependent elements when checkbox state changes.
		 * 
		 * @param {Event} e - The change event object
		 * @return {void}
		 */
		this.checkbox_changed = function (e) {
			e.preventDefault();
			const button = $(e.currentTarget);
			const checkbox_on = button.is(':checked');
			const hiddenButtonParent = button.parents('.hidden-action');
			if (button.data('toggle-label')) {
				const toggle_data = button.data('toggle-label');
				jQuery.each(toggle_data, function (css_class, toggle) {
					let string = '';
					if (checkbox_on) {
						string = toggle[0];
					} else {
						string = toggle[1];
					}
					hiddenButtonParent.find(css_class).html(string);
				});
			}
		}

		/**
		 * Handle adding a new action
		 * 
		 * Creates a new action of the selected type.
		 * 
		 * @param {string} contactFormId - The Contact Form ID.
		 * @param {string} ruleId - The Rule ID.
		 * @param {string} actionType - The action type to created.
		 * @return {void} - Returns if validation fails
		 */
		this.add_new_action = function (contactFormId, ruleId, actionType) {
			const params = {
				'post_id': contactFormId,
				'rule_id': ruleId,
				'action_type': actionType,
			};
			
			this.remove_errors();
			this.show_loader( $('.wpcf7r-tab-wrap-inner .actions-list') );
			$('.hidden-action').slideUp('fast');
			this.make_ajax_call('wpcf7r_add_action', params, 'after_ajax_call');
		}

		/**
		 * Remove error messages from the form
		 * 
		 * Clears all error messages and styling from form fields.
		 * 
		 * @return {void}
		 */
		this.remove_errors = function () {
			$('.error-message').removeClass('error-message');
			$('.error-label').remove();
		}

		/**
		 * Add error message to a form field
		 * 
		 * Attaches an error message to the specified selector element.
		 * 
		 * @param {string} selector - CSS selector for the target element
		 * @param {string} message - Error message to display
		 * @return {void}
		 */
		this.add_error = function (selector, message) {
			$(selector).addClass('error-message').after('<span class="error-label">' + message + '</span>');
		}

		/**
		 * Display loading animation overlay
		 * 
		 * Shows a loading spinner overlay on the specified element.
		 * 
		 * @param {jQuery|Element} selector - Element to overlay with the loader
		 * @return {void}
		 */
		this.show_loader = function (selector) {
			$(selector).append('<div class="wpcf7r_loader"></div>');
			$('.wpcf7r_loader').addClass('active');
		}

		/**
		 * Remove loading animation overlay
		 * 
		 * Fades out and removes any active loading spinners.
		 * 
		 * @return {void}
		 */
		this.hide_loader = function () {
			$('.wpcf7r_loader').fadeOut(function () {
				$(this).remove();
			});
		}

		/**
		 * Callback function executed after AJAX operations complete
		 * 
		 * Handles various responses from different AJAX actions and updates the UI.
		 * 
		 * @param {Event} e - The event object
		 * @param {Object} params - Parameters sent with the AJAX request
		 * @param {Object} response - Response data from the server
		 * @param {string} action - The AJAX action that was performed
		 * @return {void}
		 */
		this.after_ajax_call = function (e, params, response, action) {
			var _this = this;
			let actionWrapElement;

			/**
			 * Handle action delete request
			 */
			if ('wpcf7r_delete_action' === action) {
				$(params).each(function (k, v) {
					$('[data-actionid="' + v.post_id + '"]').fadeOut(function () {
						$(this).remove();
						_this.renumber_rows();
					});
					$('[data-postid="' + v.post_id + '"]').fadeOut(function () {
						$(this).remove();
					});
				});
			}

			if ('wpcf7r_add_action' === action || 'wpcf7r_duplicate_action' === action) {
				$('[data-wrapid=' + params.rule_id + '] #the_list:visible').append(response.action_row);

				const newActionWrapElement = $('[data-wrapid=' + params.rule_id + '] #the_list > tr.action-container').last();

				_this.init_select2();
				_this.renumber_rows();
				_this.init_colorpickers();
				_this.init_editors(newActionWrapElement);

				// Open the created action.
				Array.from( document.querySelectorAll('#the_list > tr:not(.action-container):has(.row-actions)') )
					.at(-1)
					?.querySelector('.row-actions .edit a')
					?.click();
			}

			if ('wpcf7r_reset_settings' === action) {
				window.location.reload();
			}
		
			if ('wpcf7r_make_api_test' === action) {
				actionWrapElement = $('[data-actionid=' + params.action_id + '] .field-wrap-test_section');
				$('span.err').remove();
				if (typeof response.status != 'undefined' && response.status === 'failed') {
					$.each(response.invalid_fields, function (field_key, error) {
						actionWrapElement.find('.' + field_key).append('<span class="err">' + error.reason + '</span>');
					});
				} else {
					$('body').append(response.html);
				}
			}
		
			if ('wpcf7r_get_mailchimp_lists' === action) {
				actionWrapElement = $('[data-actionid=' + params.action_id + ']');
				const listsSelectElement = actionWrapElement.find('.field-wrap-mailchimp_list_id select');
				const apiKeyInput = actionWrapElement.find('.field-wrap-mailchimp_api_key');
				listsSelectElement.html('');
				if (typeof response.error != 'undefined' && response.error) {
					this.add_error(apiKeyInput, response.error);
				} else {
					actionWrapElement.find('.field-wrap-mailchimp_settings')
						.attr('data-lists', JSON.stringify(response.lists))
						.data('lists', JSON.stringify(response.lists));

					$.each(response.lists, function (k, v) {
						var o = '<option value="' + v.list_id + '">' + v.list_name + '</option>';
						listsSelectElement.append(o);
					});

					listsSelectElement.change();
				}
			}
			this.hide_loader();
		}

		/**
		 * Duplicate an existing action
		 * 
		 * Creates a copy of an existing action with all its settings.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.duplicate_action = function (e) {
			e.preventDefault();
			const button = $(e.currentTarget);

			this.show_loader(button.parents('td'));

			const params = {
				'post_id': button.data('id'),
				'form_id': $('#post_ID').val(),
				'rule_id': button.data('ruleid'),
			};

			this.make_ajax_call('wpcf7r_duplicate_action', params, 'after_ajax_call');
		}

		/**
		 * Move a post (action or lead) to trash
		 * 
		 * Deletes an action or lead via AJAX.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.move_post_to_trash = function (e) {
			e.preventDefault();
			const button = $(e.currentTarget);
			this.show_loader(button.parents('td'));

			const params = [{
				'post_id': button.data('id')
			}];

			this.make_ajax_call('wpcf7r_delete_action', params, 'ater_ajax_delete');
		}

		/**
		 * Toggle visibility of an action's settings panel
		 * 
		 * Shows or hides the configuration panel for an action.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.show_hide_action = function (e) {
			e.preventDefault();
			const button = $(e.currentTarget);
			const hiddenActionToShow = button.parents('tr').next().find('.hidden-action');
			$('.hidden-action').not(hiddenActionToShow).slideUp('fast');
			hiddenActionToShow.slideToggle('fast');
		}

		/**
		 * Toggle visibility of a settings tab
		 * 
		 * Shows or hides a tab panel when its tab header is clicked.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.show_hide_tab = function (e) {
			const tabElement = $(e.currentTarget);
			var target = tabElement.data('tab-target');
			tabElement.toggleClass('active');
			$('[data-tab=' + target + ']').slideToggle('fast');
		}

		/**
		 * Hide irrelevant select options on page load
		 * 
		 * Triggers change events on fields to ensure proper visibility state.
		 * 
		 * @param {Event} [e] - Optional event object
		 * @return {void}
		 */
		this.hide_select_options = function (e) {
			$('.row-template .wpcf7r-fields').each(function () {
				$(this).trigger('change');
			});
		}

		/**
		 * Show field options based on selected field type
		 * 
		 * Updates comparison options and value fields based on the selected field.
		 * 
		 * @param {Event} e - The change event object
		 * @return {void}
		 */
		this.show_field_options = function (e) {
			const selectElement = $(e.currentTarget);
			const templateRow = selectElement.parents('.row-template');
			let elementToShow = "";

			if (selectElement.val()) {
				elementToShow = templateRow.find('.group_row_value[data-rel=' + selectElement.val() + ']');
			}

			templateRow.find('.group_row_value').hide();

			if (elementToShow.length) {
				elementToShow.show();
				templateRow.find('.compare-options option').hide();
				templateRow.find('.compare-options option[data-comparetype=select]').show();
			} else {
				templateRow.find('.compare-options option').show();
				templateRow.find('.wpcf7-redirect-value').show();
			}
		}

		/**
		 * Update hidden input with select field value
		 * 
		 * Syncs the value of a visible select with a hidden input field.
		 * 
		 * @param {Event} e - The change event object
		 * @return {void}
		 */
		this.set_select_value = function (e) {
			const selectElement = $(e.currentTarget);
			selectElement.siblings('.wpcf7-redirect-value').val(selectElement.val());
		}

		/**
		 * Remove a rule block from the DOM
		 * 
		 * Deletes a condition block and its associated tab.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.remove_block = function (e) {
			e.preventDefault();
			const button = $(e.currentTarget);
			const buttonParent = button.parents('.block-title').first();
			var tabToRemoveId = buttonParent.data('rel');
			buttonParent.prev().click();
			$('.conditional-group-block[data-block-id=' + tabToRemoveId + ']').remove();
			$('.block-title[data-rel=' + tabToRemoveId + ']').remove();
		}

		/**
		 * Add a new condition block to the DOM
		 * 
		 * Creates a new condition block and its associated tab.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 * 
		 * @requires wpcfr_template
		 */
		this.add_new_block = function (e) {
			this.new_block_counter++;

			const button = $(e.currentTarget);
			const action_id = this.get_block_action_id(button);

			let html_block_template = wpcfr_template.block_html;
			let block_title_html = wpcfr_template.block_title_html;

			html_block_template = this.replaceAll(html_block_template, 'new_block', 'block_' + this.new_block_counter);
			html_block_template = this.replaceAll(html_block_template, 'action_id', action_id);
			block_title_html = this.replaceAll(block_title_html, 'new_block', 'block_' + this.new_block_counter);
			block_title_html = this.replaceAll(block_title_html, 'action_id', action_id);

			$(this.tab_title_all_selector).last().after(block_title_html);
			$(this.blocks_container_selector).append(html_block_template);
			$(this.tab_title_all_selector).last().click();
		}

		/**
		 * Switch between conditional rule tabs
		 * 
		 * Activates the selected tab and displays its content.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.switch_tab = function (e) {
			e.preventDefault();
			const button = $(e.currentTarget);
			var tabToShowId = button.data('rel');
			var tabToShowElement = $('[data-block-id=' + tabToShowId + ']');
			$(this.active_tab_selector).removeClass('active');
			$(this.tab_title_active_selector).removeClass('active');
			button.addClass('active');
			tabToShowElement.addClass('active');
		}

		/**
		 * Save block title changes
		 * 
		 * Updates the block title with the user's input and exits edit mode.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.save_block_title_edit = function (e) {
			e.preventDefault();
			const button = $(e.currentTarget);
			var tabToShowId = button.data('rel');
			var tabToShowElement = $('[data-block-id=' + tabToShowId + ']');
			button.siblings('input').attr('readonly', 'readonly');
			button.parent().removeClass('edit');
			tabToShowElement.find(this.tab_inner_title).html(button.siblings('input').val());
		}

		/**
		 * Cancel block title editing
		 * 
		 * Restores original title and exits edit mode without saving changes.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.cancel_block_title_edit = function (e) {
			e.preventDefault();
			const button = $(e.currentTarget);
			var tabToShowId = button.data('rel');
			var tabToShowElement = $('[data-block-id=' + tabToShowId + ']');
			button.siblings('input').val(button.siblings('input').data('original')).attr('readonly', 'readonly');
			button.parent().removeClass('edit');
			tabToShowElement.find(this.tab_inner_title).html(button.siblings('input').val());
		}

		/**
		 * Enter block title edit mode
		 * 
		 * Makes the block title editable for the user.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.edit_block_title = function (e) {
			e.preventDefault();
			const button = $(e.currentTarget);
			button.parent().addClass('edit');
			button.siblings('input').removeAttr('readonly');
		}

		/**
		 * Add a new group of conditional rules (OR group)
		 * 
		 * Creates a new group of conditions that will be evaluated together.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.add_new_group = function (e) {
			e.preventDefault();

			const button = $(e.currentTarget);
			var blockId = 'block_1';
			var actionId = this.get_block_action_id(button);
			var ruleGroup = button.parents('.conditional-group-blocks').find('.wpcfr-rule-groups');
			this.new_group_counter = ruleGroup.find('.wpcfr-rule-group').length;
			this.new_group_counter++;
			let groupHtml = wpcfr_template.group_html;
			groupHtml = this.replaceAll(groupHtml, 'group-new_group', 'group-' + this.new_group_counter);
			groupHtml = this.replaceAll(groupHtml, 'new_group', 'group-' + this.new_group_counter);
			groupHtml = this.replaceAll(groupHtml, 'new_block', blockId);
			groupHtml = this.replaceAll(groupHtml, 'action_id', actionId);

			ruleGroup.append(groupHtml);
		}

		/**
		 * Remove a condition row from a rule group
		 * 
		 * Removes an AND condition from a group or the entire group if it's the last condition.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.remove_and_row = function (e) {
			e.preventDefault();
			const button = $(e.currentTarget);
			if (button.parents(this.rule_group_selector).find('.row-template').length == 1) {
				button.parents(this.rule_group_selector).remove();
			} else {
				button.parents(this.row_template_selector).remove();
			}
		}

		/**
		 * Get the action ID from an element within an action container
		 * 
		 * Finds the parent action container and returns its action ID.
		 * 
		 * @param {jQuery} innerItemElement - Element within an action container
		 * @return {string|number} The action ID
		 */
		this.get_block_action_id = function (innerItemElement) {
			return innerItemElement.parents('[data-actionid]').data('actionid');
		}

		/**
		 * Add a new condition row (AND condition)
		 * 
		 * Creates a new row for defining an additional condition within a group.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.add_and_row = function (e) {
			e.preventDefault();

			const button = $(e.currentTarget);
			const action_id = this.get_block_action_id(button);
			const block_id = 'block_1';
			const group_id = button.parents('[data-group-id]').first().data('group-id');

			if (!this.new_row_counter) {
				const repeaterBlock = button.parents('.repeater-table');
				this.new_row_counter = repeaterBlock.find('.row-template').length;
			}

			this.new_row_counter++;

			let rowHtml = wpcfr_template.row_html;
			rowHtml = this.replaceAll(rowHtml, 'new_block', block_id);
			rowHtml = this.replaceAll(rowHtml, 'new_group', group_id);
			rowHtml = this.replaceAll(rowHtml, 'new_row', 'row-' + this.new_row_counter);
			rowHtml = this.replaceAll(rowHtml, 'action_id', action_id);

			button.parents('table').first().find('tbody').append(rowHtml);
		}

		/**
		 * Replace all occurrences of a string
		 * 
		 * Global string replacement utility function.
		 * 
		 * @param {string} str - The input string
		 * @param {string} find - The substring to find
		 * @param {string} replace - The replacement string
		 * @return {string} The modified string
		 */
		this.replaceAll = function (str, find, replace) {
			return str.replace(new RegExp(find, 'g'), replace);
		}

		/**
		 * Initialize conditional field visibility based on saved values
		 * 
		 * Sets up initial state for fields with conditional visibility.
		 * 
		 * @return {void}
		 */
		this.admin_fields_init = function () {
			$('.field-wrap input[type=checkbox],.field-wrap select').each(function () {
				if ($(this).is(":checked")) {
					$(this).siblings('.field-notice-hidden').removeClass('field-notice-hidden').addClass('field-notice-show');
				}
			});
			$('.wpcf7-redirect-after-sent-script').each(function () {
				if ($(this).val()) {
					$(this).siblings('.field-notice-danger').removeClass('field-notice-hidden');
				}
			});
		}

		/**
		 * Set up event handlers for admin field visibility and warnings
		 * 
		 * Initializes UI behaviors for field dependencies and alert messages.
		 * 
		 * @return {void}
		 */
		this.admin_field_handlers = function () {
			this.admin_fields_init();
			// field - open in a new tab
			$(document.body).on('change', '.field-wrap input[type=checkbox],.field-wrap select', function () {
				if ($(this).is(":checked")) {
					$(this).siblings('.field-notice-hidden').removeClass('field-notice-hidden').addClass('field-notice-show');
				} else {
					$(this).siblings('.field-notice-show').addClass('field-notice-hidden').removeClass('field-notice-show');
				}
			});
			// field - after sent script
			$(document.body).on('keyup', '.wpcf7-redirect-after-sent-script', function (event) {
				if ($(this).val().length != 0) {
					$(this).siblings('.field-notice-danger').removeClass('field-notice-hidden');
				} else {
					$(this).siblings('.field-notice-danger').addClass('field-notice-hidden');
				}
			});
			$(document.body).on('change', '.checkbox-radio-1', function () {
				const checked = $(this).is(':checked');
				$('.checkbox-radio-1').prop('checked', false);
				if (checked) {
					$(this).prop('checked', true);
				}
			});
		}

		/**
		 * Reset all plugin settings to defaults
		 * 
		 * Performs a complete reset of all plugin configuration.
		 * 
		 * @param {Event} e - The click event object
		 * @return {void}
		 */
		this.reset_all_settings = function (e) {
			e.preventDefault();
			var action = 'wpcf7r_reset_settings';
			var params = [];
			// TODO: Translation
			if (confirm('Are you sure? this process will delete all of your plugin settings. There is no way back from this process!')) {
				this.make_ajax_call(action, params);
			}
		}

		/**
		 * Make an AJAX call to the WordPress admin-ajax endpoint
		 * 
		 * Generic function for making admin AJAX requests with proper nonce security.
		 * 
		 * @param {string} action - The AJAX action to perform
		 * @param {Object} params - Parameters to send with the request
		 * @return {void}
		 */
		this.make_ajax_call = function (action, params) {
			jQuery.ajax({
				type: "post",
				dataType: "json",
				url: ajaxurl,
				data: {
					action: action,
					data: params,
					wpcf7r_nonce: wpcf_get_nonce(),
				},
				success: function (response) {
					$(document.body).trigger('wpcf7r_after_ajax_call', [params, response, action]);
				}
			});
		}
		this.init();
	}

	/**
	 * Connect the toggle status in the row with action form input.
	 * 
	 * @param {JQuery.Event} event The toggle status event.
	 * @returns 
	 */
	function toggleActionStatus( event ) {
		const { actionId } = event.target.dataset;
		const actionStatusInput = document.querySelector(`.field-wrap-action_status input[type="checkbox"][name*="[${actionId}]"]`);
		
		if ( ! actionStatusInput ) {
			return;
		}

		actionStatusInput.checked = event.target.checked;
	}

	/**
	 * Init the dropdown for addin a new Action.
	 */
	function initAddActionsDropdown() {
		const addActionBtn = document.getElementById('rcf7-add-action-btn');
		const actionDropdown = document.getElementById('rcf7-action-dropdown');
		const actionItems = document.querySelectorAll('.rcf7-dropdown__action-item');
		const searchInput = document.querySelector('.rcf7-dropdown__search-input');

		if ( ! addActionBtn || ! actionDropdown ) {
			return;
		}

		const contactFormId = addActionBtn.dataset.id;
		const ruleId = addActionBtn.dataset.ruleid;
		
		addActionBtn.setAttribute('aria-expanded', 'false');
		addActionBtn.setAttribute('aria-controls', 'actionDropdown');

		// Function to position the dropdown based on available space
		const positionDropdown = () => {
			// We need to temporarily show it to measure its dimensions
			const isActive = actionDropdown?.classList.contains('active');
			if (!isActive) {
				actionDropdown?.classList.add('active');
			}
			
			// Remove any existing position classes
			actionDropdown?.classList.remove('dropdown--top', 'dropdown--bottom');
			
			const buttonRect = addActionBtn.getBoundingClientRect();
			const dropdownHeight = actionDropdown.offsetHeight;
			const windowHeight = window.innerHeight;
			const spaceBelow = windowHeight - buttonRect.bottom;
			const spaceAbove = buttonRect.top;
			
			// Reset any previous positioning
			actionDropdown.style.bottom = '';
			actionDropdown.style.top = '';
			
			if (spaceBelow >= dropdownHeight || spaceBelow > spaceAbove) {
				actionDropdown.style.top = (buttonRect.height + 10) + 'px';
				actionDropdown.classList.add('dropdown--bottom');
			} else {
				actionDropdown.style.bottom = (buttonRect.height + 10) + 'px';
				actionDropdown.classList.add('dropdown--top');
			}
			
			// Hide it again if it wasn't active before
			if (!isActive) {
				actionDropdown.classList.remove('active');
			}
		}
		
		// Toggle dropdown visibility
		addActionBtn.addEventListener('click', function(e) {
			e.stopPropagation();

			const isExpanded = addActionBtn.getAttribute('aria-expanded') === 'true';
			addActionBtn.setAttribute('aria-expanded', !isExpanded);

			if (actionDropdown.classList.contains('active')) {
				actionDropdown.classList.remove('active');
			} else {
				actionDropdown.classList.add('active');
				positionDropdown();
				setTimeout(() => searchInput?.focus(), 100);
			}
		});
		
		window.addEventListener('resize', function() {
			if (actionDropdown.classList.contains('active')) {
				positionDropdown();
			}
		});
		
		// Close dropdown when clicking outside
		document.addEventListener('click', function(event) {
			if ( ! event.target ) {
				return;
			}

			if ( !actionDropdown.contains(event.target) && !addActionBtn.contains(event.target) ) {
				actionDropdown.classList.remove('active');
				addActionBtn.setAttribute('aria-expanded', 'false');
			}
		});
		
		// Handle action selection
		actionItems.forEach(function(item) {
			if (item.hasAttribute('disabled')) {
				return;
			}

			item.addEventListener('click', function(event) {
				const actionName = event.target?.getAttribute('data-action');
				window.wpcf7_redirect_admin.add_new_action( contactFormId, ruleId, actionName );
				actionDropdown.classList.remove('active');
			});
		});
		
		// Search functionality
		searchInput?.addEventListener('input', function() {
			const searchTerm = this.value.toLowerCase().trim();
			document.querySelectorAll('.rcf7-dropdown__category-section').forEach(function(category) {
				let visibleInCategory = false;
				
				category.querySelectorAll('.rcf7-dropdown__action-item').forEach(function(item) {
					const actionName = item.textContent.trim().toLowerCase();
					
					if (actionName.includes(searchTerm) || searchTerm === '') {
						item.style.display = 'flex';
						visibleInCategory = true;
					} else {
						item.style.display = 'none';
					}
				});
				
				// Show/hide category headers based on whether they have visible items
				const categoryHeader = category.querySelector('.rcf7-dropdown__category-header');
				categoryHeader.style.display = visibleInCategory ? 'flex' : 'none';
			});
			
			positionDropdown();
		});
		
		if (window.location.href.indexOf('wpcf7r-action-menu-open=true') !== -1) {
			setTimeout(() => {
				addActionBtn.click();
				
				// Remove the parameter from URL.
				const url = new URL(window.location.href);
				url.searchParams.delete('wpcf7r-action-menu-open');
				window.history.replaceState({}, '', url);
			}, 100);
		}
		
	}

	$(document).ready(function () {
		//init the class functionality
		wpcf7_redirect_admin = new Wpcf7_redirect_admin();
		window.wpcf7_redirect_admin = wpcf7_redirect_admin;

		$(document.body).trigger('wpcf7r-loaded', wpcf7_redirect_admin);

		initAddActionsDropdown();

		// Auto-open the Actions tab if URL contains wpcf7r-tab.
		if (window.location.href.indexOf('wpcf7r-tab=true') !== -1) {
			// Use a small timeout to ensure DOM is ready
			const tabElement = $('#redirect-panel-tab');
			if (tabElement.length) {
				tabElement.trigger('click');
			}
		}
	});
})(jQuery);

function wpcf_get_nonce() {
	return jQuery('[name=actions-nonce]').val() ? jQuery('[name=actions-nonce]').val() : jQuery('[name=_wpcf7nonce]').val();
}

