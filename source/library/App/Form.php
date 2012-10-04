<?php
/**
 * Default parent form for all the forms in the application
 *
 * @category App
 * @package App_Form
 * @copyright company
 */

abstract class App_Form extends Zend_Form
{
    /**
     * Convenience method to recognize translatable text with gettext
     *
     * @param string $text 
     * @return void
     */
    public function t($text){
        return $text;
    }
}
