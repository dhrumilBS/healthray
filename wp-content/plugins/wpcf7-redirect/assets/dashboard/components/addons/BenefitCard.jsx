import { HighlightedIcon } from '../common/HighlightedIcon';

export const BenefitCard = ( { benefit } ) => (
	<div className="rcf7-card">
		<div className="rcf7-benefit">
			<HighlightedIcon
				icon={ benefit.icon }
				classes={ benefit.iconClass }
			/>
			<h3 className="rcf7-benefit__title">{ benefit.title }</h3>
			<p className="rcf7-benefit__description">{ benefit.description }</p>
		</div>
	</div>
);
