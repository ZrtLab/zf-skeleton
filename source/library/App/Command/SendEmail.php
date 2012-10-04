<?php
/**
 * Command
 *
 * @package default
 */
class App_Command_SendEmail extends App_Command_Abstract{
    /**
     * Store the command name
     *
     * @var string
     */
    private $_commandName = 'sendEmail';
    
    /**
     * Convenience method to run the command
     *
     * @param string $name
     * @param mixed $args
     * @return boolean
     */
    public function onCommand($name, $args){
        if(strcasecmp($name, $this->_commandName) !== 0){
            return FALSE;
        }
        
        //Do the command
        $mailObject = sprintf('App_Mail_%s', $args['type']);
        $mail = new $mailObject();
        $mail->recipients = $args['recipients'];
        $mail->send($args);
        
        return TRUE;
    }
}