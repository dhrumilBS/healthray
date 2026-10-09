import {
	BookAIcon,
	BookOpenText,
	MessageCircleQuestion,
	Zap,
} from 'lucide-react';
import { __ } from '@wordpress/i18n';
import { useData } from '../../DataContext';

const ResourceItem = ( { icon, title, description, href } ) => {
	return (
		<a href={ href } target="_blank" className="rcf7-resource rcf7-card">
			<span>{ icon }</span>
			<span className="rcf7-resource__title">{ title }</span>
			<span className="rcf7-resource__description">{ description }</span>
		</a>
	);
};

export const ResourcesPanel = () => {
	const data = useData();

	return (
		<div className="rcf7-resources">
			<ResourceItem
				icon={ <BookAIcon /> }
				title={ __( 'Documentation', 'wpcf7-redirect' ) }
				description={ __( 'Browse our guides', 'wpcf7-redirect' ) }
				href={ data.links.docs }
			/>
			<ResourceItem
				icon={ <BookOpenText /> }
				title={ __( 'Tutorials', 'wpcf7-redirect' ) }
				description={ __( 'Step-by-step guides', 'wpcf7-redirect' ) }
				href={ data.links.tutorial }
			/>
			<ResourceItem
				icon={ <MessageCircleQuestion /> }
				title={ __( 'Support', 'wpcf7-redirect' ) }
				description={ __( 'Get help from our team', 'wpcf7-redirect' ) }
				href={ data.links.support }
			/>
			<ResourceItem
				icon={ <Zap /> }
				title={ __( 'Premium Features', 'wpcf7-redirect' ) }
				description={ __( 'Explore add-ons', 'wpcf7-redirect' ) }
				href={ data.links.upgrade }
			/>
		</div>
	);
};
