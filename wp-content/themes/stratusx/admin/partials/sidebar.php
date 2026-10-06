<?php
// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
$theme_name = ucfirst( THEME_NAME );

// Remove X at the end of the theme name.
$theme_name = rtrim($theme_name,"x");

$th_dashboard = isset( $_GET['page'] ) && $_GET['page'] == MENU_STRATUS_HOME ? 'active' : '';
$th_plugins = isset( $_GET['page'] ) && $_GET['page'] == MENU_STRATUS_PLUGINS ? 'active' : '';
$th_templates = isset( $_GET['page'] ) && $_GET['page'] == MENU_STRATUS_TEMPLATES ? 'active' : '';
$th_updates = isset( $_GET['page'] ) && $_GET['page'] == MENU_STRATUS_UPDATES ? 'active' : '';

if ( $checker == STATUS_ACTIVATED || $checker == STATUS_ACTIVATING_SUCCESS || $checker == STATUS_ACTIVATING_FAILURE_ACTIVATED_EARLY ) {
    $thv_welcome_title = __( 'Activated', 'stratus' );
}else{
    $thv_welcome_title = __( 'Activate', 'stratus' );
}

?>
<ul>
    <li class='th-stratus-theme <?php echo esc_attr( $th_dashboard ); ?>'><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . MENU_STRATUS_HOME ) ); ?>"><?php echo $thv_welcome_title;?></a></li>
    <?php if ( MENU_STRATUS_PLUGINS_ACTIVE ):?>
        <li class='th-stratus-theme <?php echo esc_attr( $th_plugins ); ?>'><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . MENU_STRATUS_PLUGINS ) ); ?>"><?php echo MENU_STRATUS_PLUGINS_TITLE;?></a></li>
    <?php endif;?>
    <?php if ( MENU_STRATUS_TEMPLATES_ACTIVE ):?>
        <li class='th-stratus-theme <?php echo esc_attr($th_templates); ?>'><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . MENU_STRATUS_TEMPLATES ) ); ?>"><?php echo MENU_STRATUS_TEMPLATES_TITLE;?></a></li>
    <?php endif;?>
    <?php if ( MENU_STRATUS_UPDATES_ACTIVE ):?>
        <li class='th-stratus-theme <?php echo esc_attr($th_updates); ?>'><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . MENU_STRATUS_UPDATES ) ); ?>"><?php echo MENU_STRATUS_UPDATES_TITLE;?></a></li>
    <?php endif;?>
</ul>