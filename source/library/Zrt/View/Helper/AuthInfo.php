<?php

class Zrt_View_Helper_AuthInfo extends Zrt_View_Helper_Abstract
{

    /**
     * @var Zend_Auth
     */
    protected $_authService;

    /**
     *
     * @var object
     */
    protected $_authdata = null;

    public function direct($info = null)
    {
        return $this->_authInfo($info);
    }

    public function __construct()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_authData = Zend_Auth::getInstance()->getStorage()->read();
            $this->_authdata = $this->_authdata['postulante'];
        }
    }

    public function AuthInfo()
    {
        return $this->_authdata;
    }

    /**
     * Get user info from the auth session
     *
     * @param string|null $info The data to fetch, null to chain
     * @return string|Zend_View_Helper_AuthInfo
     */
    public function _authInfo($info = null)
    {
        if (null === $this->_authService) {
            $this->_authService = new Auth();
        }

        if (null === $info) {
            return $this;
        }

        if (false === $this->isLoggedIn()) {
            return null;
        }

        return $this->_authService->getIdentity()->$info;
    }

    /**
     * Check if we are logged in
     *
     * @return boolean
     */
    public function isLoggedIn()
    {
        return Zend_Auth::getInstance()->hasIdentity();
    }

}
