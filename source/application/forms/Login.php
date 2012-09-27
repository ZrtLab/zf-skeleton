<?php

class App_Form_Login extends Zrt_Form_Abstract
{

    /**
     *
     * @var Zend_Form_Element_Text
     */
    protected $_email = null;

    /**
     *
     * @var Zend_Form_Element_Password
     */
    protected $_password = null;

    /**
     *
     * @var Zend_Form_Element_Button 
     */
    protected $_submit = null;

    public function __construct($options = null)
    {
        $this->_email = new Zend_Form_Element_Text('email');
        $this->_password = new Zend_Form_Element_Password('password');
        $this->_submit = new Zend_Form_Element_Button('submit');
        parent::__construct($options);
    }

    public function _setLabels()
    {
        $this->_email->setLabel('E-mail');
        $this->_password->setLabel('Password');
        $this->_submit->setLabel('Login');
    }

    public function _setValidators()
    {
        $this->_email->addValidator(new Zend_Validate_StringLength(
                array('min' => 3, 'max' => 45))
        );
        $this->_email->addValidator(new Zend_Validate_EmailAddress());
    }

    public function _setRequireds()
    {
        $this->_email->setRequired();
        $this->_password->setRequired();
    }

    public function _addContentElement()
    {
        $this->addElement($this->_email);
        $this->addElement($this->_password);
        $this->addElement($this->_submit);
    }

    public function init()
    {
        parent::init();
    }

}

?>
