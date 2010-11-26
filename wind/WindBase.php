<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */
//error_reporting(E_ERROR | E_PARSE);


/* ·�����������Ϣ  */
!defined('D_S') && define('D_S', DIRECTORY_SEPARATOR);
!defined('WIND_PATH') && define('WIND_PATH', dirname(__FILE__) . D_S);
!defined('COMPILE_PATH') && define('COMPILE_PATH', WIND_PATH . 'compile' . D_S);

!defined('VERSION') && define('VERSION', '1.0.2');

!defined('RUNTIME_START') && define('RUNTIME_START', microtime(true));
!defined('USEMEM_START') && define('USEMEM_START', memory_get_usage());
!defined('LOG_PATH') && define('LOG_PATH', WIND_PATH . 'log' . D_S);
!defined('LOG_DISPLAY_TYPE') && define('LOG_DISPLAY_TYPE', 'log');

!defined('IS_DEBUG') && define('IS_DEBUG', true);

/*define('LOG_RECORD', true);
define('DEBUG', true);*/

/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 */
class W {
	private static $_apps = array();
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
		if ($name && isset(W::$_apps[$name]))
			return W::$_apps[$name];
		elseif (W::$_current && isset(W::$_apps[W::$_current]))
			return W::$_apps[W::$_current];
		else
			return '';
	}
	
	/**
	 * @param string $name
	 * @param array $value
	 * @param boolean $default
	 */
	static public function setApps($name = '', $value = array(), $current = false) {
		if (empty($value)) return;
		W::$_apps[$name] = $value;
		if ($current) self::$_current = $name;
		L::register($name, $value['rootPath']);
	}
	
	static public function setCurrentApp($name) {
		if ($name) self::$_current = $name;
	}
	
	/**
	 * ��õ�ǰӦ������
	 * @return string $name
	 */
	static public function getCurrentApp() {
		return self::$_current;
	}
	/**
	 * �Զ����ؿ�ܵײ����
	 * ���������ĳ�����ͽӿ�
	 */
	static private function _initBaseLib() {
		if (false === self::_initLoad()) {
			L::import('WIND:core.base.*');
			L::import('WIND:core.*');
		}
	}
	
	/**
	 * �Զ�����
	 */
	static private function _initLoad() {
		if (self::ifCompile() && !IS_DEBUG) {
			$packfile = COMPILE_PATH . 'preload_' . VERSION . '.php';
			if (!is_file($packfile)) {
				L::import('WIND:utility.WindPack');
				$pack = L::getInstance('WindPack');
				$pack->packCompress(array(WIND_PATH.'core'), $packfile);
			}
			if (is_file($packfile)) {
				@include $packfile;
				return true;
			}
		}
		return false;
	}
	
	/**
	 * �Ƿ�֧��Ԥ����
	 * @return string
	 */
	static public function ifCompile() {
		return defined('COMPILE_PATH') && is_writable(COMPILE_PATH) ? true : false;
	}
	
	/**
	 * ���������ļ�
	 */
	static private function _initConfig() {
		W::setApps('WIND', array('name' => 'WIND', 'rootPath' => WIND_PATH));
		if (!is_file(COMPILE_PATH . '/config.php')) return false;
		$sysConfig = @include COMPILE_PATH . '/config.php';
		foreach ($sysConfig as $appName => $appConfig) {
			W::setApps($appName, $appConfig);
		}
	}
	
	/**
	 * ��ʼ��ϵͳ��־������ϵͳ
	 */
	static private function _initLog() {
		set_exception_handler(array('W', 'WExceptionHandler'));
		defined('LOG_RECORD') && L::import('utility.WLog');
		defined('DEBUG') && L::import('utility.WDebug');
	}
	
	/**
	 * �쳣�����Լ�������Ϣ��¼����־
	 * @param $message
	 * @param $trace
	 */
	static public function recordLog($message, $type = 'INFO', $ifrecord = 'add') { //TODO �ع�
		if (defined('LOG_RECORD')) {
			$message = str_replace('<br/>', "\r\n", $message);
			$ifrecord == 'add' ? WindLog::add($message, strtoupper($type)) : WindLog::log($message, strtoupper($type));
		}
	}
	
	/**
	 * ���������Ϣ�Ƿ�debug����
	 * @param $message
	 * @param $trace
	 */
	static public function debug($message, $trace = array()) {
		//TODO �ع�
		return defined('DEBUG') ? WindDebug::debug($message, $trace) : $message;
	}
	
	static public function WExceptionHandler($e) {
		$trace = in_array('WindException',class_parents($e)) ? $e->getStackTrace() : $e->getTrace();
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
	
	static public function setImports($class = array()) {
		foreach ($class as $key => $value) {
			self::$_imports[$key] = isset(self::$_imports[$key]) ? self::$_imports[$key] : $value;
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
		if ($namespace && isset(L::$_namespace[$namespace])) {
			return L::$_namespace[$namespace];
		} else {
			return W::getCurrentApp() ? L::$_namespace[W::getCurrentApp()] : '';
		}
	}
}

/**
 * ȫ�����÷���
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class C {
	private static $config = array();
	private static $c;
	/**
	 * ��ʼ�������ļ�����
	 * @param array $configSystem
	 */
	static public function init($configSystem) {
		if (empty($configSystem)) {
			throw new Exception('system config file is not exists.');
		}
		self::$config = $configSystem;
		self::$c = new C();
	}
	
	/**
	 * ����������ȡ����Ӧ������
	 * @param string $configName
	 * @param string $subConfigName
	 * @return string
	 */
	static public function getConfig($configName = '', $subConfigName = '') {
		if (!$configName) return self::$config;
		$_config = array();
		if (isset(self::$config[$configName])) {
			$_config = self::$config[$configName];
		}
		if (!$subConfigName) return $_config;
		
		$_subConfig = array();
		if (is_array($_config) && isset($_config[$subConfigName])) {
			$_subConfig = $_config[$subConfigName];
		}
		return $_subConfig;
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	static public function getModules($name = '') {
		return self::getConfig(IWindConfig::MODULES, $name);
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	static public function getTemplate($name = '') {
		return self::getConfig(IWindConfig::TEMPLATE, $name);
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	static public function getFilters($name = '') {
		return self::getConfig(IWindConfig::FILTERS, $name);
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	static public function getViewerResolvers($name = '') {
		return self::getConfig(IWindConfig::VIEWER_RESOLVERS, $name);
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	static public function getRouter($name = '') {
		return self::getConfig(IWindConfig::ROUTER, $name);
	}
	
	/**
	 * @param string $name
	 * @return array|string
	 */
	static public function getRouterParsers($name = '') {
		return self::getConfig(IWindConfig::ROUTER_PARSERS, $name);
	}
}

W::init();