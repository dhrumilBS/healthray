import { createContext, useContext, useReducer } from '@wordpress/element';

type FormShortcut = {
	label: string;
	link: string;
};

type SubmissionTableEntry = {
	id: number;
	title: string;
	formLabel: string;
	formLink: string;
	date: string;
	actions: {
		view: string;
	};
};

type ServerDashboardData = {
	assets: {
		logo: string;
	};
	tabs: {
		showLicenses: boolean;
		showPremiumFeatures: boolean;
	};
	formShortcuts: FormShortcut[];
	stats: {
		totalEntries: number;
		todayEntries: string;
		lastEntryDisplayDate: string;
	};
	chart: {
		labels: string[];
		data: ( number | string )[];
		legend: {
			label: string;
		};
	};
	submissionTable: SubmissionTableEntry[];
	plugins: {
		slug: string;
		label: string;
		status: string;
		statusLabel: string;
		description: string;
		licenseMask: string;
		installed: boolean;
	}[];
	endpoints: {
		updateLicense: string;
		toggleDebugMode: string;
		resetSettings: string;
	};
	debugMode: boolean;
	links: {
		upgrade: string;
		docs: string;
		tutorial: string;
		support: string;
	};
};

type DashboardData = ServerDashboardData & {
	isResetModalOpen: boolean;
	isUpdatingOptions: boolean;
};

export const DataContext = createContext< DashboardData >( null );
export const DataDispatchContext = createContext( null );

export function useData() {
	return useContext( DataContext );
}

export function useDataDispatch() {
	return useContext( DataDispatchContext );
}

export function DataProvider( { children } ) {
	const [ tasks, dispatch ] = useReducer( dataReducer, {
		...( ( window?.wpcf7rDash as ServerDashboardData ) ?? {} ),
		isResetModalOpen: false,
		isUpdatingOptions: false,
	} );

	return (
		<DataContext.Provider value={ tasks }>
			<DataDispatchContext.Provider value={ dispatch }>
				{ children }
			</DataDispatchContext.Provider>
		</DataContext.Provider>
	);
}

export const ACTIONS = {
	openResetModal: 'open-reset-modal',
	closeResetModal: 'close-reset-modal',
	updateOptionLoadingStatus: 'update-option-loading-status',
};

function dataReducer( data: DashboardData, action ): DashboardData {
	switch ( action.type ) {
		case ACTIONS.openResetModal: {
			return {
				...data,
				isResetModalOpen: true,
			};
		}
		case ACTIONS.closeResetModal: {
			return {
				...data,
				isResetModalOpen: false,
			};
		}
		case ACTIONS.updateOptionLoadingStatus: {
			return {
				...data,
				isUpdatingOptions: action.isLoading,
			};
		}
		default: {
			throw Error( 'Unknown action: ' + action.type );
		}
	}
}
