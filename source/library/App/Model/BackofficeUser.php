<?php
/**
 * Model that manages the users within the application
 *
 * @category backoffice
 * @package backoffice_models
 * @copyright company
 */

class BackofficeUser extends BaseUser
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
    protected $_name = 'backoffice_users';
    
    /**
     * Holds the associated model class
     * 
     * @var string
     * @access protected
     */
    protected $_rowClass = 'App_Table_BackofficeUser';
    
    /**
     * Updates the user's profile. 
     * 
     * @param array $data 
     * @access public
     * @return void
     */
    public function updateProfile(array $data){
        $user = Zend_Auth::getInstance()->getIdentity();
        $data['id'] = $user->id;
        
        $this->save($data);
    }
    
    /**
     * Overrides save() in App_Model
     * 
     * @param array $data 
     * @access public
     * @return int
     */
    public function save(array $data){
        $id = parent::save($data);
        if (isset($data['groups']) && is_array($data['groups']) && !empty($data['groups'])) {
            $groups = $data['groups'];
        } else {
            $groups = array();
        }
        
        $userGroupModel = new BackofficeUserGroup();
        $userGroupModel->saveForUser($groups, $id);
        
        return $id;
    }
    
    /**
     * Overrides insert() in App_Model
     * 
     * @param array $data 
     * @access public
     * @return int
     */
    public function insert($data){
        $data['last_password_update'] = new Zend_Db_Expr('NOW()');
        $data['password'] = BackofficeUser::hashPassword($data['password']);
        $data['password_valid'] = 0;
        
        return parent::insert($data);
    }
    
    /**
     * Overrides getAll() in App_Model
     * 
     * @param int $page 
     * @access public
     * @return Zend_Paginator
     */
    public function findAll($page = 1){
        $paginator = parent::findAll($page);
        $users = array();
        
        foreach($paginator as $user){
            $user->groups = $user->findManyToManyRowset('Group', 'BackofficeUserGroup');
            
            foreach($user->groups as $group){
                $user->groupNames[] = $group->name;
                $user->groupIds[] = $group->id;
            }
            
            $users[] = $user;
        }
        
        return Zend_Paginator::factory($users);
    }
    
    /**
     * Overrides findById() in App_Model
     * 
     * @param int $userId 
     * @access public
     * @return array
     */
    public function findById($userId){
        $user = parent::findById($userId);
        if(!empty($user)){
            $user->groups = $user->findManyToManyRowset('Group', 'BackofficeUserGroup');
            
            foreach($user->groups as $group){
                $user->groupNames[] = $group->name;
                $user->groupIds[] = $group->id;
            }
        }
        
        return $user;
    }
    
    /**
     * Overrides delete() in App_Model.
     *
     * When an user is deleted, all associated objects are also
     * deleted
     * 
     * @param mixed $where 
     * @access public
     * @return int
     */
    public function delete($where){
        if (is_numeric($where)) {
            $where = $this->_primary . ' = ' . $where;
        }
        
        $select = new Zend_Db_Select($this->_db);
        $select->from($this->_name);
        $select->where($where);
        
        $rows = $this->_db->fetchAll($select);
        $userGroupModel = new BackofficeUserGroup();
        
        foreach ($rows as $row) {
            $userGroupModel->deleteByUserId($row['id']);
        }
        
        return parent::delete($where);
    }
    
    /**
     * Changes the current user's password
     * 
     * @param string $password 
     * @access public
     * @return void
     */
    public function changePassword($password){
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            throw new Zend_Exception('You must have one authenticated user in the application in order to be able to call this method');
        }
        
        $user = Zend_Auth::getInstance()->getIdentity();
        
        $password = BackofficeUser::hashPassword($password);
        
        $this->update(
            array(
                'password' => $password,
                'last_password_update' => new Zend_Db_Expr('NOW()'),
                'password_valid' => 1
            ),
            $this->_db->quoteInto('id = ?', $user->id)
        );
    }
}