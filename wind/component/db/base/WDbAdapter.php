<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-11
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
abstract class WDbAdapter {
	
	/**
	 * @var resource ��ǰdbl���Ӿ��
	 */
	protected $linking = null;
	
	/**
	 * @var resource ��ǰ��ѯ���
	 */
	protected $query = '';
	/**
	 * @var string ǰ��ִ�е�sqly���
	 */
	protected $last_sql = '';
	/**
	 * @var string ǰ��ִ��sqlʱ�Ĵ����ַ���
	 */
	protected $last_errstr = '';
	/**
	 * @var int ǰ��ִ��sqlʱ�Ĵ������
	 */
	protected $last_errcode = 0;
	/**
	 * @var WSqlBuilder sql���������
	 */
	protected $sqlBuilder = null;
	/**
	 * @var int �Ƿ�����
	 */
	protected $isConntected = 0;
	
	/**
	 * @var string | int ��ǰ���ݿ�������ָ�����ݿ����õ�key
	 */
	protected $key = '';
	/**
	 * @var string ���ݿ��ַ���
	 */
	protected $charset = 'gbk';
	/**
	 * @var boolean �Ƿ�ǿ������
	 */
	protected $force = false;
	/**
	 * @var boolean �Ƿ���������
	 */
	protected $pconnect = false;
	/**
	 * @var int �Ƿ��л������ݿ�
	 */
	protected $switch = 0;
	
	/**
	 * @var strint ���ݿ�schema
	 */
	protected $dbtype = '';
	/**
	 * @var array ���֧�ֵ����ݿ�����
	 */
	protected $dbMap = array ('mysql' => 'MySql', 'mssql' => 'MsSql', 'pgsql' => 'PgSql', 'ocsql' => 'OcSql' );
	/**
	 * @var int ���������
	 */
	protected $transCounter = 0;
	/**
	 * @var int �Ƿ���������
	 */
	public $enableSavePoint = 0;
	/**
	 * @var array ����ع���
	 */
	protected $savepoint = array ();
	
	/**
	 * @var array ��¼�����ݿ�д�����
	 */
	public static $writeTimes = array();
	/**
	 * @var array ��¼�����ݿ�������
	 */
	protected static $readTimes = array();
	/**
	 * @var array ���ݿ����ӳ�
	 */
	protected static $linked = array ();
	/**
	 * @var array ���ݿ����Ӿ��
	 */
	protected static $config = array ();
	public function __construct($config) {
		$this->parseConfig ( $config );
		$this->patchConnect ();
		$this->getSqlBuilderFactory();
	}
	
	/**
	 * ��������������
	 * @param array $config ���ݿ����ã������ǻ��ڼ�ֵ�Ķ�ά���������ǻ��ڼ�ֵDNS��ʽ��һά����
	 * @example DSN��ʽarray('phpwind'=>'mysql:://username:password@localhost:port/dbname/optype/pconnect/force');
	 * 			arrays��ʽarray('phpwind'=>array('dbtype'=>'mysql','dbname'=>'root','dbpass'=>'123456',
	 * 							'dbuser'=>'root','dbhost'=>'locahost','dbport'=>3306,
	 * 							'optype'=>'master','pconnect'=>1,'force'=>1);
	 * @return array ���ؽ���������ݿ�����
	 */
	final private function parseConfig($config) {
		$db_config = array ();
		if (empty ( $config ) || ! is_array ( $config )) {
			throw new WSqlException ( "Database Config is not correct", 1 );
		}
		foreach ( $config as $key => $value ) {
			if (is_array ( $value ))
				$db_config [$key] = $value;
			if (is_string ( $value ))
				$db_config [$key] = $this->parseDsn ( $value );
		}
		return self::$config = $db_config;
	}
	/**
	 * ��DSN��ʽ�������������ã�����(����optype,��������pconnect,ǿ��������force)��ѡ
	 * @example mysql:://username:password@localhost:port/dbname/optype/pconect/force
	 * @param unknown_type $dsn ���ݿ����Ӹ�ʽ
	 * @return array 
	 */
	final private function parseDSN($dsn) {
		$ifdsn = preg_match ( '/^(.+)\:\/\/(.+)\:(.+)\@(.+)\:(\d{1,6})\/(.+)\/?(master|slave)?\/?(0|1)?\/?(0|1)?\/?$/', trim ( $dsn ), $config );
		if (empty ( $dsn ) || empty ( $ifdsn ) || empty ( $config )) {
			throw new WSqlException ( "Database config is not correct format", 1 );
		}
		return array ('dbtype' => $config [1], 'dbuser' => $config [2], 'dbpass' => $config [3], 'dbhost' => $config [4], 'dbport' => $config [5], 'dbname' => $config [6], 'optype' => $config [7],'pconnect'=>$config [8],'force'=>$config [9] );
	}
	
