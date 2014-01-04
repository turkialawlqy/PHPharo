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
 * Pharo SqliteDriver Plugin For Database Package .
 *
 * Deal with mysql easily .
 *
 * @package		Database
 * @author		Mohammed Alashaal
 */
class Database_SqliteDriver extends Database
{
    /**
     * Database_SqliteDriver::__construct()
     * 
     * Start SqliteDriver
     * 
     * @param string $filename
     * @return void
     */
    function __construct($filename = ':memory:')
    {
        parent::__construct('sqlite:'.$filename);
        $this->query('pragma synchronous = off;');
    }
    
    /**
     * Database_SqliteDriver::insert()
     * 
     * create insert stmnt and execute it .
     * 
     * @param string $table
     * @param string $columns
     * @param array $data
     * @return bool
     */
    function insert($table, $columns, array $data)
    {
        // just one insertion .
        // array( array(), array() ).
        if(!is_array($data[0]) and !isset($data[1])):
            // for prepared statement
            $values = implode(', ', array_fill(1, count($data), '?'));
            // run it .
            return (bool)$this->query('INSERT INTO ' . $table . '('.$columns.') VALUES('.$values.')', $data);
        endif;
        
        // for multi-insertion .
        if(is_array($data[0]) and isset($data[1]) and is_array($data[1])):
            $bound = array();
            $values = implode(' UNION ', array_fill(1, count($data), ' SELECT ' . implode(', ', array_fill(1, count($data[0]), '?'))));
            foreach($data as &$a) $bound = array_merge($bound, $a);
            // free some memory
            unset($data, $a);
            // run-it
            return (bool)$this->query('INSERT INTO ' . $table . '(' . $columns . ') ' . $values, $bound);
        endif;
        
        // un-known
        return false;
    }
    
    /**
     * Database_SqliteDriver::update()
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
     * Database_SqliteDriver::delete()
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
     * Database_SqliteDriver::select()
     * 
     * run select stmnt .
     * 
     * @param string $table
     * @param string $columns
     * @param string $more_sql
     * @param mixed $binds
     * @return bool
     */
    function select($table, $columns = '*', $more_sql = null, $binds = null)
    {
        return (bool)$this->query('SELECT '. $columns .' FROM ' . $table . ' ' . $more_sql, $binds);
    }
    
    /**
     * Database_SqliteDriver::count()
     * 
     * get rows count of a table .
     * 
     * @param string $table
     * @param string $more_sql
     * @return int
     */
    function count($table, $more_sql = null)
    {
        $this->query('SELECT COUNT(*) FROM '.$table.' AS num_rows ' . $more_sql);
        return (int)($this->fetch(PDO::FETCH_COLUMN));
    }
    
    /**
     * Database_SqliteDriver::num_rows()
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
