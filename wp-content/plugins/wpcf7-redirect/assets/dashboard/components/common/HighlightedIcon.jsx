import clsx from 'clsx';

export const HighlightedIcon = ( { icon, classes = {} } ) => {
	return (
		<span className={ clsx( 'rcf7-highlighted-icon', classes ) }>
			{ icon }
		</span>
	);
};
