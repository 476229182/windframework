<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * �쳣�������
 * the last known user to change this file in the repository  <$LastChangedBy: weihu $>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id: WException.php 37 2010-11-08 12:57:04Z weihu $ 
 * @package
 */
class WException extends Exception {
	const ERROR = 0;
	const WARN = 1;
	const NOTICE = 2;
	const PARSE = 3;
	const SYSTEM = 4;
	private $innerException = null;
	
	/**
	 * �쳣���캯��
	 * @param $message		     �쳣��Ϣ
	 * @param $code			     �쳣����
	 * @param $innerException �ڲ��쳣
	 */
	public function __construct($message = '', $code = 0, Exception $innerException = null) {
		$message = $this->buildMessage($message, $code);
		parent::__construct($message, $code);
		$this->innerException = $innerException;
	}
	
	/**
	 * ȡ���ڲ��쳣
	 */
	public function getInnerException() {
		return $this->innerException;
	}
	
	/**
	 * ȡ���쳣��ջ��Ϣ
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
		return $message;
	}

}