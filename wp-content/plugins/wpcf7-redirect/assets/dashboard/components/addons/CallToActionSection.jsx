import { __ } from '@wordpress/i18n';

export const CallToActionSection = ( {
	title = __( 'Ready to Enhance Your Forms?', 'wpcf7-redirect' ),
	description = __(
		'Unlock all premium features and take your Contact Form 7 to the next level with our powerful add-ons.',
		'wpcf7-redirect'
	),
	buttonLabel = __( 'Get Premium Now', 'wpcf7-redirect' ),
	buttonHref = '#',
} ) => {
	return (
		<div className="rcf7-cta">
			<h2 className="rcf7-cta__title">{ title }</h2>
			<p className="rcf7-cta__description">{ description }</p>
			<a
				href={ buttonHref }
				target="_blank"
				className="rcf7-btn  rcf7-btn--blue"
			>
				{ buttonLabel }
			</a>
		</div>
	);
};
