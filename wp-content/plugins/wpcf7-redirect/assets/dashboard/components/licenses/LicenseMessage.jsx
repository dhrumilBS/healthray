import clsx from 'clsx';

export const LicenseMessage = ( { status, message } ) => {
	return (
		<div
			className={ clsx( 'rcf7-badge', {
				'rcf7-badge--green': 'valid' === status,
				'rcf7-badge--red': 'valid' !== status && 'inactive' !== status,
				'rcf7-badge--gray': 'inactive' === status,
			} ) }
		>
			{ message }
		</div>
	);
};
