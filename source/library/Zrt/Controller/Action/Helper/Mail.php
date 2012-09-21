<?php

class Core_Controller_Action_Helper_Mail extends Zend_Controller_Action_Helper_Abstract
{

    /**
     *
     * @var Zend_Mail
     */
    private $_mail;

    public function __call($name, $arguments)
    {
        $this->_mail = new Zend_Mail('utf-8');
        $options = $arguments[0];
        $f = new Zend_Filter_Word_CamelCaseToDash();
        $tplDir = APPLICATION_PATH . '/../emailing/';
        $mailView = new Zend_View();
        $layoutView = new Zend_View();
        $mailView->setScriptPath($tplDir);
        $layoutView->setScriptPath($tplDir);
        $template = strtolower($f->filter($name)) . '.phtml';
        $subjects = new Zend_Config_Ini(APPLICATION_PATH . '/configs/mailing.ini', 'subjects');
        $subjects = $subjects->toArray();
        if (!is_readable(realpath($tplDir . $template))) {
            throw new Zend_Mail_Exception('No existe template para este email');
        }
        if (!array_key_exists($name, $subjects) || trim($subjects[$name]) == "") {
            throw new Zend_Mail_Exception('Subject no puede ser vacío, verificar mailing.ini');
        } else {
            $options['subject'] = $subjects[$name];
        }
        if (!array_key_exists('to', $options)) {
            throw new Zend_Mail_Exception('Falta indicar destinatario en $options["to"]');
        } else {
            $v = new Zend_Validate_EmailAddress();
            if (!$v->isValid($options['to'])) {
                //throw new Zend_Mail_Exception('Email inválido');
                // En lugar de lanzar un error, mejor lo logeo.
                $log = Zend_Registry::get('log');
                $log->warn('Email inválido: ' . $options['to']);
            }
        }
        foreach ($options as $key => $value) {
            $mailView->assign($key, $value);
            $options['subject'] = str_replace('{%' . $key . '%}', $value,
                $options['subject']);
        }
        $mailView->addHelperPath('Core/View/Helper', 'Core_View_Helper');
        $layoutView->addHelperPath('Core/View/Helper', 'Core_View_Helper');
        $mailViewHtml = $mailView->render($template);
        $layoutView->assign('emailTemplate', $mailViewHtml);
        $mailHtml = $layoutView->render('_layout.phtml');
        $this->_mail->setBodyHtml($mailHtml);
        $this->_mail->addTo($options['to']);
        $this->_mail->setSubject($options['subject']);
        $this->_mail->send();
    }

}