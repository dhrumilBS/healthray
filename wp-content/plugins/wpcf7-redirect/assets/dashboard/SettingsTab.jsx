import { __ } from '@wordpress/i18n';

import { useData } from './DataContext';
import { QuickActions } from './components/settings/QuickActions';
import { FormSubmissionsOverview } from './components/settings/FormSubmissionsOverview';
import { AdvancedSettings } from './components/settings/AdvancedSettings';
import { UnlockSnackBar } from './components/common/UnlockSnackBar';
import { ResourcesPanel } from './components/settings/ResourcesPanel';

const SettingsTab = () => {
	const data = useData();

	return (
		<div className="rcf7-settings-tab">
			<div className="rcf7-dashboard__section">
				<h2>{ __( 'Quick Setup Guide', 'wpcf7-redirect' ) }</h2>
				<QuickActions />
			</div>

			<div className="rcf7-dashboard__section">
				<h2>{ __( 'Form Analytics', 'wpcf7-redirect' ) }</h2>
				<FormSubmissionsOverview />
			</div>

			<div className="rcf7-dashboard__section">
				<h2>{ __( 'Resources', 'wpcf7-redirect' ) }</h2>
				<ResourcesPanel />
			</div>

			<div className="rcf7-dashboard__section">
				<AdvancedSettings />
			</div>

			<div className="rcf7-dashboard__section">
				<UnlockSnackBar upgradeLink={ data.links.upgrade } />
			</div>
		</div>
	);
};

export default SettingsTab;
