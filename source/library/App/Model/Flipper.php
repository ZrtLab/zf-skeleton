<?php
/**
 * Model for managing the Flippers in the application.
 *
 * It operates according to the following rules:
 * - each controller is considered a resource
 * - each controller action is considered a privilege
 * - each user group is considered to be a role
 *
 * For details, see http://framework.zend.com/manual/en/zend.acl.introduction.html
 *
 * @package backoffice_models
 * @copyright company
 */

class Flipper extends App_Model
{
    /**
     * Column for the primary key
     *
     * @var string
     * @access protected
     */
    protected $_primary = 'id';
    
    /**
     * Holds the table's name
     *
     * @var string
     * @access protected
     */
    protected $_name = 'flippers';
    
    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_Flipper';
    
    /**
     * Define the relationship with another tables
     *
     * @var array
     */
    protected $_referenceMap = array(
        'Group' => array(
            'columns' => 'group_id',
            'refTableClass' => 'Group',
            'refColumns' => 'id'
        ),
        'Flag' => array(
            'columns' => 'flag_id',
            'refTableClass' => 'Flag',
            'refColumns' => 'id'
        ),
        'Privilege' => array(
            'columns' => 'privilege_id',
            'refTableClass' => 'Privilege',
            'refColumns' => 'id'
        ),
    );
    
    /**
     * Finds all the Flippers associated with a certain group
     * 
     * @param int $groupId 
     * @access public
     * @return void
     */
    public function findByGroupId($groupId){
        $select = $this->_select();
        $select->where('group_id = ?', $groupId);
        
        return $this->fetchAll($select);
    }
    
    /**
     * Saves the permissions for a group
     * 
     * @param array $data 
     * @access public
     * @return void
     */
    public function savePermissions($data){
        $this->delete($this->_db->quoteInto('group_id = ?', $data['group_id']));
        
        foreach($data['flipper'] as $resourceId => $privileges){
            foreach($privileges as $privilegeId => $allow){
                if($allow){
                    try{
                        $this->insert(array(
                            'group_id' => $data['group_id'],
                            'flag_id' => $resourceId,
                            'privilege_id' => $privilegeId,
                            'allow' => 1
                        ));
                    }catch(Zend_Exception $ze){
                        // nothing special, just a duplicate key
                    }
                } else {
                    $this->delete(array(
                        'group_id' => $data['group_id'],
                        'flag_id' => $resourceId,
                        'privilege_id' => $privilegeId,
                    ));
                }
            }
        }
    }
    
    /**
     * Deletes all associations with the given privilege id
     * 
     * @param int $privilegeId
     * @access public
     * @return void
     */
    public function deleteByPrivilegeId($privilegeId){
        $this->delete($this->_db->quoteInto('privilege_id = ?', $privilegeId));
    }
}