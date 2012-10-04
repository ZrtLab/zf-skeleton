<?php
/**
 * DB table for managing a Flipper
 *
 *
 * @category App
 * @package App_Table
 * @copyright company
 */

class App_Table_Flipper extends App_Table
{
    /**
     * Store the privilege name
     *
     * @var string
     */
    public $privilegeName;
    
    /**
     * Store the flag name
     *
     * @var string
     */
    public $flagName;
    
    /**
     * Store the group name
     *
     * @var string
     */
    public $groupName;
    
    /**
     * Store the group id
     *
     * @var int
     */
    public $groupId;
}