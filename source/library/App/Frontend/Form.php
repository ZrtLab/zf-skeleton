<?php
/**
 * Parent form for all the frontend forms
 *
 * @category App
 * @package App_Frontend
 * @copyright company
 */

abstract class App_Frontend_Form extends App_Form
{
    /**
     * Overrides init() in App_Form
     * 
     * @access public
     * @return void
     */
    public function init(){
        parent::init();
        
        $config = App_DI_Container::get('ConfigObject');
        
        // add an anti-CSRF token to all forms
        $csrfHash = new Zend_Form_Element_Hash('csrfhash');
        $csrfHash->setOptions(
            array(
                'required'   => TRUE,
                'filters'    => array(
                    'StringTrim',
                    'StripTags',
                ),
                'validators' => array(
                    'NotEmpty',
                ),
                'salt' => $config->security->csrfsalt . get_class($this),
            )
        );
        $this->addElement($csrfHash);
        
        $formName = new Zend_Form_Element_Hidden('formName');
        $formName->setOptions(
            array(
                'filters'    => array(
                    'StringTrim',
                    'StripTags',
                ),
                'value' => get_class($this)
            )
        );
        $this->addElement($formName);
    }
    
    /**
     * Overrides render() in App_Form
     * 
     * @param Zend_View_Interface $view 
     * @access public
     * @return string
     */
    public function render(Zend_View_Interface $view = NULL){
        $this->clearDecorators();
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => $this->_partial, 'form' => $this, 'view' => $this->getView()))
        ));
        
        foreach($this->getElements() as $element){
            $element->clearDecorators();
            
            if($element instanceof Zend_Form_Element_File){
                $element->setDecorators(array(
                    array('File'),
                    array('Errors')
                ));
            }else{
                $element->setDecorators(array(
                    array('ViewHelper'),
                    array('Errors')
                ));
            }
            
            $element->getView()->getHelper('FormErrors')->setElementStart('<strong class="error"><em>');
            $element->getView()->getHelper('FormErrors')->setElementEnd('</em></strong>');
            $element->getView()->getHelper('FormErrors')->setElementSeparator('</em><em>');
        }
        
        if(NULL === $this->getAttrib('id')) {
            $controllerName = Zend_Registry::get('controllerName');
            $actionName = Zend_Registry::get('actionName');
            
            $this->setAttrib('id', $controllerName . '-' . $actionName);
        }
        
        return parent::render($view);
    }
}