<?php

class Zrt_Conversion_Dbf
    extends Zrt_Conversion_Abstract
    implements Zrt_Conversion
{

    private $_dBase;

    public function __construct($options)
    {
        $this->_dBase = dbase_open($options['file'], 0);
        if ($this->_dBase === false) {
            throw new Zend_Exception('Ошибка открытия файла импорта');
        }
    }

    public function __destruct()
    {
        dbase_close($this->_dBase);
    }

    public function Write($data)
    {
        
    }

    public function Read()
    {
        
    }

    public function ListWrite($data)
    {
        foreach ($data as $row) {
            
        }
    }

    public function ListRead()
    {
        $count = dbase_numrecords($this->_dBase);
        
        for ($i = 1; $i <= $count; $i++) {
            $row = dbase_get_record_with_names($this->_dBase, $i);
            
        }
    }

    public function Close()
    {
        
    }

    public function Open($options)
    {
        
    }

}