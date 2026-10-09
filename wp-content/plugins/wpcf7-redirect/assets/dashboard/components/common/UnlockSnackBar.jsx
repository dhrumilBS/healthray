import { __ } from '@wordpress/i18n';

export const UnlockSnackBar = ( { upgradeLink } ) => {
	return (
		<div className="rcf7-unlock-banner">
			<div className="rcf7-unlock-banner__content">
				<h3 className="rcf7-unlock-banner__title">
					{ __( 'Unlock Advanced Features', 'wpcf7-redirect' ) }
				</h3>
				<p className="rcf7-unlock-banner__description">
					{ __(
						'Get conditional redirects, CRM integrations, and more with premium add-ons.',
						'wpcf7-redirect'
					) }
				</p>
			</div>
			<a href={ upgradeLink } target="_blank" className="rcf7-btn">
				{ __( 'Explore Premium', 'wpcf7-redirect' ) }
			</a>
		</div>
	);
};
