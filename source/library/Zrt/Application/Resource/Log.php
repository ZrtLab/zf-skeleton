<?php

class Zrt_Application_Resource_Log extends Zend_Application_Resource_Log
{

    public function init()
    {
        parent::init();
    }

    public function getLog()
    {
        if (null === $this->_log) {
//           $options = array_change_key_case( $this->getOptions() ,
//                                              CASE_LOWER );
            //$db = Zend_Registry::get( 'db' );
//            $column = $options['db']['column'];
//            
//            $writer = new Zend_Log_Writer_Db( $db , $options['db']['table'] , array(
//                        $column['date'] => 'timestamp' ,
//                        $column['ip'] => 'ip' ,
//                        $column['username'] => 'username' ,
//                        $column['useragent'] => 'useragent' ,
//                        $column['url'] => 'url' ,
//                        $column['priority'] => 'priority' ,
//                        $column['message'] => 'message'
//                            ) );
//            $writer = new Zend_Log_Writer_Stream( $options['stream'] );
//            $logger = new Zend_Log( $writer );
//            $this->setLog( $logger );
        }

        return $this->_log;
    }

}
