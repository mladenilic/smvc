<?php

class Smvc_Model_Db extends Smvc_Model_Abstract
{
    protected $_db;
    protected $_statment = "";
    protected $_preparedStmt;
    
    public function __construct($dbName)
    {
        Smvc_Debug::start("Smvc_Model_Db::__constrict('{$dbName}')");
        Smvc_Debug::assert(is_string($dbName));
        $host = Smvc_App::getConfig("database", "host");
        $user = Smvc_App::getConfig("database", "user");
        $password = Smvc_App::getConfig("database", "password");
        try {
            $this->_db = new PDO("mysql:host=$host;dbname=$dbName;charset=UTF8", $user, $password);
            $this->_db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        Smvc_Debug::finish("Smvc_Model_Db::__constrict('{$dbName}')");
    }
    
    public function getAdapter()
    {
        return $this->_db;
    }
    
    public function getTables()
    {
        $stmt = $this->getAdapter()->query("SHOW TABLES");
        $stmt->setFetchMode(PDO::FETCH_NUM);
        return $stmt->fetchAll();
    }
    
    public function select($columns = "*")
    {
        Smvc_Debug::assert(is_string($columns));
        $this->_statment .= "SELECT $columns ";
        return $this;
    }
    
    public function from($table)
    {
        Smvc_Debug::assert(is_string($table));
        $this->_statment .= "FROM $table ";
        return $this;
    }
    
    public function where($condition)
    {
        Smvc_Debug::assert(is_string($condition));
        $this->_statment .= "WHERE $condition ";
        return $this;
    }
    
    public function limit($limit)
    {
        Smvc_Debug::assert(is_string((string)$limit));
        $this->_statment .= "LIMIT {$limit} ";
        return $this;
    }
    
    public function prepare($statement = "") 
    {
        Smvc_Debug::assert(is_string($statement));
        if ($statement !== "") {
            $this->_statment = rtrim($statement, ";") . ";";
        }
        
        $this->_preparedStmt = $this->getAdapter()->prepare($this->_statment);
        $this->_statment = "";
        
        return $this;
    }
    
    public function exec(array $params = array()) 
    {
        $this->_preparedStmt->execute($params);       
        return $this;
    }
    
    public function fetchAll() 
    {
        return $this->_preparedStmt->fetchAll();
    }
}