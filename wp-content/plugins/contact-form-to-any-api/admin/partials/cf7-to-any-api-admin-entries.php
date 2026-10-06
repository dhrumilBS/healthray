<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>
<script src="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>

<?php
global $wpdb;
$Cf7_To_Any_Api = new Cf7_To_Any_Api();
$cf_id = filter_input(INPUT_GET, 'form_id', FILTER_VALIDATE_INT);
$cf_id = $cf_id !== null && $cf_id !== false ? intval($cf_id) : 0; 
?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Contact Form Entries', 'contact-form-to-any-api' ); ?></h1> 
	<h2 class="screen-reader-text"><?php esc_html_e( 'Filter Contact Form Entries List', 'contact-form-to-any-api' ); ?></h2>
	<div class="cf_entries" id="cf_entries">
		<form name="form_entries" id="form_entries" class="cf7toanyapi_entries" method="get">
			<label for="form_id" class="cf7toanyapi_select_form"><?php esc_html_e( 'Choose a form:', 'contact-form-to-any-api' ); ?></label>
			<select name="form" id="form_id" class="form_id cf7toanyapi_forms">
				<option value=""><?php esc_html_e( 'Select Form', 'contact-form-to-any-api' ); ?></option>
				<?php
				$posts = get_posts(
	                array(
	                    'post_type'     => 'wpcf7_contact_form',
	                    'numberposts'   => -1,
	                    'suppress_filters' => false
	                )
	            );
	            $count = 0;
	            foreach($posts as $post){
	            	$is_selected = ( $cf_id === 0 && $count == 0 ) || ( $post->ID === $cf_id );
				    if ( $cf_id === 0 && $count == 0 ) {
				        $cf_id = $post->ID;
				    }?>
	                <option value="<?php echo esc_attr($post->ID); ?>" <?php selected( $is_selected, true ); ?>>
	                	<?php echo esc_html($post->post_title.'('.$post->ID.')'); ?> 
	                </option>
	                <?php $count++;
	            } ?>
			</select>
		</form>
		<?php
			if(isset($cf_id) && $cf_id != ''){
				$result = $wpdb->get_results($wpdb->prepare(
			        'SELECT * FROM ' . $wpdb->prefix . 'cf7anyapi_entries 
			        WHERE `form_id` = %d 
			        AND data_id IN( 
			            SELECT * FROM ( 
			                SELECT data_id FROM ' . $wpdb->prefix . 'cf7anyapi_entries 
			                WHERE 1 = 1 
			                AND `form_id` = %d 
			                GROUP BY `data_id` 
			                ORDER BY `data_id` DESC
			            ) temp_table
			        ) 
			        ORDER BY `data_id` DESC',
			        $cf_id,
			        $cf_id
			    ));

				if($result){
				$data_sorted = $Cf7_To_Any_Api->cf7toanyapi_sortdata($result);
				$fields = $Cf7_To_Any_Api->cf7toanyapi_get_db_fields($cf_id);
				$display_character = (int) apply_filters('cf7toanyapi_display_character_count',500);
				$arr_field_type_info = $Cf7_To_Any_Api->cf7toanyapi_field_type_info($cf_id);
				?>				
					<div id="table_data">
						<table class="tbl table table-striped table-bordered cf7toanyapi_table" id="cf7toanyapi_table">
							<?php echo wp_kses_post( wp_nonce_field('cf_to_any_api_entrie_del_nonce','cf_to_any_api_entrie_del_nonce' ) ); ?>
							<thead>
								<tr class="cf7toanyapi_dataid_all">								
									<?php
									
											echo '<th class="manage-column">checkbox</th>';
										foreach ($fields as $k => $v){
											echo '<th class="manage-column" data-key="'.esc_attr($v).'">'.esc_html(ucfirst(str_replace('_',' ',$Cf7_To_Any_Api->cf7toanyapi_admin_get_field_name($v)))).'</th>';
										}
									?>
								</tr>
							</thead>
							<tbody>
								<?php
								
									if(!empty($data_sorted)){
										foreach ($data_sorted as $k => $v )   {					
											echo '<tr data-id="'.esc_attr($k).'" class="cf7toanyapi_dataid">';
											$k = (int)$k;
											echo '<td data-id="'.esc_attr($k).'" class="cf7toanyapi_dataid"></td>';
											foreach ($fields as $k2 => $v2) {
												//Get fields related values
												$_value = ((isset($v[$k2])) ? $v[$k2] : '&nbsp;');
												$_value1 = filter_var($_value, FILTER_SANITIZE_URL);

												//Check value is URL or not
												if (!filter_var($_value1, FILTER_VALIDATE_URL) === false) {
													$_value = esc_url($_value);
													//If value is url then setup anchor tag with value
													if(!empty($arr_field_type_info) && array_key_exists($k2,$arr_field_type_info) && $arr_field_type_info[$k2] == 'file'){
														//Add download attributes in tag if field type is attachement
														?><td data-head="<?php echo esc_attr( $Cf7_To_Any_Api->cf7toanyapi_admin_get_field_name($v2) ); ?>">
															<a href="<?php echo esc_url($_value); ?>" target="_blank" title="<?php echo esc_url($_value); ?>" download ><?php echo esc_html(basename($_value)); ?>
															</a>
														</td><?php
													}
													else{
														?><td data-head="<?php echo esc_attr( $Cf7_To_Any_Api->cf7toanyapi_admin_get_field_name($v2) ); ?>">
															<a href="<?php echo esc_url($_value); ?>" target="_blank" title="<?php echo esc_url($_value); ?>" ><?php echo esc_html(basename($_value)); ?>
															</a>
														</td><?php
													}
												} else if($Cf7_To_Any_Api->cf7toanyapi_admin_get_field_name($v2) == 'submitted_from'){
													echo '<td data-head="'.esc_attr( $Cf7_To_Any_Api->cf7toanyapi_admin_get_field_name($v2) ).'"><a href="'.esc_url(get_the_permalink($_value)).'" target="_blank">'.esc_html(get_the_title($_value)).'</a></td>';
												} else{
													$_values = esc_html(html_entity_decode($_value));
													if(strlen($_values) > $display_character){
														echo '<td data-head="'. esc_attr(  $Cf7_To_Any_Api->cf7toanyapi_admin_get_field_name($v2) ).'">'.esc_html(substr($_values, 0, $display_character)).'...</td>';
													}else{
														echo '<td data-head="'. esc_attr(  $Cf7_To_Any_Api->cf7toanyapi_admin_get_field_name($v2) ).'">'.htmlspecialchars_decode($_value).'</td>';
													}
												}
											}//Close foreach
											echo '</tr>';
										}//Close foreach							
									
									}
								?>
							</tbody>
						</table>
					</div>
				<?php
				}
				else{
					?>
						<div id="table_data">
							<h3 class="cf7toanyapi_data_not_found"><?php esc_html_e( 'No data Found...!!!', 'contact-form-to-any-api' ); ?></h3>
						</div>
					<?php
				}
			}
		?>
	</div>
</div>