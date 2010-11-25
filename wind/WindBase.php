<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */
//error_reporting(E_ERROR | E_PARSE);


/* ·�����������Ϣ  */
define('D_S', DIRECTORY_SEPARATOR);
define('WIND_PATH', dirname(__FILE__) . D_S);
define('COMPILE_PATH', WIND_PATH . 'compile' . D_S);

define('PRELOAD_FILE','WindPreload.php');
define('RUNTIME_START', microtime(true));
define('USEMEM_START', memory_get_usage());
define('LOG_PATH', WIND_PATH . 'log' . D_S);
define('LOG_DISPLAY_TYPE', 'log');

/*define('LOG_RECORD', true);
define('DEBUG', true);*/

/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 */
class W {
	private static $_apps = array();
	private static $_default = '';
	private static $_current = '';
	private static $_systemConfig = null;
	
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
	 * ����ϵͳ������Ϣ
	 *
	 * @return array
	 */
	static public function getSystemConfig() {
		if (W::$_systemConfig === null) {
			$cahceConfig = CONFIG_CACHE_PATH . '/config.php';
			if (!file_exists($cahceConfig)) {
				throw new Exception('System config file ' . $cahceConfig . ' is not exists!');
			}
			@include $cahceConfig;
			$vars = get_defined_vars();
			W::$_systemConfig = (array) array_pop($vars);
		}
		return W::$_systemConfig;
	}
	
	/**
	 * ���Ӧ�����������Ϣ
	 *
	 * @param string $name
	 * @return array
	 */
	static public function getApps($name = '') {
		return $name ? W::$_apps[$name] : W::$_apps[W::$_current];
	}
	
	/**
	 * @param string $name
	 * @param array $value
	 * @param boolean $default
	 */
	static public function setApps($name = '', $value = array(), $default = false) {
		W::$_apps[$name] = $value;
		if ($default) W::$_default = $name;
		L::register($name, $value['rootPath']);
	}
	
	/**
	 * ���������ļ�
	 */
	static public function parserConfig($appConfig = array()) {
		if ($appConfig) {
			$currentApp = $appConfig[IWindConfig::APP];
			W::setApps($currentApp[IWindConfig::APP_NAME], $currentApp[IWindConfig::APP_ROOTPATH]);
			W::setCurrentApp($currentApp[IWindConfig::APP_NAME]);
		}
		$cahceConfig = COMPILE_PATH . '/config.php';
		if (!is_file($cahceConfig)) return false;
		@include $cahceConfig;
		foreach ($sysConfig as $appName => $appConfig) {
			W::setApps($appName, $appConfig);
		}
	}
	
	/**
	 * ���õ�ǰӦ������
	 * @param string $name
	 */
	static public function setCurrentApp($name) {
		if (!$name) throw new WindException('Please set a exists AppName!');
		W::$_current = $name;
	}
	/**
	 * ��õ�ǰӦ������
	 * @return string $name
	 */
	static public function getCurrentApp() {
		return W::$_current;
	
	}
	/**
	 * �Զ����ؿ�ܵײ����
	 * ���������ĳ�����ͽӿ�
	 */
	static private function _initBaseLib() {
		/* ���ļ��� */
		L::import('WIND:core.base.*');
		L::import('WIND:core.*');
		
		L::import('WIND:component.exception.base.*');
		L::import('WIND:component.exception.WindException');
	}
	
	/**
	 * ���������ļ�
	 */
	static private function _initConfig() {
		W::setApps('WIND', array('name' => 'WIND', 'rootPath' => WIND_PATH));
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
		$trace = is_a($e, 'WindException') ? $e->getStackTrace() : $e->getTrace();
		$message = W::debug("{$e}", $trace);
		W::recordLog($message, 'TRACE', 'log');
		die($message);
	}
}

/**
 * �ļ�������
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package
 */
class L {
	private static $_namespace = array();
	private static $_imports = array();
	private static $_instances = array();
	private static $_extensions = array('php', 'htm', 'class.php', 'db.php', 'phpx');
	
	static public function getImports($key = '') {
		return $key ? L::$_imports[$key] : L::$_imports;
	}
	
	/**
	 * ��·����Ϣע�ᵽ�����ռ�
	 *
	 * @param string $name
	 * @param string $path
	 */
	static public function register($name, $path) {
		if (!isset(L::$_namespace[$name])) {
			L::$_namespace[$name] = $path;
		}
	}
	
