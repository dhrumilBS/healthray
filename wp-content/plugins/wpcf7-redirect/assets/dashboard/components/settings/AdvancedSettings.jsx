import { useState, useCallback, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { CheckboxField } from './CheckboxField';
import { ACTIONS, useData, useDataDispatch } from '../../DataContext';
import apiFetch from '@wordpress/api-fetch';
import clsx from 'clsx';
import { LoaderPlaceholder } from '../common/Loader';

export const AdvancedSettings = () => {
	const data = useData();
	const dispatch = useDataDispatch();
	const [ debugMode, setDebugMode ] = useState( Boolean( data.debugMode ) );
	const [ error, setError ] = useState( '' );

	const handleToggleDebug = useCallback( () => {
		const controller = new AbortController();
		const t = setTimeout( () => controller.abort(), 5000 );
		apiFetch( {
			path: data.endpoints.toggleDebugMode,
			method: 'POST',
			signal: controller.signal,
		} )
			.then( ( response ) => {
				clearTimeout( t );
				setError( '' );
				dispatch( {
					type: ACTIONS.updateOptionLoadingStatus,
					isLoading: false,
				} );
				setDebugMode( Boolean( response.data?.debugMode ) );
			} )
			.catch( ( e ) => {
				setError( e.message );
				dispatch( {
					type: ACTIONS.updateOptionLoadingStatus,
					isLoading: false,
				} );
			} );
	}, [ data ] );

	return (
		<div className="rcf7-adv-settings">
			<h2>{ __( 'Advanced Settings', 'wpcf7-redirect' ) }</h2>
			<div className="rcf7-adv-settings__container rcf7-card">
				<CheckboxField
					id="debug"
					checked={ debugMode }
					onChange={ ( e ) => {
						dispatch( {
							type: ACTIONS.updateOptionLoadingStatus,
							isLoading: true,
						} );
						setDebugMode( e.target.checked );
						handleToggleDebug();
					} }
					label={ __( 'Enable Debug Mode', 'wpcf7-redirect' ) }
					description={ __(
						'This will open the actions post type and display debug feature.',
						'wpcf7-redirect'
					) }
					disabled={ data.isUpdatingOptions }
				/>
				{ debugMode && ! data.isUpdatingOptions && (
					<button
						disabled={ data.isUpdatingOptions }
						className={ clsx( 'rcf7-btn', 'rcf7-btn--red', {
							disabled: data.isUpdatingOptions,
						} ) }
						onClick={ () => {
							dispatch( {
								type: ACTIONS.openResetModal,
							} );
						} }
					>
						{ __( 'Reset Settings', 'wpcf7-redirect' ) }
					</button>
				) }
				{ data.isUpdatingOptions && (
					<div className="rcf7-adv-settings__updates">
						<LoaderPlaceholder classes={ 'rcf7-loader--blue' } />
						{ __( 'Updating option', 'wpcf7-redirect' ) }
					</div>
				) }
				{ error && (
					<div className="rcf7-adv-settings__error">
						{ __( 'Error', 'wpcf7-redirect' ) }:{ error }
					</div>
				) }
			</div>
		</div>
	);
};
