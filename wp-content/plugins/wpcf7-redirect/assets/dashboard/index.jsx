import { createRoot } from '@wordpress/element';
import App from './App';
import { DataProvider } from './DataContext';

const dashboardDomNode = document.getElementById( 'redirect-dashboard' );
const root = createRoot( dashboardDomNode );

root.render(
	<DataProvider>
		<App />
	</DataProvider>
);
