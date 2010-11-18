<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */
//error_reporting(E_ERROR | E_PARSE);


/* ·�����������Ϣ  */
defined('WIND_PATH') or define('WIND_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
defined('SYSTEM_CONFIG_PATH') or define('SYSTEM_CONFIG_PATH', WIND_PATH . 'config.php');

/* import */
defined('IMPORT_NAMESPACE') or define('IMPORT_NAMESPACE', ':');
defined('IMPORT_SEPARATOR') or define('IMPORT_SEPARATOR', '.');
defined('IMPORT_PACKAGE') or define('IMPORT_PACKAGE', '*');

defined('RUNTIME_START') or define('RUNTIME_START', microtime(true));

defined('USEMEM_START') or define('USEMEM_START', memory_get_usage());

defined('LOG_PATH') or define('LOG_PATH', WIND_PATH . 'log' . DIRECTORY_SEPARATOR);

defined('LOG_DISPLAY_TYPE') or define('LOG_DISPLAY_TYPE', 'log');

//defined('LOG_RECORD') or define('LOG_RECORD', true);


//defined('DEBUG') or define('DEBUG', true);


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
	
	static $_namespace = '';
	static $_apps = array();
	
	/**
	 * ��ʼ�����������
	 * 1. ���Լ��ؿ�ܱ���Ļ������
	 */
	static public function init() {
		self::_initConfig();
		self::_initBaseLib();
		self::_initLog();
	}
	
	/**
	 * ����Ӧ�÷���·��, Ĭ��Ӧ�� $name ����Ϊdefault
	 * @param string $name
	 * @param string $path
	 */
	static public function setApps($name, $path) {
		self::$_apps[$name] = $path;
	}
	
	/**
	 * ���ϵͳ���ö���
	 * @return multitype:
	 */
	static public function getSystemConfig() {
		return self::getInstance('WSystemConfig');
	}
	
	/**
	 * ����ļ��ľ���·��
	 * @param string $path
	 */
	static public function getRealPath($path = '', $ext = '', $info = false) {
		if (file_exists($path))
			return $path;
		
		self::_setNamespace($path);
		
		$realPath = self::getApplicationRootPath() . self::getSeparator() . $path;
		$realPath = str_replace(IMPORT_SEPARATOR, self::getSeparator(), $realPath);
		if ($ext && file_exists($realPath . '.' . $ext))
			$realPath .= '.' . $ext;
		elseif (!is_dir($realPath) && !$ext) {
			foreach ((array) self::getExtendNames() as $key => $value) {
				if (file_exists($realPath . '.' . $value)) {
					$realPath .= '.' . $value;
					break;
				}
			}
		}
		if ($info) {
			if (!file_exists($realPath))
				throw new WException('The file path ' . $realPath . ' is not a file.');
			return array(basename($realPath, $value), $value, $realPath);
		}
		return realpath($realPath);
	}
	
	/**
	 * ����ȫ��import����
	 * @param string $name
	 * @return multitype:
	 */
	static public function getVar($name) {
		return self::$_vars[$name];
	}
	
	/**
	 * ����ȫ��import����
	 * @param string $name
	 * @param array|string|obj $value
	 */
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
	 * ����Ӧ�����Ʒ���Ӧ�õĸ�·����Ϊ�յ�����·��ص�ǰӦ��
	 * @param string $app
	 * @return tring
	 */
	static public function getApplicationRootPath() {
		return (self::$_namespace && self::$_apps[self::$_namespace]) ? self::$_apps[self::$_namespace] : self::$_apps['default'];
	}
	
	/**
	 * ���ϵͳ�����ļ�·����Ϣ
	 * @return string
	 */
	static public function getSystemConfigPath() {
		return self::getRealPath(SYSTEM_CONFIG_PATH);
	}
	
	/**
	 * ����ļ�·���ָ���
	 * @return string
	 */
	static public function getSeparator() {
		return DIRECTORY_SEPARATOR;
	}
	
	/**
	 * ���֧�ּ��ص���չ������
	 * �ж���չ���Ƿ�֧��
	 * @param string $ext
	 * @return boolean|multitype:string 
	 */
	static public function getExtendNames($ext = '') {
		$exts = array('php', 'htm', 'class.php', 'db.php', 'phpx');
		return $ext ? $exts[$ext] : $exts;
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
	
	static private function _setNamespace(&$filePath) {
		if (($pos = strpos($filePath, IMPORT_NAMESPACE)) !== false) {
			self::$_namespace = (string) substr($filePath, 0, $pos);
			$filePath = (string) substr($filePath, $pos + 1);
		} else
			self::$_namespace = '';
		return $filePath;
	}
	
	/**
	 * ·����������
	 * ����һ��·�������������
	 * array('�ǲ���һ���ļ���','�ļ�·��','�ļ���','Ӧ�������ռ�')
	 * @param string $filePath
	 * @return array
	 */
	static public function parseFilePath($filePath) {
		/*$isDir = $realPath = $fileName = $nameSpace = '';
		if (!is_dir($filePath) ||!file_exists($filePath)) {
		}
		
		if (($pos = strrpos($filePath, '.')) === false) {
			self::_include($filePath);
			return;
		}
		
		$className = (string) substr($filePath, $pos + 1);
		$filePath = (string) substr($filePath, 0, $pos);
		//self::_setNamespace($filePath);
		$isPackage = $className === IMPORT_PACKAGE;
		return array();*/
	}
	
	/**
	 * ����һ������߼���һ����
	 * �Կ��·��Ϊ��·�����м���
	 * ����һ����Ĳ�����ʽ��'core.WFrontController'
	 * ����һ�����Ĳ�����ʽ��'core.*'
	 *
	 * ������ص����Ǽ̳����������� WContext
	 * ��ô�������ͬʱ�����ɸ���ľ�̬��������
	 * �û�����ͨ��getInstance()������øö���
	 *
	 * @param string $classPath
	 * @param boolean $instance
	 * @author Qiong Wu
	 * @return void
	 */
	static public function import($filePath) {
		if (!isset($filePath))
			throw new Exception('is not right path');
		
		if (file_exists($filePath)) {
			self::_include($filePath);
			return;
		}
		
		if (($pos = strrpos($filePath, '.')) === false) {
			self::_include($filePath);
			return;
		}
		
		$className = (string) substr($filePath, $pos + 1);
		$filePath = (string) substr($filePath, 0, $pos);
		//self::_setNamespace($filePath);
		$isPackage = $className === IMPORT_PACKAGE;
		$classNames = array();
		if ($isPackage) {
			$dir = self::getRealPath($filePath);
			if (!is_dir($dir))
				throw new Exception('the file path ' . $dir . ' is not exists!!');
			
			if (!$dh = opendir($dir))
				throw new Exception('the file ' . $dir . ' open failed!');
			
			while (($file = readdir($dh)) !== false) {
				if ($file != "." && $file != ".." && !(is_dir($dir . self::getSeparator() . $file))) {
					$pos = strrpos($file, '.');
					if (($pos = strrpos($file, '.')) !== false)
						$classNames[] = substr($file, 0, $pos);
				}
			}
			closedir($dh);
		} else
			$classNames[] = $className;
		
		foreach ($classNames as $className) {
			self::_include($className, $filePath);
		}
		return;
	}
	
	/**
	 * ȫ�ְ����ļ���Ψһ���
	 * @param string $className ������/�ļ���
	 * @param string $classPath ��·��/�ļ�·��
	 * @return string
	 */
	static private function _include($fileName, $filePath = '') {
		if (empty($fileName)) {return;}
		if ($filePath)
			$realPath = self::getRealPath($filePath . IMPORT_SEPARATOR . $fileName);
		else
			$realPath = self::getRealPath($fileName);
		
		if (!file_exists($realPath))
			throw new Exception('file ' . $realPath . ' is not exists');
		
		if (key_exists($fileName, self::$_included))
			return $realPath;
		
		if (!is_dir($realPath) && $realPath)
			include $realPath;
		
		$var = get_defined_vars();
		if (count($var) > 3)
			self::$_vars += array_splice($var, 3);
		
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
		$object = call_user_func_array(array($class, 'newInstance'), $args);
		self::$_instances[$className] = & $object;
	}
	
	/**
	 * �Զ����ؿ�ܵײ����
	 * ���������ĳ�����ͽӿ�
	 */
	static private function _initBaseLib() {
		/* ���ļ��� */
		W::import('WIND:core.base.impl.*');
		W::import('WIND:core.base.*');
		W::import('WIND:core.*');
		
		/* ������� */
		W::import('WIND:components.exception.base.impl.*');
		W::import('WIND:components.exception.base.*');
		W::import('WIND:components.exception.*');
		W::import('WIND:components.form.base.impl.*');
		W::import('WIND:components.form.base.*');
		W::import('WIND:components.form.*');
		W::import('WIND:components.filter.base.impl.*');
		W::import('WIND:components.filter.base.*');
		W::import('WIND:components.filter.*');
		W::import('WIND:components.log.base.impl.*');
		W::import('WIND:components.log.base.*');
		W::import('WIND:components.log.*');
		W::import('WIND:components.request.base.impl.*');
		W::import('WIND:components.request.base.*');
		W::import('WIND:components.request.*');
		W::import('WIND:components.response.base.impl.*');
		W::import('WIND:components.response.base.*');
		W::import('WIND:components.response.*');
		W::import('WIND:components.router.base.impl.*');
		W::import('WIND:components.router.base.*');
		W::import('WIND:components.router.*');
		W::import('WIND:components.viewer.base.impl.*');
		W::import('WIND:components.viewer.base.*');
		W::import('WIND:components.viewer.*');
	}
	
	/**
	 * ���������ļ�
	 */
	static private function _initConfig() {
		self::setApps('WIND', self::getFrameWorkPath());
	}
	
	/**
	 * ��ʼ��ϵͳ��־������ϵͳ
	 */
	static private function _initLog() {
		set_exception_handler(array('W', 'WExceptionHandler'));
		defined('LOG_RECORD') && W::import('utility.WLog');
		defined('DEBUG') && W::import('utility.WDebug');
	}
	
	/**
	 * �쳣�����Լ�������Ϣ��¼����־
	 * @param $message
	 * @param $trace
	 */
	static public function recordLog($message, $type = 'INFO', $ifrecord = 'add') {
		//TODO �ع�
		if (defined('LOG_RECORD')) {
			$message = str_replace('<br/>', "\r\n", $message);
			$ifrecord == 'add' ? WLog::add($message, strtoupper($type)) : WLog::log($message, strtoupper($type));
		}
	}
	
	/**
	 * ���������Ϣ�Ƿ�debug����
	 * @param $message
	 * @param $trace
	 */
	static public function debug($message, $trace = array()) {
		//TODO �ع�
		return defined('DEBUG') ? WDebug::debug($message, $trace) : $message;
	}
	
	static public function WExceptionHandler($e) {
		$trace = is_a($e, 'WException') ? $e->getStackTrace() : $e->getTrace();
		$message = W::debug("{$e}", $trace);
		W::recordLog($message, 'TRACE', 'log');
		die($message);
	}
}

W::init();
