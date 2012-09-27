<?php

class Zrt_View_Helper_Abstract extends Zend_View_Helper_Abstract
{

    /**
     *
     * @var Zend_Config 
     */
    protected $_config = null;

    public function __construct()
    {
        $this->_config = Zend_Registry::get('config');
    }

}
