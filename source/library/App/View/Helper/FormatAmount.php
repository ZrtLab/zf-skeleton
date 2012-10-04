<?php
/**
 * Helper that will be used to display amounts throughout 
 * the application
 *
 * 
 * @category App
 * @package App_View
 * @subpackage Helper
 * @copyright company
 */

class App_View_Helper_FormatAmount extends Zend_View_Helper_Abstract
{
    /**
     * Convenience method
     * call $this->formatDate() in the view to access 
     * the helper
     *
     * @access public
     * @return string
     */
    public function formatAmount($amount, $currencyIso = NULL){
        $formattedAmount = new Zend_Currency();
        $formattedAmount->setValue($amount);
        
        if(!is_null($currencyIso)){
            switch($currencyIso){
                case 'EUR':
                    $locale = 'es_ES';
                    break;
                case 'GBP':
                    $locale = 'en_GB';
                    break;
                default:
                    $locale = 'en_US';
                    break;
            }
            
            $formattedAmount->setLocale($locale);
        }
        
        return $formattedAmount;
    }
}