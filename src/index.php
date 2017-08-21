<?php
require 'classloader.php';

use lib\DbAnalysis;
use lib\MysqlHelper;

class Index {
    public $dbanalysis = null;
    
    public function __construct() {
        $dsn = 'mysql:host=localhost;port=3306;dbname=hiidosys_bak';
        $username = 'root';
        $passwd = '';
        $options = null;
        
        $mysql_helper = new MysqlHelper($dsn, $username, $passwd, $options);
        
        $this->dbanalysis = new DbAnalysis($mysql_helper);
    }
    
    public function index() {

        return include('./public/index.html');
    }
    
    public function Test() {
        var_dump($this->dbanalysis->ReadWritePrecent());
        var_dump($this->dbanalysis->slowQueryPrecent());
        var_dump($this->dbanalysis->connectionCount());
        var_dump($this->dbanalysis->threadCache());
        var_dump($this->dbanalysis->tableCache());
        var_dump($this->dbanalysis->tmpTable());
        var_dump($this->dbanalysis->extraSort());
        var_dump($this->dbanalysis->binLog());
        var_dump($this->dbanalysis->redo());
        var_dump($this->dbanalysis->innodbCache());
        var_dump($this->dbanalysis->smartTest());
    }
    
    public function baseData() {
        echo json_encode( array_merge($this->dbanalysis->getBaseCount(), $this->dbanalysis->readWritePrecent()) );
    }
    
    public function connectionCount() {
        echo json_encode( $this->dbanalysis->connectionCount() );
    }
    
    public function cacheCount() {
        $data = [
                'threadCache' => $this->dbanalysis->threadCache(),
                'tableCache' => $this->dbanalysis->tableCache(),
                'innodbCache' => $this->dbanalysis->innodbCache(),
        ];
        echo json_encode( $data );
    }
    
    public function otherData() {
        $data = [
                'tmpTable' => $this->dbanalysis->tmpTable(),
                'extraSort' => $this->dbanalysis->extraSort(),
                'binLog' => $this->dbanalysis->binLog(),
                'redo' => $this->dbanalysis->redo(),
        ];
        
        echo json_encode( $data );
    }

}

$map = [
        'index' => 'index',
        'test' => 'test',
        'baseData' => 'baseData',
        'connectionCount' => 'connectionCount',
        'cacheCount' => 'cacheCount',
        'otherData' => 'otherData',
];

$api = isset($_GET['api']) ? $_GET['api'] : 'index';
if ( in_array($api, $map) ) {
    (new Index())->$api();
} else {
    echo(-1);
    exit;
}
