<?php
/**
 * Render extension promo box
 */

defined( 'ABSPATH' ) || exit;

$ver   = $this->get_extension_ver();
$badge = method_exists( $this, 'get_badge' ) ? $this->get_badge() : '';
?>

<div
	class="promo-box <?php echo esc_attr( $this->get_name() ); ?> <?php echo $this->needs_activation() ? 'needs-activation' : ''; ?> <?php echo $this->has_update() ? 'has-update' : ''; ?> <?php echo esc_attr( $this->get_name() ); ?> <?php echo $this->is_active() ? 'active-ext' : ''; ?>"
	data-extension="<?php echo esc_attr( $this->get_name() ); ?>"
>
	<div class="promo-box-inner">
		<div class="promo-bot-title">
			<div class="promo-version">
				<?php if ( $ver && ! $badge ) : ?>
					<small>v.<?php echo esc_html( $this->get_extension_ver() ); ?></small> <span class="dashicons dashicons-editor-code"></span>
				<?php elseif ( $badge ) : ?>
					<?php echo wp_kses_post( $badge ); ?>
				<?php endif; ?>
			</div>

			<?php if ( ! $this->is_active() ) : ?>
				<div class="promo-purchase-link">
					<?php if ( $this->get_aff_url() ) : ?>
							<a href="<?php echo esc_url( $this->get_aff_url() ); ?>" class="">
								<?php esc_html_e( 'Activate', 'wpcf7-redirect' ); ?>
							</a>
					<?php else : ?>
						<span type="button" class="btn-activate">
							<?php esc_html_e( 'Activate', 'wpcf7-redirect' ); ?>
						</span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</div>
		<div class="promo-box-content">
			<div class="promo-box-thumb">
				<img src="<?php echo esc_url( $this->get_icon() ); ?>" alt="<?php echo esc_attr( $this->get_name() ); ?>">
			</div>
			<div class="promo-box-description">
				<h3><?php echo esc_html( $this->get_title() ); ?></h3>

				<div class="description">
					<?php echo wp_kses_post( $this->get_description() ); ?>
				</div>

				<div class="promo-actions-box">
					<div class="actions">
						<?php if ( $this->get_aff_url() ) : ?>
								<a
									href="<?php echo esc_url( $this->get_aff_url() ); ?>"
									class="button-primary"
								>
									<?php echo esc_html( $this->get_btn_text() ); ?>
								</a>
						<?php else : ?>
							<?php if ( ! $this->is_active() ) : ?>
								<?php if ( $this->extension_file_exists() ) : ?>
									<span type="button" class="btn-activate">
										<?php esc_html_e( 'Activate', 'wpcf7-redirect' ); ?>
									</span>
								<?php else : ?>
									<a
										href="<?php echo esc_url( $this->get_purchase_link() ); ?>"
										class="btn-getit"
										target="_blank"
									>
										<span class="get-it-label">
											<?php esc_html_e( 'Get It', 'wpcf7-redirect' ); ?>
										</span>
										<span class="dashicons dashicons-arrow-down-alt"></span>
									</a>
								<?php endif; ?>
							<?php else : ?>
								<span type="button" class="button-primary btn-deactivate">
									<?php esc_html_e( 'Deactivate', 'wpcf7-redirect' ); ?>
								</span>
							<?php endif; ?>

							<?php if ( $this->is_active() && $this->has_update() ) : ?>
								<span type="button" class="button-primary btn-update">
									<?php esc_html_e( 'Update', 'wpcf7-redirect' ); ?>
								</span>
							<?php endif; ?>

							<div class="serial">
								<input
									type="text"
									class="serial-number"
									value="<?php echo esc_attr( $this->get_serial() ); ?>"
									placeholder="<?php esc_attr_e( 'License Key', 'wpcf7-redirect' ); ?>"
								>
								<span
									class="button-primary btn-activate-serial"
									value="<?php esc_attr_e( 'Save', 'wpcf7-redirect' ); ?>"
								>
									<span class="dashicons dashicons-yes"></span>
								</span>
								<span
									class="button-primary btn-close"
									title="<?php esc_attr_e( 'Cancel', 'wpcf7-redirect' ); ?>"
								>
									<span class="dashicons dashicons-no"></span>
								</span>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
