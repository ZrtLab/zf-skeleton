<?php

/**
 * @author slovacus 
 */
abstract class Zrt_Form_Abstract extends Zend_Form implements Zrt_Form_Interface
{

    /**
     * 
     */
    public function _setValue()
    {
        
    }

    /**
     * 
     */
    public function _setRequireds()
    {
        
    }

    public function _setAttribs()
    {
        
    }

    public function _setOptions()
    {
        
    }

    public function _setLabels()
    {
        
    }

    public function _setValidators()
    {
        
    }

    public function _addContentElement()
    {
        
    }

    public function init()
    {
        $this->setMethod('post');
        parent::init();

        $this->_setLabels();
        $this->_setAttribs();
        $this->_setOptions();
        $this->_setRequireds();
        $this->_setValue();
        $this->_addContentElement();
    }

    public function _setDisplayGroup()
    {
        
    }

}