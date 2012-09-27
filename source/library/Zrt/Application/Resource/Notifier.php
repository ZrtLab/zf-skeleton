<?php

/**
 * @author slovacus 
 */
class Zrt_Application_Resource_Notifier extends Zend_Application_Resource_ResourceAbstract
{

    /**
     * @var Zend_Log
     */
    private $_log;

    /**
     * @var Zle_Log_Writer_Mail
     */
    private $_mailWriter;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return void
     */
    public function init()
    {
        if (!$this->_mailWriter) {
            if (!isset($this->_options['disabled'])
                || $this->_options['disabled'] == false
            ) {
                $this->_mailWriter = new Zrt_Log_Writer_Mail($this->getOptions());
                $this->getLog()->addWriter($this->_mailWriter);
            }
        }
        return $this->_mailWriter;
    }

    /**
     * Return the log plugin resource
     *
     * @throws Zend_Application_Resource_Exception if log is not configured
     * using plugin resource syntax (i.e. via application.ini)
     * @return Zend_Log
     */
    protected function getLog()
    {
        if (!$this->_log) {
            /** @var $bootstrap Zend_Application_Bootstrap_Bootstrap */
            $bootstrap = $this->getBootstrap();
            // check for log availability
            if (!$bootstrap->hasPluginResource('log')) {
                throw new Zend_Application_Resource_Exception(
                    'This resource depends on Zend_Application_Resource_Log'
                );
            }
            // bootstrap log resource
            $this->_log = $bootstrap->bootstrap('log')->getResource('log');
        }
        return $this->_log;
    }

}
