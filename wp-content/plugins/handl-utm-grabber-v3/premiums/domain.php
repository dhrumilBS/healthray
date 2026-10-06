<?php


function handl_register_domain(){
    register_setting( 'handl-utm-grabber-settings-group', 'handl_cookie_domain' );
}
add_action( 'admin_init', 'handl_register_domain' );

function handl_add_domain(){
    global $handl_fields_disabled;
    ?>
    <tr>
        <th scope='row'>Domain</th>
        <td>
            <fieldset>
                <legend class='screen-reader-text'>
                    <span>Domain</span>
                </legend>
                <label for='handl_cookie_domain'>
                    <input name='handl_cookie_domain' id='handl_cookie_domain' type='text' value='<?php print get_option( 'handl_cookie_domain' ) ? get_option( 'handl_cookie_domain' ) : '' ?>' <?php print $handl_fields_disabled;?>/>
                    <p class="description">If you are using subdomains and lock all the cookies to parent domain. You can do so here. e.g. <code>.domain.com</code></p>
                </label>
            </fieldset>
        </td>
    </tr>
    <?php
}
add_filter("insert_rows_to_handl_options", "handl_add_domain", 10);