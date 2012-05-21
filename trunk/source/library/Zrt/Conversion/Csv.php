<?php

define('ABS_FREE_SPASE_CSV', '#h!@h!@#_abs_free_spase');

class Zrt_Conversion_Csv
{

    private $_File;
    private $_Delimiter = ';';

    public function __construct($options = null)
    {
        if (!is_null($options)) {
            $this->Open($options);
        }
    }

    public function __destruct()
    {
        if (is_resource($this->_File)) {
            $this->Close();
        }
    }

    /**
     * Открытие файла
     *
     * @param array $options file=>'путь к файлу' mode=>'режим открытия'
     */
    public function Open($options)
    {
        $this->_File = fopen($options['file'], $options['mode']);
    }

    public function Close()
    {
        fclose($this->_File);
    }

    public function Write($data)
    {
        return fputcsv($this->_File, $data, $this->_Delimiter);
    }

    /**
     * 
     *
     * @return array
     */
    public function Read()
    {
        return fgetcsv($this->_File, null, $this->_Delimiter);
    }

    public function ListWrite($data, $tranlate = null)
    {
        $cols = array_keys($data[0]);
        
        if (!is_null($tranlate) and !isset($tranlate['prefix'])) {
            $tranlate['prefix'] = '';
        }

        foreach ($cols as $key => $value) {
            if ($value === 'id') {
                unset($cols[$key]);
            }
            if (!is_null($tranlate)) {
                $cols[$key] = t($tranlate['prefix'] . '.' . $cols[$key]);
                
            }
        }

        fputcsv($this->_File, $cols, $this->_Delimiter);
        foreach ($data as $row) {
            foreach ($row as $key => $value) {
                if ($key === 'id') {
                    unset($row[$key]);
                }
            }
            fputcsv($this->_File, $row, $this->_Delimiter);
        }
    }

    public function ListRead()
    {
        
        $data = array();
        
        $is_nodata = true;
        while ($is_nodata) {
            $buf = $this->Read();
            if (($buf[0] !== ABS_FREE_SPASE_CSV)) {
                $is_nodata = false;
            }
        }
        $cols = $buf;
        while ($str = $this->Read()) {
            $row = array();
            foreach ($str as $key => $value) {
                $row[$cols[$key]] = $value;
            }
            $data[] = $row;
        }
        return $data;
    }

}
