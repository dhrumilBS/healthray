import { __ } from '@wordpress/i18n';

export const FeaturedAddonCard = ( {
	title,
	description,
	details,
	features,
	isPremium,
	learnMoreLink,
	badgeLabel,
} ) => (
	<div className="rcf7-card rcf7-featured">
		<div className="rcf7-featured__header">
			<div className="rcf7-featured__header-meta">
				<h3>{ title }</h3>
				<p>{ description }</p>
			</div>
			{ isPremium && (
				<span className="rcf7-badge rcf7-badge--blue">
					{ badgeLabel }
				</span>
			) }
		</div>
		<div className="rcf7-featured__body">
			<p className="rcf7-featured__description">{ details }</p>
			<ul className="rcf7-featured__list">
				{ features.map( ( feature, index ) => (
					<li key={ index } className="rcf7-featured__list-item">
						<span>{ feature }</span>
					</li>
				) ) }
			</ul>
		</div>
		<div className="rcf7-featured__footer">
			<a
				href={ learnMoreLink }
				target="_blank"
				className="rcf7-btn full-width"
			>
				{ __( 'Learn More', 'wpcf7-redirect' ) }
			</a>
		</div>
	</div>
);
