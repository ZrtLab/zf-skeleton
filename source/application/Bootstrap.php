<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initSession()
    {

        Zend_Session::start();
    }

    public function _initConfig()
    {
        $zconfig = new Zend_Config($this->getOptions());
        Zend_Registry::set('config', $zconfig);
        define('DATE_DB', 'Y-m-d H:i:s');
    }

    public function _initView()
    {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $v = $layout->getView();

        $v->addHelperPath('Zrt/View/Helper', 'Zrt_View_Helper');
        $config = Zend_Registry::get('config');

        //Definiendo Constante para Partials
        defined('MEDIA_URL')
            || define('MEDIA_URL', $config->app->mediaUrl);
        defined('ELEMENTS_URL')
            || define('ELEMENTS_URL', $config->app->elementsUrl);
        defined('SITE_URL')
            || define('SITE_URL', $config->app->siteUrl);
        // Config Built-in View Helpers
        //
        $doctypeHelper = new Zend_View_Helper_Doctype();
        $doctypeHelper->doctype(Zend_View_Helper_Doctype::HTML5);
        $v->headTitle($config->resources->view->title)->setSeparator(' - ');
        $v->headMeta()->appendHttpEquiv('Content-Type',
            'text/html; charset=utf-8');
        $v->headMeta()->appendName("robots", "noindex, nofollow"); // for development
        $v->headMeta()->appendName("author", "slovacus");
        $v->headMeta()->appendName("description", "solviv surco"); //
        $v->headMeta()->setCharset("utf-8");
        $v->headLink()->appendStylesheet($v->s('/css/bootstrap.min.css'), 'all');
        $v->headLink()->appendStylesheet($v->s('/css/main.css'), 'all');
        $v->headLink()->appendStylesheet($v->s('/css/fixie.css'), 'all',
            'lte IE 8');
        $v->headLink(array('rel' => 'shortcut icon', 'href' => $v->s('/images/favicon.ico')));
        $v->headLink(array('rel' => 'image_src', 'href' => $v->s('/images/fb_share.png'), 'id' => "image_src"));
        $v->headLink(array('rel' => 'apple-touch-icon', 'href' => $v->s('/images/apple-touch-icon.png')));
        $v->headLink(array('rel' => 'apple-touch-icon', 'href' => $v->s('/images/apple-touch-icon-72x72.png'), 'sizes' => '72x72')); // fix sizes attribute 
        $v->headLink(array('rel' => 'apple-touch-icon', 'href' => $v->s('/images/apple-touch-icon-114x114.png'), 'sizes' => '114x114')); // fix sizes attribute 
        $v->headScript()->appendFile($v->s('/js/jquery-1.4.2.min.js'));
        $v->headScript()->appendFile($v->s('/js/bootstrap-alerts.js'));
        $v->headScript()->appendFile($v->s('/js/main.js'));
        $v->headScript()->appendFile(
            'http://html5shim.googlecode.com/svn/trunk/html5.js',
            'text/javascript', array('conditional' => 'lt IE 9')
        );
        $js = sprintf("var urls = {siteUrl : '%s'}", $config->app->siteUrl);
        $v->headScript()->appendScript($js);
        $v->headLink()->appendAlternate($v->S('/humans.txt'), 'text/plain',
            'author', '');
    }

    public function _initRegistries()
    {
        $config = Zend_Registry::get('config');

        $this->_executeResource('cachemanager');
        $cacheManager = $this->getResource('cachemanager');
        Zend_Registry::set('cache', $cacheManager->getCache($config->app->cache));

        $this->_executeResource('db');
        $adapter = $this->getResource('db');
        Zend_Registry::set('db', $adapter);

        $this->_executeResource('log');
        $log = $this->getResource('log');
        Zend_Registry::set('log', $log);
    }

    public function _initActionHelpers()
    {
        /* Adding hook action helpers
          Zend_Controller_Action_HelperBroker::addHelper(new App_Controller_Action_Helper_Auth());
          Zend_Controller_Action_HelperBroker::addHelper(new App_Controller_Action_Helper_Security());
          Zend_Controller_Action_HelperBroker::addHelper(new App_Controller_Action_Helper_Mail());
         * */
    }

    public function _initTranslate()
    {
        $translator = new Zend_Translate(
                Zend_Translate::AN_ARRAY,
                APPLICATION_PATH . '/configs/locale/',
                'es',
                array('scan' => Zend_Translate::LOCALE_DIRECTORY)
        );

        Zend_Validate_Abstract::setDefaultTranslator($translator);
    }

    protected function _initZFDebug()
    {
        $config = Zrt_DI_Container::get('ConfigObject');

        $dbAdapter = Zend_Registry::get('dbAdapter');

        $options = array(
            'plugins' => array(
                'Variables',
                'Html',
                'Database' => array('adapter' => array('default' => $dbAdapter)),
                'File' => array('basePath' => ROOT_PATH),
                'Memory',
                'Time',
                'Registry',
                'Exception'
            )
        );

        if ($config->zfdebug->show_cache_panel) {
            $defaultCache = App_DI_Container::get('CacheManager')->getCache('default');

            $options['plugins']['Cache'] = array(
                'backend' => array(
                    $defaultCache->getBackend(),
                )
            );
        }

        $debug = new ZFDebug_Controller_Plugin_Debug($options);

        $frontController = Zend_Controller_Front::getInstance()->registerPlugin($debug);
    }

    /**
     * Initialize and register the plugins
     * 
     * @access protected
     * @return void
     */
    protected function _initPlugins()
    {
        $frontController = Zend_Controller_Front::getInstance();

        // Application_Plugin_VersionHeader sends a X-SF header with the system version for debugging
        $frontController->registerPlugin(new App_Plugin_VersionHeader());
    }

    /**
     * Initialize the translation system
     *
     * @return void
     */
    protected function _initTranslator()
    {
        $this->bootstrap('Locale');

        //Extract some info from the request
        $lang = Zend_Registry::get('Zend_Locale')->getLanguage();
        $translationFile = ROOT_PATH . '/library/App/Translations/' . $lang . '.mo';

        //Check if the translations file is available, if not fallback default to english
        if (!file_exists($translationFile)) {
            $translationFile = APPLICATION_PATH . '/modules/' . CURRENT_MODULE . '/translations/en.mo';
        }

        $options = array(
            'adapter' => 'gettext',
            'content' => $translationFile,
            'locale' => $lang,
            'disableNotices' => App_DI_Container::get('ConfigObject')->translations->disable_notices,
            'logMessage' => "Missing translation: %message%",
            'logUntranslated' => App_DI_Container::get('ConfigObject')->translations->log_missing_translations
        );

        //Create a zend_log for missing translations
        if (App_DI_Container::get('ConfigObject')->translations->log_missing_translations) {
            $pathLog = ROOT_PATH . '/logs/' . CURRENT_MODULE . '/missing_translations/' . date('Ymd') . '_' . $lang . '.log';
            $writer = new Zend_Log_Writer_Stream($pathLog);
            $logger = new Zend_Log($writer);

            $options['log'] = $logger;
        }

        $translate = new Zend_Translate($options);

        Zend_Registry::set('Zend_Translate', $translate);

        Zend_Validate_Abstract::setDefaultTranslator($translate);
        Zend_Form::setDefaultTranslator($translate);
    }

    /**
     * Initialize the Flag and Flipper System
     *
     * @return void
     */
    protected function _initFlagFlippers()
    {
        $this->bootstrap('ModulePaths');

        $path = realpath(APPLICATION_PATH . '/../logs/' . CURRENT_MODULE . '/flagflippers.log');
        $logger = new Zend_Log(new Zend_Log_Writer_Stream($path));

        if (!Zend_Registry::get('IS_PRODUCTION')) {
            $logger->addWriter(new Zend_Log_Writer_Firebug());
        }

        Zend_Registry::set('Zend_Log_FlagFlippers', $logger);

        App_FlagFlippers_Manager::load();
    }

    /**
     * Initialize and configure the jQuery options
     *
     * @return void
     */
    protected function _initJQuery()
    {
        $view = Zend_Layout::getMvcInstance()->getView();
        $view->jQuery()->addStylesheet('/css/jquery-ui.css');
        $view->jQuery()->setLocalPath('/js/jquery.min.js');
        $view->jQuery()->setUiLocalPath('/js/jquery-ui.min.js');
    }

}

