<?php

/**
 * Description of Auth
 *
 * @author Luis Mayta
 */
class Zrt_Controller_Action_Helper_Auth extends Zend_Controller_Action_Helper_Abstract
{

    protected $view;

    public function preDispatch()
    {
        $view = $this->getView();
        $controller = $this->getActionController();
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $view->isAuth = $controller->isAuth = true;
            $authData = Zend_Auth::getInstance()->getStorage()->read();
            $view->auth = $controller->auth = $authData;
        } else {
            $view->isAuth = $controller->isAuth = false;
        }
        parent::preDispatch();
    }

    public function getView()
    {
        if ($this->view !== null) {
            return $this->view;
        }
        $controller = $this->getActionController();
        $this->view = $controller->view;
        return $this->view;
    }

}
