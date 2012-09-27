<?php


/**
 * @author slovacus <slovacus@gmail.com>
 * @example path description
 */
class App_View_Helper_Title extends Zend_View_Helper_Abstract
{

    /**
     *
     * @var Zend_Filter_Word_CamelCaseToSeparator 
     */
    protected $_filterWordStS = null;

    /**
     *
     * @var Zend_Filter_Word_DashToCamelCase
     */
    protected $_filter = null;

    public function __construct()
    {
        $this->_filterWordCaC = new
            Zend_Filter_Word_SeparatorToSeparator('-', ' ');
    }

    public function Title($title)
    {
        return ucwords($this->_filterWordCaC->filter($title));
    }

}


