<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-25
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
class WindError {
	private static $error = array();
	
	/**
	 * ���һ�������¼
	 * 
	 * @param string $message
	 * @param boolean $clear
	 */
	public function addError($message, $clear = false) {
		self::$error[] = $message;
	}
	
	/**
	 * �������д����¼
	 */
	public function clearError() {
		self::$error = array();
	}
	
	/**
	 * �������д����¼
	 */
	public function getError() {
		return self::$error;
	}
	
	/**
	 * �жϴ������������������Ϣ
	 */
	public function showMessage($message) {

	}
	
	static public function getInstance() {

	}

}