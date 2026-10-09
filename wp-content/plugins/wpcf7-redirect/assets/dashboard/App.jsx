import { useState, useEffect, useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

import './styles/dashboard.scss';
import SettingsTab from './SettingsTab';
import AddonsTab from './AddonsTab';
import LicensesTab from './LicensesTab';
import { ACTIONS, useData, useDataDispatch } from './DataContext';
import { Modal } from './components/common/Modal';

const App = () => {
	const data = useData();
	const dispatch = useDataDispatch();

	const [ tabSlug, setTabSlug ] = useState( 'settings' );
	const showLicenseTab = data.plugins.some( ( plugin ) => plugin.installed );

	// Check URL hash for tab
	useEffect( () => {
		const hash = window.location.hash;
		if ( ! hash ) {
			return;
		}

		const hashValue = hash.substring( 1 );
		if (
			hashValue === 'settings' ||
			hashValue === 'addons' ||
			hashValue === 'licenses'
		) {
			setTabSlug( hashValue );
		}

		if ( window.tsdk_reposition_notice ) {
			window.tsdk_reposition_notice();
		}
	}, [] );

	// Update hash when tab changes
	useEffect( () => {
		window.location.hash = tabSlug;
	}, [ tabSlug ] );

	const handleResetSettings = useCallback( () => {
		const controller = new AbortController();
		const t = setTimeout( () => controller.abort(), 5000 );
		apiFetch( {
			path: data.endpoints.resetSettings,
			method: 'POST',
			data: {},
		} )
			.then( ( response ) => {
				clearTimeout( t );
				dispatch( {
					type: ACTIONS.updateOptionLoadingStatus,
					isLoading: false,
				} );
				window.location.reload();
			} )
			.catch( ( e ) => {
				clearTimeout( t );
				console.error( e.message );
				dispatch( {
					type: ACTIONS.updateOptionLoadingStatus,
					isLoading: false,
				} );
			} );
	}, [ data ] );

	const tabs = [
		{
			slug: 'settings',
			label: __( 'Settings', 'wpcf7-redirect' ),
			isVisible: true,
		},
		{
			slug: 'licenses',
			label: __( 'Licenses', 'wpcf7-redirect' ),
			isVisible: showLicenseTab,
		},
		{
			slug: 'addons',
			label: __( 'Premium Features', 'wpcf7-redirect' ),
			isVisible: true,
		},
	];

	return (
		<div className="rcf7-dashboard">
			<h1>
				<img src={ window.wpcf7rDash.assets.logo } />
				{ __( 'Redirection for Contact Form 7', 'wpcf7-redirect' ) }
			</h1>
			<div className="rcf7-dashboard__content">
				<div className="rcf7-dashboard__tabs">
					{ tabs.map(
						( tab ) =>
							tab.isVisible && (
								<button
									key={ tab.slug }
									className={ `rcf7-dashboard__tab ${
										tabSlug === tab.slug ? 'is-active' : ''
									}` }
									onClick={ () => setTabSlug( tab.slug ) }
								>
									{ tab.label }
								</button>
							)
					) }
				</div>
				
				<div id="tsdk_banner"></div>
				<div className="rcf7-dashboard__tab-content">
					{ tabSlug === 'settings' && <SettingsTab /> }
					{ tabSlug === 'licenses' && <LicensesTab /> }
					{ tabSlug === 'addons' && <AddonsTab /> }
				</div>
			</div>
			<Modal
				isOpen={ data.isResetModalOpen }
				title={ __( 'Reset Settings', 'wpcf7-redirect' ) }
				onClose={ () => dispatch( { type: ACTIONS.closeResetModal } ) }
				onConfirm={ () => {
					dispatch( {
						type: ACTIONS.updateOptionLoadingStatus,
						isLoading: true,
					} );
					dispatch( { type: ACTIONS.closeResetModal } );
					handleResetSettings();
				} }
			>
				<p>
					{ __(
						'You are about to reset all Redirection for Contact Form 7 settings. ',
						'wpcf7-redirect'
					) }
				</p>
			</Modal>
		</div>
	);
};

export default App;
