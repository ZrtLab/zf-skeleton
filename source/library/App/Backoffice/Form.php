<?php
/**
 * Parent form for all the backoffice forms
 *
 * @category App
 * @package App_Backoffice
 * @copyright company
 */

abstract class App_Backoffice_Form extends App_Form
{
    /**
     * URL for the cancelLink
     * 
     * @var mixed
     * @access protected
     */
    protected $_cancelLink;
    
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
    }
    
    /**
     * Overrides render() in App_Form
     * 
     * @param Zend_View_Interface $view 
     * @access public
     * @return string
     */
    public function render(Zend_View_Interface $view = NULL){
        foreach($this->getElements() as $element) {
            $this->_replaceLabel($element);
            
            switch(TRUE){
                case $element instanceof Zend_Form_Element_Hidden:
                case $element instanceof Zend_Form_Element_Hash:
                    $this->_addHiddenClass($element);
                    break;
                case $element instanceof Zend_Form_Element_Checkbox:
                    $this->_appendLabel($element);
                    break;
                case $element instanceof Zend_Form_Element_MultiCheckbox:
                    $element->getDecorator('Label')->setOption('tagOptions', array('class' => 'checkboxGroup'));
                    $element->getDecorator('HtmlTag')->setOption('class', 'checkboxGroup');
                    break;
            }
        }
        
        $this->_cancelLink();
        
        $this->getDecorator('HtmlTag')->setOption('class', 'zend_form clearfix');
        
        if (NULL === $this->getAttrib('id')) {
            $controllerName = Zend_Registry::get('controllerName');
            $actionName = Zend_Registry::get('actionName');
            
            $this->setAttrib('id', $controllerName . '-' . $actionName);
        }
        
        return parent::render($view);
    }
    
    /**
     * Add the hidden class
     * 
     * @param Zend_Form_Element_Abstract $element 
     * @access protected
     * @return void
     */
    protected function _addHiddenClass($element){
        $label = $element->getLabel();
        if (empty($label)) {
            $element->setLabel('');
        }
        
        $element->getDecorator('HtmlTag')
                ->setOption('class', 'hidden');
        
        $element->getDecorator('Label')
                ->setOption('tagOptions', array('class' => 'hidden'));
    }
    
    /**
     * Forces the element's label to be appended to it rather
     * than prepend it
     * 
     * @param Zend_Form_Element_Abstract $element 
     * @access protected
     * @return void
     */
    protected function _appendLabel($element){
        $element->getDecorator('Label')
                ->setOption('placement', Zend_Form_Decorator_Abstract::APPEND);
    }
    
    /**
     * Replaces the default label decorator with a more
     * versatile one
     * 
     * @param Zend_Form_Element_Abstract $element 
     * @access protected
     * @return void
     */
    protected function _replaceLabel($element){
        $decorators = $element->getDecorators();
        if (isset($decorators['Zend_Form_Decorator_Label'])) {
            $newDecorators = array();
            foreach ($decorators as $key => $decorator) {
                if ($key === 'Zend_Form_Decorator_Label') {
                    $label = new App_Form_Decorator_Label();
                    $label->setOptions($decorator->getOptions());
                    
                    $newDecorators['App_Form_Decorator_Label'] = $label;
                } else {
                    $newDecorators[$key] = $decorator;
                }
            }
            $element->clearDecorators();
            $element->setDecorators($newDecorators);
        }
    }
    
    /**
     * Adds a cancel link to the form
     * 
     * @access protected
     * @return void
     */
    protected function _cancelLink(){
        if ($this->_cancelLink !== FALSE) {
            if ($this->_cancelLink === NULL) {
                $this->_cancelLink = '/' . Zend_Registry::get('controllerName');
            }
            
            $cancelLink = Zend_Controller_Front::getInstance()->getBaseUrl() . $this->_cancelLink;
            
            $cancelLinkDecorator = new App_Form_Decorator_Backlink();
            $cancelLinkDecorator->setOption('url', $cancelLink);
            
            $element = $this->getElement('submit');
            $decorators = $element->getDecorators();
            $element->clearDecorators();
            
            foreach($decorators as $decorator) {
                $element->addDecorator($decorator);
                if ($decorator instanceof Zend_Form_Decorator_ViewHelper) {
                    $element->addDecorator($cancelLinkDecorator);
                }
            }
        }
    }
    
    /**
     * Setter for $this->_cancelLink
     *
     * @param string $cancelLink
     * @access public
     * @return void
     */
    public function setCancelLink($cancelLink){
        $this->_cancelLink = $cancelLink;
    }
    
    /**
     * Getter for $this->_cancelLink
     *
     * @access public
     * @return string
     */
    public function getCancelLink(){
        if (NULL === $this->_cancelLink) {
            $this->_cancelLink = '/' . Zend_Registry::get('controllerName') . '/';
        }
        
        return $this->_cancelLink;
    }
}