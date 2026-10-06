(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	$(document).ready(function(){

		// Form Change Event
		jQuery('#cf7anyapi_selected_form').on('change',function(){
			jQuery('.cf7anyapi_loader').show();
			var form_id = jQuery(this).val();
			var post_id = jQuery('#post_ID').val();
			var data = {
				'form_id': form_id,
				'post_id': post_id,
	            'action': 'cf7_to_any_api_get_form_field'
			};

			var cf7anyapi_response = cf7anyapi_ajax_request(data);
			cf7anyapi_response.done(function(result){
				var json_obj = JSON.parse(result);
                jQuery('#cf7anyapi-form-fields').html(json_obj);
                jQuery('.cf7anyapi_loader').hide();

                //Conditional Field
                var htmlString = json_obj || json_obj || '';
		        if (!htmlString) {
		            return;
		        }
		        // Convert HTML string to DOM
		        var $parsedHtml = jQuery('<div>').html(htmlString);
		        var $select = jQuery('.cf7anyapi-condition-field');
		        $select.empty().append('<option value="">Select Field</option>');
		        $parsedHtml.find('.cf7anyapi_field').each(function () {
		            var $field = jQuery(this);
		            var $input = $field.find('input, textarea, select').first();
		            if (!$input.length) {
		                return;
		            }
		            var fieldId = $input.attr('id');
		            if (!fieldId) {
		                return;
		            }
		            var $label = $parsedHtml.find('label[for="' + fieldId + '"]');
		            if (!$label.length) {
		                return;
		            }
		            var labelText = $.trim($label.text()); 
		            $select.append('<option value="' + labelText + '">[' + labelText + ']</option>');
		        });
                
			});

			jQuery('#cf7anyapi_predefined_tag_select option:disabled').prop('disabled', false)

		});
		
		// API Save Button 
		jQuery(document).on('click', '#cf7anyapi_submit', function(e){
	      	jQuery('.post-type-cf7_to_any_api #publish').trigger('click');
	  	});
		
		// API Submit Button
		jQuery('.post-type-cf7_to_any_api #publish').on('click',function(){
			if(jQuery("#title").val().replace( / /g, '' ).length === 0){
				window.alert('A title is required.');
				jQuery('#major-publishing-actions .spinner').hide();
				jQuery('#major-publishing-actions').find(':button, :submit, a.submitdelete, #post-preview').removeClass('disabled');
				jQuery("#title").focus();
				return false;
			}
			if (jQuery("#cf7anyapi_selected_form").val() === "" || jQuery("#cf7anyapi_selected_form").val() === null) {
	            jQuery('.cf7anyapi_step ul li:nth-child(2)').trigger('click');
	        }
		});

		// Delete Logo
		$('.cf7anyapi_bulk_log_delete').on('click',function(){
			if(confirm("Are you Sure you want to delete all logs records?") == true){
				const selected = $('input[name="log_ids[]"]:checked').map(function() {
	                return this.value;
	            }).get();

	            if (selected.length === 0) {
	                alert('Please select at least one log to delete.');
	                return;
	            }
				var cf_to_any_api_log_del_nonce = jQuery(".cf7_to_any_api_page_cf7anyapi_logs #cf_to_any_api_log_del_nonce").val();

				var data = {
		                'action': 'cf7_to_any_api_bulk_log_delete',
		                'cf_to_any_api_log_del_nonce' : cf_to_any_api_log_del_nonce,
		                'cf_to_any_api_log_ids' : selected,
		            };

				var cf7anyapi_response = cf7anyapi_ajax_request(data);
				cf7anyapi_response.done(function(result){
					if (result.success) {
						//window.location.reload();
					} else {
				        alert(result.data || 'Failed to delete logs.');
				    }
				});
				cf7anyapi_response.fail(function () {
				    alert('AJAX request failed. Please try again.');
				});
			}
		});

		if($('#form_id').length){
			$('#form_id').on('change',function(){
				var value = $(this).val();
				var url = window.location.href;
				if(value != ''){
					if(url.includes('?')){
						url=url+"&form_id="+value;
					}
					else{
						url=url+"?form_id="+value;
					}
				}
				else{
					url = url.replace('form_id','');
				}
				location.assign(url);
			});
		}

		// Delete Single log data
		if(jQuery('#cf7toanyapi_table').length){
			var table = jQuery('#cf7toanyapi_table').DataTable({
				'columnDefs': [
					{
					   'targets': 0,
					   'checkboxes': {
						  'selectRow': true
					   },
					   className: 'cf7toanyapi-form-checkbox',
					}
					
				 ],
				 'select': {
					'style': 'multi'
				 },
				 'order': [[1, 'asc']],
				dom: 'Blfrtip',
			    autoWidth: false,
				scrollX: true,
				order: [],
		        buttons: [
					{
						text: 'Delete',
						className: 'cf7toanyapi-btn-delete',
						action: function(){
							var data_ids = [];
							jQuery('.cf7toanyapi_dataid.selected').each(function(i){
								data_ids.push($(this).attr('data-id'));
							});
							//console.log(array);
							var nonce = jQuery('#cf_to_any_api_entrie_del_nonce').val();
							//let data_ids = array.toString();
							if(confirm("Are you Sure you want to delete selected records?") == true)
							{

								return jQuery.ajax({
							            type: "POST",
							            url:cf7_to_any_api_ajax_object.cf7_to_any_api_ajax_url,
							            dataType: "json",
							            data:{
	      									action : 'delete_records',
	      									nonce : nonce,
								            id : data_ids,
								        },
							            success: function (data) {
	        								var status = data['status'];
	        								if(status == 1)
	        								{
	        									window.location.reload();
	        								}

	      								},
									    error: function (jqXHR, textStatus, errorThrown) {
									        console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
									    },
							       });
							}
						}
					},
		            'csv', 'excel', 'pdf', 'print'
		        ]
			});
			// merge filter ,  uttons , search into one div
			jQuery('.dt-buttons, .dataTables_length, .dataTables_filter').wrapAll( jQuery('<div>').addClass('cf7toanyapi_table_wrap') );
		}

		// Expand JSON for the log table
		jQuery('.cf7anyapi_logs .view_more').on('click', function() {
	        var logData = jQuery(this).siblings('pre').text();
		    try {
		        var parsedData = JSON.parse(logData);
		    } catch (e) {
		        var parsedData = logData; 
		    }
	        jQuery('#cf7anyapi-log-popup .cf7anyapi-log-content pre').html(syntaxHighlight(parsedData));
	        jQuery('#cf7anyapi-log-popup').fadeIn();
	    });

		function syntaxHighlight(json) {
		    if (typeof json != 'string') {
		        json = JSON.stringify(json, undefined, 2);
		    }
		    json = json
		        .replace(/&/g, '&amp;')
		        .replace(/</g, '&lt;')
		        .replace(/>/g, '&gt;');

		    return json.replace(
		        /("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|\d+)/g,
		        function (match) {
		            var cls = 'number';
		            if (/^"/.test(match)) {
		                if (/:$/.test(match)) {
		                    cls = 'key';
		                } else {
		                    cls = 'string';
		                }
		            } else if (/true|false/.test(match)) {
		                cls = 'boolean';
		            } else if (/null/.test(match)) {
		                cls = 'null';
		            }
		            return '<span class="' + cls + '">' + match + '</span>';
		        }
		    );
		}
		// Expand JSON Model close
	    jQuery('#cf7anyapi-log-popup .close-popup').on('click', function() {
	        jQuery('#cf7anyapi-log-popup').fadeOut();
	    	jQuery('#cf7anyapi-log-popup .cf7anyapi-log-content pre').empty();
	    });

	    jQuery(document).on('change', '.cf7api-status-switch .cf7api-toggle', function(e) {
	    	e.preventDefault();
	    	var $wrapper = jQuery(this).closest('.cf7api-status-switch');
		    $wrapper.find('img').show();
		    $wrapper.find('.cf7api-slider').css('opacity', '0.1');
	        let $checkbox = jQuery(this);
	        let post_id = $checkbox.data('post-id');
	        let nonce = $checkbox.data('nonce');
	        let isChecked = $checkbox.is(':checked');

	        let newStatus = $checkbox.is(':checked') ? 'enabled' : 'disabled';

	        $.post(cf7_to_any_api_ajax_object.cf7_to_any_api_ajax_url, {
	            action: 'cf7_to_any_api_toggle_status',
	            post_id: post_id,
	            is_checked: isChecked,
	            nonce: nonce
	        }, function(response) {
	            if (!response.success) {
	                $checkbox.prop('checked', ! $checkbox.is(':checked'));
	            }
	            $wrapper.find('img').hide();
	            $wrapper.find('.cf7api-slider').css('opacity', '1');
	        });
	    });

	    // Add predefined tag
	    jQuery('#cf7anyapi_add_predefined_tag').on('click', function(){
            var tag = jQuery('#cf7anyapi_predefined_tag_select').val();
            if(tag && jQuery('#cf7anyapi-form-fields').find('[data-tag="'+tag+'"]').length === 0){
            	var cleanLabel = tag.replace(/^_/, '').replace(/_/g, ' ');
               	var html = '<div class="cf7anyapi_field" data-tag="'+tag+'">' +
	               '<label for="cf7anyapi_'+tag+'">'+tag+'</label>' +
	               '<input type="text" ' +
	                   'id="cf7anyapi_'+tag+'" ' +
	                   'name="cf7anyapi_form_field['+tag+']" ' +
	                   'data-basetype="textarea" ' +
	                   'placeholder="Enter your API mapping key">' +
	               '<button type="button" class="button cf7anyapi_remove_predefined_tag"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 511.76 511.76" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M436.896 74.869c-99.84-99.819-262.208-99.819-362.048 0-99.797 99.819-99.797 262.229 0 362.048 49.92 49.899 115.477 74.837 181.035 74.837s131.093-24.939 181.013-74.837c99.819-99.818 99.819-262.229 0-362.048zm-75.435 256.448c8.341 8.341 8.341 21.824 0 30.165a21.275 21.275 0 0 1-15.083 6.251 21.277 21.277 0 0 1-15.083-6.251l-75.413-75.435-75.392 75.413a21.348 21.348 0 0 1-15.083 6.251 21.277 21.277 0 0 1-15.083-6.251c-8.341-8.341-8.341-21.845 0-30.165l75.392-75.413-75.413-75.413c-8.341-8.341-8.341-21.845 0-30.165 8.32-8.341 21.824-8.341 30.165 0l75.413 75.413 75.413-75.413c8.341-8.341 21.824-8.341 30.165 0 8.341 8.32 8.341 21.824 0 30.165l-75.413 75.413 75.415 75.435z" fill="#189b9b" opacity="1" data-original="#189b9b" class=""></path></g></svg> Remove</button>' +
	           '</div>'
                
                if( jQuery('#cf7anyapi-form-fields').text().trim() === 'No Fields Found for Selected Form.' || jQuery('#cf7anyapi-form-fields').text().trim() === 'Select Your Contact Form'){
                	jQuery('#cf7anyapi-form-fields').empty();
                }
                let lastField = jQuery('#cf7anyapi-form-fields .cf7anyapi_field:last');

				if (lastField.length) {
				    jQuery(html).insertAfter(lastField);
				} else {
				    jQuery('#cf7anyapi-form-fields').append(html);
				}

                jQuery('#cf7anyapi_predefined_tag_select option[value="'+tag+'"]').prop('disabled', true);
            }
        });

        // Remove predefined tag
        jQuery(document).on('click', '.cf7anyapi_remove_predefined_tag', function(){
            var tag = jQuery(this).parent('.cf7anyapi_field').data('tag');
            jQuery(this).closest('.cf7anyapi_field').remove();
            jQuery('#cf7anyapi_predefined_tag_select option[value="'+tag+'"]').prop('disabled', false);
            if(jQuery('#cf7anyapi-form-fields').text().trim() == ''){
            	jQuery('#cf7anyapi-form-fields').append("No Fields Found for Selected Form.");
            }
        });

        // Disable options on page load if tag already exists
        jQuery('#cf7anyapi-form-fields .cf7anyapi_field').each(function(){
            var tag = jQuery(this).data('tag');
            if(tag){
                jQuery('#cf7anyapi_predefined_tag_select option[value="'+tag+'"]').prop('disabled', true);
            }
        });

        //json Preview Model
        var modal =jQuery('#jsonPreviewModal');
	    jQuery('#jsonPreviewBtn').on('click', function(){
	    	jQuery('#jsonPreviewOutput').html('');
	        var apiUrl = jQuery('#cf7anyapi_base_url').val() || '';
		    var method = jQuery('#cf7anyapi_method').val() || '';
		    var inputType = jQuery('#cf7anyapi_input_type').val() || '';
		    var headers = jQuery('#cf7anyapi_header_request').val() || '';

		    var fields = {};
		    jQuery('#cf7anyapi-form-fields .cf7anyapi_field, #cf7anyapi_predefined_tag_fields .cf7anyapi_field').each(function() {
		        var key = jQuery(this).find('input').val(); // mapping key
		        if (!key) return; 
		        var value = jQuery(this).data('tag') || jQuery(this).find('label').text().trim();
		        fields[key] = `[${value}]`;
		    });

		    var html = `
		      <div style="font-family:monospace; font-size:14px; line-height:1.4;">
		        <p><strong>API URL:</strong> ${apiUrl}</p>
		        <p><strong>Method:</strong> ${method}</p>
		        <p><strong>Input Type:</strong> ${inputType}</p>
		        <p><strong>Headers:</strong><br><pre style="background:#f9f9f9; padding:8px; border-radius:6px;">${headers}</pre></p>
		        <p><strong>Fields JSON:</strong></p>
		        <pre style="background:#f4f4f4; padding:12px; border-radius:8px; font-size:13px; color:#333;">${JSON.stringify(fields, null, 2)}</pre>
		      </div>
		    `;

		    jQuery('#jsonPreviewOutput').html(html);
	        modal.show();
	    });
	    jQuery('.cf7anyapi-json-preview-close').on('click', function(){
	        modal.hide();
	    });
	    jQuery(window).on('click', function(e){
	        if($(e.target).is(modal)){
	            modal.hide();
	        }
	    });

	    // Multi-step form
	    if ( jQuery('#cf7anyapi_admin').length > 0) {
	    	var $step1   = jQuery('#cf7anyapi_step1');
	    	var $step2   = jQuery('#cf7anyapi_step2');
	    	// Next Button
	        jQuery('#cf7anyapi_next_btn, .cf7anyapi_step ul li:nth-child(2)').on('click', function(e) {
	        	e.preventDefault();
	        	var valid = true;
	        	$step1.find('input[required], select[required], textarea[required]').each(function() {
				    if (!this.checkValidity()) {
				        this.reportValidity();
				        valid = false;
				        return false; 
				    }
				});
				if (valid) {
		            $step1.hide();
		            $step2.show();
		            jQuery('.cf7anyapi_step ul li:nth-child(2)').addClass("active");
		            jQuery('.cf7anyapi_step ul li:nth-child(1)').removeClass("active");
		        }
	        });

	        // Previous Button
	        jQuery('#cf7anyapi_prev_btn, .cf7anyapi_step ul li:nth-child(1)').on('click', function() {
	            $step2.hide();
	            $step1.show();
	            jQuery('.cf7anyapi_step ul li:nth-child(2)').removeClass("active");
	            jQuery('.cf7anyapi_step ul li:nth-child(1)').addClass("active");
	        });
	    }

	    // Sample Header
	    jQuery(document).on('click', 'span.sample_header', function() {
            if (jQuery("#cf7anyapi_header_request").val() == '') {
                var json_format = jQuery("#cf7anyapi_header_request").attr("data-sample");
                jQuery("#cf7anyapi_header_request").val(json_format);
            }
        });
	        
	    // Hide admin notic for the upgrading to Pro 
	    if (document.cookie.indexOf("cf7anyapi_notice_dismissed=1") !== -1) {
	        jQuery(".cf7anyapi-notice-bar").hide();
	    }

	    // On close button click hide admin notic for the pro features
	    jQuery(document).on("click", ".cf7anyapi-close-btn", function () {
	        // Hide notice
	        jQuery(".cf7anyapi-notice-bar").fadeOut();
	        var expires = new Date();
	        expires.setTime(expires.getTime() + (24 * 60 * 60 * 1000)); // 1 day
	        document.cookie = "cf7anyapi_notice_dismissed=1; expires=" + expires.toUTCString() + "; path=/";
	    });

	    // Conditional Logic
	    jQuery('#cf7anyapi_enable_condition').on('change', function () {
	        jQuery('.cf7anyapi-condition-wrap').toggle(this.checked);
	    });

	});

})( jQuery );
function cf7anyapi_ajax_request(cf7anyapi_data){
	return jQuery.ajax({
        type: "POST",
        url: cf7_to_any_api_ajax_object.cf7_to_any_api_ajax_url,
        data: cf7anyapi_data,
    });
}
