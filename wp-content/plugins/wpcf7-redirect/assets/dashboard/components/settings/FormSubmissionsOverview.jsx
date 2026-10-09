import { __ } from '@wordpress/i18n';

import { SubmissionsStats } from './SubmissionsStats';
import { SubmissionsChart } from './SubmissionsChart';
import { SubmissionsEntries } from './SubmissionsEntries';
import { useData } from '../../DataContext';

export const FormSubmissionsOverview = () => {
	const data = useData();

	return (
		<div className="rcf7-form-entries">
			{ data.submissionTable.length > 0 && (
				<>
					<div className="rcf7-form-entries__section no-padding">
						<SubmissionsStats />
					</div>
					<div className="rcf7-form-entries__section rcf7-card">
						<SubmissionsChart />
					</div>
				</>
			) }

			<h3>{ __( 'Latest Entries', 'wpcf7-redirect' ) }</h3>
			<div className="rcf7-form-entries__section no-padding">
				<SubmissionsEntries />
			</div>
		</div>
	);
};
