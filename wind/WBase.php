<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */

/* ·�����������Ϣ  */
defined('WIND_PATH') or define('WIND_PATH', dirname(__FILE__));
defined('SYSTEM_CONFIG_PATH') or define('SYSTEM_CONFIG_PATH', WIND_PATH);

/* ��չ�� */
defined('EXT') or define('EXT', 'php');

/* import */
defined('IMPORT_SEPARATOR') or define('IMPORT_SEPARATOR', '.');
defined('IMPORT_PACKAGE') or define('IMPORT_PACKAGE', '*');

defined('RUNTIME_START') or define('RUNTIME_START', microtime(true));

defined('USEMEM_START') or define('USEMEM_START', memory_get_usage());

defined('LOG_PATH') or define('LOG_PATH', WIND_PATH . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR);

defined('LOG_DISPLAY_TYPE') or define('LOG_DISPLAY_TYPE', 'log');

defined('LOG_RECORD') or define('LOG_RECORD', true);

defined('DEBUG') or define('DEBUG', true);

/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 */
class W {
	
	/* �Ѿ���include��������߰� */
	static $_included = array();
	
	/* �Ѿ���ʵ�������Ķ��󼯺� */
	static $_instances = array();
	
	static $_vars = array();
	
	static $_system_config = 'config.php';
	
	/**
	 * ��ʼ�����������
	 * 1. ���Լ��ؿ�ܱ���Ļ������
	 * 
	 */
	static public function init() {
		self::_autoIncludeBaseLib();
		set_exception_handler(array('W','WExceptionHandler'));
		defined('LOG_RECORD') && W::import('utility.wlog.php');
		defined('DEBUG') && W::import('utility.wdebug.php');
	}
	
	/**
	 * ����ļ��ľ���·��
	 * @param string $path
	 */
	static public function getRealPath($path = '', $is_dir = false, $root = '') {
		if (!$root)
			$root = self::getFrameWorkPath();
		$realPath = $root . self::getSeparator() . $path;
		$realPath = str_replace(IMPORT_SEPARATOR, self::getSeparator(), $realPath);
		if (!$is_dir) {
			$pos = strrpos($realPath, self::getSeparator());
			$ext = substr($realPath, $pos + 1);
			$realPath = substr($realPath, 0, $pos) . '.' . substr($realPath, $pos + 1);
		}
		return realpath($realPath);
	}
	
	static public function getVar($name) {
		return self::$_vars[$name];
	}
	
	static public function setVar($name, $value) {
		if (!isset(self::$_vars[$name]))
			self::$_vars[$name] = $value;
	}
	
	/**
	 * ��ÿ�ܸ�·��
	 * @return string
	 */
	static public function getFrameWorkPath() {
		return WIND_PATH;
	}
	
	/**
	 * ���ϵͳ�����ļ�·����Ϣ
	 * @return string
	 */
	static public function getSystemConfigPath() {
		return SYSTEM_CONFIG_PATH;
	}
	
	/**
	 * ����ļ�·���ָ���
	 * @return string
	 */
	static public function getSeparator() {
		return DIRECTORY_SEPARATOR;
	}
	
	/**
	 * ����ļ���չ��
	 * @return string
	 */
	static public function getExtendName() {
		return EXT;
	}
	
	/**
	 * ���֧�ּ��ص���չ������
	 * �ж���չ���Ƿ�֧��
	 * @param string $ext
	 * @return boolean|multitype:string 
	 */
	static public function getExtendNames($ext = '') {
		$exts = array();
		if ($ext)
			return in_array($ext, $exts);
		return $exts;
	}
	
	/**
	 * ���һ����ľ�̬��������
	 * ȫ�ֵľ�̬�����������������ʽ������ < self::$_instances >�У�����Ϊ������
	 * �����Ʊ�����ļ�������ͬ�������׳��쳣
	 * ֧�ֹ��캯������
	 * ����һ�����������
	 * 
	 * @param string $className
	 * @retur Object
	 */
	static public function getInstance($className, $args = array()) {
		if (!key_exists($className, self::$_instances))
			self::_createInstance($className, $args);
		return self::$_instances[$className];
	}
	
