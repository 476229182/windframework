<?php
require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'BaseTestSetting.php');
require_once R_P . '/core/WmySqlBuilder.php';
/**
 * test case.
 */
class TestWMySqlBuilder extends PHPUnit_Framework_TestCase {
	private $testObject;
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		$this->testObject = new WMySqlBuilder();
	    // TODO Auto-generated TestWMySqlBuilder::setUp()
	}
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->testObject = null;
		// TODO Auto-generated TestWMySqlBuilder::tearDown()
		parent::tearDown ();
	}
	//��������������ʱ�����
	public function testbuildTable() {
		//��Ϊ�յ����
		$this->assertEquals(' test ', $this->testObject->buildTable('test'));
		$this->assertEquals(' test1,test2 ', $this->testObject->buildTable('test1,test2'));
		$this->assertEquals(' test1,test2 ', $this->testObject->buildTable('test1,,test2'));
		$this->assertEquals(' test1 as t1,test2 as t2', $this->testObject->buildTable(array('t1' => 'test1', 't2' => 'test2')));
		$this->assertEquals(' test1 as t1,test2 ', $this->testObject->buildTable(array('t1' => 'test1', 'test2')));
	}
	public function testbuildTableFailed() {
		try{
			$this->testObject->buildTable('');
			$this->testObject->buildTable(0);
			$this->testObject->buildTable(new self);
			$this->fail('Exception expected');
		}catch(WSqlException $e) {
			$this->assertTrue($e instanceof WSqlException);
		}
	}
	
	public function testbuildDistinct() {
		//���Խ����ֻҪ�ǲ�Ϊ�յ��κ�mixd���ͣ����ܷ���DISTINCT,���򷵻�'';
		$this->assertEquals(' DISTINCT ', $this->testObject->buildDistinct($this->testObject));
		$this->assertEquals(' DISTINCT ', $this->testObject->buildDistinct(array('yes')));
		$this->assertEquals('  ', $this->testObject->buildDistinct(array()));//������Ҳ�Ὣ�ո���
		$this->assertEquals(' DISTINCT ', $this->testObject->buildDistinct('yes'));
		$this->assertEquals(' DISTINCT ', $this->testObject->buildDistinct(1));
	}
}


