<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * ���������
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WError {
	private static $error = array();
	
	/**
	 * ���һ�������¼
	 * 
	 * @param string $message
	 * @param boolean $clear
	 */
	static public function addError($message, $clear = false) {
		self::$error[] = $message;
	}
	
	/**
	 * �������д����¼
	 */
	static public function clearError() {
		self::$error = array();
	}
	
	/**
	 * �������д����¼
	 */
	static public function getError() {
		return self::$error;
	}
	
	/**
	 * �жϴ������������������Ϣ
	 */
	static public function showMessage($message) {

	}

}