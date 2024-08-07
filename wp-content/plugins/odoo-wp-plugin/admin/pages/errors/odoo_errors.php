<?php

namespace odoo_conn\admin\pages\errors;

require_once(__DIR__ . "/../../api/endpoints/odoo_errors.php");

use odoo_conn\admin\api\endpoints\OdooConnGetOdooErrors;
use odoo_conn\admin\api\endpoints\OdooConnDeleteOdooErrors;
use odoo_conn\admin\pages\OdooConnPageRouter;
use odoo_conn\admin\table_display\OdooConnCustomTableDeletableDisplay;


class OdooConnOdooErrorsListTableEditable extends OdooConnCustomTableDeletableDisplay
{

    public function column_contact_7_title($item)
    {
        return $item["contact_7_title"] . " " . $this->row_actions($this->row_action_buttons($item));
    }

    public function get_columns()
    {
        return array(
            "cb" => "<input type='checkbox' />",
            "contact_7_title" => "Contact 7 Form",
            "time_occurred" => "Time of Error",
            "error_message" => "Error Message"
        );
    }

}


class OdooConnOdooErrorRouter extends OdooConnPageRouter {

    public function __construct()
    {
        $this->get_backend = new OdooConnGetOdooErrors(ARRAY_A);
        $this->delete_backend = new OdooConnDeleteOdooErrors();

        parent::__construct();
    }

    protected function create_table_display()
    {
        return new OdooConnOdooErrorsListTableEditable(
            $this->get_backend, $this->delete_backend
        );
    }

    protected function delete($id)
    {
        $this->delete_backend->request(["id" => $id]);
    }
}


function odoo_conn_odoo_errors_page()
{
    $odoo_conn_errors_router = new OdooConnOdooErrorRouter();
    $odoo_conn_errors_router->request();
}

?>
