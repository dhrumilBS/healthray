import { __ } from '@wordpress/i18n';

export const AddonCard = ( {
	icon,
	title,
	isPremium,
	description,
	badgeLabel,
	learnMoreLink,
} ) => (
	<div className="rcf7-addon rcf7-card">
		<div className="rcf7-addon__icon">{ icon }</div>
		<div className="rcf7-addon__container">
			<div className="rcf7-addon__header">
				<h3>{ title }</h3>
				{ isPremium && (
					<span className="rcf7-badge rcf7-badge--blue">
						{ badgeLabel }
					</span>
				) }
			</div>
			<p className="rcf7-addon__description">{ description }</p>
			{ learnMoreLink && (
				<a
					className="rcf7-addon__learn-more"
					href={ learnMoreLink }
					target="_blank"
				>
					{ __( 'Learn more', 'wpcf7-redirect' ) }
				</a>
			) }
		</div>
	</div>
);
