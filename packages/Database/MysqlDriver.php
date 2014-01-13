<?php

/**
 * PHP-Pharo .
 *
 * An open source application development framework for PHP 5.2.17 or newer .
 *
 * @package		PHP-Pharo
 * @author		Mohammed Alashaal, fb.me/alash3al
 * @copyright	Copyright (c) 2013 - 2015 .
 * @license		GPL-v3
 * @link		http://github.com/alash3al/PHPharo
 */

// ------------------------------------------------------------------------

/**
 * Pharo MysqlDriver Plugin For Database Package .
 *
 * Deal with mysql easily .
 *
 * @package		Database
 * @author		Mohammed Alashaal
 */
class Database_MysqlDriver extends Database
{
    /**
     * Database_MysqlDriver::__construct()
     * 
     * Start mysqlDriver
     * 
     * @param string $dbhost
     * @param string $dbuser
     * @param string $dbpassword
     * @param string $dbname
     * @return void
     */
    function __construct($dbhost, $dbname, $dbuser = null, $dbpassword = null)
    {
        parent::__construct('mysql:host='.$dbhost.'; dbname='.$dbname.'', $dbuser, $dbpassword);
    }
    
    /**
     * Database_MysqlDriver::insert()
     * 
     * create insert stmnt and execute it .
     * 
     * @param string $table
     * @param string $columns
     * @param array $data
     * @return bool
     */
    function insert($table, $data)
    {
        if(is_array($data)):
        $columns = implode(', ' , array_keys($data));
        $values = implode("\', \'" , $data);
            return (bool)$this->query('INSERT INTO '.$table.'('.$columns.') VALUES ('.$values.')');
        endif;

        // un-known
        return false;
    }
    
    /**
     * Database_MysqlDriver::update()
     * 
     * create update stmnt and execute it .
     * 
     * @param string $table
     * @param array $data
     * @param string $more_sql
     * @param mixed $binds
     * @return bool
     */
    function update($table, array $data, $more_sql = null, $binds = null)
    {
        $cols = null;
        $bound = null;
        // fill columns & bound-values
        foreach($data as $c => &$v):
            $cols[] = $c . ' = ?';
            $bound[] = $v;
        endforeach;
        // free memory
        unset($data, $c, $v);
        $bound = array_merge($bound, (array)$binds);
        // run-it
        return (bool)$this->query('UPDATE '.$table.' SET ' . implode(', ', $cols) . ' ' . $more_sql, $bound);
    }
    
    /**
     * Database_MysqlDriver::delete()
     * 
     * execute delete stmnt
     * 
     * @param string $table
     * @param string $more_sql
     * @param mixed $binds
     * @return bool
     */
    function delete($table, $more_sql = null, $binds = null)
    {
        return (bool)$this->query('DELETE FROM ' . $table . ' ' . $more_sql, $binds);
    }
    
    /**
     * Database_MysqlDriver::select()
     * 
     * run select stmnt .
     * 
     * @param string $table
     * @param string $columns
     * @param string $more_sql
     * @param mixed $binds
     * @return bool
     */
    function select($option)
    {
        $defualt = array(
            'table' => '',
            'filed' => '*',
            'condition' => '1',
            'order' => '1',
            'limit' => '50',
            'while' => 0,
            'return' => 'fetch_assoc');
        $option = array_merge($defualt, $option);
        $query = "SELECT {$option['filed']} FROM {$option['table']} WHERE {$option['condition']} ORDER BY {$option['order']} LIMIT {$option['limit']}";
        $query_run = $this->DB->query($query);
        if (@$query_run->num_rows > 0) {
            if ($option['while'] == 1) {
                while ($fetch_a = $query_run->fetch_assoc()) {
                    $fetch[] = $fetch_a;
                }
            } else {
                switch ($option['return']) {
                    case "fetch_assoc":
                        $fetch = $query_run->fetch_assoc();
                        break;
                    case "num_rows":
                        $fetch = $query_run->num_rows;
                        break;
                    default:
                        $fetch = $query_run->fetch_assoc();
                        break;
                }
            }
            return $fetch;
        }
         return false;
    }
    
    /**
     * Database_MysqlDriver::count()
     * 
     * get rows count of a table .
     * 
     * @param string $table
     * @param string $more_sql
     * @return int
     */
    /*
    $select = array('table' => 'name_table', 'return' => 'num_rows');
    $this->select($select); 
    
    OR $this->count('name_table', 'more_sql');
    */
    function count($table, $more_sql = null)
    {
        $this->query('SELECT COUNT(*) FROM '.$table.' AS num_rows ' . $more_sql);
        return (int)($this->fetch(PDO::FETCH_COLUMN));
    }
    
    /**
     * Database_MysqlDriver::num_rows()
     * 
     * get num rows affected by last sql-stmnt
     * 
     * @return int
     */
    function num_rows()
    {
        return (int)$this->stmnt()->rowCount();
    }
}
