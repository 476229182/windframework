<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-11
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import ( 'WIND:component.db.base.WindDbAdapter' );
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindMySql extends WindDbAdapter {
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#connect()
	 */
	protected function connect() {
		if (!is_resource ( $this->connection )) {
			$this->connection = $this->config ['pconnect'] ? mysql_pconnect ( $this->config ['host'], $this->config ['dbuser'], $this->config ['dbpass'] ) : mysql_connect ( $this->config ['host'], $this->config ['dbuser'], $this->config ['dbpass'], $this->config ['force'] );
			$this->changeDB ( $this->config ['dbname'] );
			$this->setCharSet ( $this->config ['charset'] );
		}
		return $this->connection;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#query()
	 */
	public function query($sql) {
		$this->query = mysql_query ( $sql, $this->connection );
		$this->error ();
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#getAllResult()
	 */
	public function getAllResult($fetch_type = MYSQL_ASSOC) {
		if (! is_resource ( $this->query )) {
			throw new WindSqlException ( WindSqlException::DB_QUERY_LINK_EMPTY );
		}
		if (! in_array ( $fetch_type, array (1, 2, 3 ) )) {
			throw new WindSqlException ( WindSqlException::DB_QUERY_FETCH_ERROR );
		}
		$result = array ();
		while ( ($record = mysql_fetch_array ( $this->query, $fetch_type )) ) {
			$result [] = $record;
		}
		return $result;
	}
	
	public function beginTrans() {
		if ($this->transCounter == 0) {
			$this->query ( 'START TRANSACTION');
		} elseif ($this->transCounter && $this->enableSavePoint) {
			$savepoint = 'savepoint_' . $this->transCounter;
			$this->query ( "SAVEPOINT `{$savepoint}`");
			array_push ( $this->savepoint, $savepoint );
		}
		++ $this->transCounter;
		return true;
	}
	
	public function commitTrans() {
		if ($this->transCounter <= 0) {
			throw new WindSqlException ( WindSqlException::DB_QUERY_TRAN_BEGIN );
		}
		-- $this->transCounter;
		if ($this->transCounter == 0) {
			if ($this->last_errstr) {
				$this->query ( 'ROLLBACK');
			} else {
				$this->query ( 'COMMIT');
			}
		} elseif ($this->enableSavePoint) {
			$savepoint = array_pop ( $this->savepoint );
			if ($this->last_errstr) {
				$this->query ( "ROLLBACK TO SAVEPOINT `{$savepoint}`" );
			}
		}
	}
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#close()
	 */
	public function close() {
		if($this->connection){
			mysql_close ( $this->connection );
		}
	}
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#dispose()
	 */
	public function dispose() {
		$this->close($this->connection);
		$this->connection = null;
	}
	/**
	 * ȡ��mysql�汾��
	 * @param string|int|resource $key ���ݿ����ӱ�ʶ
	 * @return string
	 */
	public function getVersion() {
		return mysql_get_server_info ( $this->connection);
	}
	
	/**
	 * @param string $charset �ַ���
	 * @param string | int $key ���ݿ����ӱ�ʶ
	 * @return boolean
	 */
	public function setCharSet($charset) {
		$version = ( int ) substr ( $this->getVersion (), 0, 1 );
		if ($version > 4) {
			$this->read ( "SET NAMES '" . $charset . "'");
		}
		return true;
	}
	
	/**
	 * �л����ݿ�
	 * @see wind/base/WDbAdapter#changeDB()
	 * @param string $databse Ҫ�л������ݿ�
	 * @param string|int|resource $key ���ݿ����ӱ�ʶ
	 * @return boolean
	 */
	public function changeDB($database) {
		return $this->read ( "USE $database");
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WDbAdapter#error()
	 */
	protected function error($sql) {
		$this->last_errstr = mysql_error ();
		$this->last_errcode = mysql_errno ();
		$this->last_sql = $sql;
		if ($this->last_errstr || $this->last_errcode) {
			throw new WindSqlException ( $this->last_errstr, $this->last_errcode );
		}
		return true;
	}
	
	
}