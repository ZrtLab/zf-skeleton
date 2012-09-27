<?php


/**
 * Description of Hace
 *
 * @author eanaya
 */
class App_View_Helper_FriendlyDate extends Zend_View_Helper_Abstract
{

    public function FriendlyDate($diahora)
    {

        $fh = new Zend_Date($diahora);

        return $diahora == '' ? '' : $fh->get(
                sprintf(
                    "%s %s %s", Zend_Date::DAY, Zend_Date::MONTH_NAME_SHORT,
                    Zend_Date::YEAR
                )
            );
    }

}