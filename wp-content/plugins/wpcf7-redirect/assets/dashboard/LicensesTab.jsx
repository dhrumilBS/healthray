import { __ } from '@wordpress/i18n';

import { useData } from './DataContext';
import { LicenseConfigurator } from './components/licenses/LicenseConfigurator';

const LicensesTab = () => {
	const data = useData();

	return (
		<div className="rcf7-licenses-tab">
			{ data.plugins.map( ( plugin, index ) => (
				<LicenseConfigurator
					key={ index }
					slug={ plugin.slug }
					label={ plugin.label }
					status={ plugin.status }
					description={ plugin.description }
					statusLabel={ plugin.statusLabel }
					inputPlaceholder={ plugin.licenseMask }
					endpointURL={ data.endpoints.updateLicense }
				/>
			) ) }
		</div>
	);
};

export default LicensesTab;
