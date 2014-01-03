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
 * Pharo Database Wrapper Class
 *
 * This class enables you to work with any db api easily .
 * it is a PDO wrapper .
 *
 * @package		Database
 * @author		Mohammed Alashaal
 */
class Database
{
    protected $db = false;
    protected $query = false;
    protected $dbname;
    
    /**
     * Database::__construct()
     * connect to the database
     * @param string $dns
     * @param string $dbname
     * @param string $username
     * @param password $password
     * @return mixed
     */
    function __construct($dns, $username = null, $password = null)
    {
        try {
            $this->db = new PDO((string )$dns, (string )$username, (string )$password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if(preg_match('/(.*?)dbname=(.[a-z_\.0-9]+)(.*?)/i', $dns, $m))
                $this->dbname = $m[2];
            return $this->db;
        }
        catch (PDOException $e) {
            throw new DatabaseException($e->getMessage());
            return false;
        }
    }
    
    /**
     * Database::query()
     * run a sql query
     * @param string $statement
     * @param mixed $binded_params
     * @return bool
     */
    function query($statement, $binded_params = null)
    {
        if(!$this->db) die(trigger_error('You must create a connection before start'));
        $this->query = $this->db->prepare($statement);
        return (bool)$this->query->execute((array)$binded_params);
    }
    
    /**
     * Database::fetch()
     * fetch from database
     * @param int $fetch_style
     * @param bool $getAll
     * @return mixed
     */
    function fetch($fetch_style = PDO::FETCH_ASSOC, $getAll = false)
    {
        if(!$this->query) {throw new DatabaseException(' You didn\'t run any query to fetch it\'s result '); exit;}
        if($getAll)
            return $this->query->fetchAll((int)$fetch_style);
        else
            return $this->query->fetch((int)$fetch_style);
    }
    
    /**
     * Database::last_inserted()
     * get the last inserted id
     * @return integer
     */
    function last_inserted()
    {
        return $this->db->lastInsertId();
    }
    
    /**
     * Database::db()
     * access pdo object
     * @return pdo;
     */
    function db()
    {
        return $this->db;
    }
    
    /**
     * Database::stmnt()
     * access pdo statement
     * @return PDOStatement
     */
    function stmnt()
    {
        return $this->query;
    }
}

class DatabaseException extends Exception{}