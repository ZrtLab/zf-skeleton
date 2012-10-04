<?php
/**
 * Default model class for all company models
 *
 * @category App
 * @package App_Model
 * @copyright company
 */

abstract class App_Model extends Zend_Db_Table_Abstract
{
    /**
     * Name of the column whose content will be displayed
     * on <select> widgets
     * 
     * @var mixed
     * @access protected
     */
    protected $_displayColumn = NULL;
    
    /**
     * Default behaviour for quering the model: should return an
     * array or a Zend_Paginator object
     * 
     * @var bool
     * @access protected
     */
    protected $_returnPaginators = TRUE;
    
    /**
     * Receives an array of data that needs to be saved
     * into the database. If the primary key is contained in
     * this array, it will do an update, otherwise, it will do
     * an insert
     *
     * It returns the primary key of the inserted / updated row
     *
     * @param array $data 
     * @access public
     * @return int
     */
    public function save(array $data){
        $primary = (is_array($this->_primary)? $this->_primary[1] : $this->_primary);
        
        if(isset($data[$primary]) && $data[$primary]) {
            // we have a non-null value for the primary key, check if we can update
            $select = $this->_select();
            $select->where($primary . '= ?', $data[$primary]);
            $select->reset(Zend_Db_Table::COLUMNS);
            $select->columns(array('COUNT(' . $primary . ')'));
            
            if($this->fetchRow($select) == 1){
                // we have valid pk, update it
                $id = $data[$primary];
                $this->update($data, $this->_db->quoteInto($primary . '= ?', $data[$primary]));
                return $id;
            } else {
                // we don't have a valid pk, insert it
                $data[$primary] = NULL;
                return $this->insert($data);
            }
        } else {
            // no primary provided, do a regular insert
            $data[$primary] = NULL;
            return $this->insert($data);
        }
    }
    
    /**
     * Overrides insert() from Zend_Db_Table_Abstract
     *
     * @param mixed $data 
     * @access public
     * @return int
     */
    public function insert(array $data){
        $data = $this->_filter($data);
        return parent::insert($data);
    }
    
    /**
     * Overrides update() from Zend_Db_Table_Abstract
     *
     * @param mixed $data 
     * @param mixed $where 
     * @access public
     * @return int
     */
    public function update(array $data, $where){
        $data = $this->_filter($data);
        $where = $this->_normalizeWhere($where);
        
        return parent::update($data, $where);
    }
    
    /**
     * Overrides delete() from Zend_Db_Table_Abstract
     * 
     * @param mixed $where 
     * @access public
     * @return int
     */
    public function delete($where){
        $where = $this->_normalizeWhere($where);
        return parent::delete($where);
    }
    
    /**
     * Find by slug
     *
     * @param string $slug 
     * @return App_Table
     */
    public function findBySlug($slug){
        $select = $this->select();
        $select->where('slug = ?', $slug);
        
        return parent::fetchRow($select);
    }
    
    /**
     * Deletes a row based on the primary key
     *
     * @param int $id
     * @access protected
     * @return array
     */
    public function deleteById($id){
        if ($this->canBeDeleted($id)) {
            if(is_array($this->_primary)){
                return $this->delete($this->_db->quoteInto($this->_primary[1] . ' = ?', $id));
            }else{
                return $this->delete($this->_db->quoteInto($this->_primary . ' = ?', $id));
            }
        } else {
            throw new Zend_Exception('This item cannot be deleted. Please check the dependencies first.');
        }
    }
    
    /**
     * Filters the input data according to the columns
     * of this table
     *
     * @param array $data
     * @access protected
     * @return array
     */
    protected function _filter($data){
        $filteredData = array();
        foreach($this->info(Zend_Db_Table_Abstract::COLS) as $key) {
            if(isset($data[$key])) {
                $filteredData[$key] = $data[$key];
            }
        }
        
        return $filteredData;
    }
    
    /**
     * Normalizes a $where clause so that it can be fed as
     * an array or as an integer (in which case the integer is 
     * considered to be a value for the primary key)
     * 
     * @param mixed $where 
     * @access protected
     * @return string
     */
    protected function _normalizeWhere($where){
        if (is_numeric($where)) {
            $where = $this->_db->quoteInto($this->_primary . ' = ?', $where);
        } else {
            if (is_array($where)) {
                $parts = array();
                foreach ($where as $key => $value) {
                    if(is_numeric($key)){
                        $parts[] = $value;
                    }else{
                        $part = $this->_db->quoteInto(
                            $this->_db->quoteIdentifier($key) . ' = ?', 
                            $value
                        ); 
                        $parts[] = $part;
                    }
                }
                $where = implode(' AND ', $parts);
            }
        }
        
        return $where;
    }
    
    /**
     * Finds a row based on its value for the
     * primary key. 
     *
     * Use $force to force a default query instead
     * of the one returned by $this->_getSelect()
     * 
     * @param int $id 
     * @param bool $force
     * @access public
     * @return array
     */
    public function findById($id, $force = FALSE){
        if (!is_numeric($id)) {
            return array();
        }
        
        $select = $this->_getSelect($force);
        
        $column = $this->_extractTableAlias($select) . '.' . $this->_primary[1];
        $select->where($column . ' = ?', $id);
        
        return $this->fetchRow($select);
    }
    
    /**
     * Returns a paginator with all the elements
     * 
     * Use $force to force a default query instead
     * of the one returned by $this->_getSelect()
     *
     * @param int $page
     * @param bool $paginate
     * @param bool $force
     * @access public
     * @return Zend_Paginator
     */
    public function findAll($page = 1, $paginate = NULL, $force = FALSE){
        $select = $this->_getSelect($force);
        return $this->_paginate($select, $page, $paginate);
    }
    
