<?php
/**
 * This class defines a few common methods to send emails
 *
 * @category App
 * @package App_Mail
 * @copyright company
 */
class App_Mail_Abstract
{
    /**
     * Store the template filename
     *
     * @var string
     */
    protected $_template;
    
    /**
     * Store the email subject
     *
     * @var string
     */
    protected $_subject;
    
    /**
     * Store the recipients of the email
     *
     * @var array
     */
    public $recipients;
    
    /**
     * Instantiate the translator object
     */
    public function __construct(){
        if(!Zend_Registry::isRegistered('Zend_Translate')){
            $bootstrap = Zend_Registry::get('Bootstrap');
            $bootstrap->bootstrap(array('Translator'));
        }
        
        $this->t = Zend_Registry::get('Zend_Translate');
    }
    
    /**
     * Send the email
     *
     * @param array $args
     * @return void
     */
    public function send(array $args){
        if(!Zend_Registry::get('IS_PRODUCTION') && !App_DI_Container::get('ConfigObject')->testing->mail){
            $this->_log(Zend_Layout::getMvcInstance()->getView()->partial($this->_template, $args));
        }else{
            if(App_DI_Container::get('ConfigObject')->system->gearman_support){
                App_DI_Container::get('GearmanClient')->doBackground('send_email', serialize(array(
                    'to' => $this->recipients,
                    'subject' => $this->_subject,
                    'html' => Zend_Layout::getMvcInstance()->getView()->partial($this->_template, $args),
                    'reply' => (array_key_exists('replyTo', $args)? $args['replyTo']->email : NULL),
                    'attachment' => (array_key_exists('attachment', $args)? $args['attachment'] : NULL),
                    'type' => $args['type']
                )));
            }else{
                $mail = new Zend_Mail('utf-8');

                if(App_DI_Container::get('ConfigObject')->system->email_system->send_by_amazon_ses){
                    $transport = new App_Mail_Transport_AmazonSES(array(
                        'accessKey' => App_DI_Container::get('ConfigObject')->amazon->aws_access_key,
                        'privateKey' => App_DI_Container::get('ConfigObject')->amazon->aws_private_key
                    ));   
                }
                
                $mail->setBodyHtml(Zend_Layout::getMvcInstance()->getView()->partial($this->_template, $args));
                
                if(array_key_exists('replyTo', $args)){
                    $mail->setReplyTo($args['replyTo']->email);
                }

                $mail->setFrom(App_DI_Container::get('ConfigObject')->amazon->ses->from_address, App_DI_Container::get('ConfigObject')->amazon->ses->from_name);
                $mail->addTo($this->recipients);
                $mail->setSubject($this->_subject);

                if(isset($transport) && $transport instanceOf App_Mail_Transport_AmazonSES){
                    $mail->send($transport);
                }else{
                    $mail->send();
                }
            }
        }
    }

    /**
     * Store the email in the log
     *
     * @param string $msg 
     * @param string $level 
     * @return void
     */
    private function _log($msg, $level = Zend_Log::INFO){
        App_DI_Container::get('MailerLog')->log($msg, $level);
    }
}