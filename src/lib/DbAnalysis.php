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
        // 获得服务器启动到目前查询操作执行的次数
        $this->select_count = $this->mysql_helper->getMysqlValue("show global status like 'com_select'");
        // 获得服务器启动到目前插入操作执行的次数
        $this->insert_count = $this->mysql_helper->getMysqlValue("show global status like 'com_insert'");
        // 获得服务器启动到目前更新操作执行的次数
        $this->update_count = $this->mysql_helper->getMysqlValue("show global status like 'com_update'");
        // 获得服务器启动到目前删除操作执行的次数
        $this->delete_count = $this->mysql_helper->getMysqlValue("show global status like 'com_delete'");
    }
    
    /**
    * 读写比例
    *
    * @param type 
    * @return type 
    * @version 2017年3月24日
    */
    public function readWritePrecent() {
        // 所有操作总数
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
        // 获得服务器启动到目前慢查询操作记录的次数
        $slow_query = $this->mysql_helper->getMysqlValue("show global status like 'Slow_queries '");
        
        return round( $slow_query / ($this->select_count + $this->update_count + $this->delete_count) * 100, 2 );
    }
    
    /**
    * 连接数检查
    * 通过连接数检查，可得知数据库在不同时间段被请求的压力
    *
    * @param type 
    * @return type 
    * @version 2017年3月24日
    */
    public function connectionCount() {
        // 获得数据库运行的最大连接数 #允许的最大连接数
        $max_conn = $this->mysql_helper->getMysqlValue("show global status like 'max_connections'");
        // 获得最大一次的连接数 #最大突发并行连接数  
        $max_used_conn = $this->mysql_helper->getMysqlValue("show global status like 'Max_used_connections'");
        // 获得数据库运行到目前，总共被连接了多少次 #登陆的次数
        $conn = $this->mysql_helper->getMysqlValue("show global status like 'connections'");
        // 获得当前连接数
        $thread_connected = $this->mysql_helper->getMysqlValue("show global status like 'Threads_connected'");
        // 获得当前正在运行的连接数
        $thread_running = $this->mysql_helper->getMysqlValue("show global status like 'Threads_running'");
        
        //计算连接数比例
        if ($max_conn > 0) {
            //  计算最大一次的连接数比例
            $max_conn_precent = round( $thread_connected / $max_conn * 100, 2 );
            // 通过连接数检查，可得知数据库在不同时间段被请求的压力
            $max_use_conn_precent = round( $max_used_conn / $max_conn *100, 2 );
        } else {
            $max_conn_precent = 0.00;
            
            $max_use_conn_precent = 0.00;
        }

        
        return [
                'max_connections' => $max_conn,
                'max_used_conn' => $max_used_conn,
                'connections' => $conn,
                'thread_connected' => $thread_connected,
                'thread_running' => $thread_running,
                'max_conn_precent' => $max_conn_precent,
                'max_use_conn_precent' => $max_use_conn_precent,
        ];
        
    }
    
    /**
    * 线程缓存
    * 通过计算连接线程缓存命中率，可反映出连接线程的命中情况，命中率越大越好。
    * 如果命中率过低，则表示缓存连接线程的数量过少，可以考虑加大 thread_cache_size 的值。 
    *
    * @param type 
    * @return type 
    * @version 2017年3月24日
    */
    public function threadCache() {
        // 获得数据库运行到目前，总共被连接了多少次
        $connections = $this->mysql_helper->getMysqlValue("show global status like 'Connections'");
        // 获得数据库运行到目前，创建连接线程的次数
        $thread_created = $this->mysql_helper->getMysqlValue("show global status like 'Threads_created'");
        
        return round($thread_created / $connections * 100, 2);
    }
    
    /**
    * 表缓存
    * 通过计算表缓存的命中率，可反映出表缓存的情况，该比例越大越好
    * 通过已知自己数据库中有多少表，再观察 Opened_tables 的值，可以得知表缓存的数量是否合理，如果打开表的次数大于数据库中已有的表数
    * 量，则表示 table_open_cache 的值不够，可以考虑加大。
    *
    * @param type 
    * @return type 
    * @version 2017年3月24日
    */
    public function tableCache() {
        // 新打开的表的次数。   不命中
        $table_open_cache_misses = $this->mysql_helper->getMysqlValue("show global status like 'Table_open_cache_misses'");
        // 从表缓存中拿已打开的表的次数，该状态变量 5.6 才开始存在  命中
        $table_open_cache_hits = $this->mysql_helper->getMysqlValue("show global status like 'Table_open_cache_hits'");
        // 打开表的总次数
        $opened_tables = $this->mysql_helper->getMysqlValue("show global status like 'Opened_tables'");
        
        $hit_precent = round( $table_open_cache_hits / ($table_open_cache_hits + $table_open_cache_misses) *100, 2 ); 
        
        return [
                'hit_precent' => $hit_precent,
                'opened_talbes' => $opened_tables
        ];
    }
    
    /**
    * 临时表
    * 通过计算在磁盘上创建临时表的比例，可反映出数据库的使用临时表的情况，该比例越小越好。
    * 如果发现在磁盘上创建临时表的次数过多，则表示临时表的缓存区内存不够，可以考虑加大 tmp_table_size 和 max_heap_table_size 的值。
    *
    * @param type 
    * @return type 
    * @version 2017年3月24日
    */
    public function tmpTable() {
        // 查看在磁盘上创建临时表的次数
        $created_tmp_disk_tables = $this->mysql_helper->getMysqlValue("show global status like 'Created_tmp_disk_tables'");
        // 查看创建临时表的总次数，包括在内存中和磁盘。
        $created_tmp_tables = $this->mysql_helper->getMysqlValue("show global status like 'Created_tmp_tables'");
        
        return round( $created_tmp_disk_tables / $created_tmp_tables * 100, 2 );
    }
    
    /**
    * 额外的排序
    * 通过计算在磁盘上进行额外排序的比例，可反映出数据库排序的情况，该比例越小越好。
    * 如果发现在磁盘上进行排序的次数过多，则表示排序缓冲区内存不够，可以考虑加大 sort_buffer_size 的值。
    *
    * @param type 
    * @return type 
    * @version 2017年3月24日
    */
    public function extraSort() {
        // 在磁盘中进行额外排序的次数
        $sort_merge_pass = $this->mysql_helper->getMysqlValue("show global status like 'Sort_merge_passes'");
        // 通过表扫描进行排序的总次数，也就是额外排序的总次数
        $sort_scan = $this->mysql_helper->getMysqlValue("show global status like 'Sort_scan'");
        
        return round( $sort_merge_pass / $sort_scan * 100, 2 );
    }
    
    /**
    * binlog 缓冲
    * 通过计算在磁盘上创建临时文件保 binlog 的比例，可反映出数据库 binlog 的情况，该比例越小越好。
    * 如果发现在磁盘上创建临时文件保存 binlog 的次数过多，则表示 binlog 缓冲区内存不够，可以考虑加大 binlog_cache_size 的值。
    *
    * @param type variable
    * @return type variable
    * @version 2017年3月24日
    */
    public function binLog() {
        // 在磁盘上创建临时文件用于保存 binlog 的次数
        $binlog_cache_disk_use = $this->mysql_helper->getMysqlValue("show global status like 'Binlog_cache_disk_use'");
        // 缓冲 binlog 的总次数，包括 binlog 缓冲区和在磁盘上创建临时文件保存 binlog 的总次数
        $binlog_cache_use = $this->mysql_helper->getMysqlValue("show global status like 'Binlog_cache_use'");
        
        return round( $binlog_cache_disk_use / $binlog_cache_use * 100, 2 );
    }
    
    /**
    * redo 日志
    * 如果发现 redo 日志等待刷新的次数过多，则表示 innodb redo 日志缓冲区的大小不够，可以考虑加大 innodb_log_buffer_size 的值。
    *
    * @param type 
    * @return type 
    * @version 2017年3月24日
    */
    public function redo() {
        // 查看 innodb redo 日志等待缓冲区刷新的次数
        $innodb_log_waits = $this->mysql_helper->getMysqlValue("select global status like 'Innodb_log_waits'");
        return $innodb_log_waits;
    }
    
    /**
    * Innodb缓存
    * 通过计算 innodb 缓存命中率，可反映出 innodb 缓存的效率，该比例越大越好
    * 如果发现从磁盘读取页的次数过多，则有可能是因为 innodb 缓冲池的大小不够，此时可以考虑加到 innodb_buffer_pool_size 的值
    *
    * @param type 
    * @return type 
    * @version 2017年3月24日
    */
    public function innodbCache() {
        // 读取页的总次数
        $innodb_buffer_pool_read_requests = $this->mysql_helper->getMysqlValue("show global status like 'Innodb_buffer_pool_read_requests'");
        // 从磁盘读取页的次数
        $innodb_buffer_pool_read = $this->mysql_helper->getMysqlValue("show global status like 'Innodb_buffer_pool_read'");

        return round( ($innodb_buffer_pool_read_requests - $innodb_buffer_pool_read) / $innodb_buffer_pool_read_requests * 100, 2);
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