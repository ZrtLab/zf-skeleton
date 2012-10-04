<?php
/**
 * Logger Wrapper to integrate SNS on errors and info messages
 *
 * @category App
 * @package App_Logger
 * @copyright company
 */
class App_Logger
{
    /**
     * Write messages to the log and send notifications to email if configured
     *
     * @param string $msg
     * @param int
     * @return void
     */
    public static function log($msg, $level = Zend_Log::INFO){
        App_DI_Container::get('GeneralLog')->log($msg, $level);
        
        $config = App_DI_Container::get('ConfigObject');
        if($config->system->notifications->notify_on_errors){
            if($config->system->notifications->use_sns){
                if($level == Zend_Log::ERR){
                   App_DI_Container::get('SNSFrontendErrors')->publish('Critical Error', $msg);
                }
                
                if($level == Zend_Log::INFO){
                    App_DI_Container::get('SNSFrontendInfo')->publish('Info event', $msg);
                }
            }else{
                $command = new App_Command_SendEmail();
                $command->onCommand('sendEmail', array(
                    'type' => 'Notification',
                    'recipients' => $config->system->notifications->recipients->toArray(),
                    'message' => $msg,
                    'level' => $level
                ));
            }
        }
    }
}