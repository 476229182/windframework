<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-25
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindErrorMessage extends WindMessage {
	private $template = '';
	private $errorAction = 'WIND:core.base.WindErrorAction';
	private static $instance = null;
	private function __construct() {}
	
	/**
	 * ��Ӵ�����Ϣ
	 * 
	 * @param string $message
	 * @param string $key
	 */
	public function addError($message, $key = '') {
		parent::addMessage($message, $key);
	}
	
	/**
	 * ���ش�����Ϣ
	 * 
	 * @param string $key
	 * @return Ambigous <string, multitype:>
	 */
	public function getError($key = '') {
		return parent::getMessage($key);
	}
	
	/**
	 * ���ô������ģ��
	 * 
	 * @param string $template
	 */
	public function setTemplate($template = '') {
		$this->template = $template;
	}
	
	/**
	 * ���ô��������
	 * 
	 */
	public function setErrorAction($action = '') {
		$this->errorAction = $action;
	}
	
	/**
	 * @return WindErrorMessage
	 */
	static public function &getInstance() {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}

}