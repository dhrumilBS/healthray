import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import clsx from 'clsx';
import { useData } from '../../DataContext';

const CardStep = ( { children, badgeLabel, title } ) => {
	return (
		<div className="rcf7-quick-action-card rcf7-card">
			<div className="rcf7-quick-action-card__header">
				<span className="rcf7-badge rcf7-badge--black">
					{ badgeLabel }
				</span>
				<h3 className="rcf7-quick-action-card__title">{ title }</h3>
			</div>
			<div className="rcf7-quick-action-card__content">{ children }</div>
		</div>
	);
};

export const QuickActions = () => {
	const data = useData();
	const [ selectedFormLink, setSelectedFormLink ] = useState( '' );

	return (
		<div className="rcf7-quick-actions">
			<CardStep
				badgeLabel={ __( 'Step 1', 'wpcf7-redirect' ) }
				title={ __( 'Select a Form', 'wpcf7-redirect' ) }
			>
				<p className="rcf7-quick-action-card__description">
					{ __(
						'Choose which Contact Form 7 form you want to enhance with post-submission actions.',
						'wpcf7-redirect'
					) }
				</p>
				<div className="rcf7-select-wrapper">
					<select
						id="form-select"
						className="rcf7-select"
						value={ selectedFormLink }
						onChange={ ( e ) =>
							setSelectedFormLink( e.target.value )
						}
					>
						{ data.formShortcuts.map( ( { label, link } ) => {
							return (
								<option key={ link } value={ link }>
									{ label }
								</option>
							);
						} ) }
					</select>
				</div>
			</CardStep>

			<CardStep
				badgeLabel={ __( 'Step 2', 'wpcf7-redirect' ) }
				title={ __( 'Add Post-Submission Actions', 'wpcf7-redirect' ) }
			>
				<p className="rcf7-quick-action-card__description">
					{ __(
						'Configure what happens after a user submits your form: redirect to a thank-you page, save entries, and more.',
						'wpcf7-redirect'
					) }
				</p>

				<a
					href={ selectedFormLink }
					className={ clsx( 'rcf7-btn', 'full-width', {
						disabled: ! Boolean( selectedFormLink ),
					} ) }
					{ ...( ! selectedFormLink && { 'aria-disabled': 'true' } ) }
				>
					{ __( 'Add Actions', 'wpcf7-redirect' ) }
				</a>
			</CardStep>
		</div>
	);
};
