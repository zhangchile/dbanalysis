<?php
namespace lib;

class MysqlHelper {
    
    public $pdo = null;
    public $statement = null;
    
    public function __construct($dsn, $username, $passwd, $options) {
        $this->pdo = new \PDO($dsn, $username, $passwd, $options);
    }
    
    /**
    * 获取pdo对象
    *
    * @param type 
    * @return \PDO 
    * @version 2017年3月24日
    */
    public function getPdo() {
        return $this->pdo;
    }
    
    /**
    * 执行pdo查询
    *
    * @param string $sql 
    * @return \PDOStatement 
    * @version 2017年3月24日
    */
    public function query($sql) {
        $this->statement = $this->pdo->query($sql);
        return $this;
    }
    
    /**
    * 获取mysql的变量
    *
    * @param none 
    * @return int 
    * @version 2017年3月24日
    */
    public function getMysqlValue() {
        $arr = $this->statement->fetchAll(\PDO::FETCH_COLUMN, 1);
        return array_pop($arr);
    }
}