<?php

class HandLContactForm7Tag {

    private $tag;
    private $title;

    public function __construct($tag, $title)
    {
        $this->tag = $tag;
        $this->title = $title;

        add_action( 'wpcf7_init', array($this,'wpcf7_add_form_tag_handl'), 10, 0 );
        add_action( 'wpcf7_admin_init', array($this, 'wpcf7_add_tag_generator_handl'), 999, 0 );
     }

    public function wpcf7_add_form_tag_handl(){
        wpcf7_add_form_tag($this->tag, array($this, 'wpcf7_handl_form_tag_handler'), array( 'name-attr' => true ));
    }

    public function wpcf7_handl_form_tag_handler($tag){
        $class = wpcf7_form_controls_class( $tag->type );
        $atts = array();

        $atts['class'] = $tag->get_class_option( $class );
        $atts['id'] = $tag->get_id_option();
        $atts['name'] = $tag->name;
        $atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

        $value = isset( $tag->values[0] ) ? $tag->values[0] : '';

        if ( empty( $value ) && isset($_COOKIE[$this->title]) && $_COOKIE[$this->title] != "" ) {
            $value = $_COOKIE[$this->title];
        }

        $atts['type'] = 'hidden';
        $atts['value'] = $value;

        $atts = wpcf7_format_atts( $atts );

        $html = sprintf( '<input %1$s />', $atts );

        return $html;
    }

    public function wpcf7_add_tag_generator_handl() {
        $tag_generator = WPCF7_TagGenerator::get_instance();
        $tag_generator->add( $this->tag, $this->title,
            array($this, 'wpcf7_tag_generator_handl'));
    }

    public function wpcf7_tag_generator_handl($cf, $args){
        $args = wp_parse_args( $args, array() );
        ?>
        <div class="control-box">
            <fieldset>

                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Label', 'contact-form-7' ) ); ?></label></th>
                        <td><input type="text" name="values" class="oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>" /></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
                        <td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label></th>
                        <td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
                        <td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
                    </tr>

                    </tbody>
                </table>
            </fieldset>
        </div>

        <div class="insert-box">
            <input type="text" name="<?php print $this->tag; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
            </div>

            <br class="clear" />

            <p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
        </div>


        <?php
    }
}

function createContactForm7Fields(){
//    TODO: We'll implement auto addition of all the fields into both form and mail template later on.
//    $posts = WPCF7_ContactForm::find( array(
//        'post_status' => 'any',
//        'posts_per_page' => -1,
//    ) );
//
//    foreach ( $posts as $post ) {
//        /** @var WPCF7_ContactForm $post */
//        $props = $post->get_properties();
//        $props['form'] = $props['form']."\nHaktan1";
//        $props['mail']['body'] = $props['mail']['body']."\nHaktan2";
//        $post->set_properties($props);
////        $post->save();
////        dd($post->get_properties());
//    }

    $fields = generateUTMFields();
    foreach ($fields as $field){
        new HandLContactForm7Tag($field."_cf7", $field);
    }
}
add_action( 'wpcf7_init','createContactForm7Fields',9);

