<?php

/**
 * PHP Pharaoh 'PHPharo'
 * PHPharo is a full featured oop non-mvc modular framework that
 * helps you create any type of app(s)
 * it`s very fast light
 * @license GPL-V3
 * @author PHPharo | Mohammed Abdullah Al-Ashaal
 * @link <https://twitter.com/phpharo> <PHPharo@Gmail.Com>
 * @copyright 2013
 */

/* ------------------------------------------------------------- */

/**
 * db
 * PDO Wrapper Class
 * @author PHPharo
 * @version 0.2
 * @since 0.1 RC2
 */
class db
{
    
    protected static $Config = false;
    protected static $PDO;
    protected static $Query;
    protected static $Prepared;
    protected static $Binds_Params;
    protected static $Fetch_Types = array(
                                            'assoc' => PDO::FETCH_ASSOC,
                                            'object' => PDO::FETCH_OBJ
                                         );
    
    
    /**
     * db::config()
     * set connection configuration
     * @param string $dns
     * @param string $username
     * @param string $password
     * @param string $options
     * @return void
     */
    public static function config($dns, $username = null, $password = null, array $options =
        array())
    {
        self::$Config = array(
                                'dns' => $dns ,
                                'username' => $username ,
                                'password' => $password ,
                                'options' => $options
                            );
    }
    
