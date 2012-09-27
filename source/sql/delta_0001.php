<?php
class Delta_0001 extends App_Migration_Delta
{
    protected $_author = "Luis Mayta";
    protected $_desc = "Admin user";

    public function up()
    {
//        $sql = "CREATE TABLE t(f INT); DROP TABLE t;";
//        $this->_db->query($sql);
        if(APPLICATION_ENV=='production')
            return true;
        
        $mUser = new App_Model_User();
        $id = $mUser->insert(array(
            'name' => 'Admin',
            'lastname' => 'User',
            'email' => 'admin@example.com',
            'pwd' => App_Auth_Adapter_DbTable_Salted::generatePassword('admin'),
            'role' => App_Model_User::ROLE_ADMIN,
            'active' => 1,
            'created_at' => date(DATE_DB),
            'created_by' => null
        ));
        $mUser->insert(array(
            'name' => 'Dummy',
            'lastname' => 'User',
            'email' => 'user@example.com',
            'pwd' => App_Auth_Adapter_DbTable_Salted::generatePassword('user'),
            'role' => App_Model_User::ROLE_USER,
            'active' => 1,
            'created_at' => date(DATE_DB),
            'created_by' => $id
        ));
        
        return true;
    }
}