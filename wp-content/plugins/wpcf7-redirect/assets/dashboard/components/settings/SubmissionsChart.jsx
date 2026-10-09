import { useState, useEffect, useRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	Chart,
	BarController,
	CategoryScale,
	LinearScale,
	BarElement,
	Tooltip,
	Legend,
} from 'chart.js';
import { useData } from '../../DataContext';

Chart.register(
	BarController,
	CategoryScale,
	LinearScale,
	BarElement,
	Tooltip,
	Legend
);

export const SubmissionsChart = () => {
	const chartRef = useRef( null );
	const data = useData();

	const [ chartInstance, setChartInstance ] = useState( null );
	const [ dateRange, setDateRange ] = useState( 90 ); // Default to 90 days

	const handleDateRangeChange = ( e ) => {
		setDateRange( Number( e.target.value ) );
	};

	useEffect( () => {
		// Clean up any existing chart
		if ( chartInstance ) {
			chartInstance.destroy();
		}

		// Create new chart only if the canvas exists
		if ( chartRef.current ) {
			const ctx = chartRef.current.getContext( '2d' );

			// Filter data based on date range
			const filteredLabels = data.chart.labels.slice( -dateRange );
			const filteredData = data.chart.data.slice( -dateRange );

			const newChartInstance = new Chart( ctx, {
				type: 'bar',
				data: {
					labels: filteredLabels,
					datasets: [
						{
							label: data.chart.legend.label,
							data: filteredData,
							backgroundColor: 'rgba(54, 162, 235, 0.6)',
							borderColor: 'rgba(54, 162, 235, 1)',
							borderWidth: 1,
						},
					],
				},
				options: {
					responsive: true,
					scales: {
						x: {
							ticks: {
								// Show fewer labels for readability
								maxTicksLimit: 15,
							},
						},
					},
				},
			} );
			setChartInstance( newChartInstance );
		}

		// Cleanup function
		return () => {
			if ( chartInstance ) {
				chartInstance.destroy();
			}
		};
	}, [ dateRange ] ); // Re-run when dateRange changes

	return (
		<div className="rcf7-chart">
			<div className="rcf7-chart__controls">
				<label htmlFor="date-range-select">
					{ __( 'Show data for', 'wpcf7-redirect' ) }
				</label>
				<select
					id="date-range-select"
					value={ dateRange }
					onChange={ handleDateRangeChange }
				>
					<option value={ 15 }>
						{ __( 'Last 15 days', 'wpcf7-redirect' ) }
					</option>
					<option value={ 30 }>
						{ __( 'Last 30 days', 'wpcf7-redirect' ) }
					</option>
					<option value={ 90 }>
						{ __( 'Last 90 days', 'wpcf7-redirect' ) }
					</option>
				</select>
			</div>
			<div className="rcf7-chart__render">
				<canvas ref={ chartRef }></canvas>
			</div>
		</div>
	);
};
