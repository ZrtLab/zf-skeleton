<?php
/**
 * DB table for managing a user
 *
 *
 * @category App
 * @package App_Table
 * @copyright company
 */

class App_Table_BackofficeUser extends App_Table
{
    /**
     * Store the primary group of the user
     *
     * @var App_Table_Group
     */
    public $group;
    
    /**
     * Store the related groups
     *
     * @var array App_Table_Group
     */
    public $groups;
    
    /**
     * Store an array of the related group names
     *
     * @var array
     */
    public $groupNames;
    
    /**
     * Store an array of related group ids
     *
     * @var string
     */
    public $groupIds;
}