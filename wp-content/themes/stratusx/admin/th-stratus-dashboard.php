<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
// generate links to action buttons
$url = get_site_url();
$activate_action = esc_url( admin_url( 'admin.php?page=stratus_dashboard&action=activate' ) );
$deactivate_action = esc_url( admin_url( 'admin.php?page=stratus_dashboard&action=thmv_deactivate' ) );
$install_action = esc_url( admin_url( 'admin.php?page=stratus_dashboard&action=install' ) );

// Check current status of activation theme

$checker = th_stratus_dashboard_checker();

//error_log('CHECKER CODE: '.$checker);

// Variable for Purchase codes, based on Envato Token
$envato_codes = false;

?>

<!-- Display/hide TGMPA notice about plugins -->
<?php if ( isset( $_POST ) ) : ?>
  <script type="text/javascript">
    jQuery( window ).load( function( $ ) {
      <?php if ( $checker == STATUS_ACTIVATED || $checker == STATUS_ACTIVATING_SUCCESS || $checker == STATUS_ACTIVATING_FAILURE_ACTIVATED_EARLY ) : ?>
        jQuery( 'body' ).addClass( 'th-activated' );
      <?php else: ?>
        jQuery( 'body' ).removeClass( 'th-activated' );
      <?php endif; ?>
    } );
  </script>
<?php endif; ?>

