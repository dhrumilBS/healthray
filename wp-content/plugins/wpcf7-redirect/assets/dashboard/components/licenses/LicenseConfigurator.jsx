import { useState, useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import clsx from 'clsx';
import apiFetch from '@wordpress/api-fetch';
import { LicenseMessage } from './LicenseMessage';
import { DotStatus } from '../common/DotStatus';
import { LoaderPlaceholder } from '../common/Loader';

export const LicenseConfigurator = ( {
	slug,
	label,
	status,
	description,
	statusLabel,
	inputPlaceholder,
	endpointURL,
} ) => {
	const [ open, setOpen ] = useState( false );
	const [ licenseKey, setLicenseKey ] = useState( '' );
	const [ error, setError ] = useState( '' );
	const [ loading, setLoading ] = useState( false );
	const [ license, setLicense ] = useState( {
		status,
		statusLabel,
		inputPlaceholder,
	} );

	const handleLicenseUpdate = useCallback(
		( endpointData ) => {
			const controller = new AbortController();
			const t = setTimeout( () => controller.abort(), 8000 );

			apiFetch( {
				path: endpointURL,
				data: endpointData,
				method: 'POST',
				signal: controller.signal,
			} )
				.then( ( response ) => {
					clearTimeout( t );

					if ( response.success ) {
						setLicense( {
							status: response.data?.status,
							statusLabel: response.data?.statusLabel,
							inputPlaceholder: response.data?.licenseMask,
						} );
						setError( '' );
					} else {
						setLicense( {
							status: response.data?.status,
							statusLabel: response.data?.statusLabel,
						} );
						if ( response.data.message ) {
							setError( response.data?.message );
						}
					}

					setLoading( false );
				} )
				.catch( ( e ) => {
					setError(
						e.message ||
							__(
								'An error occurred while updating the license',
								'wpcf7-redirect'
							)
					);
					setLoading( false );
				} );
		},
		[ endpointURL ]
	);

	return (
		<div className="rcf7-license-config rcf7-card">
			<div className="rcf7-license-config__header">
				<h4>{ label }</h4>
				<DotStatus status={ license.status } />
			</div>
			<div className="rcf7-license-config__body">
				<p className="rcf7-license-config__description">
					{ description }
				</p>
				<div className="rcf7-license-config__actions">
					{
						<button
							onClick={ () => setOpen( ! open ) }
							className={ clsx( 'rcf7-btn rcf7-btn--compact', {
								disabled: 'inactive' === status,
							} ) }
							disabled={ 'inactive' === status }
						>
							{ __( 'Configure', 'wpcf7-redirect' ) }
						</button>
					}
					<LicenseMessage
						status={ license.status }
						message={ license.statusLabel }
					/>
				</div>
				{ open && (
					<div className="rcf7-license-config__fields">
						<div className="rcf7-license-field">
							<input
								type="text"
								id="license-key"
								placeholder={
									license.inputPlaceholder ||
									__(
										'Enter your license key',
										'wpcf7-redirect'
									)
								}
								value={ licenseKey }
								onChange={ ( e ) => {
									setLicenseKey( e.currentTarget.value );
								} }
								className="rcf7-license-input"
								disabled={
									loading || 'valid' === license.status
								}
							/>
							<button
								onClick={ () => {
									setLoading( true );

									handleLicenseUpdate( {
										slug,
										key: licenseKey,
										action:
											'valid' === license.status
												? 'deactivate'
												: 'activate',
									} );
								} }
								className={ clsx(
									'rcf7-btn',
									'compact',
									'valid' === license.status
										? 'rcf7-btn--red'
										: 'rcf7-btn--purple'
								) }
								disabled={ loading }
							>
								{ loading ? (
									<LoaderPlaceholder />
								) : 'valid' === license.status ? (
									__( 'Deactivate', 'wpcf7-redirect' )
								) : (
									__( 'Save', 'wpcf7-redirect' )
								) }
							</button>
						</div>
					</div>
				) }
				{ error && (
					<p className="rcf7-license-config__error">{ error }</p>
				) }
			</div>
		</div>
	);
};
