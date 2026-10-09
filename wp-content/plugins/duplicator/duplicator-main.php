<?php

defined('ABSPATH') || exit;

/** @var string $currentPluginBootFile */
/** @var string $currentPluginName */
/** @var string $currentPluginType */
/** @var string $currentPluginHsh */
/** @var string $currentPluginTextDomain */

// CHECK IF PLUGIN CAN BE EXECTUED
require_once __DIR__ . '/src/Utils/Requirements/ConflictChecker.php';

if (Duplicator\Utils\Requirements\ConflictChecker::canRun($currentPluginBootFile, $currentPluginName) === false) {
    return;
} else {
    // NOTE: Plugin code must be inside a conditional block to prevent functions definition, simple return is not enough
    define('DUPLICATOR____PATH', dirname($currentPluginBootFile));
    define('DUPLICATOR____FILE', $currentPluginBootFile);
    define('DUPLICATOR____NAME', $currentPluginName);
    define('DUPLICATOR____TYPE', $currentPluginType);
    define('DUPLICATOR____TEXT_DOMAIN', $currentPluginTextDomain);
    require_once DUPLICATOR____PATH . '/src/Utils/AbstractAutoloader.php';
    require_once DUPLICATOR____PATH . '/src/Utils/Autoloader.php';
    \Duplicator\Utils\Autoloader::register();

    require_once DUPLICATOR____PATH . "/define.php";
    \Duplicator\Core\Bootstrap::init($currentPluginHsh);
}
