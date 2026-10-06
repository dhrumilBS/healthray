<?php

function add_gravity_forms_to_tabs($tabs){
    if (is_plugin_active('gravityforms/gravityforms.php')){
        array_push($tabs, array( 'gravity-forms' => __( 'Gravity Forms', 'handlutmgrabber' ) ) );
    }
    return $tabs;
}
add_filter('filter_admin_tabs','add_gravity_forms_to_tabs', 10, 1);

function getGravityFormsContent(){
    if (is_plugin_active('gravityforms/gravityforms.php')){
        global $handl_active, $handl_fields_disabled;
        $forms = array();
        if (class_exists('GFAPI')){
            $forms = GFAPI::get_forms();
            $definition = UTMFieldsDefinition();
        ?>
        <form method='post' action='options.php'>
            <?php settings_fields( 'handl-utm-grabber-gravityforms-group' ); ?>
            <?php do_action('maybe_dispay_license_error_notice') ?>
            <table class='form-table'>
                <tr>
                    <th scope='row'>Forms <br/><br/> <input name="forms_check_all" type="checkbox" class="forms_check_all" value="0" onclick="toggleCheckAll(this,'.forms')" <?php print $handl_fields_disabled;?>><b>Select All</b></label></th>
                    <td>
                        <?php foreach($forms as $form): ?>
                        <fieldset>
                            <legend class='screen-reader-text'>
                                <span><?php print $form['title']." (".$form['id'].")";?></span>
                            </legend>
                            <label for="forms[]">
                                <input name="forms[]" type="checkbox" class="forms" value="<?php print $form['id'];?>" <?php print $handl_fields_disabled;?>><?php print $form['title'] ." (".$form['id'].")";?></label>
                        </fieldset>
                        <?php endforeach;?>
                    </td>
                </tr>
                <tr>
                    <th scope='row'>Action</th>
                    <td>
                        <select name="gf__action" onChange="toggleFieldsAddDisplay(this)" <?php print $handl_fields_disabled;?>>
                            <option value="add">Add</option>
                            <option value="remove">Remove</option>
                        </select>
                        <p class="description gf__action_description handl_display_hide">It will remove ALL the fields added by HandL UTM Grabber</p>
                    </td>
                </tr>
                <tr class="add_to_fields_row">
                    <th scope='row'>Fields to Add <br/><br/> <input name="fields_check_all" type="checkbox" class="fields_check_all" value="1" checked onclick="toggleCheckAll(this,'.fields')" <?php print $handl_fields_disabled;?>><b>Select All</b></label></th>
                    <td class="column-count2">
                        <?php foreach(generateUTMFields() as $field): ?>
                            <fieldset>
                                <legend class='screen-reader-text'>
                                    <span><?php print $field;?></span>
                                </legend>
                                <label for="fields" class="label__fields">
                                    <input name="fields[]" type="checkbox" class="fields" value="<?php print $field;?>" checked <?php print $handl_fields_disabled;?>><?php print $field;?></label>
                                <?php if ( isset($definition[$field]) ): ?>
                                    <p class="description"><?php print $definition[$field];?></p>
                                <?php endif; ?>
                            </fieldset>
                        <?php endforeach;?>

                    </td>
                </tr>
            </table>

            <?php submit_button(null, 'primary', 'submit', true, $handl_active ? '' : 'disabled'); ?>
        </form>
        <style>
            .column-count2{
                -moz-column-count: 2;
                -webkit-column-count: 2;
                column-count: 2;
            }

            .column-count3{
                -moz-column-count: 3;
                -webkit-column-count: 3;
                column-count: 3;
            }

            .handl_display_hide{
                display: none;
            }

            .form-table td fieldset label.label__fields{
                margin: 0 !important;
            }

            .add_to_fields_row fieldset{
                margin-bottom: 0.25em;
            }


            .form-table p.description {
                font-style: italic;
                color: #8e8e8e;
                font-size: 93%;
                margin-top:0;
            }

            .form-table p.gf__action_description{
                color: red;
                font-weight: bold;
            }

        </style>
        <script>
            function toggleCheckAll(t, target){
                jQuery(target).prop('checked', jQuery(t).prop('checked'))
            }

            function toggleFieldsAddDisplay(t) {
                if (jQuery(t).val() === 'remove'){
                    jQuery('.add_to_fields_row').addClass('handl_display_hide')
                    jQuery('.gf__action_description').removeClass('handl_display_hide')
                }else{
                    jQuery('.add_to_fields_row').removeClass('handl_display_hide')
                    jQuery('.gf__action_description').addClass('handl_display_hide')
                }
            }
        </script>
        <?php
        }
    }
}
add_filter( 'get_admin_tab_content_gravity-forms', 'getGravityFormsContent', 10 );