    /**
     * db::connect()
     * create normal connection
     * @return void
     */
    public static function connect()
    {
        self::_checker();
        $config = self::$Config;
        $default = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT);
        $config['options'] = array_merge($config['options'],$default);
        try {
            @self::$PDO = new PDO($config['dns'], $config['username'], $config['password'], $config['options']);
        }
        catch (PDOException $e) {
            die(self::_error($e->getMessage()));
        }
    }
    
    /**
     * db::pc_connect()
     * create persistant connection
     * @return void
     */
    public static function pc_connect()
    {
        self::_checker();
        $config = self::$Config;
        $default = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT, PDO::ATTR_PERSISTENT => true);
        $config['options'] = array_merge($config['options'],$default);
        try {
            @self::$PDO = new PDO($config['dns'], $config['username'], $config['password'], $config['options']);
        }
        catch (PDOException $e) {
            die(self::_error($e->getMessage()));
        }
    }

    /**
     * db::query()
     * create query
     * @param string $query_string
     * @param string $binds_params
     * @param bool $execute
     * @return bool if execute is true or prepared stmnt if execute is false
     */
    public static function query($query_string, array $binds_params = array() ,$execute = false)
    {
        self::_checker();
        self::$Query = $query_string;
        self::$Binds_Params = $binds_params;
        self::$Prepared = self::$PDO->prepare(self::$Query);

        if($execute)
            return self::execute();

        return self::$Prepared;
    }
    
    /**
     * db::Qfetch()
     * Quick Query Fetch
     * @param string $query_string
     * @param array $binds_params
     * @param string $fetch_type
     * @return
     */
    public static function Qfetch($query_string,array $binds_params = array(),$fetch_type = 'assoc')
    {
        self::query($query_string,$binds_params);
        return self::fetch($fetch_type);
    }

    /**
     * db::execute()
     * execute query
     * @return
     */
    public static function execute()
    {
        self::_checker();
        return self::$Prepared->execute(self::$Binds_Params);
    }

    /**
     * db::fetch()
     * fetch data
     * @default assoc
     * @param string $type
     * @return
     */
    public static function fetch($type = 'assoc')
    {
        self::_checker();
        self::execute();
        return self::$Prepared->fetchAll(self::$Fetch_Types[$type]);
    }
    
    /**
     * db::num_rows()
     * get number of affected rows
     * @return
     */
    public static function num_rows()
    {
        return count(self::fetch());
    }
    
    /**
     * db::information()
     * information about server, client & connection
     * @return
     */
    public static function information()
    {
        self::_checker();
        $info = array();
        
        $info['server_version'] = self::$PDO->getAttribute(PDO::ATTR_SERVER_VERSION);
        $info['server_info'] = self::$PDO->getAttribute(PDO::ATTR_SERVER_VERSION);
        $info['client_version'] = self::$PDO->getAttribute(PDO::ATTR_CLIENT_VERSION);
        $info['connection_status'] = self::$PDO->getAttribute(PDO::ATTR_CONNECTION_STATUS);
        
        return (object)$info;
    }
    
    /**
     * db::last_inserted()
     * get last inserted id
     * @return
     */
    public static function last_inserted()
    {
        return self::$PDO->lastInsertId();
    }
    
    /**
     * db::insert()
     * 
     * @param string $table
     * @param array $columns
     * @param array $values
     * @return void
     */
    public static function insert( $table ,array $array )
    {
        $columns = array_keys($array);
        $values = array_values($array);
        
        $sql = 'INSERT INTO ' . $table . '( ' . implode( ', ', $columns ) . ' )  VALUES( ' ;
        $binds = $values;
        $new_vals = '';
        // convert values to ?
        for( $i = 1; $i<= (count($values)); ++$i ) {
             ($i < count($values)) ? $new_vals .= '?, ' : '';
             ($i == count($values)) ? $new_vals .= '?' : '';
        }
        // complete query
        $sql .= $new_vals . ' )';
        // return insert state
        return self::query( $sql, $binds, true );
    }
    
    /**
     * db::delete()
     * 
     * @param string $table
     * @param string $where
     * @param array $where_binds
     * @return
     */
    public static function delete( $table , $where = '' ,array $where_binds = array() )
    {
        // where statement
        $where = (strlen(trim($where)) < 1) ? '' : ' WHERE ' . $where ;
        // delete sql statement
        $sql = 'DELETE FROM ' . $table . $where ;
        // execute
        return self::query($sql,$where_binds,true);
    }
    
    /**
     * db::update()
     * 
     * @param string $table
     * @param array $set
     * @param string $where
     * @param array $where_binds
     * @return
     */
    public static function update( $table , array $set , $where = '', array $where_binds = array() )
    {
        // where statement
        $where_stmt = (strlen(trim($where)) < 1) ? '' : ' WHERE ' . $where ;
        // create array with sets
        $sets = array();
        // convert set vals to '?'
        $set_binds = array_values($set);
        foreach( $set as $k => $v ) {
            $sets[] = $k . ' = ?';
        }
        // set statement
        $set_stmt = ' SET ' . implode( ', ' , $sets );
        // merge set binds with where binds
        $all_binds = array_merge( $set_binds , $where_binds );
        // merge all to create update statement
        $sql = 'UPDATE ' . $table . $set_stmt . $where_stmt;
        //execute
        return self::query($sql,$all_binds,true);
    }
    
    /**
     * db::select()
     * 
     * @param string $table
     * @param array $columns
     * @param string $where
     * @param array $where_binds
     * @return
     */
    public static function select( $table , array $columns , $where = '', array $where_binds = array() , $extra = '')
    {
        // where statement
        $where_stmt = (strlen(trim($where)) < 1) ? '' : ' WHERE ' . $where ;
        // sql statement
        $sql = 'SELECT ' . implode(', ', $columns) . ' FROM ' . $table . $where_stmt . ' ' . $extra;
        //return $sql;
        // create query
        self::query($sql,$where_binds);
        // execute & fetch
        return self::fetch('assoc');
    }
    
    /**
     * db::table_exists()
     * check if table exists or not
     * @param string $table
     * @return bool
     */
    public static function table_exists($table)
    {
        return (bool)@self::query('SELECT 1 FROM ' . $table . ' LIMIT 1', array() , true);
    }
    
    /**
     * db::close()
     * close the connection
     * @return
     */
    public static function close()
    {
        self::$PDO = null;
        return true;
    }
    
    /**
     * db::_checker()
     * Check if the config is done or not
     * @return
     */
    protected static function _checker()
    {
        if(self::$Config == false)
            die(self::_error('You Didn\'t Config The Connections Info'));
        
    }
    
    /**
     * db::_error()
     * Show db Error
     * @param string $error
     * @return
     */
    protected static function _error($error)
    {
        $css = 'padding:8px;color:#555;background:#f9f9f9;margine:auto;border:1px solid #ccc;';
        return '<div style="' . $css . '"> <b>' . __class__ . 'Error ::</b> ' . $error .
            ' </div>';
    }
    
}
