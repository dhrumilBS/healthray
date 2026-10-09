<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="wpcfr-popup-wrap wpcfr-popup-hidden <?php echo esc_attr( $template ); ?> middle-center">
	<div class="wpcfr-popup-wrap-inner">
		<span class="dashicons dashicons-no-alt wpcfr-close-popup top-right"></span>
		<div class="wpcfr-popup-wrap-content">
			<h3>
				<?php esc_html_e( 'Record', 'wpcf7-redirect' ); ?>
			</h3>
			<div class="wrapper rcf7-result-container">
				<pre ><?php echo esc_textarea( print_r( $this->record, true ) ); ?></pre>
			</div>
			<h3>
				<?php esc_html_e( 'Request', 'wpcf7-redirect' ); ?>
			</h3>
			<div class="wrapper rcf7-result-container">
				<pre><?php echo esc_textarea( print_r( $this->request, true ) ); ?></pre>
			</div>
			<h3>
				<?php esc_html_e( 'Response', 'wpcf7-redirect' ); ?>
			</h3>
			<div class="wrapper">
				<?php if ( is_wp_error( $this->results ) ) : ?>
					<span class="err"><?php esc_html_e( 'Error!', 'wpcf7-redirect' ); ?></span>
					<pre><?php echo esc_textarea( print_r( $this->results, true ) ); ?></pre>
				<?php else : ?>
					<div class="field-wrap">
						<div class="label">
							<label>
								<?php esc_html_e( 'Response code', 'wpcf7-redirect' ); ?>
							</label>
							<span><?php echo esc_html( $this->results['response']['message'] ); ?> (<?php echo esc_html( $this->results['response']['code'] ); ?>)</span>
						</div>
						<pre class="rcf7-result-container"><?php echo esc_textarea( print_r( $this->results, true ) ); ?></pre>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
