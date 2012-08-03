<?php

/**
 * Allows controllers to log messages easily.
 *
 * @category Zrt
 * @package Zrt_Controller
 */
class Zrt_Controller_Action_Helper_Logger
    extends Zend_Controller_Action_Helper_Abstract
{

    /**
     *
     * @var Zend_Log
     */
    protected $_logger;

    public function __construct()
    {
        $this->_logger = Zend_Registry::get('logger');
    }

    public function direct($message, $priority)
    {
        $this->_logger->log($message, $priority);
    }

    /**
     * Used to passthru Wildfire's err(), info() etc functions.
     * @param $name string
     * @param $arguments array
     * @return void
     */
    public function __call($name, $arguments)
    {
        try {
            $this->_logger->$name($arguments[0]);
        } catch (Zend_Exception $e) {
            // There was an error logging this error, so log that instead! :)
            $this->direct($e->getMessage() . ": $name", Zend_Log::ERR);
        }
    }

}

