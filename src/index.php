<?php
require 'classloader.php';

use lib\DbAnalysis;
use lib\MysqlHelper;

class Index {
    public function Test() {
        $dsn = 'mysql:host=localhost;port=3306;dbname=hiidosys_bak';
        $username = 'root';
        $passwd = '';
        $options = null;
        
        $mysql_helper = new MysqlHelper($dsn, $username, $passwd, $options);
        
        $dbanalysis = new DbAnalysis($mysql_helper);
        var_dump($dbanalysis->ReadWritePrecent());
    }
}

(new Index())->Test();