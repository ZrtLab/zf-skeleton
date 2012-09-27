<?php

class App_Form_User extends Zrt_Form_Abstract
{

    public function init()
    {
        parent::init();

        // name
        $e = new Zend_Form_Element_Text('name');
        $e->setLabel('Name');
        $e->setRequired();
        $v = new Zend_Validate_StringLength(array('min' => 1, 'max' => 45));
        $e->addValidator($v);
        $this->addElement($e);

        // lastname
        $e = new Zend_Form_Element_Text('lastname');
        $e->setLabel('Last Name');
        $v = new Zend_Validate_StringLength(array('min' => 1, 'max' => 45));
        $e->addValidator($v);
        $this->addElement($e);

        // email
        $e = new Zend_Form_Element_Text('email');
        $e->setLabel('E-Mail');
        $e->setRequired();
        $v = new Zend_Validate_StringLength(array('min' => 3, 'max' => 45));
        $e->addValidator($v);
        $v = new Zend_Validate_EmailAddress();
        $e->addValidator($v);
        $this->addElement($e);

        // pwd
        $e = new Zend_Form_Element_Text('pwd');
        $e->setLabel('Password');
        $e->setRequired();
        $v = new Zend_Validate_StringLength(array('min' => 6, 'max' => 64));
        $e->addValidator($v);
        $this->addElement($e);

        // role
        $e = new Zend_Form_Element_Select('role');
        $e->setLabel('Role');
        $e->addMultiOptions(App_Model_User::getRoles());
        $e->setValue(App_Model_User::ROLE_USER);
        $e->setRequired();
        $this->addElement($e);

        // submit
        $e = new Zend_Form_Element_Submit('submit');
        $e->setLabel('Add');
        $e->setAttrib('class', 'btn primary');
        $this->addElement($e);
    }

}

?>
