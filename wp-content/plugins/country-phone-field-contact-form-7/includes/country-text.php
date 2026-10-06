<?php
/**
** A base module for [countrytext] and [countrytext*]
**/

/* form_tag handler */

add_action( 'wpcf7_init', 'nbcpf_add_form_tag_countrytext' );

function nbcpf_add_form_tag_countrytext() {
	wpcf7_add_form_tag(
		array( 'countrytext', 'countrytext*'),
		'nbcpf_countrytext_form_tag_handler', array( 'name-attr' => true ) );
}

function nbcpf_countrytext_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type, 'wpcf7-text' );

	if ( in_array( $tag->basetype, array( 'countrytext', 'countrytext*' ) ) ) {
		$class .= ' wpcf7-validates-as-' . $tag->basetype;
	}

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}

	$atts = array();

	$atts['size'] = $tag->get_size_option( '40' );
	$atts['maxlength'] = $tag->get_maxlength_option();
	$atts['minlength'] = $tag->get_minlength_option();

	if ( $atts['maxlength'] && $atts['minlength']
	&& $atts['maxlength'] < $atts['minlength'] ) {
		unset( $atts['maxlength'], $atts['minlength'] );
	}

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

	$atts['autocomplete'] = $tag->get_option( 'autocomplete',
		'[-0-9a-zA-Z]+', true );

	if ( $tag->has_option( 'readonly' ) ) {
		$atts['readonly'] = 'readonly';
	}

	if ( $tag->is_required() ) {
		$atts['aria-required'] = 'true';
	}

	if ( $validation_error ) {
		$atts['aria-invalid'] = 'true';
		$atts['aria-describedby'] = wpcf7_get_validation_error_reference(
			$tag->name
		);
	} else {
		$atts['aria-invalid'] = 'false';
	}

	$value = (string) reset( $tag->values );

	if ( $tag->has_option( 'placeholder' ) || $tag->has_option( 'watermark' ) ) {
		$atts['placeholder'] = $value;
		$value = '';
	}

	$value = $tag->get_default_option( $value );

	$value = wpcf7_get_hangover( $tag->name, $value );

	$atts['value'] = $value;

	$atts['type'] = 'text';
	
	$atts['name'] = $tag->name;

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap" data-name="%1$s"><input %2$s />%3$s</span>',
		sanitize_html_class( $tag->name ), $atts, $validation_error );

	return $html;
}


/* Validation filter */


add_filter( 'wpcf7_validate_countrytext', 'nbcpf_countrytext_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_countrytext*', 'nbcpf_countrytext_validation_filter', 10, 2 );

function nbcpf_countrytext_validation_filter( $result, $tag ) {
	$type = $tag->type;
	$name = $tag->name;

	$value = isset( $_POST[$name] ) ? (string) wp_unslash($_POST[$name]) : '';

	if ( $tag->is_required() && '' == $value ) {
		$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
	}

	return $result;
}


/* Tag generator */

add_action( 'wpcf7_admin_init', 'nbcpf_add_tag_generator_countrytext', 20 );

function nbcpf_add_tag_generator_countrytext() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'countrytext', __( 'country drop-down', 'nb-cpf' ),
		'nbcpf_tag_generator_countrytext', array( 'version' => '2' ) );
}
function nbcpf_tag_generator_countrytext( $contact_form, $options ) {
	$field_types = array(
		'countrytext' => array(
			'display_name' => __( 'Country dropdown field', 'nb-cpf' ),
			'heading' => __( 'Country dropdown field form-tag generator', 'nb-cpf' ),
			'description' => __( 'Generate a form-tag for a country dorp list with flags icon text input field.', 'nb-cpf' ),
			'maybe_purpose' => 'author_country',
		),
	);
	$basetype = $options['id'];

	if ( ! in_array( $basetype, array_keys( $field_types ) ) ) {
		$basetype = 'countrytext';
	}

	$tgg = new WPCF7_TagGeneratorGenerator( $options['content'] );

?>
<header class="description-box">
	<h3><?php
		echo esc_html( $field_types[$basetype]['heading'] );
	?></h3>

	<p><?php
		$description = wp_kses(
			$field_types[$basetype]['description'],
			array(
				'a' => array( 'href' => true ),
				'strong' => array(),
			),
			array( 'http', 'https' )
		);

		echo $description;
	?></p>
</header>

<div class="control-box">
	<?php
		$tgg->print( 'field_type', array(
			'with_required' => true,
			'select_options' => array(
				$basetype => $field_types[$basetype]['display_name'],
			),
		) );

		$tgg->print( 'field_name', array(
			'ask_if' => $field_types[$basetype]['maybe_purpose']
		) );

		$tgg->print( 'class_attr' );

		$tgg->print( 'min_max', array(
			'title' => __( 'Length', 'nb-cpf' ),
			'min_option' => 'minlength:',
			'max_option' => 'maxlength:',
		) );

		$tgg->print( 'default_value', array(
			'with_placeholder' => true,
		) );
	?>
</div>

<footer class="insert-box">
	<?php
		$tgg->print( 'insert_box_content' );

		$tgg->print( 'mail_tag_tip' );
	?>
</footer>
<?php
}

function nbcpf_tag_generator_countrytext_old( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$type = 'countrytext';

	$description = __( "Generate a form-tag for a country dorp list with flags icon text input field.", 'nb-cpf' );

	//$desc_link = wpcf7_link( __( 'https://contactform7.com/text-fields/', 'nb-cpf' ), __( 'Text Fields', 'nb-cpf' ) );
	$desc_link = '';
?>
<div class="control-box">
<fieldset>
<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

<table class="form-table">
<tbody>
	<tr>
	<th scope="row"><?php echo esc_html( __( 'Field type', 'nb-cpf' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'nb-cpf' ) ); ?></legend>
		<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'nb-cpf' ) ); ?></label>
		</fieldset>
	</td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'nb-cpf' ) ); ?></label></th>
	<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Default value', 'nb-cpf' ) ); ?></label></th>
	<td><input type="text" name="values" class="oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>" /><br />
	<label><input type="checkbox" name="placeholder" class="option" /> <?php echo esc_html( __( 'Use this text as the placeholder of the field', 'nb-cpf' ) ); ?></label></td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'nb-cpf' ) ); ?></label></th>
	<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'nb-cpf' ) ); ?></label></th>
	<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
	</tr>

</tbody>
</table>
</fieldset>
</div>

<div class="insert-box">
	<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

	<div class="submitbox">
	<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'nb-cpf' ) ); ?>" />
	</div>

	<br class="clear" />

	<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'nb-cpf' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
</div>
<?php
}