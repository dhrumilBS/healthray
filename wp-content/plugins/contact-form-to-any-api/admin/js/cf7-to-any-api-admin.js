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
		$('#cf7anyapi_selected_form').on('change',function(){
			var form_id = $(this).val();
			var post_id = $('#post_ID').val();
			var data = {
				'form_id': form_id,
				'post_id': post_id,
	            'action': 'cf7_to_any_api_get_form_field'
			};

			var cf7anyapi_response = cf7anyapi_ajax_request(data);
			cf7anyapi_response.done(function(result){
				var json_obj = JSON.parse(result);
                $('#cf7anyapi-form-fields').html(json_obj);
			});
		});
		
		$('.post-type-cf7_to_any_api #publish').on('click',function(){
			if($("#title").val().replace( / /g, '' ).length === 0){
				window.alert('A title is required.');
				$('#major-publishing-actions .spinner').hide();
				$('#major-publishing-actions').find(':button, :submit, a.submitdelete, #post-preview').removeClass('disabled');
				$("#title").focus();
				return false;
			}
		});

		$('.cf7anyapi_bulk_log_delete').on('click',function(){
			if(confirm("Are you Sure you want to delete all logs records?") == true){
				var cf_to_any_api_log_del_nonce = jQuery(".cf7_to_any_api_page_cf7anyapi_logs #cf_to_any_api_log_del_nonce").val();
				var data = {
		                'action': 'cf7_to_any_api_bulk_log_delete',
		                'cf_to_any_api_log_del_nonce' : cf_to_any_api_log_del_nonce,
		            };

				var cf7anyapi_response = cf7anyapi_ajax_request(data);
				cf7anyapi_response.done(function(result){
					window.location.reload();
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
							            url:ajax_object.ajax_url,
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
	});

})( jQuery );
function cf7anyapi_ajax_request(cf7anyapi_data){
	return jQuery.ajax({
            type: "POST",
            url: ajax_object.ajax_url,
            data: cf7anyapi_data,
        });
}
