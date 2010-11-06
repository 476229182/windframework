<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * �쳣�������
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
class WException extends exception {
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
	public function __construct($message = '',$code=0,exception $innerException = null) {
		$message = $this->buildMessage($message,$code);
        parent::__construct($message,$code);
        $this->innerException = $innerException;
    }
    
    /**
     * ȡ���ڲ��쳣
     */
    public function getInnerException(){
    	return $this->innerException;
    }
    
    /**
     * ȡ���쳣��ջ��Ϣ
     */
    public function getStackTrace(){
    	if($this->innerException){
	    	$thisTrace = $this->getTrace();
	    	$innerTrace = get_class($this->innerException) == __CLASS__ ? $this->innerException->getStackTrace() :$this->innerException->getTrace();
	    	foreach($innerTrace as $trace) $thisTrace[] = $trace;
	    	return $thisTrace;
    	}else{
    		return $this->getTrace();
    	}
    	return array();
    }
    
    public function buildMessage($message,$code){
    	return $message;
    }
    
    public function __destruct(){
    	if(defined('LOG_RECORD')){
    		$message = $this->getMessage()."\r\n";
    		if(defined('DEBUG')){
    			$message .= WDebug::debug($this->getStackTrace());
    		}
    		WLog::add($message,WLog::TRACE);
    	}
    }
}