	/**
	 * �������ݿ�,�������ӳ�
	 */
	final protected function patchConnect() {
		foreach ( self::$config as $key => $value ) {
			$this->connect ( $value, $key );
		}
	}
	
	/**
	 * �������ݿ�
	 * @param array $config ���ݿ�����
	 * @param string $key ���ݿ����ӱ�ʶ
	 */
	public abstract function connect($config, $key);
	/**
	 * ִ�����sql������
	 * @param string $sql sql���
	 * @param string|int|resource $key ���ݿ����ӱ�ʶ
	 * @param string optype �������ݿ�(master/slave);
	 * @return boolean;
	 */
	public abstract function query($sql,$key='',$optype = '');
	/**
	 * @param int $fetch_type ȡ�ý����
	 */
	public abstract function getAllResult($fetch_type = MYSQL_ASSOC);
	/**
	 *ȡ�����ݿ�Ԫ���ݱ�
	 */
	public abstract function getMetaTables();
	/**
	 *ȡ�����ݱ�Ԫ������ 
	 */
	public abstract function getMetaColumns();
	/**
	 * ���������
	 */
	//public abstract function savePoint();
	/**
	 * ��ʼ�����
	 */
	public abstract function beginTrans();
	/**
	 * �ع�����
	 */
	//public abstract function rollbackTrans();
	/**
	 * ȡ����Ӱ�����������
	 * @param string|int ���ݿ����ӱ�ʶ
	 * @return int
	 */
	public abstract function getAffectedRows($key = '');
	/**
	 * ȡ��������ID
	 * @param string|int ���ݿ����ӱ�ʶ
	 * @return int
	 */
	public abstract function getInsertId($key = '');
	/**
	 * �ر����ݿ�
	 */
	public abstract function close();
	/**
	 * �ͷ����ݿ�������Դ
	 */
	public abstract function dispose();
	/**
	 * ���ݿ������������
	 */
	protected abstract function error();

	/**
	 * ִ�����ݿ��ȡ
	 * @param string $sql sql���
	 * @param string|int|resource $key ���ݿ����ӱ�ʶ
	 * @return boolean;
	 */
	final public function read($sql, $key = '') {
		$this->query ( $sql, $key ,'slave');
		$key = $key && !is_resource($key) ? $key : $this->key;
		if (isset ( self::$readTimes [$key] )) {
			self::$readTimes [$key] ++;
		} else {
			self::$readTimes [$key] = 1;
		}
	}
	
	/**
	 * ִ�����ݿ�д��
	 * @param string $sql sql���
	 * @param string|int|resource $key ���ݿ����ӱ�ʶ
	 * @return boolean;
	 */
	final public function write($sql, $key = '') {
		$this->query( $sql, $key ,'master');
		$key = $key && !is_resource($key) ? $key : $this->key;
		if (isset ( self::$writeTimes [$key] )) {
			self::$writeTimes [$key] ++;
		} else {
			self::$writeTimes [$key] = 1;
		}
	}
	
	/**
	 * �л����ݿ�����
	 * @param string $key ���ݿ����ӱ�ʶ
	 */
	final public function changeConn($key) {
		if (! isset ( self::$linked [$key] )) {
			throw new WSqlException ( "this Database Connecton is not exists", 1 );
		}
		$this->linking = self::$linked [$key];
		$this->key = $key;
		$this->switch = 1;
	}
	/**
	 *�ͷ��л����� 
	 */
	public function freeChange(){
		$this->switch = 0;
	}

	
	
	/**
	 * sqlbuilder Factory
	 * @return WSqlBuilder ����sql���������
	 */
	final public function getSqlBuilderFactory() {
		$config = self::$config [$this->key];
		if (empty ( $config ) || ! is_array ( $config )) {
			throw new WSqlException ( "Database Config is not correct format", 1 );
		}
		$dbType = $this->dbMap[strtolower($config ['dbtype'])];
		$builderClass = 'W'.$dbType.'Builder';
		return $this->sqlBuilder = W::getInstance($builderClass);
	}

	/**
	 * ִ��������ݲ��� (insert)
	 * @param string | array $option ��ѯ����
	 * @param string | int $key ���ݿ����ӱ�ʶ
	 * @return boolean
	 */
	final public  function insert($option,$key = ''){
		$this->write($this->sqlBuilder->getInsertSql($option),$key);
	}
	
