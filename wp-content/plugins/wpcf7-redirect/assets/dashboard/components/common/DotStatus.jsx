import clsx from 'clsx';

export const DotStatus = ( { status } ) => {
	return (
		<div
			className={ clsx( 'rcf7-status-dot', {
				'rcf7-dot-success': 'valid' === status,
				'rcf7-dot-danger': 'valid' !== status && 'inactive' !== status,
				'rcf7-dot-warning': 'inactive' === status,
			} ) }
		></div>
	);
};
