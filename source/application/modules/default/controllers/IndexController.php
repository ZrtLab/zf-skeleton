<?php

class Default_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $formLogin = new App_Form_Login();
        $this->view->assign('formLogin', $formLogin);
    }

}

