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
 * Pharo SQLGenerator Class
 *
 * This class enables you to Easily Generate Any Secure And Valid SQL-STMNT .
 *
 * @package		Database
 * @author		Mohammed Alashaal
 */
class Database_SQLGenerator
{
    private $table_name;
    private $bound;
    private $full_sql;
    
    /**
     * Database_SQLGenerator::with()
     * choose table(s) to do ops on .
     * @param string $table
     * @return object
     */
    function with($table)
    {
        $this->table_name = $table;
        return $this;
    }
    
    /**
     * Database_SQLGenerator::select()
     * generate select stmnt
     * @param string $what
     * @return object
     */
    function select($what)
    {
        $this->reset();
        $this->full_sql = "SELECT {$what} FROM {$this->table_name} ";
        return $this;
    }
    
    /**
     * Database_SQLGenerator::update()
     * generate the upate stmnt
     * NOTE: this auto binds params
     * @param string $data
     * @return object
     */
    function update(array $data)
    {
        $this->reset();
        $this->bind(array_values($data));
        $alt = array_fill(1, count($data), ' = ?');
        $cols = array_keys($data);
        $updates = null;
        foreach($cols as &$col)
            foreach($alt as &$a)
                $updates[] = $col . $a;
        $updates = implode(', ', $updates);
        $this->full_sql = "UPDATE {$this->table_name}  SET {$updates} ";
        return $this;
    }
    
    /**
     * Database_SQLGenerator::insert()
     * generae insert stmnt
     * NOTE: this auto-bind params
     * @param string $into
     * @param array $values
     * @return object
     */
    function insert($into, array $values)
    {
        $this->reset();
        if(is_array($into)) $into = implode(', ', $into);
        if(!is_array($values[0])) {
            $this->bind(array_values($values));
            $values = '(' . implode(', ', array_fill(1, count($values), '?')) . ')';
        } else {
            foreach($values as &$v){
                $vals[] = '(' . implode(', ', array_fill(1, count($v), '?')) . ')';
                $this->bind(array_values($v));
            }
            $values = implode(', ', $vals);
        }
        $this->full_sql = "INSERT INTO {$this->table_name} ({$into}) VALUES {$values}";
        return $this;
    }
    
    /**
     * Database_SQLGenerator::delete()
     * generate delete stmnt
     * @return object
     */
    function delete()
    {
        $this->reset();
        $this->full_sql = "DELETE FROM {$this->table_name} ";
        return $this;
    }
    
    /**
     * Database_SQLGenerator::regexp()
     * generate REGEXP stmnt
     * NOTE: this auto-bind params
     * @param string $pattern
     * @return object
     */
    function regexp($pattern)
    {
        $this->full_sql .= " REGEXP ? ";
        $this->bind($pattern);
        return $this;
    }
    
    /**
     * Database_SQLGenerator::where()
     * generate where stmnt
     * @param string $what
     * @return object
     */
    function where($what)
    {
        $this->full_sql .= " WHERE {$what} ";
        return $this;
    }
    
    /**
     * Database_SQLGenerator::matchAgianst()
     * generate MATCH(...) AGAINST(...) stmnt
     * @param string $match_what
     * @param string $agianst_what
     * @return object
     */
    function matchAgianst($match_what, $agianst_what)
    {
        $this->full_sql .= " MATCH({$match_what}) AGAINST({$agianst_what}) ";
        return $this;
    }
    
    /**
     * Database_SQLGenerator::orderBy()
     * generate order by stmnt
     * @param string $what
     * @param string $type
     * @return object
     */
    function orderBy($what, $type = 'DESC')
    {
        $this->full_sql .= " ORDER BY {$what} {$type} ";
        return $this;
    }
    
    /**
     * Database_SQLGenerator::limit()
     * generate limit stmnt
     * @param string $what
     * @return object
     */
    function limit($what)
    {
        $this->full_sql .= " LIMIT {$what} ";
        return $this;
    }

    /**
     * Database_SQLGenerator::using()
     * add custom sql stmnt to the query
     * @param string $sql
     * @return object
     */
    function using($sql)
    {
        $this->full_sql .= " {$sql} ";
        return $this;
    }
    
    /**
     * Database_SQLGenerator::bind()
     * binds some params
     * @param mixed $what
     * @return object
     */
    function bind($what)
    {
        $this->bound = array_merge((array)$this->bound, (array)$what);
        return $this;
    }

    /**
     * Database_SQLGenerator::compile()
     * show the full generated sql code
     * @return string
     */
    function compile()
    {
        $r = (object)array('sql' => $this->full_sql, 'bound' => $this->bound);
        $this->reset();
        return $r;
    }
    
    private function reset()
    {
        $this->bound = null;
        $this->full_sql = null;
    }
}