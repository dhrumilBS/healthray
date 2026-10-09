import { __ } from '@wordpress/i18n';

export const CheckboxField = ( {
	id,
	checked,
	onChange,
	label,
	description,
	disabled = false,
} ) => {
	return (
		<>
			<div className="rcf7-checkbox__input">
				<input
					type="checkbox"
					id={ id }
					checked={ checked }
					onChange={ onChange }
					disabled={ disabled }
				/>
				<label htmlFor={ id } className="rcf7-checkbox__label">
					{ label }
				</label>
			</div>
			{ description && (
				<p className="rcf7-checkbox__description">{ description }</p>
			) }
		</>
	);
};
