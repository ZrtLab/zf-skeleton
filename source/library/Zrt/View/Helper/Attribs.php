<?php


/**
 * Description of Attribs
 *
 * @author 
 */
class App_View_Helper_Attribs extends Zend_View_Helper_HtmlElement
{

    public function Attribs($attribs)
    {

        if (!is_array($attribs)) {
            return '';
        }

        $attr = array_map(
            function ($item) {
                if (is_array($item)) {
                    return implode(' ', $item);
                }
                return $item;
            }, $attribs
        );

        return $this->_htmlAttribs($attr);
    }

}