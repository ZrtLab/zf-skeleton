<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(__DIR__ . '/../application'));

defined('APPLICATION_PUBLIC')
    || define('APPLICATION_PUBLIC', realpath(__DIR__ . '/'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV',
        (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'local'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR,
        array(
        realpath(APPLICATION_PATH . '/../library'),
        get_include_path(),
    )));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run


$application = new Zend_Application(
        APPLICATION_ENV,
        APPLICATION_PATH . '/configs/application.ini'
);

$resources = new Zend_Config_Ini(APPLICATION_PATH . "/configs/resources.ini");
$routes = new Zend_Config_Ini(APPLICATION_PATH . "/configs/routes.ini");
$params = new Zend_Config_Ini(APPLICATION_PATH . "/configs/params.ini");
$cache = new Zend_Config_Ini(APPLICATION_PATH . "/configs/cache.ini");

$application->setOptions($application->mergeOptions($application->getOptions(),
        $resources->toArray()));
$application->setOptions($application->mergeOptions($application->getOptions(),
        $routes->toArray()));
$application->setOptions($application->mergeOptions($application->getOptions(),
        $params->toArray()));
$application->setOptions($application->mergeOptions($application->getOptions(),
        $cache->toArray()));


$application->bootstrap()
    ->run();

class index
{

    protected $_application = null;

    public static function getApplication()
    {
        $application = new Zend_Application(
                APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini'
        );

        return $application;
    }

}