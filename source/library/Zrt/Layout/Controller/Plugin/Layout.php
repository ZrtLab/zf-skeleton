<?php

class Zrt_Layout_Controller_Plugin_Layout extends Zend_Layout_Controller_Plugin_Layout
{
    /*
      public function preDispatch(Zend_Controller_Request_Abstract $request)
      {
      $config = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();

      $moduleName = $request->getModuleName();


      if (isset($config[$moduleName]['resources']['layout']['layout'])) {
      $layoutScript = $config[$moduleName]['resources']['layout']['layout'];

      Zend_Layout::getMvcInstance()->setLayout($layoutScript);
      }

      if (isset($config[$moduleName]['resources']['layout']['layoutPath'])) {

      $layoutPath = $config[$moduleName]['resources']['layout']['layoutPath'];
      $moduleDir = Zend_Controller_Front::getInstance()->getModuleDirectory();

      Zend_Layout::getMvcInstance()->setLayoutPath($layoutPath);
      }
      }
     */
}

?>
