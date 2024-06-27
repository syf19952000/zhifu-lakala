<?php
/**
* 设计模式之单例模式
* $_instance必须声明为静态的私有变量
* 构造函数和析构函数必须声明为私有,防止外部程序new
* 类从而失去单例模式的意义
* getInstance()方法必须设置为公有的,必须调用此方法
* 以返回实例的一个引用
* ::操作符只能访问静态变量和静态函数
* new对象都会消耗内存
* 使用场景:最常用的地方是数据库连接。 
* 使用单例模式生成一个对象后，
* 该对象可以被其它众多对象所使用。 
*/
class Db {
 
	//保存类实例的静态成员变量
	private static $_instance;
	private static $_connectSource;
    /*public $_dbConfig = array(
        'host' => '119.188.250.221',
        'user' => 'duanju-alipay',
        'database' => 'duanju-alipay',
        'password' => 'MWiMrKaGwpLiEdkc',
    );*/
    public $_dbConfig = array(
		'host' => '127.0.0.1',
		'user' => 'root',
        'database' => 'duanju-alipay',
		'password' => 'root',
	);
    public $query_count = 0;
    var $query_list = array();
	//private标记的构造方法
	private function __construct(){
		return false;
	}
	 
	//创建__clone方法防止对象被复制克隆
	/*public function __clone(){
		trigger_error('Clone is not allow!',E_USER_ERROR);
		echo 'Clone is not allow!;'.E_USER_ERROR;
	}*/
	 
	// 单例方法,用于访问实例的公共的静态方法
	public static function getInstance(){
		if(!(self::$_instance instanceof self)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
		 
	public function connect(){
        // fix database 5.7
		if(!self::$_connectSource){
		    self::$_connectSource = mysqli_connect($this->_dbConfig['host'],$this->_dbConfig['user'],$this->_dbConfig['password']);
		    if(!self::$_connectSource){
		    	die('mysql connect error'.mysqli_error(self::$_connectSource));
		    }
            mysqli_select_db(self::$_connectSource, $this->_dbConfig['database']);
	    }
    	return self::$_connectSource;       
  	}

	/**
     * 执行 Db_query 并返回其结果.
     */
    public function query($sql){
        $result = mysqli_query(self::$_connectSource, $sql);
        return $result;
    }

  	 /**
     * 执行 Db_once 并返回其一条结果.
     */
    public function once($sql){
        $result = $this->query($sql);
        $data = mysqli_fetch_array($result);
        return $data;
    }

  	 /**
     * 执行 Db_once 更新一条记录.
     */
    public function update($sql){
        $result = mysqli_query(self::$_connectSource, $sql);;
        return $result;
    }
}
$connect = Db::getInstance();
$connect->connect();
	 /**
     *  调用试例
	 *	$connect = Db::getInstance();
	 *	$connect->connect();
	 *  $sql = "select * from table";
	 *	$data = $connect->query($sql);
     */
$sql = "SELECT * FROM `app` where `status` = '0' order by sort limit 1 ";
$result_mch = mysqli_fetch_array($connect->query($sql));
if($result_mch){
    $mch['data'] += $result_mch;
}