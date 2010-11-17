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
	protected $key = '';
	protected $charset = 'gbk';
	protected $force = false;
	protected $pconnect = false;
	protected $switch = 0;
	
	protected $dbtype = '';
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
	 * @param unknown_type $config
	 * @return Ambigous <multitype:, multitype:unknown >
	 */
	private function parseConfig($config) {
		$db_config = array ();
		if (empty ( $config ) || ! is_array ( $config )) {
			throw new WSqlException ( "database config is not correct", 1 );
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
	 * @param unknown_type $dsn
	 * @return multitype:unknown 
	 */
	private function parseDSN($dsn) {
		$ifdsn = preg_match ( '/^(.+)\:\/\/(.+)\:(.+)\@(.+)\:(\d{1,6})\/(.+)\/?(master|slave)*$/', trim ( $dsn ), $config );
		if (empty ( $dsn ) || empty ( $ifdsn ) || empty ( $config )) {
			throw new WSqlException ( "database config is not correct", 1 );
		}
		return array ('dbtype' => $config [1], 'dbuser' => $config [2], 'dbpass' => $config [3], 'dbhost' => $config [4], 'dbport' => $config [5], 'dbname' => $config [6], 'optype' => $config [7] );
	}
	
	/**
	 * 
	 */
	protected function patchConnect() {
		foreach ( self::$config as $key => $value ) {
			$this->connect ( $value, $key );
		}
	}
	
	public abstract function connect($config, $key);
	public abstract function query($sql,$key='');
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
	
	public  function getExecSqlTime(){
		
	}
	
	/**
	 * @param unknown_type $key
	 */
	public function changeConn($key) {
		if (! isset ( self::$linked [$key] )) {
			throw new WSqlException ( "this database connecton is not exists", 1 );
		}
		$this->linking = self::$linked [$key];
		$this->key = $key;
		$this->switch = 1;
	}
	public function freeChange(){
		$this->switch = 0;
	}

	
	/**
	 * 
	 */
	public function getSqlBuilderFactory() {
		$config = self::$config [$this->key];
		if (empty ( $config ) || ! is_array ( $config )) {
			throw new WSqlException ( "database config is not correct", 1 );
		}
		$dbType = $this->dbMap[strtolower($config ['dbtype'])];
		$builderClass = 'W'.$dbType.'Builder';
		$this->sqlBuilder = W::getInstance($builderClass);//��������
		
	}

	/**
	 * @param unknown_type $option
	 * @param unknown_type $key
	 */
	public  function insert($option,$key = ''){
		$sql = $this->sqlBuilder->getInsertSql($optiion);
		return $this->exceute($sql,$key);
	}
	
	/**
	 * @param unknown_type $option
	 * @param unknown_type $key
	 */
	public  function update($option,$key = ''){
		$sql = $this->sqlBuilder->getUpdateSql($option);
		return $this->exceute($sql,$key);
	}
	/**
	 * @param unknown_type $option
	 * @param unknown_type $key
	 */
	public function select($option,$key = ''){
		$sql = $this->sqlBuilder->getSelectSql($option);
		return $this->query($sql,$key);
	}
	/**
	 * @param unknown_type $option
	 * @param unknown_type $key
	 */
	public  function delete($option,$key = ''){
		$sql = $this->sqlBuilder->getDeleteSql($option);
		return $this->exceute($sql,$key);
	}
	
	public function replace($option,$key = ''){
		$sql = $this->sqlBuilder->getReplaceSql($option);
		return $this->exceute($sql,$key);
	}

	public function getLastSql() {
		return $this->last_sql;
	}
	public function getWriteTimes() {
		return self::$writeTimes;
	}
	public function getReadTimes() {
		return self::$readTimes;
	}
	public function getQueryTimes() {
		return ( int ) self::$writeTimes + ( int ) self::$readTimes;
	}
	
	protected function getLinked($key = '') {
		return $key ? self::$linked [$key] : $this->linking;
	}
	
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
	 * @param unknown_type $optype
	 * @param unknown_type $key
	 */
	protected function getLinking($optype = '', $key = '') {
		$masterSlave = $this->getMasterSlave ();
		$config = empty ( $masterSlave ) || empty ( $optype ) ? self::$config : $masterSlave [$optype];
		$key = $key ? $key : $this->getConfigKeyByPostion ( $config, mt_rand ( 0, count ( $config ) - 1 ) );
		$this->linking = self::$linked [$key];
		$this->key = $key;
	}
	
	/**
	 * @param unknown_type $config
	 * @param unknown_type $pos
	 * @return unknown|string
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

}