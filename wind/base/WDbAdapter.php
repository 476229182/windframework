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
	protected $transCounter = 0;
	public $enableSavePoint = 0;
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
	private function parseConfig($config) {
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
	private function parseDSN($dsn) {
		$ifdsn = preg_match ( '/^(.+)\:\/\/(.+)\:(.+)\@(.+)\:(\d{1,6})\/(.+)\/?(master|slave)?\/?(0|1)?\/?(0|1)?\/?$/', trim ( $dsn ), $config );
		if (empty ( $dsn ) || empty ( $ifdsn ) || empty ( $config )) {
			throw new WSqlException ( "Database config is not correct format", 1 );
		}
		return array ('dbtype' => $config [1], 'dbuser' => $config [2], 'dbpass' => $config [3], 'dbhost' => $config [4], 'dbport' => $config [5], 'dbname' => $config [6], 'optype' => $config [7],'pconnect'=>$config [8],'force'=>$config [9] );
	}
	
	/**
	 * �������ݿ�,�������ӳ�
	 */
	protected function patchConnect() {
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
	 * ִ�����ݿ��ѯ
	 * @param string $sql sql���
	 * @param string | int $key ���ݿ����ӱ�ʶ
	 * @return boolean;
	 */
	public abstract function query($sql,$key='');
	/**
	 * ִ�����ݿ�д��
	 * @param string $sql sql���
	 * @param string | int $key ���ݿ����ӱ�ʶ
	 * @return boolean;
	 */
	public abstract function execute($sql,$key='');
	public abstract function getAll();
	public abstract function getMetaTables();
	public abstract function getMetaColumns();
	public abstract function savePoint();
	public abstract function beginTrans();
	public abstract function rollbackTrans();
	public abstract function getAffectedRows();
	public abstract function getInsertId();
	public abstract function close();
	public abstract function dispose();
	protected abstract function error();
	public  function getExecSqlTime(){
		
	}
	
	/**
	 * �л����ݿ�����
	 * @param string $key ���ݿ����ӱ�ʶ
	 */
	public function changeConn($key) {
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
	public function getSqlBuilderFactory() {
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
	public  function insert($option,$key = ''){
		$this->last_sql = $this->sqlBuilder->getInsertSql($optiion);
		$this->exceute($this->last_sql,$key);
		$this->error();
		$this->logSql();
	}
	
	/**
	 * ִ�и������ݲ���
	 * @param string | array $option ��ѯ����
	 * @param string | int $key ���ݿ����ӱ�ʶ
	 * @return boolean
	 */
	public  function update($option,$key = ''){
		$this->last_sql = $this->sqlBuilder->getUpdateSql($option);
		$this->exceute($this->last_sql,$key);
		$this->error();
		$this->logSql();
	}
	/**
	 * ִ�в�ѯ���ݲ���
	 * @param string | array $option ��ѯ����
	 * @param string | int $key ���ݿ����ӱ�ʶ
	 * @return boolean
	 */
	public function select($option,$key = ''){
		$this->last_sql = $this->sqlBuilder->getSelectSql($option);
		$this->query($this->last_sql,$key);
		$this->error();
		$this->logSql();
	}
	/**
	 * ִ��ɾ�����ݲ���
	 * @param string | array $option ��ѯ����
	 * @param string | int $key ���ݿ����ӱ�ʶ
	 * @return boolean
	 */
	public  function delete($option,$key = ''){
		$this->last_sql = $this->sqlBuilder->getDeleteSql($option);
		$this->exceute($this->last_sql,$key);
		$this->error();
		$this->logSql();
	}
	
	/**
	 * ִ���������ݲ���(replace)
	 * @param string | array $option ��ѯ����
	 * @param string | int $key ���ݿ����ӱ�ʶ
	 * @return boolean
	 */
	public function replace($option,$key = ''){
		$this->last_sql = $this->sqlBuilder->getReplaceSql($option);
		$this->exceute($this->last_sql,$key);
		$this->error();
		$this->logSql();
	}

	/**
	 * ������һ��sqly���
	 * @return string
	 */
	public function getLastSql() {
		return $this->last_sql;
	}
	/**
	 * @param strin | int $key ���ݿ����ӱ�ʶ
	 * @return number д���ݿ����
	 */
	public function getWriteTimes($key = '') {
		if($key = $this->checkKey($key)){
			return self::$writeTimes[$key];
		}
		$writes = 0;
		foreach(self::$writeTimes as $value){
			$writes += $value;
		}
		return $writes;
	}
	
	/**
	 * @param strin | int $key ���ݿ����ӱ�ʶ
	 * @return number �����ݿ����
	 */
	public function getReadTimes($key='') {
		if($key = $this->checkKey($key)){
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
	public function getQueryTimes($key = '') {
		return $this->getReadTimes($key) + $this->getWriteTimes($key);
	}
	
	/**
	 * �������ݿ�����
	 * @param strin | int $key ���ݿ��ʶ
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
	 */
	protected function getLinking($optype = '', $key = '') {
		$this->checkKey($key);
		$masterSlave = $this->getMasterSlave ();
		$config = empty ( $masterSlave ) || empty ( $optype ) ? self::$config : $masterSlave [$optype];
		$key = $key ? $key : $this->getConfigKeyByPostion ( $config, mt_rand ( 0, count ( $config ) - 1 ) );
		$this->linking = self::$linked [$key];
		$this->key = $key;
	}
	
	/**
	 *����config��pos����key
	 * @param array $config ���ݿ�����
	 * @param int $pos config��λ��
	 * @return string ����config��key
	 */
	private function getConfigKeyByPostion($config, $pos = 0) {
		$i = 0;
		foreach ( ( array ) $config as $key => $value ) {
			if ($pos === $i)
				return $key;
			$i ++;
		}
		return '';
	}
	
	protected function logSql(){
		W::recordLog($this->last_sql,'DB','log');
	}
	
	/**
	 * ���$linked�����ӵĺϷ���
	 * @param string $key config��key
	 * @return string
	 */
	protected function checkKey($key = ''){
		if($key && !in_array($key,array_keys(self::$linked))){
			throw new WSqlException('Database identify is not exists',1);
		}
		return $key;
	}
	
	

}