	/**
	 * ����һ������߼���һ����
	 * ������صİ��������ļ��в�����ѭ������
	 * ������ʽ˵����'WIND:core.base.WFrontController'
	 * WIND ע���Ӧ�����ƣ�Ӧ��������·����Ϣ�á�:���ŷָ�
	 * core.base.WFrontController ��Ե�·����Ϣ
	 * �������дӦ������ �����确core.base.WFrontController������ô����·���������Ĭ�ϵ�Ӧ��·��
	 *
	 * ����һ����Ĳ�����ʽ��'WIND:core.base.WFrontController'
	 * ����һ�����Ĳ�����ʽ��'WIND:core.base.*'
	 *
	 * @param string $filePath //�ļ�·����Ϣ
	 * @author Qiong Wu
	 * @return
	 */
	static public function import($filePath) {
		if (!$filePath) return null;
		if (is_file($filePath)) {
			L::_include($filePath);
			return $filePath;
		}
		list($isPackage, $fileName, $ext, $realPath) = self::getRealPath($filePath, true);
		$fileNames = array();
		if (!$isPackage) {
			L::_include($realPath, $fileName);
			return $realPath;
		}
		if (!$dh = opendir($realPath)) throw new Exception('the file ' . $realPath . ' open failed!');
		while (($file = readdir($dh)) !== false) {
			if ($file != "." && $file != ".." && !(is_dir($realPath . D_S . $file))) {
				if (($pos = strrpos($file, '.')) === false) $pos = strlen($file);
				$fileNames[] = array(substr($file, 0, $pos), substr($file, $pos + 1));
			}
		}
		closedir($dh);
		foreach ($fileNames as $var) {
			L::_include($realPath . D_S . $var[0] . '.' . $var[1], $var[0]);
		}
		return $realPath;
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
	static public function &getInstance($className, $args = array()) {
		if (!key_exists($className, L::$_instances)) L::_createInstance($className, $args);
		return L::$_instances[$className];
	}
	
	/**
	 * ����·����Ϣ��������·��������
	 * ����array('isPackage','fileName','extension','realPath')
	 * @param string $filePath ·����Ϣ
	 * @param boolean $info �Ƿ񷵻�·������
	 * @param string $ext ��չ��,��������ֵ�����Զ����������չ���б���ƥ��
	 * @return string|array
	 */
	static public function getRealPath($filePath, $info = false, $ext = '', $dir = '') {
		$isPackage = false;
		$fileName = $namespace = '';
		if (is_dir($filePath)) {
			if (!$info) return realpath($filePath);
			$isPackage = true;
		} elseif (is_file($filePath)) {
			if (!$info) return realpath($filePath);
			$pathinfo = pathinfo($filePath);
			$filePath = $pathinfo['dirname'];
			$ext = $pathinfo['extension'];
			$fileName = basename($filePath, '.' . $ext);
			$isPackage = false;
		} elseif (!is_file($filePath) && !is_dir($filePath)) {
			if (($pos = strrpos($filePath, '.')) === false) {
				$fileName = $filePath;
			} else {
				$fileName = substr($filePath, $pos + 1);
				$filePath = substr($filePath, 0, $pos);
			}
			if (($pos = strpos($filePath, ':')) !== false) {
				$namespace = substr($filePath, 0, $pos);
				$filePath = substr($filePath, $pos + 1);
			}
			if ($dir)
				$filePath = $dir . D_S . str_replace('.', D_S, $filePath);
			else
				$filePath = L::_getAppRootPath($namespace) . D_S . str_replace('.', D_S, $filePath);
			$isPackage = $fileName === '*';
			if (!$isPackage && !$ext) {
				foreach ((array) L::_getExtension() as $key => $value) {
					if (file_exists($filePath . D_S . $fileName . '.' . $value)) {
						$ext = $value;
						break;
					}
				}
			}
		}
		$realpath = !$isPackage ? $filePath . D_S . $fileName . '.' . $ext : $filePath;
		if ($info) return array($isPackage, $fileName, $ext, realpath($realpath));
		return realpath($realpath);
	}
	
	/**
	 * ���������ƴ�����ĵ������󣬲����浽��̬������
	 * ͬʱ��������������Ĳ���
	 *
	 * @param string $className ������
	 * @param array $args ��������
	 * @return void|string
	 */
	static private function _createInstance($className, $args) {
		$class = new ReflectionClass($className);
		if ($class->isAbstract() || $class->isInterface()) return;
		if (!is_array($args)) $args = array($args);
		$object = call_user_func_array(array($class, 'newInstance'), $args);
		L::$_instances[$className] = & $object;
	}
	
	/**
	 * ȫ�ְ����ļ���Ψһ���
	 *
	 * @param string $className ������/�ļ���
	 * @param string $classPath ��·��/�ļ�·��
	 * @return string
	 */
	static private function _include($realPath, $fileName = '') {
		if (empty($realPath)) return;
		if (!file_exists($realPath)) throw new Exception('file ' . $realPath . ' is not exists');
		if (key_exists($fileName, self::$_imports)) return $realPath;
		include $realPath;
		$fileName && self::$_imports[$fileName] = $realPath;
		return $realPath;
	}
	
	/**
	 * �������֧�ֵ���չ��
	 *
	 * @return array
	 */
	static private function _getExtension() {
		return L::$_extensions;
	}
	
	/**
	 * ��ø�·����Ϣ
	 * @return string
	 */
	static private function _getAppRootPath($namespace = '') {
		if ($namespace && isset(L::$_namespace[$namespace])) return L::$_namespace[$namespace];
		$rp = W::getApps();
		return $rp['rootPath'];
	}
}

if(!is_file(WIND_PATH.PRELOAD_FILE)){
	 require WIND_PATH.PRELOAD_FILE;
}else{
	L::import('WIND:utility.WindPack');
	$pack = L::getInstance('WindPack');
	$pack->pack(array('core','utility'),WIND_PATH.PRELOAD_FILE);
	require WIND_PATH.PRELOAD_FILE;
}
W::init();