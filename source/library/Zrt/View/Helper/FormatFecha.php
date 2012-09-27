<?php

/**
 * @author slovacus
 * @version 0.1 
 * TODO: Areglar
 */
class Zrt_View_Helper_FormatFecha extends Zrt_View_Helper_Abstract
{

    /**
     *
     * @var Zend_Date
     */
    protected $_date = null;

    public function __construct()
    {
        parent::__construct();
        $this->_date = new Zend_Date();
    }

    /**
     *
     * @param type $formato
     * @return type 
     */
    public function FormatFecha()
    {
        return $this->_formatFecha();
    }

    /**
     * @todo areglar codigo para que trabaje desde el application.ini
     * @return type 
     */
    protected function _formatFecha()
    {
        $this->_date->setOptions(array('format_type' => 'php'));
        $this->_date->setLocale(Zend_Registry::get('Zend_Locale'));
        $this->_date->setTimestamp(Zend_Date::now());
        return $this->_date->toString('l, d F Y');
    }

}
