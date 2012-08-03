<?php

interface Zrt_Conversion
{

    function Open($options);

    function Close();

    function Write($data);

    function Read();

    function ListWrite($data, $tranlate = null);

    function ListRead();
}