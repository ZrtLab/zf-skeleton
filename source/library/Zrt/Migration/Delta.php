<?php

class Zrt_Migration_Delta
{
    protected $_db;
    protected $_log;
    protected $_author;
    protected $_desc;

    public function __construct($db, Zend_Log $log = null)
    {
        $this->_db = $db;
        $this->_log = $log;
    }

    public function up()
    {
    }

    public function down()
    {
    }

}