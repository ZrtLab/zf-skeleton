<?php

/**
 * Example email
 *
 * @category App
 * @package App_Mail
 * @copyright company
 */
class App_Mail_Example extends App_Mail_Abstract
{
    /**
     * Set email related info
     */
    public function __construct(){
        parent::__construct();
        
        $this->_template = 'example_en.phtml';
        $this->_subject = $this->t->_('Example email');
    }
}
