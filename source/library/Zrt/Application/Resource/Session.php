<?php

/**
 *@author slovacus 
 */
class Zrt_Application_Resource_Session extends Zend_Application_Resource_Session
{

    private $session;

    public function init()
    {
        parent::init();
        $this->session = new Zend_Session_Namespace('session', true);
    }

    public function getSession()
    {
        return $this->session;
    }

}