function processHandLGravityForm(){
    $queryArgs = array();
    $option_group = "handl-utm-grabber-gravityforms-group";
    if ( isset($_POST) &&
        sizeof($_POST) > 0 &&
        isset($_POST['option_page']) &&
        $_POST['option_page'] === $option_group &&
        check_admin_referer( $option_group."-options" ) ){

        if (
                isset($_POST['forms']) &&
                sizeof($_POST['forms']) > 0 &&
                isset($_POST['fields']) &&
                sizeof($_POST['fields']) > 0

        ){
            //do the GF update
            $myforms = array();
            foreach ($_POST['forms'] as $formID){
                if ($_POST['gf__action'] == 'add'){
                    $myforms[] = updateTheGFForm($formID, $_POST['fields']);
                }elseif ($_POST['gf__action'] == 'remove'){
                    $myforms[] = removeFieldFromGFFrom($formID);
                }
            }
//            dd($myforms);
            $result = GFAPI::update_forms( $myforms );

            if ($result) {
                $queryArgs['msg'] = 'Selected Gravity Forms Updated';
            }else{
                $queryArgs['msg'] = 'Selected Gravity Forms has not been updated';
                $queryArgs['err'] = 1;
            }
        }


        wp_redirect(add_query_arg($queryArgs, $_POST['_wp_http_referer']));
        exit;
    }


}
add_action('init', 'processHandLGravityForm');


function removeFieldFromGFFrom($formID){
    $form = GFAPI::get_form($formID);
    $remainingFields = array_filter($form['fields'], function($field){
        return preg_match("/HandL/", $field->label) == 0;
    });
    if ($remainingFields < $form['fields']){
        $form['fields'] = $remainingFields;
//        $result = GFAPI::update_form( $form );
//        return $result;
    }
    return $form;
}

function updateTheGFForm($formID, $post_fields){
    $form = GFAPI::get_form($formID);

    $fields = array_map(function($field){
        return $field['id'];
    }, $form['fields'] );
    $lastFieldID = max($fields);

    $templateHidden = array(
        'adminLabel' => '',
        'isRequired' => false,
        'size' => 'medium',
        'errorMessage' => '',
        'visibility' => 'visible',
        'inputs' => NULL,
        'description' => '',
        'allowsPrepopulate' => true,
        'inputMask' => false,
        'inputMaskValue' => '',
        'inputMaskIsCustom' => false,
        'maxLength' => '',
        'inputType' => '',
        'labelPlacement' => '',
        'descriptionPlacement' => '',
        'subLabelPlacement' => '',
        'placeholder' => '',
        'cssClass' => '',
        'noDuplicates' => false,
        'defaultValue' => '',
        'choices' => '',
        'conditionalLogic' => '',
        'productField' => '',
        'multipleFiles' => false,
        'maxFiles' => '',
        'calculationFormula' => '',
        'calculationRounding' => '',
        'enableCalculation' => '',
        'disableQuantity' => false,
        'displayAllCategories' => false,
        'useRichTextEditor' => false,
        'pageNumber' => 1,
        'fields' => '',
        'displayOnly' => '',
    );

    foreach ($post_fields as $UTMfield){
        $field = new GF_Field_Hidden();
        foreach($templateHidden as $key=>$value)
            $field->$key = $value;

        $lastFieldID++;
        $field->formId = $formID;
        $field->id = $lastFieldID;
        $field->label = sprintf("%s (HandL)", $UTMfield);
        $field->inputName = $UTMfield;
        array_push($form['fields'], $field);
    }
    return $form;
}

function handl_gf_client_side_prefill($form_string, $form){
	$script_wrapper = '';
//    if (isset($_POST) && sizeof($_POST) == 0){ // TODO: This is conflicting with TCM https://wordpress.org/support/topic/conflict-with-wp-super-cache-15/#post-10600376
	    $utmfields = generateUTMFields();
	    $script = '';
	    foreach ($form['fields'] as $field){
		    foreach ($utmfields as $utmfield){
			    if ($field['inputName'] === $utmfield){
				    $input_id   = "input_{$field['formId']}_{$field->id}";
				    $script .= "jQuery('#$input_id').val(decodeURIComponent(Cookies.get(\"$utmfield\") ?? ''));\n";
			    }
		    }
	    }

	    $script_wrapper = sprintf("
    <script>
        jQuery( document ).ready(function() {
        setTimeout(() => { %s }, 1000);
        })
    </script>
    ", $script);
//    }

	return $form_string.$script_wrapper;
}
add_filter('gform_get_form_filter', 'handl_gf_client_side_prefill', 10, 2);