	/**
	 * ִ�и������ݲ���
	 * @param string | array $option ��ѯ����
	 * @param string | int $key ���ݿ����ӱ�ʶ
	 * @return boolean
	 */
	final public  function update($option,$key = ''){
		$this->write($this->sqlBuilder->getUpdateSql($option),$key);
	}
	/**
	 * ִ�в�ѯ���ݲ���
	 * @param string | array $option ��ѯ����
	 * @param string | int $key ���ݿ����ӱ�ʶ
	 * @return boolean
	 */
	final public function select($option,$key = ''){
		$this->read($this->sqlBuilder->getSelectSql($option),$key);
	}
	/**
	 * ִ��ɾ�����ݲ���
	 * @param string | array $option ��ѯ����
	 * @param string | int $key ���ݿ����ӱ�ʶ
	 * @return boolean
	 */
	final public  function delete($option,$key = ''){
		$this->write($this->sqlBuilder->getDeleteSql($option),$key);
	}
	
	/**
	 * ִ���������ݲ���(replace)
	 * @param string | array $option ��ѯ����
	 * @param string | int $key ���ݿ����ӱ�ʶ
	 * @return boolean
	 */
	final public function replace($option,$key = ''){
		$this->write($this->sqlBuilder->getReplaceSql($option),$key);
	}

	/**
	 * ������һ��sqly���
	 * @return string
	 */
	final public function getLastSql() {
		return $this->last_sql;
	}
	/**
	 * @param string | int $key ���ݿ����ӱ�ʶ
	 * @return number д���ݿ����
	 */
	final public function getWriteTimes($key = '') {
		if(($key = $this->checkKey($key))){
			return self::$writeTimes[$key];
		}
		$writes = 0;
		foreach(self::$writeTimes as $value){
			$writes += $value;
		}
		return $writes;
	}
	
	/**
	 * @param string | int $key ���ݿ����ӱ�ʶ
	 * @return number �����ݿ����
	 */
	final public function getReadTimes($key='') {print_r(self::$readTimes);
		if(($key = $this->checkKey($key))){
			return self::$readTimes[$key];
		}
		$reads = 0;
		foreach(self::$readTimes as $value){
			$reads += $value;
		}
		return $reads;
	}
	
	/**
	 * @param string | int $key ���ݿ����ӱ�ʶ
	 * @return number ��д���ݿ����
	 */
	final public function getQueryTimes($key = '') {
		return $this->getReadTimes($key) + $this->getWriteTimes($key);
	}
	
	/**
	 * �������ݿ�����
	 * @param string | int $key ���ݿ��ʶ
	 * @return resource �������ݿ�����
	 */
	protected function getLinked($key = '') {
		return $key ? self::$linked [$key] : $this->linking;
	}
	
	/**
	 * �鿴���Ƿ�Ҫ�������ݿ����ã������������÷������ݿ�������Ϣ
	 * @return array
	 */
	protected function getMasterSlave() {
		$array = array ();
		foreach ( self::$config as $key => $value ) {
			if (in_array ( $value ['optype'], array ('master', 'slave' ) )) {
				$array [$value ['optype']] [$key] = $value;
			}
		}
		return $array;
	}
	
	/**
	 * ȡ�õ�ǰ���ݿ�����
	 * @param string $optype ���ݿ���������(master/slave);
	 * @param string | int $key ���ݿ����ӱ�ʶ
	 * @return resource
	 */
	protected function getLinking($optype = '', $key = '') {
		$this->checkKey($key);
		if(is_resource($key)){
			return $this->linking = $key;
		}
		$masterSlave = $this->getMasterSlave ();
		$config = empty ( $masterSlave ) || empty ( $optype ) ? self::$config : $masterSlave [$optype];
		$key = $key ? $key : $this->getConfigKeyByPostion ( $config, mt_rand ( 0, count ( $config ) - 1 ) );
		$this->key = $key;
		return $this->linking = self::$linked [$key];
	}
	
	/**
	 *����config��pos����key
	 * @param array $config ���ݿ�����
	 * @param int $pos config��λ��
	 * @return string ����config��key
	 */
	final private function getConfigKeyByPostion($config, $pos = 0) {
		$i = 0;
		foreach ( ( array ) $config as $key => $value ) {
			if ($pos === $i)
				return $key;
			$i ++;
		}
		return '';
	}
	
	/**
	 * @param string $sql
	 */
	final protected function logSql($sql = ''){
		$sql = $sql ? $sql : $this->last_sql;
		W::recordLog($sql,'DB','log');
	}
	
	/**
	 * ���$linked�����ӵĺϷ���
	 * @param string|int|resource $key config��key����������Դ
	 * @return string|resource
	 */
	final protected function checkKey($key = ''){
		if($key && !in_array($key,array_keys(self::$linked)) && !is_resource($key)){
			throw new WSqlException('Database identify is not exists',1);
		}
		return $key;
	}
	
	

}