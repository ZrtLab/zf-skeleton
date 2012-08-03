<?php


/**
 * Personalización de Flash Messenger para que soporte tipo de mensaje
 */
class Zrt_Controller_Action_Helper_MyFlashMessenger
        extends Zend_Controller_Action_Helper_FlashMessenger
{

    const DEBUG = 'debug';
    const INFO = 'info';
    const SUCCESS = 'success';
    const WARNING = 'warning';
    const ERROR = 'error';


    /**
     * Niveles de mensaje
     * 
     * @var array
     */
    private $_levels = array(
        'debug' ,
        'info' ,
        'success' ,
        'warning' ,
        'error'
    );


    /**
     * Agrega un mensaje
     * 
     * @param string $message Mensaje a mostrar en pantalla
     * @param string $level Nivel del mensaje
     * 
     * @return void
     */
    public function addMessage( $message , $level = self::INFO )
    {
        $msg = new stdClass();
        $msg->message = $message;
        $msg->level = $level;

        parent::addMessage( $msg );
    }


    /**
     * Permite llamadas dinámicas 
     * Ejem:
     *  - $this->info('Mensaje')
     *  - $this->success('Mensaje')
     *  - $this->error('Mensaje')
     *  
     * @param unknown_type $name
     * @param unknown_type $params
     */
    public function __call( $name , $params )
    {
        if ( in_array( $name ,
                       $this->_levels ) )
        {
            $this->addMessage( $params[0] ,
                               $name );
        }
    }


    public function direct( $message , $level = self::INFO )
    {
        return $this->addMessage( $message ,
                                  $level );
    }


}
