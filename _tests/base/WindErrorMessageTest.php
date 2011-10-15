<?php
/**
 * WindErrorMessage test case.
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-14
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package base
 */
class WindErrorMessageTest extends BaseTestCase {
	
	/**
	 * @var WindErrorMessage
	 */
	private $WindErrorMessage;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp();
		require_once 'base\WindErrorMessage.php';
		$this->WindErrorMessage = new WindErrorMessage("error","errorAction");
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->WindErrorMessage = null;
		parent::tearDown();
	}

	/**
	 * @dataProvider dataForClearError
	 */
	public function testClearError($data, $key = '') {
		$this->WindErrorMessage->addError($data, $key);
		$this->assertEquals($this->WindErrorMessage->getError("content"), "shilong");
		$this->WindErrorMessage->clearError();
		$this->assertEquals($this->WindErrorMessage->getError(), array());
	}
	
	public function dataForClearError(){
		$args = array();
		$object = new stdClass();
		$object->content = 'shilong';
		$args[] = array($object);
		$args[] = array(array('content' => 'shilong'),'');
		$args[] = array('shilong', 'content');
		return $args;
	}

	/**
	 * Tests WindErrorMessage->getErrorAction()
	 */
	public function testGetErrorAction() {
		$this->assertEquals($this->WindErrorMessage->getErrorAction(), "errorAction");
	}
}
