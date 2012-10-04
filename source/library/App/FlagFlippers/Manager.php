<?php
/**
 * Flag and flipper manager
 *
 * @category App
 * @package App_FlagFlippers
 * @copyright company
 */

/**
 * Handle different operations with the Flag and Flippers
 */
class App_FlagFlippers_Manager
{
    public static $indexKey = 'companyFlagFlippers';
    private static $_membersAllowedResources = array(
        'backoffice-index',
    );
    
    private static $_guestsAllowedResources = array(
        'backoffice-profile' => array('login'),
    );
    
    /**
     * Load the ACL to the Registry if is not there
     * 
     * This function takes care about generating the acl from the db
     * if the info is not in the registry and/or cache.
     * 
     * If the acl is inside the cache we load it from there.
     * 
     * @return void
     */
    public static function load(){
        if(!App_FlagFlippers_Manager::_checkIfExist()){
            if(!$acl = App_FlagFlippers_Manager::_getFromCache()){
                $acl = App_FlagFlippers_Manager::_generateFromDb();
                App_FlagFlippers_Manager::_storeInCache($acl);
            }
            
            App_FlagFlippers_Manager::_storeInRegistry($acl);
        }
    }
    
    /**
     * Regenerate the Acl from the DB and update the cache and Zend_Registry
     *
     * @return boolean
     */
    public static function save(){
        $acl = App_FlagFlippers_Manager::_generateFromDb();
        App_FlagFlippers_Manager::_storeInCache($acl);
        App_FlagFlippers_Manager::_storeInRegistry($acl);
    }
    
    /**
     * Check if a role is allowed for a certain resource
     *
     * @param string $role 
     * @param string $resource 
     * @return boolean
     */
    public static function isAllowed($role = NULL, $resource = NULL, $action = NULL){
        if(empty($role)){
            $user = BaseUser::getSession();
            $role = $user->group->name;
        }
        
        if(!empty($resource)){
            $resource = strtolower(CURRENT_MODULE) . '-' . $resource;
        }
        
        if(!empty($action)){
            $action = App_Inflector::camelCaseToDash($action);
        }
        
        return App_FlagFlippers_Manager::_getFromRegistry()->isAllowed($role, $resource, $action);
    }
    
    /**
     * Log a message related to Flag And Flippers
     *
     * @param string $msg 
     * @param string $level 
     * @return void
     */
    public static function log($msg, $level = Zend_Log::INFO){
        Zend_Registry::get('Zend_Log_FlagFlippers')->log($msg, $level);
    }
    
    /**
     * Check if the acl exists in Zend_Registry
     *
     * @return boolean
     */
    private static function _checkIfExist(){
        return Zend_Registry::isRegistered(App_FlagFlippers_Manager::$indexKey);
    }
    
    /**
     * Get Acl from Registry
     *
     * @return void
     */
    private static function _getFromRegistry(){
        if(App_FlagFlippers_Manager::_checkIfExist()){
            return Zend_Registry::get(App_FlagFlippers_Manager::$indexKey);
        }
        
        return FALSE;
    }
    
    /**
     * Retrieve the acl from the cache
     *
     * @return Zend_Acl | boolean
     */
    private static function _getFromCache(){
        $cacheHandler = App_DI_Container::get('CacheManager')->getCache('default');
        
        if($acl = $cacheHandler->load(App_FlagFlippers_Manager::$indexKey)){
            return $acl;
        }
        
        return FALSE;
    }
    
    /**
     * Generate the Acl object from the permission file
     *
     * @return Zend_Acl
     */
    private static function _generateFromDb(){
        $aclObject = new Zend_Acl();
        $aclObject->deny();
        
        //Get all the models
        $backofficeUserModel = new BackofficeUser();
        $groupModel = new Group();
        $flagModel = new Flag();
        $flipperModel = new Flipper();
        $privilegeModel = new Privilege();
        
        //Add all groups
        $groups = $groupModel->fetchAllThreaded();
        foreach($groups as $group){
            if($group->parent_name){
                $aclObject->addRole(new Zend_Acl_Role($group->name), $group->parent_name);
            }else{
                $aclObject->addRole(new Zend_Acl_Role($group->name));
            }
        }
        
        //Add all users
        $users = $backofficeUserModel->findAll();
        foreach($users as $user){
            $aclObject->addRole(new Zend_Acl_Role($user->username), $user->groupNames);
        }
        
        //Add all resources
        $flags = $flagModel->fetchAll();
        foreach($flags as $flag){
            $aclObject->addResource(new Zend_Acl_Resource($flag->name));
        }
        
        //Add hardcoded resources
        $aclObject->addResource('frontend-error');
        $aclObject->addResource('backoffice-error');
        
        //Populate the ACLs
        $flippers = $flipperModel->fetchAll();
        foreach($flippers as $flipper){
            switch(APPLICATION_ENV){
                case APP_STATE_PRODUCTION:
                    $flag = $flag->active_on_prod;
                    break;
                default:
                    $flag = $flag->active_on_dev;
            }
            
            $privilege = $flipper->findParentRow('Privilege');
            $flipper->privilegeName = $privilege->name;
            
            $group = $flipper->findParentRow('Group');
            $flipper->groupName = $group->name;
            
            $flag = $flipper->findParentRow('Flag');
            $flipper->flagName = $flag->name;
            
            if(Zend_Registry::get('IS_PRODUCTION')){
                $envAllowed = $flag->active_on_prod;
            }else{
                $envAllowed = $flag->active_on_dev;
            }
            
            if($flipper->allow && $envAllowed){
                $aclObject->allow($flipper->groupName, $flipper->flagName, $flipper->privilegeName);
            } else {
                $aclObject->deny($flipper->groupName, $flipper->flagName, $flipper->privilegeName);
            }
        }
        
        //Hardcode basic paths for members
        foreach(App_FlagFlippers_Manager::$_membersAllowedResources as $resource){
            $aclObject->allow('members', $resource);
        }
        
        //Hardcode basic paths for guests
        foreach(App_FlagFlippers_Manager::$_guestsAllowedResources as $resource => $roles){
            if(!is_array($roles)){
                $aclObject->allow('guests', $resource);
            }else{
                foreach($roles as $r){
                    $aclObject->allow('guests', $resource, $r);
                }
            }
        }
        
        //Everbody can see the errors
        $aclObject->allow(null, 'frontend-error');
        $aclObject->allow(null, 'backoffice-error');
        
        //Admins are allowed everywhere
        $aclObject->allow('administrators');
        
        return $aclObject;
    }
    
    /**
     * Store the Acl in the cache
     *
     * @return void
     */
    private static function _storeInCache($acl = NULL){
        if(is_null($acl) && App_FlagFlippers_Manager::_checkIfExist()){
            $acl = App_FlagFlippers_Manager::_getFromRegistry();
        }
        
        if(empty($acl)){
            throw new Exception('You must provide a valid Acl in order to store it');
        }
        
        $cacheHandler = App_DI_Container::get('CacheManager')->getCache('default');
        
        $cacheHandler->save($acl, App_FlagFlippers_Manager::$indexKey);
    }
    
    /**
     * Store the Acl in the Registry
     *
     * @return void
     */
    private static function _storeInRegistry($acl){
        Zend_Registry::set(App_FlagFlippers_Manager::$indexKey, $acl);
    }
}