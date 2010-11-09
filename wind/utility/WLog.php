<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * ��־��¼
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
class WLog {
	/*��������*/
	const ERROR = 'error';
	const TRACE = 'trace';
	const INFO = 'info';
	const DB = 'db';
	/*д����־���*/
	private static $msgType =  array(
		'system'=>0,
		'email'=>1,
		'tcp'=>2,
		'file'=>3
	);
	
	private static $logs = array();
	/**
	 * ��¼��־��Ϣ������д���ļ�
	 * @param string $msg	     ��־��Ϣ
	 * @param const  $logType ��־���
	 */
	public static function add($msg,$logType = self::INFO){
		self::$logs[] = self::build($msg,$logType);
	}
	
	/**
	 * ֱ������־д���ļ�
	 * @param $msg 		��־��Ϣ
	 * @param $logType	��־���
	 * @param $type		��¼���
	 * @param $dst		��־����¼�ںδ�
	 * @param $header	������Ϣ
	 */
	public static function log($msg,$logType = self::INFO,$type = 'file',$dst = '',$header = ''){
		$type = in_array($type,self::$msgType) ? $type : 'file';
		$dst = empty($dst) ? self::getFileName() : $dst;
		$msg = self::build($msg,$logType);
		error_log($msg,self::$msgType[$type],$dst,$header);
	}
	
	/**
	 * ����¼����־�б���Ϣд���ļ�
	 * @param string $type ��־���
	 * @param string $dst  ��־����¼�ںδ�
	 * @param string $header ������Ϣ
	 */
	public static function flush($type = 'file',$dst = '',$header = ''){
		if(empty(self::$logs)) return false;
		$type = in_array($type,self::$msgType) ? $type : 'file';
		$dst = empty($dst) ? self::getFileName() : $dst;
		error_log(join("",self::$logs),self::$msgType[$type],$dst,$header);
		self::$logs = array();
	}
	
	/*
	 * �����־�ļ�
	 */
	public static function clearFiles($time = 0){
		if(!is_int($time) || 0 > intval($time) || !is_dir(LOG_PATH) ) return false;
		$dir = dir(LOG_PATH);
		while(false != ($file = $dir->read())){
			$file = LOG_PATH.$file;
			is_file($file) ? (microtime(true)-filectime($file)) > $time && unlink($file) : '';		
		}
		$dir->close();
		return true;
	}
	/**
	 * ȡ����־�ļ���
	 */
	private static function getFileName(){
		self::createFolder(LOG_PATH);
		$filename = LOG_PATH.date("Y_m_d").'.'.LOG_DISPLAY_TYPE;
		if(is_file($filename) && 1024*50 < filesize($filename)){
			$fileArray = explode('_',basename($filename));
			$after = $fileArray[count($fileArray)-1];
			$after = is_int($after) ? ++$after : 0;
			$filename = LOG_PATH.date("Y_m_d_{$after}").'.'.LOG_DISPLAY_TYPE;
		}
		return $filename;
	}
	
	private static function createFolder($path) {
		if (!is_dir($path)) {
			self::createFolder(dirname($path));
			mkdir($path);
			chmod($path, 0777);
		}
	}
	
	/**
	 * ��װ��־��Ϣ
	 * @param string $msg	     ��־��Ϣ
	 * @param const  $logType ��־���
	 * @return string
	 */
	private static function build($msg,$logType = self::INFO){
		if(self::TRACE != $logType){
			$trace = debug_backtrace();
			foreach($trace as $info){
				if('log' == $info['function'] || 'add' == $info['function']){
					$msg .= ' in '.$info['file'] . ' on line '.$info['line'].' ['.date('Y-m-d H:i',time()).']';
					break;
				}
			}
		}
	   return 'log' == LOG_DISPLAY_TYPE ? self::buildLog($msg,$logType) :  self::buildHtm($msg,$logType);
	}
	
	private static function buildHtm($msg,$logType = self::INFO){
		return '<span style="color:red"><strong>'.strtoupper($logType).'</span></strong> : '.$msg.'<br/>';
	}
	
	private static function buildLog($msg,$logType = self::INFO){
		return '��'.strtoupper($logType).'��'.' : '.$msg."\r\n\r\n";
	}
	
}

	