<?php

function handl_insert_custom_user_meta($custom_meta, $user, $update, $userdata){
	$fields = generateUTMFields();
	foreach ( $fields as $field ) {
		if ( isset( $_COOKIE[ $field ] ) && $_COOKIE[ $field ] != '' ) {
//			if (in_array($field, ['email', 'username', 'password']))
			$custom_meta[$field] = esc_attr( $_COOKIE[ $field ] );
		}
	}
	return $custom_meta;
}
add_filter('insert_custom_user_meta', 'handl_insert_custom_user_meta', 10, 4);

function handl_show_user_profile( $user ) {

	if( ! current_user_can('edit_users') ) {
		return;
	}

	?>
	<table class="form-table">
		<?php
		$fields = generateUTMFields();
		$i = 0;
		foreach ($fields as $field) {
			if(  $handlValue = get_user_meta( $user->ID, $field, true ) ) {
				if ($i == 0){
					print "<h3>HandL UTM Grabber Fields</h3>";
				}
				print "<tr>
						<th><label>$field</label></th>
						<td>$handlValue</td>
					</tr>";
				$i++;
			}
		}
		?>
	</table>
	<?php
}
add_action( 'edit_user_profile', 'handl_show_user_profile' );