<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-11
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.exception.WindSqlExceptiion');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
abstract class WindDbAdapter {
	
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
	protected $enableSavePoint = 0;
	/**
	 * @var array ����ع���
	 */
	protected $savepoint = array ();
	/**
	 * @var resoruce ���ݿ�����
	 */
	protected  $connection = null;
	
	/**
	 * @var WindSqlBuilder sql���������
	 */
	protected $sqlBuilder = null;
	
	/**
	 * @var resource ��ǰ��ѯ���
	 */
	protected $query = null;
	/**
	 * @var array ���ݿ���������
	 */
	protected  $config = array ();
	
	
	public function __construct($config) {
		$this->parseConfig ( $config );
		$this->connect();
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
	final protected function parseConfig($config) {
		$config = is_array($config) ? $config : $this->parseDSN($config);
		return $this->checkConfig($config);
	}
	/**
	 * ��DSN��ʽ�������������ã�����(����optype,��������pconnect,ǿ��������force)��ѡ
	 * @example mysql:://username:password@localhost:port/dbname/force/pconect/optype/
	 * @param unknown_type $dsn ���ݿ����Ӹ�ʽ
	 * @return array 
	 */
	final public function parseDSN($dsn) {
		$ifdsn = preg_match ( '/^(.+)\:\/\/(.+)\:(.+)\@(.+)\:(\d{1,6})\/(\w+)\/(\w+)\/(0|1)\/(0|1)\/(master|slave)?\/?$/', trim ( $dsn ), $config );
		if (empty ( $dsn ) || empty ( $ifdsn ) || empty ( $config )) {
			throw new WindSqlException (WindSqlException::DB_CONFIG_FORMAT);
		}
		return array ('dbtype' => $config [1], 'dbuser' => $config [2], 'dbpass' => $config [3], 'dbhost' => $config [4], 'dbport' => $config [5], 'dbname' => $config [6],'charset'=>$config [7], 'force' => $config [8],'pconnect'=>$config [9],'optype'=>$config [10] );
	}
	
	final private function checkConfig($config){
		if (empty ( $config ) || ! is_array ( $config ) || !is_string($config)) {
			throw new WindSqlException (WindSqlException::DB_CONFIG_EMPTY);
		}
		if(empty($config['dbtype']) || empty($config['dbhost']) || empty($config['dbname']) || empty($config['dbuser'])  || empty($config['dbpass'])){
			throw new WindSqlException (WindSqlException::DB_CONFIG_ERROR);
		}
		$config ['dbhost'] = $config ['dbport'] ? $config ['dbhost'] . ':' . $config ['dbport'] : $config ['dbhost'];
		$config ['pconnect'] = $config ['pconnect'] ? $config ['pconnect'] : $this->pconnect;
		$config ['force'] = $config ['force'] ? $config ['force'] : $this->force;
		$config ['charset'] = $config ['charset'] ? $config ['charset'] : $this->charset;
		return $this->config = $config;
	}
	
	/**
	 * �������ݿ�
	 */
	protected abstract function connect();
	/**
	 * ִ�����sql������
	 * @param string $sql sql���
	 * @return boolean;
	 */
	public abstract function query($sql);
	/**
	 * @param int $fetch_type ȡ�ý����
	 */
	public abstract function getAllResult($fetch_type = MYSQL_ASSOC);
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
	 * sqlbuilder Factory
	 * @return WSqlBuilder ����sql���������
	 */
	final public function getSqlBuilderFactory() {
		if(empty($this->sqlBuilder)){
			$name = 'Wind'.$this->dbMap[$this->getSchema()].'Builder';
			L::import('WIND:component.db.'.$name);
			$this->sqlBuilder = L::getInstance($name);
		}
		return $this->sqlBuilder;
	}

	/**
	 * ִ��������ݲ��� (insert)
	 * @param string | array $sql ��ѯ����

	 * @return boolean
	 */
	final public  function insert($sql){
		return $this->query($this->sqlBuilder->getInsertSql($sql));
	}
	
	/**
	 * ִ�и������ݲ���
	 * @param string | array $sql ��ѯ����

	 * @return boolean
	 */
	final public  function update($sql){
		return $this->query($this->sqlBuilder->getUpdateSql($sql));
	}
	/**
	 * ִ�в�ѯ���ݲ���
	 * @param string | array $sql ��ѯ����

	 * @return boolean
	 */
	final public function select($sql){
		$sql = is_string($sql) ? $sql : $this->sqlBuilder->getSelectSql($sql);
		return $this->query($sql);
	}
	/**
	 * ִ��ɾ�����ݲ���
	 * @param string | array $sql ��ѯ����
	 * @return boolean
	 */
	final public  function delete($sql){
		return $this->query($this->sqlBuilder->getDeleteSql($sql));
	}
	
	/**
	 * ִ���������ݲ���(replace)
	 * @param string | array $sql ��ѯ����
	 * @return boolean
	 */
	final public function replace($sql){
		return $this->query($this->sqlBuilder->getReplaceSql($sql));
	}
	
	/**
	 * ȡ����Ӱ�����������
	 * @param �Ƿ��ǲ�ѯ
	 * @param string|int ���ݿ����ӱ�ʶ
	 * @return int
	 */
	final public  function getAffectedRows($ifquery = false){
		return $this->query($this->sqlBuilder->getAffectedSql($ifquery));
	}
	/**
	 * ȡ��������ID
	 * @param string|int ���ݿ����ӱ�ʶ
	 * @return int
	 */
	final public  function getInsertId(){
		return $this->query($this->sqlBuilder->getLastInsertIdSql());
	}
	
	/**
	 *ȡ�����ݿ�Ԫ���ݱ�
	 */
	public  function getMetaTables($schema = ''){
		return $this->query($this->sqlBuilder->getMetaTableSql($schema));
	}
	/**
	 *ȡ�����ݱ�Ԫ������ 
	 */
	public  function getMetaColumns($table){
		return $this->query($this->sqlBuilder->getMetaColumnSql($table));
	}
	
	public function getConnection(){
		return $this->connection;
	}
	
	public function getConfig(){
		return $this->config;
	}

	/**
	 * ������һ��sqly���
	 * @return string
	 */
	final public function getLastSql() {
		return $this->last_sql;
	}

	final public function getSchema(){
		return  $this->config['dbname'];
	}
	
	final public function getDbDriver(){
		return  $this->config['dbtype'];
	}

	public function __destruct(){
		is_resource($this->connection) && $this->dispose();
	}
	
	
	
	

}