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
abstract class WindSqlBuilder {
	
	/**
	 * @var array ������ʽ
	 */
	protected $compare = array ('gt' => '>', 'egt' => '>=', 'lt' => '<', 'elt' => '<=', 'eq' => '=', 'neq' => '!=', 'in' => 'IN', 'notin' => 'NOT IN', 'notlike' => 'NOT LIKE', 'like' => 'LIKE' );
	/**
	 * @var array �߼������
	 */
	protected $logic = array ('and' => 'AND', 'or' => 'OR', 'xor' => 'XOR' );
	/**
	 * @var array ��������
	 */
	protected $group = array ('lg' => '(', 'rg' => ')' );
	
	/**
	 * �����
	 * @example array('tablename'=>'alais') or array('tablename'),tablename as alais
	 * @param array|string $table ��
	 * @return string;
	 */
	public abstract function buildTable($table = array());
	/**
	 * �Ƿ������ͬ����
	 * @param boolean $distinct
	 * @return string
	 */
	public abstract function buildDistinct($distinct = false);
	/**
	 * �����������
   	 * @example array('filedname'=>'alais') or array('filedname'),filedname as alais
	 * @param array|string $field ��ѯ���ֶ�
	 * @return string
	 */
	public abstract function buildField($field = array());
	/**
	 * �������Ӳ�ѯ
	 * @example array('tablename'=>array(jointype,onwhere,alias)) or array('left join tablename as a on a.id=b.id') 
	 * 			'left join tablename as a on a.id=b.id'
	 * @param string|array $join ��������
	 * @return string
	 */
	public abstract function buildJoin($join = array());
	/**
	 * ������ѯ����
	 * @example array('lg','gt'=>('age',2),and,'lt'=>array('age',23),'gt',or,like=>array('name','suqian%')) or 
	 * 			( age > 2 and age < 23) or name like 'suqian%'
	 * @param array $where ��ѯ����
	 * @return string
	 */
	public abstract function buildWhere($where = array());
	/**
	 * ��������
	 * @example array('field1','field2') or 'group by field1,field2'
	 * @param string|array $group ��������
	 * @return string
	 */
	public abstract function buildGroup($group = array());
	/**
	 * ��������
	 * @example array('field1'=>'desc','field2'=>'asc') or 'order by field1 desc,field2 asc'
	 * @param array|string $order ��������
	 * @return string
	 */
	public abstract function buildOrder($order = array());
	/**
	 * �����Է���Ĺ������
	 * @param string $having
	 * @return string
	 */
	public abstract function buildHaving($having = '');
	/**
	 * ������ѯlimit���
	 * @param int $limit  ȡ������
	 * @param int $offset ƫ����
	 * @return string
	 */
	public abstract function buildLimit($limit = 0, $offset = 0);
	/**
	 * ������������
	 * @example array('field'=>'value');
	 * @param array $data 
	 * @return string
	 */
	public abstract function buildSet($data);
	/**
	 * �����������
	 * @example array('field1','field2') or array(array('field1','field2'),array('field1','field2'))
	 * @param array $setData
	 * @return string
	 */
	public abstract function buildData($setData);
	
	/**
	 *����Ӱ��������sql���
	 *@param $ifquery �Ƿ���select ���
	 *@return string 
	 */
	public abstract function buildAffected($ifquery);
	
	/**
	 *����ȡ�����������sql���
	 *@return string 
	 */
	public abstract function buildLastInsertId();
	
	/**
	 * ���ַ���ת��
	 * @param string $value
	 * @return string
	 */
	public abstract function escapeString($value);
	
	/**
	 * @param strint $schema ���ݿ���
	 */
	public abstract function getMetaTableSql($schema);
	
	/**
	 * @param string $table  ����
	 */
	public abstract function getMetaColumnSql($table);
	/**
	 * ��������SQL���
	 * @param array $option
	 * @return string
	 */
	public function getInsertSql($option) {
		return sprintf ( "INSERT%s%sVALUES%s", $this->buildTable ( $option ['table'] ), $this->buildField ( $option ['field'] ), $this->buildData ( $option ['data'] ) );
	}
	/**
	 * ��������QL���
	 * @param array $option
	 * @return string
	 */
	public function getUpdateSql($option) {
		return sprintf ( "UPDATE%sSET%s%s%s%s", $this->buildTable ( $option ['table'] ), $this->buildSet ( $option ['set'] ), $this->buildWhere ( $option ['where'] ), $this->buildOrder ( $option ['order'] ), $this->buildLimit ( $option ['limit'] ) );
	}
	/**
	 * ����ɾ��SQL���
	 * @param array $option
	 * @return string
	 */
	public function getDeleteSql($option) {
		return sprintf ( "DELETE FROM%s%s%s%s", $this->buildTable ( $option ['table'] ), $this->buildWhere ( $option ['where'] ), $this->buildOrder ( $option ['order'] ), $this->buildLimit ( $option ['limit'] ) );
	}
	/**
	 * ������ѯSQL���
	 * @param array $option
	 * @return string
	 */
	public function getSelectSql($option) {
		return sprintf ( "SELECT%s%sFROM%s%s%s%s%s%s%s", $this->buildDistinct ( $option ['distinct'] ), $this->buildField ( $option ['field'] ), $this->buildTable ( $option ['table'] ), $this->buildJoin ($option ['join']), $this->buildWhere ( $option ['where'] ), $this->buildGroup ( $option ['group'] ), $this->buildHaving ( $option ['having'] ), $this->buildOrder ( $option ['order'] ), $this->buildLimit ( $option ['limit'], $option ['offset'] ) );
	}
	
	/**
	 * ����replace SQL���
	 * @param array $option
	 * @return string
	 */
	public function getReplaceSql($option){
		return sprintf ( "REPLACE%s%sSET%s", $this->buildTable ( $option ['table'] ), $this->buildField ( $option ['field'] ), $this->buildData ( $option ['data'] ) );
	}
	
	public function getAffectedSql($ifquery){
		return sprintf ("SELECT%s",$this->buildAffected($ifquery));
	}
	
	public function getLastInsertIdSql(){
		return sprintf ("SELECT%s",$this->buildLastInsertId());
	}
	
	/**
	 * �ж��Ƿ��Ƕ�ά����
	 * @param array $array
	 * @return number
	 */
	public function getDimension($array = array()) {
		$dim = 0;
		foreach ($array as $value ) {
			return  is_array($value) ? $dim+=2 : ++$dim;
		}
		return $dim;
	}
	
	/**
	 * Ҫ������һά���飬�����������
	 * @param array $data Ҫ����������
	 * @return string
	 */
	public function buildSingleData($data) {
		foreach ( $data as $key => $value ) {
			$data [$key] = $this->escapeString ( $value );
		}
		return $this->sqlFillSpace('(' . implode ( ',', $data ) . ')');
	}
	
	/**
	 * ������ά���飬�������
	 * @param array $multiData Ҫ����������
	 * @return string
	 */
	public function buildMultiData($multiData) {
		$iValue = '';
		foreach ( $multiData as $data ) {
			$iValue .= $this->buildSingleData ( $data );
		}
		return $iValue;
	}
	
	/**
	 * ���ַ���ͷβ��ӿո��հ��ַ�
	 * @param string $value  �ַ���
	 * @return string
	 */
	public function sqlFillSpace($value) {
		return str_pad ( $value, strlen ( $value ) + 2, " ", STR_PAD_BOTH );
	}
}