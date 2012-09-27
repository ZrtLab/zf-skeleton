<?php

/**
 * @author slovacus 
 * @todo Terminar
 */
class Zrt_Service_LastVisited
{

    /**
     * Example use:
     * App_Helpers_LastVisited::saveThis($this->_request->getRequestUri());
     */
    public static function saveThis()
    {
        $url = Zend_Controller_Front::getInstance()->getRequest();
        $lastPg = new Zend_Session_Namespace('history');
        $lastPg->last = "/{$url->getControllerName()}/{$url->getActionName()}";
    }

    /**
     * I typically use redirect:
     * $this->_redirect(App_Helpers_LastVisited::getLastVisited());
     */
    public static function getLastVisited()
    {
        $lastPg = new Zend_Session_Namespace('history');
        if (!empty($lastPg->last)) {
            $path = $lastPg->last;
            //$lastPg->unsetAll();
            return $path;
        }

        return '';
    }

}
