import { __ } from '@wordpress/i18n';
import { useData } from '../../DataContext';

export const SubmissionsEntries = () => {
	const data = useData();

	const formatToLocalTime = ( dateString ) => {
		if ( ! dateString ) return '-';
		const date = new Date( dateString );
		return date.toLocaleString();
	};

	return (
		<div className="rcf7-table">
			{ data.submissionTable.length > 0 ? (
				<table>
					<thead>
						<tr>
							<th>{ __( 'ID', 'wpcf7-redirect' ) }</th>
							<th>
								{ __( 'Submission Title', 'wpcf7-redirect' ) }
							</th>
							<th>{ __( 'Form', 'wpcf7-redirect' ) }</th>
							<th>{ __( 'Date', 'wpcf7-redirect' ) }</th>
							<th>{ __( 'Actions', 'wpcf7-redirect' ) }</th>
						</tr>
					</thead>
					<tbody>
						{ data.submissionTable.map( ( entry ) => (
							<tr key={ entry.id }>
								<td>{ entry.id }</td>
								<td className="rcf7-table__title">
									{ entry.title }
								</td>
								<td>
									<a target="_blank" href={ entry.formLink }>
										{ entry.formLabel ?? '-' }
									</a>
								</td>
								<td>{ formatToLocalTime( entry.date ) }</td>
								<td className="rcf7-table__actions">
									<a
										className="rcf7-btn"
										href={ entry.actions.view }
										target="_blank"
										title={ __(
											'View details',
											'wpcf7-redirect'
										) }
									>
										{ __( 'View', 'wpcf7-redirect' ) }
									</a>
								</td>
							</tr>
						) ) }
					</tbody>
				</table>
			) : (
				<div className="rcf7-table__no-entries">
					<p>
						{ __( 'No form submissions found.', 'wpcf7-redirect' ) }
					</p>
					<p>
						{ __(
							'Add a Save Entry action to your contact forms to capture the submitted data.',
							'wpcf7-redirect'
						) }
					</p>
				</div>
			) }
		</div>
	);
};
