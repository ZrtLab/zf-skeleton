<?php


class Zrt_Application_Resource_Api extends Zend_Application_Resource_ResourceAbstract
{

    /**
     *
     * @var Zend_Session_Namespace
     */
    protected $_session = null;

    /**
     *
     * @var type 
     */
    protected $_options = array();

    /**
     *
     * @var type 
     */
    protected $_token = null;

    /**
     *
     * @var array
     */
    protected $_error = null;

    /**
     *
     * @var type 
     */
    protected $_secret = null;

    public function init()
    {
        $this->_options = $this->getOptions();

        $this->_verifyOptions();
        $this->_setData();
        $this->_setKeyApi();
        
        $this->_session = new Zend_Session_Namespace('api');
        Zend_Registry::set('api', $this);
    }

    /**
     *
     * @param type $code
     * @return type 
     */
    public function getError($code)
    {
        return $this->_error[$code];
    }

    /**
     * @author slovacus
     * @param type $options 
     */
    private function _setKeyApi()
    {
        $this->_options['keyApi'] = base64_encode(
            $this->_token . "$$" . $this->_secret
        );
    }

    /**
     * @author slovacus
     * @return null 
     */
    protected function _setData()
    {
        $this->_token = $this->_options['token'];
        $this->_secret = $this->_options['secret'];
        $this->_error = $this->_options['error'];
    }

    private function _verifyOptions()
    {
        if (!$this->_options) {
            throw new Zrt_Exception_Abstract(
                "no se esta configurando los options "
            );
        }
    }

    public function get_session()
    {
        return $this->_session;
    }

    public function set_session($_session)
    {
        $this->_session = $_session;
    }

    public function get_options()
    {
        return $this->_options;
    }

    public function set_options($_options)
    {
        $this->_options = $_options;
    }

    public function get_token()
    {
        return $this->_token;
    }

    public function set_token($_token)
    {
        $this->_token = $_token;
    }

    public function get_secret()
    {
        return $this->_secret;
    }

    public function set_secret($_secret)
    {
        $this->_secret = $_secret;
    }

}


