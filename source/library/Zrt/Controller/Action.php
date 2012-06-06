<?php

class Zrt_Controller_Action
    extends Zend_Controller_Action
{

    protected $_url;
    protected $_translate;

    public function __call($method, $args)
    {
        $this->_redirect("/default/index/index");
    }

    public function init()
    {

        $this->view->messages = $this->_helper->flashMessenger->getMessages();

        $this->_url =
            $this->getRequest()->getModuleName()
            . '/' . $this->getRequest()->getControllerName();
    }

    public function preDispatch()
    {
        parent::preDispatch();
    }

}