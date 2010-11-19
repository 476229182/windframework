<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-18
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
class WindMsSqlBuilder extends WSqlBuilder{
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildTable()
	 */
	public function buildTable($table = array()) {
		if (empty ( $table ) || (! is_string ( $table ) && ! is_array ( $table ))) {
			throw new WSqlException ( 'table is not mepty' );
		}
		$table = is_string ( $table ) ? explode ( ',', $table ) : $table;
		$tableList = '';
		foreach ( $table as $key => $value ) {
			if (is_int ( $key )) {
				$tableList .= $tableList ? ',' . $value : $value;
			}
			if (is_string ( $key )) {
				$tableList .= $tableList ? ',' . $this->getAlias ( $key, $as, $value ) : $this->getAlias ( $key, $as, $value );
			}
		}
		return $this->sqlFillSpace ( $tableList );
	}
	
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildDistinct()
	 */
	public function buildDistinct($distinct = false) {
		return $this->sqlFillSpace ( $distinct ? 'DISTINCT' : '' );
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildField()
	 */
	public function buildField($field = array()) {
		$fieldList = '';
		if (empty ( $field )) {
			$fieldList = '*';
		}
		if (! is_string ( $field ) && ! is_array ( $field )) {
			throw new WSqlException ( 'field is illegal' );
		}
		$field = is_string ( $field ) ? explode ( ',', $field ) : $field;
		foreach ( $field as $key => $value ) {
			if (is_int ( $key )) {
				$fieldList .= $fieldList ? ',' . $value : $value;
			}
			if (is_string ( $key )) {
				$fieldList .= $fieldList ? ',' . $this->getAlias ( $key, $as, $value ) : $this->getAlias ( $key, $as, $value );
			}
		}
		return $this->sqlFillSpace ( $fieldList );
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildJoin()
	 */
	public function buildJoin($join = array()) {
		if (empty ( $join )) {
			return '';
		}
		if (is_string ( $join )) {
			return $this->sqlFillSpace($join);
		}
		$joinContidion = '';
		foreach ( $join as $table => $config ) {
			if (is_string ( $config ) && is_int($table)) {
				$joinContidion .= $joinContidion ? ' ' . $config : $config;
				continue;
			}
			if (is_array ( $config ) && is_string($table)) {
				$table = $this->getAlias ( $table, $as, $config [2] );
				$joinWhere = $config [1] ? ' ON ' . $config [1] : '';
				$condition = $config [0] . ' JOIN ' . $table . $joinWhere;
				$joinContidion .= $joinContidion ? ' ' . $condition : $condition;
			}
		}
		return $this->sqlFillSpace ( $joinContidion );
	}
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildWhere()
	 */
	public function buildWhere($where = array()) {
		if (empty ( $where )) {
			return '';
		}
		if (is_string ( $where )) {
			return 'WHERE' . $this->sqlFillSpace($where);
		}
		if (! is_array ( $where )) {
			throw new WSqlException ( 'The Where Condition is not correct', 0 );
		}
		list ( $lConter, $gConter, $conConter ) = $this->checkWhere ( $where );
		$_where = $tmp_where = '';
		$i = 0;
		$ifLogic = $this->serachLogic ( $where );
		foreach ( $where as $key => $value ) {
			if (is_int ( $key )) {
				if (in_array ( $value, array_keys ( $this->logic ) )) {
					$logic = $this->logic [$value];
				}
				if (in_array ( $value, array_keys ( $this->group ) )) {
					$group = $this->group [$value];
				}
			}
			if (is_string ( $key )) {
				if (in_array ( $key, array_keys ( $this->compare ) )) {
					$logic = $i > 0 && empty ( $ifLogic ) && $conConter > 1 ? $this->sqlFillSpace ( 'AND' ) : $logic;
					$tmp_where = $this->buildCompare ( $value [0], $value [1], $key );
					$i ++;
				}
			}
			$tmp_where = $this->sqlFillSpace ( $group ? $group == '(' ? $group . $tmp_where : $tmp_where . $group : $tmp_where );
			$_where .= $logic ? $logic . $tmp_where : $tmp_where;
			$group = $logic = $tmp_where = '';
		}
		return $_where ? 'WHERE ' . $_where : '';
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildGroup()
	 */
	public function buildGroup($group = array()) {
		return $this->sqlFillSpace ( $group ? 'GROUP BY ' . (is_array ( $group ) ? implode ( ',', $group ) : $group) . '' : '' );
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildOrder()
	 */
	public function buildOrder($order = array()) {
		$orderby = '';
		if (is_array ( $order )) {
			foreach ( $order as $key => $value ) {
				$orderby .= ($orderby ? ',' : '') . (! is_int ( $key ) ? $key . ' ' . strtoupper ( $value ) : $value);
			}
		} else {
			$orderby = $order;
		}
		return $this->sqlFillSpace ( $orderby ? 'ORDER BY ' . $orderby : '' );
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildHaving()
	 */
	public function buildHaving($having = '') {
		return $this->sqlFillSpace ( $having ? 'HAVING ' . $having : '' );
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildLimit()
	 */
	public function buildLimit($limit = 0, $offset = 0) {
		if(is_string($limit)){
			return $this->sqlFillSpace($limit);
		}
		return $this->sqlFillSpace ( ($sql = $limit > 0 ? 'LIMIT ' . $limit : '') ? $offset > 0 ? $sql . ' OFFSET ' . $offset : $sql : '' );
	}
	
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildData()
	 */
	public function buildData($data) {
		if (empty ( $data ) || ! is_array ( $data )) {
			throw new WSqlException ( "The insert data is empty",1 );
		}
		return $this->getDimension ( $data ) == 1 ? $this->buildSingleData ( $data ) : $this->buildMultiData ( $data );
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#buildSet()
	 */
	public function buildSet($set) {
		if (empty ( $set )) {
			throw new WSqlException ( "update data is empty" );
		}
		if (is_string ( $set )) {
			return $set;
		}
		foreach ( $set as $key => $value ) {
			$data [] = $key . '=' . $this->escapeString ( $value );
		}
		return $this->sqlFillSpace ( implode ( ',', $data ) );
	}
	
	/* (non-PHPdoc)
	 * @see wind/base/WSqlBuilder#escapeString()
	 */
	public function escapeString($value, $key = '') {
		return $value;
	}
	
	/**
	 * ȡ�ñ�����ʶ
	 * @param string $name Դ����
	 * @param string $as   ������ʶ
	 * @param string $alias ����
	 * @return string
	 */
	private function getAlias($name, $as = ' ', $alias = '') {
		return $this->sqlFillSpace ( ($alias ? $name . $this->sqlFillSpace ( strtoupper ( $as ) ) . $alias : $name) );
	}
	
	/**
	 * ������ѯ���ʽ
	 * @param string $field  ����
	 * @param unknown_type $value ��ֵ
	 * @param unknown_type $compare ���ʽ
	 * @return string
	 */
	private function buildCompare($field, $value, $compare) {
		if (empty ( $field ) || empty ( $value )) {
			throw new WSqlException ( '', '' );
		}
		if (! in_array ( $compare, array_keys ( $this->compare ) )) {
			throw new WSqlException ( '', '' );
		}
		if (in_array ( $compare, array ('in', 'notin' ) )) {
			$value = explode ( ',', $value );
			array_walk ( $value, array ($this, 'escapeString' ) );
			$value = implode ( ',', $value );
			$parsedCompare = $field . $this->sqlFillSpace ( $this->compare [$compare] ) . '(' . $value . ')';
		} else {
			$parsedCompare = $field . $this->sqlFillSpace ( $this->compare [$compare] ) . $this->escapeString ( $value );
		}
		return $parsedCompare;
	}
	
	/**
	 * �ж��Ƿ���߼������
	 * @param array $where
	 * @return string|string
	 */
	private function serachLogic($where) {
		foreach ( $this->logic as $key => $value ) {
			if (array_search ( $key, $where )) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * ����Ƿ��ǺϷ��Ĳ�ѯ����
	 * @param array $where
	 * @return array  
	 */
	private function checkWhere($where) {
		$logic = $group = $condition = 0;
		foreach ( $where as $key => $value ) {
			if (is_int ( $key )) {
				if (in_array ( $value, array_keys ( $this->logic ) )) {
					$logic ++;
				}
				if (in_array ( $value, array_keys ( $this->group ) )) {
					$group ++;
				}
			}
			if (is_string ( $key )) {
				if (in_array ( $key, array_keys ( $this->compare ) )) {
					$condition ++;
				}
			}
		}
		if ($group % 2 === 1) {
			throw new WSqlException ( 'kuo huao is not match', 1 );
		}
		if ($logic && $condition && $condition - $logic != 1) {
			throw new WSqlException ( 'condition is not match', 1 );
		}
		if ($group && $condition === 0) {
			throw new WSqlException ( 'condition is not match', 1 );
		}
		return array ($logic, $group, $condition );
	}
	
}