<div class="th-stratus-page-wrapper">
    <!-- Logo wrapper -->
    <div class="th-stratus-logo-wrapper">
        <?php require_once( 'partials/logo-header.php' ); ?>
    </div>
    <div class="th-stratus-admin-wrapper">
        <!-- Left Menu wrapper -->
        <div class="th-stratus-admin-menu">
            <?php require_once('partials/sidebar.php'); ?>
        </div>
        <!-- Main section -->
        <div class="th-stratus-admin-content">
          <?php
            // If Stratus theme is activated just now or early
            if ( $checker == STATUS_ACTIVATED || $checker == STATUS_ACTIVATING_SUCCESS || $checker == STATUS_ACTIVATING_FAILURE_ACTIVATED_EARLY ) :
          ?>
              <div class="th-stratus-admin-section">
                <img class="th-icon-installed" src="<?php echo get_template_directory_uri() . '/assets/images/icon-installed.png'; ?>">
                  <?php if ( $checker == STATUS_ACTIVATING_SUCCESS ) : ?>
                    <h2>Congratulations! <?php echo $theme_name; ?> has been successfully activated.</h2>
                  <?php elseif ( $checker == STATUS_ACTIVATING_FAILURE_ACTIVATED_EARLY ) : ?>
                    <h2><?php echo $theme_name; ?> has been successfully activated.</h2>
                  <?php else : ?>
                    <h2><?php echo $theme_name; ?> has been activated.</h2>
                  <?php endif; ?>

                <!-- Deactivation form -->
                <form action="<?php echo $deactivate_action; ?>" method="post">
                    <p>Active site: <a href="<?php echo $url; ?>"><?php echo $url; ?></a></p>
                    <input type="hidden" name="url" value="<?php echo $url; ?>" />
                    <input type="submit" class="stratus-button button-blue" value="<?php _e( 'Deactivate', 'stratus' ); ?>" />
                </form>

              </div>
              <!-- Launch installer -->
              <div class="th-stratus-admin-section th-admin-2col">
                <h2>Launch Installer</h2>
                <p>Add the required plugins and import starter demo content.</p>
                <a href="<?php echo $install_action; ?>" class='stratus-button button-green'><?php _e( 'Install', 'stratus' ); ?></a>
              </div>

              <!-- Visual Library -->
              <div class="th-stratus-admin-section th-admin-2col">
                <h2>Looking for a specific demo page?</h2>
                <p>Import any page right in Elementor using the Visual Library.</p>
                <a target="_blank" href="https://out.themovation.com/stratus-library" class='stratus-button button-green'>Learn more</a>
              </div>
          <?php else: ?>
              <div class="th-stratus-admin-section">
                <h2>Activation</h2>
                <p class="th-activation-intro">Activate your license to run the automated installer, import the demo content, and enable one-click theme updates.</p>

                <?php
                  // Envato_Market Plugin Check
                  if ( is_plugin_active( 'envato-market/envato-market.php' ) ) {
                    $envato_active = true;
                  } else {
                    // Prepare Installation/Activation of Envato Market plugin
                    $envato_active = th_check_envato_market();
                  }
                ?>

                <!-- Form Activation with Envato, if Envato Market is active -->
                <?php if ( $envato_active ) : ?>
                  <form id="th-stratus-envato" action="<?php echo $activate_action; ?>" method="post">
                    <p class="error-msg">Please input your Envato token</p>
                    <input type="hidden" name="url" value="<?php echo $url; ?>">
                    <?php 
                        $envato_token = "";
                        $envato_token = esc_html( envato_market()->get_option( 'token' ) );
                        $envato_token_link = ENVATO_TOKEN_LINK;

                        if ( $envato_token ) :
                          // Get all purchased codes for Stratus, based on provided Envato Token
                          $envato_codes = th_get_envato_codes();
                          if ( isset( $envato_codes['errors'] ) ): ?>
                            <p class="notice-msg">The provided Envato API token is either not valid, or it does not correspond to the <?php echo $theme_name; ?> theme.</p>
                            <?php foreach ( $envato_codes['errors'] AS $k => $v ) : ?>
                              <p class="notice-msg">Error details: <?php echo $k;?> - <?php echo $v[0];?></p>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        <?php endif; ?>
                        <input type="text" name="envato_token" value="<?php echo $envato_token; ?>" autocomplete="off" placeholder="INSERT ENVATO TOKEN" />
                        <input type="submit" name="envato" class="stratus-button button-green th-min-w" name="b1" value="<?php _e( 'Save', 'stratus' ); ?>" />
                        <!-- Showing Help popup link, if we still haven't correct Envato Token -->
                        <?php //if ( ! $envato_token || ! $envato_codes || isset( $envato_codes['errors'] ) ) : ?>
                          <a href="#" class="link-need-help upper-case ml20 th-modal-link" attr-popup="envatoModal"><?php _e( 'Create a Token', 'stratus' );?> </a>
                        <?php //endif;?>

                        <?php if ( $checker == STATUS_ACTIVATING_FAILURE_CODE_USED ) : ?>
                          <p class="notice-msg">The provided purchase code has already been used for activating the <?php echo $theme_name; ?> theme on another site.</p>
                        <?php elseif ( $checker == STATUS_ACTIVATING_ERRORS_CODE ) : ?>
                          <p class="notice-msg">The provided purchase code is either not valid, or it does not correspond to the <?php echo $theme_name; ?> theme.</p>
                        <?php elseif ( $checker == STATUS_ACTIVATING_ERRORS_CODE_EMPTY ) : ?>
                          <p class="notice-msg">The provided purchase code is empty.</p>
                        <?php endif; ?>

                        <?php 
                          if ( $envato_codes && ! isset( $envato_codes['errors'] ) ) :
                            foreach ( $envato_codes AS $k => $v ) : ?>
                              <div class="thmv-purchase-code-field">
                                  <p><strong>Purchase code(s)</strong></p>
                                <input type="text" name="purchase_code_<?php echo $k; ?>" value="<?php echo $v['code'];?>" readonly="readonly" />                                                  
                                <?php if ( $v['status'] == 1 ) : ?>
                                  <input type="submit" name="submit_code_<?php echo $k; ?>" class="stratus-button button-blue" value="<?php _e( 'Activate', 'stratus' ); ?>" />
                                <?php elseif ( $v['status'] == 2 ) : ?>
                                    <script type="text/javascript">
                                      jQuery( window ).load( function( $ ) {
                                        document.location.href = "<?php echo admin_url( 'themes.php?page=stratus_dashboard' );?>";
                                      } );
                                    </script>
                                <?php else: ?>
                                  <input type="submit" class="stratus-button" value="<?php _e('In use', 'stratus'); ?>" disabled="disabled" />
                                <?php endif; ?>
                              </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                  </form>
                <?php endif; ?>
  
                <!-- Form Activation with direct Purchase Code -->
                <p><a href="#" id="purchase-code-notices" class="link-need-help">Or insert purchase code directly</a></p>
                <div class="purchase-code-wrapper">
                  <form id="th-stratus-activate" action="<?php echo $activate_action; ?>" method="post">
                      <p class="error-msg">Please input your Envato purchase code</p>
                      <?php if ( $checker == STATUS_ACTIVATING_FAILURE_CODE_USED ) : ?>
                        <p class="notice-msg">The provided purchase code has already been used for activating the <?php echo $theme_name; ?> theme on another site.</p>
                      <?php elseif ( $checker == STATUS_ACTIVATING_ERRORS_CODE ) : ?>
                        <p class="notice-msg">The provided purchase code is either not valid, or it does not correspond to the <?php echo $theme_name; ?> theme.</p>
                      <?php endif; ?>
                      <input type="hidden" name="url" value="<?php echo $url; ?>" />                      
                      <input type="text" name="purchase_code_0" value="" autocomplete="off" placeholder="INSERT PURCHASE CODE" />
                      <input type="submit" name="submit_code_0" class="stratus-button button-blue" value="<?php _e('Activate', 'stratus'); ?>" />
                      <a href="#" class="link-need-help upper-case ml20 th-modal-link" attr-popup="codeModal"><?php _e('Get your purchase code', 'stratus' ); ?></a>
                  </form>
                </div>

                <!-- Modal content for Generate Envato Token Info -->
                <div id="envatoModal" class="th-modal">
                  <div class="th-modal-content">
                    <span class="th-close">&times;</span>
                    <div class="th-modal-header">
                      <h2><?php esc_html_e( 'Create an Envato API Token', 'stratus' ); ?></h2>
                    </div>
                    <div class="th-modal-body">
                        <?php if ( $envato_active ) : ?>
                            <p><?php printf( esc_html__( 'Sign into your %s before you proceed.', 'stratus' ), '<a href="https://account.envato.com/sign_in?to=envato-api" target="_blank">' . esc_html__( 'Envato Account', 'stratus' ) . '</a>' ); ?></p>
                            <ol>
                                <li><?php printf( esc_html__( '%s to create a Token.', 'stratus' ), '<a href="' . envato_market()->admin()->get_generate_token_url() . '" target="_blank">' . esc_html__( 'Click here', 'stratus' ) . '</a>' ); ?></li>
                                <li><?php esc_html_e( 'Give it a name eg: “My WordPress site”.', 'stratus' ); ?></li>
                                <li><?php esc_html_e( 'Check the following:', 'stratus' ); ?>
                                    <ul>
                                        <li><?php esc_html_e( '"View and search Envato sites"', 'stratus' ); ?></li>
                                        <li><?php esc_html_e( '"Download your purchased items"', 'stratus' ); ?></li>
                                        <li><?php esc_html_e( '"List purchases you\'ve made"', 'stratus' ); ?></li>
                                        <li><?php printf( esc_html__( '%s', 'stratus' ), '<a href="https://share.getcloudapp.com/ApuLgvvX" target="_blank">' . esc_html__( 'Sample', 'stratus' ) . '</a></strong>' ); ?></li>
                                    </ul>
                                </li>
                                <li><?php esc_html_e( 'Create the token, then copy and paste the token into the box on the theme activation screen.', 'stratus' ); ?></li>
                                <li><?php esc_html_e( 'Click the "Save" button, and your purchase code(s) will appear.', 'stratus' ); ?></li>
                                <li><?php esc_html_e( 'Select one and click "Activate".', 'stratus' ); ?></li>
                            </ol>
                            <p><?php printf( esc_html__( '%s', 'stratus' ), '<a href="https://out.themovation.com/stratus-activation-howto" target="_blank">' . esc_html__( 'Need help?', 'stratus' ) . '</a></p>' ); ?>
                        <?php else :?>
                            <h2><?php esc_html_e( 'Envato Market Plugin not installed or active?', 'stratus' ); ?></h2>
                            <p><?php esc_html_e( 'Please close this window and Activate the Envato Market Plugin', 'stratus' ); ?></p>
                        <?php endif; ?>
                    </div>
                  </div>
                </div>

                <!-- Modal content for Purchase code Info -->
                <div id="codeModal" class="th-modal">
                  <div class="th-modal-content">
                    <span class="th-close">&times;</span>
                    <div class="th-modal-header">
                      <h2>Get your purchase code</h2>
                    </div>
                    <div class="th-modal-body">
                        <p><?php esc_html_e( 'To get your Envato Purchase Code, follow the steps below:', 'stratus' ); ?></p>
                        <ol>
                            <li><?php printf( esc_html__( 'Login to the %s', 'stratus' ), '<a href="https://themeforest.net" target="_blank">' . esc_html__( 'Envato Marketplace', 'stratus' ) . '</a></strong>' ); ?></li>
                            <li><?php esc_html_e( 'Once logged in, move your mouse over your username on the top right', 'stratus' ); ?></li>
                            <li><?php printf( esc_html__( 'Click on the menu item "%s"', 'stratus' ), '<a href="https://themeforest.net/downloads" target="_blank">' . esc_html__( 'Downloads', 'stratus' ) . '</a></strong>' ); ?></li>
                            <li><?php esc_html_e( 'Locate the product, and then click on “Download”', 'stratus' ); ?></li>
                            <li><?php esc_html_e( 'Click on “Licence certificate & purchase code” to download the text file', 'stratus' ); ?></li>
                            <li><?php esc_html_e( 'Open it and locate the “Item Purchase Code”', 'stratus' ); ?></li>
                            <li><?php esc_html_e( 'Copy and paste the purchase code into the box on the theme activation screen.', 'stratus' ); ?></li>
                        </ol>
                        <p><?php printf( esc_html__( '%s', 'stratus' ), '<a href="https://out.themovation.com/stratus-purchasecode" target="_blank">' . esc_html__( 'Need help?', 'stratus' ) . '</a></p>' ); ?>
                    </div>
                  </div>
                </div>

                <!-- <p>Active site: <a href='<?php echo $url; ?>'><?php echo $url;?></a></p> -->

              </div>

                <div class="th-help-section"><p><a target="_blank" href="https://out.themovation.com/stratus-activation-help">Need help?</a></p></div>

              <div class="th-stratus-admin-section disabled-section">
                <h2>Launch Installer</h2>
                <a href="javascript: void(0);" class="stratus-button button-blue button-disable"><?php _e( 'Install', 'stratus' ); ?></a>
              </div>
          <?php endif; ?>
        </div>
    </div>
</div>
