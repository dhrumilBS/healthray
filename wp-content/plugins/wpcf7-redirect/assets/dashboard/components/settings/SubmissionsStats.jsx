import { ChartLine, Mail, Clock } from 'lucide-react';
import { __ } from '@wordpress/i18n';
import { useData } from '../../DataContext';
import clsx from 'clsx';
import { HighlightedIcon } from '../common/HighlightedIcon';

const SubmissionsStat = ( { icon, label, description, classes } ) => {
	return (
		<div className={ clsx( 'rcf7-stat', 'rcf7-card', classes ) }>
			<HighlightedIcon icon={ icon } />
			<span className="rcf7-stat__number">{ label }</span>
			<p className="rcf7-stat__description">{ description }</p>
		</div>
	);
};

export const SubmissionsStats = () => {
	const data = useData();
	const stats = [
		{
			icon: <Mail />,
			label: data.stats.totalEntries,
			description: __( 'Total Entries', 'wpcf7-redirect' ),
			classes: 'variation-1',
		},
		{
			icon: <ChartLine />,
			label: data.stats.todayEntries,
			description: __( "Today's Entries", 'wpcf7-redirect' ),
			classes: 'variation-2',
		},
		{
			icon: <Clock />,
			label: data.stats.lastEntryDisplayDate,
			description: __( 'Since Last Entry', 'wpcf7-redirect' ),
			classes: 'variation-3',
		},
	];

	return (
		<div className="rcf7-stats">
			{ stats.map( ( stat ) => {
				return <SubmissionsStat key={ stat.description } { ...stat } />;
			} ) }
		</div>
	);
};
