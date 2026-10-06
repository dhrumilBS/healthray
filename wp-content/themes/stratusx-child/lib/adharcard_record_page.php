<?php




function register_aadhaar_admin_page()
{
    add_menu_page(
        'Aadhaar Records', // Page Title
        'Aadhaar Records', // Menu Title
        'manage_options',  // Capability
        'aadhaar-records', // Slug
        'display_aadhaar_records_page', // Callback Function
        'dashicons-id-alt', // Icon
        50 // Position
    );
}
add_action('admin_menu', 'register_aadhaar_admin_page');


function get_aadhaar_records_from_db($page = 1, $limit = 10, $search = '', $start_date = '', $end_date = '')
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'aadhaar_auth_records'; // Update if necessary

    $offset = ($page - 1) * $limit;

    // Build the query conditions
    $conditions = [];

    if (!empty($search)) {
        $search = esc_sql($search);
        $conditions[] = "(aadhaar LIKE '%$search%' OR transaction_id LIKE '%$search%')";
    }

    if (!empty($start_date) && !empty($end_date)) {
        $conditions[] = "created_at BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";
    }

    // Convert conditions into SQL WHERE clause
    $where_clause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

    // Get records
    $query = "SELECT * FROM $table_name $where_clause ORDER BY id DESC LIMIT $limit OFFSET $offset";
    $records = $wpdb->get_results($query);

    // Get total count for pagination
    $count_query = "SELECT COUNT(*) FROM $table_name $where_clause";
    $total_records = $wpdb->get_var($count_query);

    return ['records' => $records, 'total' => $total_records];
}
function export_aadhaar_records_csv()
{
    if (!isset($_GET['export_csv']) || $_GET['export_csv'] !== '1') {
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'aadhaar_auth_records';

    // Get all records
    $records = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");

    // Define the CSV filename
    $filename = "aadhaar_records_" . date('Y-m-d') . ".csv";

    // Set headers for download
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=$filename");

    // Open output stream
    $output = fopen('php://output', 'w');

    // Add column headers
    fputcsv($output, ['ID', 'Aadhaar Number', 'Transaction ID', 'Status', 'Created At']);

    // Add data rows
    foreach ($records as $record) {
        fputcsv($output, [$record->id, $record->aadhaar, $record->transaction_id, $record->status, $record->created_at]);
    }

    fclose($output);
    exit;
}
add_action('admin_init', 'export_aadhaar_records_csv');




function display_aadhaar_records_page()
{
    global $wpdb;
    $limit = 10;
    $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '';
    $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : '';

    // Fetch records with pagination
    $data = get_aadhaar_records_from_db($page, $limit, $search, $start_date, $end_date);
    $records = $data['records'];
    $total_records = $data['total'];
    $total_pages = ceil($total_records / $limit);

    echo '<div class="wrap"><h1>Aadhaar Authentication Records</h1>';
    echo '<div class="tablenav top">';

    // Date & Search Form
    echo '<form method="get" class="alignleft">
        <input type="hidden" name="page" value="aadhaar-records">
        <input type="text" name="s" value="' . esc_attr($search) . '" placeholder="Search by Aadhaar or Transaction ID">
        <input type="date" name="start_date" value="' . esc_attr($start_date) . '">
        <input type="date" name=" " value="' . esc_attr($end_date) . '">
        <input type="submit" value="Filter" class="button">
        <a href="' . esc_url(admin_url("admin.php?page=aadhaar-records")) . '" class="button">Reset</a>
    </form>';

    // Export Button
    echo '<a href="' . esc_url(admin_url("admin.php?page=aadhaar-records&export_csv=1")) . '" class="button button-primary alignright">Export to CSV</a>';
    echo '</div>';

    // Display Table
    if (empty($records)) {
        echo '<p>No records found.</p>';
        return;
    }

    echo '<table class="widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Aadhaar Number</th>
                <th>Transaction ID</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($records as $record) {
        echo "<tr>
            <td>{$record->id}</td>
            <td>{$record->aadhaar}</td>
            <td>{$record->transaction_id}</td>
            <td>{$record->status}</td>
            <td>{$record->created_at}</td>
        </tr>";
    }

    echo '</tbody></table>';

    // Pagination
    echo '<div class="tablenav"><div class="tablenav-pages">';
    if ($total_pages > 1) {
        for ($i = 1; $i <= $total_pages; $i++) {
            echo '<a class="page-numbers ' . ($page == $i ? 'current' : '') . '" href="' . esc_url(admin_url("admin.php?page=aadhaar-records&paged=$i&s=" . urlencode($search) . "&start_date=$start_date&end_date=$end_date")) . '">' . $i . '</a> ';
        }
    }
    echo '</div></div>';

    echo '</div>';
}
