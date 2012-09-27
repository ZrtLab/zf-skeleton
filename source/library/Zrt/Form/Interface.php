<?php

/**
 * @author slovacus 
 */
interface Zrt_Form_Interface
{

    public function _setValue();

    public function _setRequireds();

    public function _setAttribs();

    public function _setOptions();

    public function _setLabels();

    public function _setValidators();

    public function _addContentElement();

    public function init();

    public function _setDisplayGroup();
}