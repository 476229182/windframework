<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'BaseTestSetting.php');
require_once(R_P . '/component/db/base/WindSqlBuilder.php');


class TestWindSqlBuilder extends PHPUnit_Framework_TestCase {
	public function testgetInsertSql() {
		
	}
	public function testgetUpdateSql() {
		
	}
	public function testgetDeleteSql() {
		
	}
	public function testgetSelectSql() {
		
	}
	public function testbuildSingleData() {
		
	}
	public function testbuildMultiData() {
		
	}
	/**
	 * 
	 */
	public function testgetDimension() {
		#��֤�����飬�����õ�0�������ȷ
		$testVar1 = array();
		$this->assertEquals(0, WindSqlBuilder::getDimension($testVar1));
		#��֤һά����
		$testVar2 = array('xxx', 'xx1');
		$this->assertEquals(1, WindSqlBuilder::getDimension($testVar2));
		#��֤��һ��Ԫ�������飬�ڶ���Ԫ�ز�������
		$testVar3 = array(array('xxx'), 'xxx');
		$this->assertEquals(2, WindSqlBuilder::getDimension($testVar3));
		#��֤��һ��Ԫ�ز������飬�ڶ���Ԫ��������
		$testVar4 = array('xxx', array('xxx'));
		$this->assertEquals(1, WindSqlBuilder::getDimension($testVar4));
		#��֤��ά����
		$testVar5 = array(array(111), array('xxx'));
		$this->assertEquals(2, WindSqlBuilder::getDimension($testVar5));
		#��֤�ַ���
		$testVar6 = 'xxx';
		$this->assertEquals(0, WindSqlBuilder::getDimension($testVar6));
		#��֤����
		$testVar7 = new self();
		$this->assertEquals(0, WindSqlBuilder::getDimension($testVar7));
	}
	public function testsqlFillSpace() {
		#��֤�ַ���
		$testVar1 = 'xxx';
		$this->assertEquals(' xxx ', WindSqlBuilder::sqlFillSpace($testVar1));
		#��֤����----��֤ʧ��----����û���жϹ�����---�Ƿ���Ҫ
		$testVar2 = array('xxx');
		$this->assertEquals($testVar2, WindSqlBuilder::sqlFillSpace($testVar2));
		#��֤����----��֤ʧ��----����û���жϹ�����---�Ƿ���Ҫ
		$testVar3 = new self();
		$this->assertEquals($testVar2, WindSqlBuilder::sqlFillSpace($testVar3));
	}
}
