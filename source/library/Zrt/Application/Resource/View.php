<?php

class Zrt_Application_Resource_View
    extends Zend_Application_Resource_ResourceAbstract
{

    protected $_view;

    public function init()
    {
        return $this->getView();
    }

    public function getView()
    {
        if (null === $this->_view) {

            $options = $this->getOptions();

            $view = new Zend_View();


            $view->doctype($options['doctype']);
            $view->headTitle($options['title']);
            $view->headMeta()
                ->appendName('keywords', 'limesoft,cms');


            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
            $viewRenderer->setView($view);

            $this->_view = $view;
        }

        return $this->_view;
    }

}
