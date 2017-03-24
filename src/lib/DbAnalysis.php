<?php
namespace lib;

/**
* 数据库分析类
*
* @filesource DbAnalysis.php
* @name lib 
* @version 2017年3月24日
*/

class DbAnalysis {
    public $mysql_helper = null;
    
    public $select_count = 0;
    public $insert_count = 0;
    public $update_count = 0;
    public $delete_count = 0;
    
    
    public function __construct(MysqlHelper $mysql_helper) {
        $this->mysql_helper = $mysql_helper;
        $this->baseCount();
    }
    
    public function baseCount() {
        $this->select_count = $this->mysql_helper->query('show global status like \'com_select\'')->getMysqlValue();
        $this->insert_count = $this->mysql_helper->query('show global status like \'com_insert\'')->getMysqlValue();
        $this->update_count = $this->mysql_helper->query('show global status like \'com_update\'')->getMysqlValue();
        $this->delete_count = $this->mysql_helper->query('show global status like \'com_delete\'')->getMysqlValue();
    }
    
    /**
    * 读写比例
    *
    * @param type 
    * @return type 
    * @version 2017年3月24日
    */
    public function readWritePrecent() {

        $total_count = $this->select_count + $this->insert_count + $this->update_count + $this->delete_count;
        
        $read_precent = round($this->select_count / $total_count * 100, 2);
        
        $write_precent = round( ($this->insert_count + $this->update_count + $this->delete_count) / $total_count * 100, 2);
        
        return  [
                'read' => $read_precent,
                'write' => $write_precent,
        ];
    }
    
    /**
    * 慢查询比例
    *
    * @param type 
    * @return type 
    * @version 2017年3月24日
    */
    public function slowQueryPrecent() {
        
    }
    
    /**
    * 连接数检查
    *
    * @param type 
    * @return type 
    * @version 2017年3月24日
    */
    public function connectionCount() {
        
    }
    
    /**
    * 线程缓存
    *
    * @param type 
    * @return type 
    * @version 2017年3月24日
    */
    public function threadCache() {
        
    }
    
    /**
    * 表缓存
    *
    * @param type 
    * @return type 
    * @version 2017年3月24日
    */
    public function tableCache() {
        
    }
    
    /**
    * 临时表
    *
    * @param type 
    * @return type 
    * @version 2017年3月24日
    */
    public function tmpTable() {
        
    }
    
    /**
    * 额外的排序
    *
    * @param type 
    * @return type 
    * @version 2017年3月24日
    */
    public function extraSort() {
        
    }
    
    /**
    * binlog 缓冲
    *
    * @param type variable
    * @return type variable
    * @version 2017年3月24日
    */
    public function binLog() {
        
    }
    
    /**
    * redo 日志
    *
    * @param type 
    * @return type 
    * @version 2017年3月24日
    */
    public function redo() {
        
    }
    
    /**
    * Innodb缓存
    *
    * @param type 
    * @return type 
    * @version 2017年3月24日
    */
    public function innodbCache() {
        
    }
    
    /**
    * 数据库智测
    *
    * @param type 
    * @return type 
    * @version 2017年3月24日
    */
    public function smartTest() {
        
    }
}