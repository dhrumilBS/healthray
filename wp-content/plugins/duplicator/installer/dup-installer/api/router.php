<?php

// Run only inside an extracted package
if (!glob(dirname(__DIR__) . '/dup_descriptors_*', GLOB_ONLYDIR)) {
    exit;
}

if (!defined('DUPXABSPATH')) {
    define('DUPXABSPATH', __DIR__);
}

use Duplicator\Installer\Core\Bootstrap;
use Duplicator\Installer\Core\Security;

define('DUPX_VERSION', '5.1.1');
define('DUPX_INIT', str_replace('\\', '/', dirname(__DIR__)));
define('DUPX_ROOT', preg_match('/^[\\\\\/]?$/', dirname(DUPX_INIT)) ? '/' : dirname(DUPX_INIT));

require_once(DUPX_INIT . '/src/Utils/Autoloader.php');
Duplicator\Installer\Utils\Autoloader::register();
/**
 * init constants and include
 */
Bootstrap::init(2);

if (Security::getInstance()->getSecurityType() !== Security::SECURITY_NONE) {
    dupxTplRender('page-security-error', ['message' => 'Installer security check required']);
    die();
}

require_once('class.api.php');
require_once('class.cpnl.base.php');
require_once('class.cpnl.ctrl.php');

//Register API Engine - If it processes the current route it spits out JSON and exits the process
$API_Server = new DUPX_API_Server();
$API_Server->add_controller(new DUPX_cPanel_Controller());
$API_Server->process_request(false);

dupxTplRender('api/front', [
    'apiControllers' => $API_Server->controllers,
    'dupVersion'     => DUPX_ArchiveConfig::getInstance()->version_dup
]);