	/**
	 * ����һ������߼���һ����
	 * �Կ��·��Ϊ��·�����м���
	 * ����һ����Ĳ�����ʽ��'core.WFrontController.php'
	 * ����һ�����Ĳ�����ʽ��'core.*'
	 *
	 * ������ص����Ǽ̳����������� WContext
	 * ��ô�������ͬʱ�����ɸ���ľ�̬��������
	 * �û�����ͨ��getInstance()������øö���
	 *
	 * @param string $classPath
	 * @author Qiong Wu
	 * @return void
	 */
	static public function import($filePath) {
		if (file_exists($filePath)) {
			self::_include($filePath);
		}
		
		$filePath = trim(str_replace(self::getSeparator(), IMPORT_SEPARATOR, $filePath), ' ');
		$filePath = trim($filePath, ' ' . IMPORT_SEPARATOR);
		if (!isset($filePath) || ($pos = strrpos($filePath, '.')) === false)
			throw new Exception('is not right path');
		
		$isPackage = (string) substr($filePath, $pos + 1) === IMPORT_PACKAGE;
		$dir = self::getFrameWorkPath();
		$classNames = array();
		if ($isPackage) {
			$dir = self::getRealPath(substr($filePath, 0, $pos), true);
			if (!is_dir($dir))
				throw new Exception('�ļ�·�� ' . $dir . ' ������');
			
			if (!$dh = opendir($dir))
				throw new Exception('�ļ� ' . $dir . ' ���쳣');
			
			while (($file = readdir($dh)) !== false) {
				if ($file != "." && $file != ".." && !(is_dir($dir . self::getSeparator() . $file)))
					$classNames[] = $dir . self::getSeparator() . $file;
			}
			closedir($dh);
		} else
			$classNames[] = self::getRealPath($filePath);
		
		foreach ($classNames as $value) {
			self::_include($value);
		}
		
		return;
	}
	
	/**
	 * ȫ�ְ����ļ���Ψһ���
	 * @param string $className ������/�ļ���
	 * @param string $classPath ��·��/�ļ�·��
	 * @return string
	 */
	static private function _include($realPath) {
		if (empty($realPath)) {return;}
		$pos = strrpos($realPath, self::getSeparator());
		$fileName = substr($realPath, $pos + 1);
		$dir = substr($realPath, 0, $pos);
		$fileName = substr($fileName, 0, strrpos($fileName, '.'));
		if (!file_exists($realPath))
			throw new Exception('file ' . $realPath . ' is not exists');
		
		if (key_exists($fileName, self::$_included)) {return $realPath;}
		include $realPath;
		
		$var = get_defined_vars();
		if (count($var) > 4)
			self::$_vars += array_splice($var, 4);
		
		self::$_included[$fileName] = $realPath;
		return $realPath;
	}
	
	/**
	 * ���������ƴ�����ĵ������󣬲����浽��̬������
	 * ͬʱ��������������Ĳ���
	 * 
	 * @param string $className ������
	 * @return void|string
	 */
	static private function _createInstance($className, $args) {
		if (key_exists($className, self::$_instances))
			return;
		$class = new ReflectionClass($className);
		if ($class->isAbstract() || $class->isInterface())
			return;
		
		if (!is_array($args))
			$args = array();
		$object = call_user_func_array(array(
			$class, 
			'newInstance'
		), $args);
		
		/*if (in_array('WContext', (array) class_implements($className))) {
			$class->setStaticPropertyValue('instance', & $object);
			$scope = $class->getStaticPropertyValue('scope', 'request');
			//TODO ��������������
		}*/
		self::$_instances[$className] = & $object;
	
	}
	
	/**
	 * �Զ����ؿ�ܵײ����
	 * ���������ĳ�����ͽӿ�
	 */
	static private function _autoIncludeBaseLib() {
		self::import('exception.WException.php');
		self::import('base.WModule.php');
		self::import('base.*');
		self::import('core.*');
		
	}
	
	/**
	 * ��ʼ��ϵͳ������Ϣ
	 * 
	 */
	static private function _initSystemConfig($config) {
		$systemConfigPath = self::getSystemConfigPath();
		$systemConfig = self::$_system_config;
		$realPath = self::getRealPath($systemConfig, false, $systemConfigPath);
		if (!file_exists($realPath))
			throw new Exception('SYS Excetion �������ļ�������!!!');
		self::import($realPath);
		self::getSystemConfig()->parse($systemConfig, $config);
	}
	
	/**
	 * �쳣�����Լ�������Ϣ��¼����־
	 * @param $message
	 * @param $trace
	 */
	static public function recordLog($message,$type = 'INFO',$ifrecord = 'add'){
		if(defined('LOG_RECORD')){
			$message = str_replace('<br/>',"\r\n",$message);
    		$ifrecord == 'add' ? WLog::add($message,strtoupper($type)) : WLog::log($message,strtoupper($type));
    	}
	}
	
	/**
	 * ���������Ϣ�Ƿ�debug����
	 * @param $message
	 * @param $trace
	 */
	static public function debug($message,$trace=array()){
		return defined('DEBUG') ? WDebug::debug($message,$trace) : $message;
	}
	

	
	static public function WExceptionHandler($e){
		$trace = is_a($e,'WException') ? $e->getStackTrace() : $e->getTrace();
		$message = W::debug("{$e}",$trace);
		W::recordLog($message,'TRACE','log');
		die($message);
	}

}

W::init();
