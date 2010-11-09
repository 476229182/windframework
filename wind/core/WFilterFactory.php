<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WFilterFactory extends WFactory {
	private static $index = 0;
	private static $filters = array();
	private static $configs = array();
	private static $state = false;
	
	private static $callBack = null;
	private static $args = array();
	
	/**
	 * ����һ��Filter
	 * @param WSystemConfig $config
	 * @return WFilter
	 */
	static function create($config = null) {
		if ($config != null && empty(self::$filters))
			self::_initFilters($config);
		return self::createFilter();
	}
	
	static function &createFilter() {
		if ((int) self::$index >= count(self::$filters)) {
			self::$state = true;
			return null;
		}
		list($filterName, $path) = self::$filters[self::$index++];
		W::import($path);
		if ($filterName && class_exists($filterName) && in_array('WFilter', class_parents($filterName))) {
			$class = new ReflectionClass($filterName);
			$object = $class->newInstance();
			return $object;
		}
		self::createFilter();
	}
	
	/**
	 * ִ�����������ִ�и÷����Ļص�
	 */
	static public function execute() {
		if (self::$callBack === null)
			self::$callBack = array(
				'WFrontController', 
				'process'
			);
		if (is_array(self::$callBack)) {
			list($className, $action) = self::$callBack;
			if (!class_exists($className, true))
				throw new WException($className . ' is not exists!');
			if (!in_array($action, get_class_methods($className)))
				throw new WException('method ' . $action . ' is not exists in ' . $className . '!');
		} elseif (is_string(self::$callBack))
			if (!function_exists(self::$callBack))
				throw new WException(self::$callBack . ' is not exists!');
				
		call_user_func_array(self::$callBack, (array) self::$args);
	}
	
	/**
	 * ���ûص�������ִ��������й������󽫻ص��÷���
	 * 
	 * @param array $callback
	 * @param array $args
	 */
	static public function setExecute($callback) {
		$args = func_get_args();
		if (count($args) > 1) {
			unset($args[0]);
			self::$args = $args;
		}
		self::$callBack = $callback;
	}
	
	/**
	 * ��filter���ж�̬��ɾ��һ��filter
	 * @param string $filterName
	 */
	static protected function deleteFilter($filterName) {
		if (!in_array($filterName, self::$filters))
			return false;
		$deleteIndex = 0;
		foreach (self::$filters as $key => $value) {
			if ($value[0] == $filterName) {
				$deleteIndex = $key;
				unset(self::$filters[$key]);
			}
		}
		if ($deleteIndex == self::$index)
			self::$index++;
	}
	
	/**
	 * ��filter���ж�̬�����һ��filter����beforΪ��ʱ����ӵ������β��
	 * @param string $filterName
	 * @param string $path
	 * @param string $beforFilter
	 */
	static protected function addFilter($filterName, $path, $beforFilter = '') {
		$addIndex = count(self::$filters);
		if ($beforFilter) {
			$exchange = null;
			foreach (self::$filters as $key => $value) {
				if ($key > $addIndex) {
					self::$filters[$key] = $exchange;
					$exchange = $value;
				}
				if ($value[0] == $beforFilter) {
					$addIndex = $key + 1;
					$exchange = self::$filters[$key + 1];
				}
			}
			$exchange != null && self::$filters[$key + 1] = $exchange;
		}
		self::$filters[$addIndex] = array(
			$filterName, 
			$path
		);
	}
	
	/**
	 * ��õ�ǰ������״̬���Ƿ��Ѿ�����ʼ����
	 * @return string
	 */
	static public function getState() {
		return self::$state;
	}
	
	/**
	 * ��ʼ��һ��������
	 * @param WSystemConfig $config
	 */
	static private function _initFilters($configObj) {
		self::$index = 0;
		self::$filters = array();
		$config = $configObj->getFiltersConfig();
		foreach ((array) $config as $key => $value) {
			self::$filters[] = array(
				$key, 
				$value
			);
		}
		self::$configs = $config;
	}

}