<?php
/**
 * Description of Api
 *
 * @author slovacus
 */
class Zrt_Exception_Api extends Zend_Exception
{

    public function __construct($msg = '', $code = 0, Exception $previous = null)
    {
        parent::__construct($msg, $code, $previous);
    }

}


?>
