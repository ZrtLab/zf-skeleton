<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(__DIR__ . '/../application'));

defined('APPLICATION_PUBLIC')
    || define('APPLICATION_PUBLIC', realpath(__DIR__ . '/'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV',
        (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

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