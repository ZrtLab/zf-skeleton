<?php


/**
 * @author slovacus
 * @version 0.1 
 */
class Zrt_View_Helper_PreviousPage extends Zrt_View_Helper_Abstract
{

    public function PreviousPage()
    {
        return Zrt_Service_LastVisited::getLastVisited();
    }

    protected function _checkPage($param)
    {
        
    }

}


