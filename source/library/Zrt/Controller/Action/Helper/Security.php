<?php

class Zrt_Controller_Action_Helper_Security extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * @todo security.ini
     */
    protected $loginRoute = 'login';
    protected $allowed = array(
        'default.auth.login'
    );

    public function preDispatchDisabled()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $r = $this->getRequest();
            $current = $r->getModuleName() . '.' . $r->getControllerName() . '.' . $r->getActionName();
            if (!in_array($current, $this->allowed)) {
                $r = new Zend_Controller_Action_Helper_Redirector();
                $r->gotoRoute(array(), $this->loginRoute, true);
            }
        }
        parent::preDispatch();
    }

}