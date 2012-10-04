<?php
/**
 * DB table for managing a privilege
 *
 *
 * @category App
 * @package App_Table
 * @copyright company
 */

class App_Table_Privilege extends App_Table
{
    /**
     * Store the related flag
     *
     * @var App_Table_Flag
     */
    public $flag;
    
    /**
     * Store the related flag_name
     *
     * @var string
     */
    public $flagName;
}