<?php

/**
 * Notification email sent to the administrator when an error occurs.
 * The behaviour should be configured in the application.ini
 *
 * @category App
 * @package App_Mail
 * @copyright company
 */
class App_Mail_Notification extends App_Mail_Abstract
{
    /**
     * Set email related info
     */
    public function __construct(){
        parent::__construct();
        
        $this->_template = 'notification_en.phtml';
        $this->_subject = $this->t->_('Error notification from your website');
    }
}
