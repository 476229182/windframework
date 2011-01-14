<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 异常处理机制
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: weihu $>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id: WindException.php 37 2010-11-08 12:57:04Z weihu $ 
 * @package
 */
class WindException extends Exception {

	/* 类错误 */
	const ERROR_CLASS_NOT_EXIST = '100';

	/* 参数错误 */
	const ERROR_PARAMETER_TYPE_ERROR = '110';

	private $innerException = null;

	/**
	 * 异常构造函数
	 * 
	 * @param $message		     异常信息
	 * @param $code			     异常代号
	 * @param $innerException 内部异常
	 */
	public function __construct($message = '', $code = 0, Exception $innerException = null) {
		$message = $this->buildMessage($message, $code);
		parent::__construct($message, $code);
		$this->innerException = $innerException;
	}

	/**
	 * 取得内部异常
	 */
	public function getInnerException() {
		return $this->innerException;
	}

	/**
	 * 取得异常堆栈信息
	 */
	public function getStackTrace() {
		if ($this->innerException) {
			$thisTrace = $this->getTrace();
			$class = __CLASS__;
			$innerTrace = $this->innerException instanceof $class ? $this->innerException->getStackTrace() : $this->innerException->getTrace();
			foreach ($innerTrace as $trace)
				$thisTrace[] = $trace;
			return $thisTrace;
		} else {
			return $this->getTrace();
		}
		return array();
	}

	public function buildMessage($message, $code) {
		eval('$message="' . addcslashes($this->messageMapper($code), '"') . '";');
		
		return $message;
	}

	private function messageMapper($code) {
		$messages = array(
			self::ERROR_CLASS_NOT_EXIST => 'Unable to create instance for \'$message\' , class is not exist.', 
			self::ERROR_PARAMETER_TYPE_ERROR => 'Incorrect parameter type \'$message\'.');
		
		return isset($messages[$code]) ? $messages[$code] : '$message';
	}

}