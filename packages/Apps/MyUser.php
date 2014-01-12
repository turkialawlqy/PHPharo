<?php

/**
 * Apps_MyUser
 * Simple Backend User System.
 * @package Apps
 * @author Amer Alrdadi 
 * @copyright AmerAlrdadi@Gmail.Com
 * @version 1.0
 * @access public
 */
class Apps_MyUser extends Database_MysqlDriver
{
    /**
     * Apps_MyUser::__construct()
     * start Apps_MyUser system .
     * @param string $dbhost
     * @param string $dbname
     * @param string $dbuser
     * @param string $dbpass
     * @return object
     */
    function __construct($dbhost, $dbname, $dbuser, $dbpass)
    {
        parent::__construct($dbhost, $dbname, $dbuser, $dbpass);
        $this->create_tables_sql();
    }
    
    /**
     * Apps_MyUser::newUser()
     * Create new User .
     * @param string $username
     * @param string $email
     * @param string $password
     * @param integer $id_group
     * @param integer $user_date_register
     * @param mixed $active
     * @return bool   
     */
    function newUser($username, $email, $password, $id_group, $user_date_register = 1, $active = 1)
    {
        return (bool)$this->insert('user_info', 'user_name,user_email,user_password,user_id_group,user_date_register,user_active',
        array($username, $email, $password, $id_group, $user_date_register, $active));
    }
    
    /**
     * Apps_MyUser::checkUser()
     * Check is found a User.
     * @param integer $user_id
     * @return bool
     */
    function checkUser($user_id)
    {
        $this->select('user_info', 'user_id', 'WHERE user_id = ? LIMIT 1', $user_id);
        return (bool) $this->num_rows(); 
    }

    /**
     * Apps_MyUser::loginUser()
     * Login User.
     * @param string $user_name
     * @param string $user_email
     * @param string $user_password
     * @return bool
     */
    function loginUser($user_name, $user_email, $user_password)
    {
        $this->select('user_info', '*', 
            'WHERE user_name = ? && user_email = ? && user_password = ? LIMIT 1', 
            array($user_name, $user_email, $user_password)
        );    

        if((bool)$this->num_rows() === true){
            $row = $this->fetch(PDO::FETCH_ASSOC);
            pp_store('user_login',true);
            pp_store('user_id',$row['user_id']);
            pp_store('user_name',$user_name);
            pp_store('user_email',$user_email);
            return true;
        }else{ 
            return false; 
        }
    }

    /**
     * Apps_MyUser::logoutUser()
     * Logout User
     * @return bool
     */
    function logoutUser() 
    {
        if(pp_store('user_login') === true)    
        {
            pp_store('user_login',false); 
            pp_store('user_id',false);
            pp_store('user_name',false);
            pp_store('user_email',false);
            return true;
        }else{
            return false;
        }
    } 

    /**
     * Apps_MyUser::deleteUser()
     * Delete a User.
     * @param integer $user_id
     * @return bool
     */
    function deleteUser($user_id)
    {
        return (bool)$this->delete('user_info', 'WHERE user_id in ('.implode(', ', array_fill(1, count((array)$user_id), '?')).')', (array)$user_id);
    }
    
    /**
     * Apps_MyUser::editUser()
     * Edit a User.
     * @param integer $post_id
     * @param string $new_title
     * @param string $new_content
     * @param string $new_author
     * @param integer $new_category_id
     * @param mixed $new_time
     * @return bool
     */
    function editUser($user_id, $new_username, $new_email, $new_password,$new_id_group,$new_active)
    {
        return (bool)$this->update('user_info', array(
                        'user_name'     => $new_username,
                        'user_email'    => $new_email,
                        'user_password' => $new_password,
                        'user_id_group' => $new_id_group,
                        'user_active'   => $new_active
                   ), 'WHERE user_id = ?', $user_id);
    }
    
    /**
     * Apps_MyUser::getUserByID()
     * Get a User by it's id .
     * @param integer $user_id
     * @return object | null
     */
    function getUserByID($user_id)
    {
        $this->select('user_info', '*', 'WHERE user_id = ?', $user_id);
        return $this->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Apps_MyBlog::getPostsByCategoryID()
     * Get posts by group id
     * @param integer $user_id_group
     * @return
     */
    function getUsersByGroupID($user_id_group)
    {
        return $this->select('user_info', '*', 'WHERE user_id_group = ?', $user_id_group);
    }
    
    /**
     * Apps_MyUser::getAllUsers()
     * Get all Users.
     * @param integer $start
     * @param integer $length
     * @return bool
     */
    function getAllUsers($start = null, $length = null)
    {
        if(!empty($start) and !empty($length)) $limit = ' LIMIT '.(int)$start.','.(int)$length;
        else $limit = null;
        return (bool)$this->select('user_info', '*', $limit);
    }
    
    /**
     * Apps_MyUser::newGroup()
     * Create new User Group .
     * @param string $user_group_name
     * @return bool
     */
    function newGroup($user_group_name)
    {
        return (bool)$this->insert('user_group', 'user_group_name',array($user_group_name));
    }
    
    /**
     * Apps_MyUser::deleteGroup()
     * Delete Group(s) and Users by Group ID .
     * @param integer $group_id
     * @return bool
     */
    function deleteGroup($group_id)
    {
        $this->delete('user_group', 'WHERE user_group_id in ('.implode(', ', array_fill(1, count((array)$group_id), '?')).')', (array)$group_id);
        return (bool)$this->delete('user_info', 'WHERE user_id_group in ('.implode(', ', array_fill(1, count((array)$group_id), '?')).')', (array)$group_id);
    }
    
    /**
     * Apps_MyUser::editGroup()
     * Edit Group User.
     * @param integer $group_id
     * @param string $new_name_group
     * @return bool
     */
    function editGroup($group_id, $new_name_group)
    {
        return (bool)$this->update('user_group', array('user_group_name' => $new_name_group), 'WHERE user_group_id = ?', $group_id);
    }
    
    /**
     * Apps_MyUser::getGroups()
     * Get All Groups .
     * @param integer $start
     * @param integer $length
     * @return bool
     */
    function getGroups($start = null, $length = null)
    {
        if(!empty($start) and !empty($length)) $limit = ' LIMIT '.(int)$start.','.(int)$length;
        else $limit = null;
        return (bool)$this->select('user_group', '*', $limit);
    }
    
    /**
     * Apps_MyBlog::create_tables_sql()
     * Build tables .
     * @return void
     */
    protected function create_tables_sql()
    {
        $this->query('
            CREATE TABLE IF NOT EXISTS `user_group` (
              `user_group_id`   int(11) NOT NULL AUTO_INCREMENT,
              `user_group_name` varchar(100) NOT NULL,
              PRIMARY KEY (`user_group_id`)
            ) ENGINE MYISAM DEFAULT CHARSET UTF8;
        ');

        $this->query('
            CREATE TABLE IF NOT EXISTS `user_info` (
              `user_id`            int(11) NOT NULL AUTO_INCREMENT,
              `user_name`          varchar(100) NOT NULL,
              `user_email`         varchar(50) NOT NULL,
              `user_password`      varchar(160) NOT NULL,
              `user_date_register` varchar(50) NOT NULL,
              `user_id_group`      int(11) NOT NULL,
              `user_active`        tinyint(1) NOT NULL,
              PRIMARY KEY (`user_id`)
            ) ENGINE MYISAM DEFAULT CHARSET UTF8;
        '); 
    }

}
