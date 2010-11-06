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
	
	static $_instance_max = 0;
	static $_instance_frequence = 0;
	
	static $_system_config = 'config.php';
	
	/**
	 * ��ʼ�����������
	 * 1. ���Լ��ؿ�ܱ���Ļ������
	 * 
	 */
	static public function init($config = NULL) {
		self::_autoIncludeBaseLib();
	
	}
	
	static public function getSystemConfig() {
		return self::getInstance('WSystemConfig');
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
	 * ���һ����ľ�̬��������
	 * ȫ�ֵľ�̬�����������������ʽ������ < self::$_instances >�У�����Ϊ������
	 * �����Ʊ�����ļ�������ͬ�������׳��쳣
	 * ֧�ֹ��캯������
	 * ����һ�����������
	 * 
	 * @param string $className
	 * @retur Object
	 */
	static public function getInstance($className) {
		if (key_exists($className, self::$_instances))
			return self::$_instances[$className]['instance'];
		return NULL;
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
	 * @author Qiong Wu
	 * @return void
	 */
	static public function import($classPath) {
		$classPath = trim($classPath, ' .');
		if (!isset($classPath))
			throw new Exception(__CLASS__ . ' throw exception!!!!');
		if (($pos = strrpos($classPath, '.')) === false)
			return self::_include($classPath, $classPath);
		
		$className = (string) substr($classPath, $pos + 1);
		$isPackage = $className === '*';
		$classPath = str_replace('.', DIRECTORY_SEPARATOR, $classPath);
		$classNames = array();
		if ($isPackage) {
			$dir = (string) substr($classPath, 0, $pos);
			if (!is_dir($dir))
				return false;
			if (!$dh = opendir($dir))
				return false;
			while (($file = readdir($dh)) !== false) {
				if ($file != "." && $file != ".." && !(is_dir($dir . self::getSeparator() . $file))) {
					$pos = strrpos($file, '.');
					if ((string) substr($file, $pos + 1) == self::getExtendName()) {
						$classNames[] = (string) substr($file, 0, $pos);
					}
				}
			}
			closedir($dh);
		} else
			$classNames[] = $className;
		
		foreach ($classNames as $value) {
			self::_include($value, str_replace('*', $value, $classPath));
		}
		return true;
	}
	
	/**
	 * ȫ�ְ����ļ���Ψһ���
	 * @param string $className ������/�ļ���
	 * @param string $classPath ��·��/�ļ�·��
	 * @return boolean
	 */
	static private function _include($className, $classPath) {
		$path = self::getFrameWorkPath() . self::getSeparator() . $classPath . '.' . self::getExtendName();
		if (!file_exists($path))
			return false;
		if (key_exists($className, self::$_included))
			return true;
		include $path;
		self::$_included[$className] = $classPath;
		self::_autoInstance($className);
		return true;
	}
	
	/**
	 * �Զ�����ʵ����
	 * ��import������������Զ�ʵ�������Զ�ʵ�����������̳���WContext�ӿ�
	 * �Զ�ʵ����ʱ�Զ������丸��
	 * @param string $className
	 * @return void
	 */
	static private function _autoInstance($className) {
		if (in_array('WContext', (array) class_implements($className, true)))
			self::getInstance($className);
	}
	
	/**
	 * ���������ƴ�����ĵ������󣬲����浽��̬������
	 * ͬʱ��������������Ĳ���
	 * 
	 * @param string $className ������
	 * @return void|string
	 */
	static private function _createInstance($className) {
		if (key_exists($className, self::$_instances))
			return;
		$class = new ReflectionClass($className);
		if ($class->isAbstract() || $class->isInterface())
			return false;
		$args = func_get_args();
		unset($args[0]);
		$object = call_user_func_array(array(
			$class, 
			'newInstance'
		), $args);
		self::$_instances[$className]['instance'] = & $object;
		if (self::$_instance_frequence)
			self::_cleanInstanceByFrequence($className);
		if (self::$_instance_max)
			self::_cleanInstancesByMax();
	}
	
	/**
	 * ȫ�־�̬����ز��� - ���ݴ洢����������
	 * @return string
	 */
	static private function _cleanInstancesByMax() {
		if (!self::$_instance_max)
			return false;
		$max = intval(self::$_instance_max);
		if (count(self::$_instances) > ($max + 10)) {
			self::$_instances = array_slice(self::$_instances, -$max, $max);
		}
	}
	
	/**
	 * ȫ�־�̬����ز��� - ����ʹ��Ƶ�������ʹ��Ƶ�ʽϵ͵�ֵ
	 * @param string $key
	 * @return string
	 */
	static private function _cleanInstanceByFrequence($key) {
		if (!self::$_instance_frequence)
			return false;
		if (!isset(self::$_instances[$key]['frequence']))
			self::$_instances[$key]['frequence'] = self::$_instance_frequence;
		foreach (self::$_instances as $k => $v) {
			if ($key == $k)
				continue;
			if (intval(self::$_instances[$k]['frequence']) < 1) {
				unset(self::$_instances[$k]);
			} else
				self::$_instances[$k]['frequence']--;
		}
	}
	
	/**
	 * �Զ����ؿ�ܵײ����
	 * ���������ĳ�����ͽӿ�
	 */
	static private function _autoIncludeBaseLib() {
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
		if (($pos = strpos($systemConfig, '.')) !== false)
			$systemConfig = substr($systemConfig, 0, $pos);
		if (!file_exists($systemConfigPath . self::getSeparator() . $systemConfig))
			throw new Exception('SYS Excetion �������ļ�������!!!');
		self::import($systemConfigPath . self::getSeparator() . $systemConfig);
		self::getInstance('WSystemConfig')->parse($systemConfig, $config);
	
	}

}

/*
 * ��ʼ�����������
 * 
 * */
W::init($sysConfig);
