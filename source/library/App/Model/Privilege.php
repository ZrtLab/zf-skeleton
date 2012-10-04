<?php
/**
 * Model that manages the privileges
 *
 * @package backoffice_models
 * @copyright company
 */

class Privilege extends App_Model
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
    protected $_name = 'privileges';
    
    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_Privilege';
    
    /**
     * Define the relationship with another tables
     *
     * @var array
     */
    protected $_referenceMap = array(
        'Flag' => array(
            'columns' => 'flag_id',
            'refTableClass' => 'Flag',
            'refColumns' => 'id'
        ),
    );
    
    /**
     * Finds a privilege based on its name and the id of the
     * resource it belongs to
     * 
     * @param string $name 
     * @param int $resourceId 
     * @access public
     * @return void
     */
    public function findByNameAndFlagId($name, $resourceId){
        $select = $this->_select();
        $select->from($this->_name);
        $select->where('name = ?', $name);
        $select->where('flag_id = ?', $resourceId);
        
        $privilege = $this->fetchRow($select);
        
        $privilege->flag = $privilege->findParentRow('Flag');
        $privilege->flagName = $privilege->flag->name;
        
        return $privilege;
    }
    
    /**
     * Retrieves all the privileges attached to
     * the specified resource
     * 
     * @param mixed $resourceId 
     * @access public
     * @return void
     */
    public function findByFlagId($resourceId){
        $select = $this->_select();
        $select->from($this->_name);
        $select->where('flag_id = ?', $resourceId);
        $select->order('name ASC');
        
        $privileges = $this->fetchAll($select);
        
        foreach($privileges as $privilege){
            $privilege->flag = $privilege->findParentRow('Flag');
            $privilege->flagName = $privilege->flag->name;
        }
        
        return $privileges;
    }
    
    /**
     * Overrides getAll() in App_Model
     * 
     * @param int $page 
     * @access public
     * @return Zend_Paginator
     */
    public function findAll($page = 1){
        $paginator = $this->fetchAll();
        $privileges = array();
        
        foreach($paginator as $privilege){
            $privilege->flag = $privilege->findParentRow('Flag');
            $privilege->flagName = $privilege->flag->name;
            
            $privileges[] = $privilege;
        }
        
        $paginator = Zend_Paginator::factory($privileges);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(App_DI_Container::get('ConfigObject')->paginator->items_per_page);
        
        return $paginator;
    }
    
    /**
     * Overrides findById() in App_Model
     * 
     * @param int $userId 
     * @access public
     * @return array
     */
    public function findById($privilegeId){
        $privilege = parent::findById($privilegeId);
        if(!empty($privilege)){
            $privilege->flag = $privilege->findParentRow('Flag');
            $privilege->flagName = $privilege->flag->name;
        }
        
        return $privilege;
    }
    
    /**
     * Overrides deleteById() in App_Model
     * 
     * @param int $privilegeId
     * @access public
     * @return void
     */
    public function deleteById($privilegeId){
        $flipperModel = new Flipper();
        
        $this->delete($this->_db->quoteInto('id = ?', $privilegeId));
        $flipperModel->deleteByPrivilegeId($privilegeId);
        
        return TRUE;
    }
}