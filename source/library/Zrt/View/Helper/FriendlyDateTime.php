<?php

/**
 * Description of Hace
 *
 * @author eanaya
 */
class App_View_Helper_FriendlyDateTime extends Zend_View_Helper_Abstract
{

    public function FriendlyDateTime($diahora)
    {
        
        $fh = new Zend_Date($diahora);
        
        $dateFormat = sprintf(
            "%s %s %s %s:%s %s",
            Zend_Date::DAY,
            Zend_Date::MONTH_NAME_SHORT,
            Zend_Date::YEAR,
            Zend_Date::HOUR_AM,
            Zend_Date::MINUTE,
            Zend_Date::MERIDIEM
        );
        return $fh->get($dateFormat);
    }
    
}