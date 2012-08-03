<?php

class Zrt_Migration_Manager
{

    private $_db;
    private $_deltaPath;
    private $_log;

    const DBVERSION_TABLE_NAME = 'dbversion';
    const DBVERSION_COL_NAME = 'version';
    const PREFIX = 'delta_';
    const ZERO_FILL = 4;
    const ENV_DEV = 'development';
    const ENV_PROD = 'production';

    public function __construct($adapter, $deltaPath = ".", Zend_Log $log = null)
    {
        $this
            ->setAdapter($adapter)
            ->setDeltaPath($deltaPath)
            ->setLog($log);
    }

    public function sync()
    {
        $this->_log->log("===== Sync Starts =====", Zend_Log::INFO);
        $currentVersion = $this->getVersion();
        $currentVersion++;
        $success = true;
        while ($success) {
            $success = false;
            $filename = sprintf(
                "%s%0" . self::ZERO_FILL . "d.php", self::PREFIX,
                $currentVersion
            );
            $filePath = $this->_deltaPath . "/" . $filename;
            $fileExists = file_exists($filePath);
            if ($fileExists) {
                include($filePath);
                $className = sprintf(
                    "%s%0" . self::ZERO_FILL . "d", ucfirst(self::PREFIX),
                    $currentVersion
                );
                $classExists = class_exists($className);
                if ($classExists) {
                    $delta = new $className($this->_db, $this->_log);
                    $success = $delta->up();
                    if ($success) {
                        $msg = "Se Aplico el Delta $currentVersion";
                        echo $msg . PHP_EOL;
                        $this->_log->log($msg, Zend_Log::INFO);
                        $this->setVersion($currentVersion++);
                    } else {
                        $msg = "Ha fallado el Delta $currentVersion";
                        $this->_log->log($msg, Zend_Log::ERR);
                        throw new Zend_Exception($msg);
                    }
                }
            }
        }
        $this->_log->log("===== Sync Ends =====", Zend_Log::INFO);
    }

    private function setAdapter($adapter)
    {
        $this->_db = $adapter;
        return $this;
    }

    private function setDeltaPath($deltaPath)
    {
        $this->_deltaPath = $deltaPath;
        return $this;
    }

    private function setLog(Zend_Log $log)
    {
        $this->_log = $log;
        return $this;
    }

    private function getVersion()
    {
        $v = $this->_db->query(
            "SELECT " . self::DBVERSION_COL_NAME . " FROM " .
            self::DBVERSION_TABLE_NAME
        );
        $version = $v->fetch();
        return (int) $version[self::DBVERSION_COL_NAME];
    }

    private function setVersion($version)
    {
        $v = $this->_db->query(
            "UPDATE " . self::DBVERSION_TABLE_NAME . " SET " .
            self::DBVERSION_COL_NAME . " = ? ", $version
        );
        $v->execute();
    }

}