    /**
     * Searches elements in the current model according to the $criteria array or
     * Zend_Db_Expr. 
     *
     *  
     * @param string|array|Zend_Db_Expr $criteria 
     * @param int $page 
     * @param bool $paginate 
     * @param bool $force 
     * @access public
     * @return mixed
     */
    public function search($criteria, $page = 1, $paginate = NULL, $force = FALSE){
        $select = $this->_getSelect($force);
        
        if (is_array($criteria)) {
            $queryParts = array();
            foreach ($criteria as $colname => $colval) {
                if (is_array($colval)) {
                    $parts = array();
                    foreach($colval as $val) {
                        $parts[] = $this->_db->quote($val);
                    }
                    $queryParts[] = $this->_db->quoteIdentifier($colname) . ' IN (' . implode(',', $parts) . ')';
                } else {
                    if ($colval instanceof Zend_Db_Expr) {
                        $queryParts[] = $this->_db->quoteIdentifier($colname) . ' = ' . $colval;
                    } else {
                        $queryParts[] = $this->_db->quoteIdentifier($colname) . ' = ' . $this->_db->quote($value);
                    }
                }
            }
            if (count($queryParts) > 1) {
                $where = '(' . implode(') AND (', $queryParts) . ')';
            } else {
                $where = $queryParts;
            }
        } else {
            $where = $criteria;
        }
        
        $select->where($where);
        
        return $this->_paginate($select, $page, $paginate);
    }
    
    /**
     * Counts all the elements in the model
     *
     * Use $force to force a default query instead
     * of the one returned by $this->_getSelect()
     * 
     * @param bool $force
     * @access public
     * @return int
     */
    public function count($force = FALSE){
        $select = $this->_getSelect($force);
        
        $select->reset(Zend_Db_Table::COLUMNS);
        $select->columns(array('COUNT(*)'));
        
        return $this->fetchOne($select);
    }
    
    /**
     * Returns a primarykey => displayColumn array to be used
     * for rendering <select> widgets and other Zend_Form_Element_Multi
     * elements
     *
     * Use $force to force a default query instead
     * of the one returned by $this->_getSelect()
     * 
     * @param bool $force
     * @access public
     * @return void
     */
    public function findPairs($force = FALSE){
        if (NULL === $this->_displayColumn) {
            $message = 'Please set the $displayColumn property for instances of the ' . get_class($this) . ' class';
            throw new Zend_Exception($message);
        }
        
        $select = $this->_getSelect($force);
        
        $select->reset(Zend_Db_Table::COLUMNS);
        
        $alias = $this->_extractTableAlias($select);
        $select->columns(array(
            $alias . '.' . $this->_primary[1], 
            $alias . '.' . $this->_displayColumn
        ));
        
        return $this->_db->fetchPairs($select);
    }
    
    /**
     * Checks if the specified element can be deleted or not.
     * Override to add custom logic
     * 
     * @param int $id 
     * @access public
     * @return bool
     */
    public function canBeDeleted($id){
        return TRUE;
    }
    
    /**
     * Returns a default query object for this model.
     * This should be used to define - override if required - the basic array of information
     * that a model should return. All other data fetching method should use
     * this one in order to get the basic query. 
     *
     * Ex: 
     * protected function _select()
     * {
     *     $select = new Zend_Db_Select($this->_db);
     *     $select->from(array($this->_name => 't'));
     *     $select->joinLeft(array('otherTable' => 'o.t_id = t.id'));
     *     
     *     return $select;
     * }
     *
     * Now, methods like findById(), findAll() and count() will fetch the correct data
     *
     * 
     * @access protected
     * @return Zend_Db_Select
     */
    protected function _select(){
        $select = $this->select();
        $select->from($this->_name);
        
        return $select;
    }
    
    /**
     * Wrapper for the _select method that will provide easy implementation
     * of the $force mechanism
     * 
     * @param mixed $force 
     * @final
     * @access protected
     * @return void
     */
    protected final function _getSelect($force = FALSE){
        if ($force) {
            $select = $this->select();
            $select->from($this->_name);
            
            return $select;
        }
        
        return $this->_select();
    }
    
    /**
     * Returns a paginator or an array, depending on the value
     * provided for the $paginate field
     * 
     * @param Zend_Db_Select $select 
     * @param int $page
     * @param bool $paginate 
     * @access protected
     * @return mixed
     */
    protected function _paginate($select, $page, $paginate){
        if (NULL === $paginate) {
            $paginate = $this->_returnPaginators;
        }
        
        if (!$paginate) {
            return $this->fetchAll($select);
        }
        
        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(App_DI_Container::get('ConfigObject')->paginator->items_per_page);
        
        return $paginator;
    }
    
    /**
     * Extracts the current table's alias from a composed
     * query.
     * 
     * @param Zend_Db_Select $select 
     * @access protected
     * @return string
     */
    protected function _extractTableAlias($select){
        $parts = $select->getPart('from');
        foreach($parts as $alias => $part){
            if($part['tableName'] == $this->_name) {
                return $alias;
            }
        }
        
        return $this->_name;
    }
    
    /**
     * Ability to use different db adapters
     *
     * @return void
     */
    protected function _setupDatabaseAdapter(){
        if(isset($this->_adapter)){
            $this->_db = Zend_Registry::get($this->_adapter);
        }else{
            $this->_db = Zend_Db_Table_Abstract::getDefaultAdapter();
        }
    }
}