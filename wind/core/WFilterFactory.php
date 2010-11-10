<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WFilterFactory extends WFactory {
	private $index = 0;
	private $filters = array();
	private $configs = array();
	private $state = false;
	
	private $callBack = null;
	private $args = array();
	
	static private $instance = null;
	
	/**
	 * ����һ��Filter
	 * @param WSystemConfig $config
	 * @return WFilter
	 */
	function create($config = null) {
		if ($config != null && empty($this->filters))
			$this->_initFilters($config);
		return $this->createFilter();
	}
	
	function createFilter() {
		if ((int) $this->index >= count($this->filters)) {
			$this->state = true;
			return null;
		}
		list($filterName, $path) = $this->filters[$this->index++];
		W::import($path);
		if ($filterName && class_exists($filterName) && in_array('WFilter', class_parents($filterName))) {
			$class = new ReflectionClass($filterName);
			$object = $class->newInstance();
			return $object;
		}
		$this->createFilter();
	}
	
	/**
	 * ִ�����������ִ�и÷����Ļص�
	 */
	public function execute() {
		if ($this->callBack === null)
			$this->callBack = array(
				'WFrontController', 
				'process'
			);
		if (is_array($this->callBack)) {
			list($className, $action) = $this->callBack;
			if (!class_exists($className, true))
				throw new WException($className . ' is not exists!');
			if (!in_array($action, get_class_methods($className)))
				throw new WException('method ' . $action . ' is not exists in ' . $className . '!');
		} elseif (is_string($this->callBack))
			if (!function_exists($this->callBack))
				throw new WException($this->callBack . ' is not exists!');
		
		call_user_func_array($this->callBack, (array) $this->args);
	}
	
	/**
	 * ���ûص�������ִ��������й������󽫻ص��÷���
	 * 
	 * @param array $callback
	 * @param array $args
	 */
	public function setExecute($callback) {
		$args = func_get_args();
		if (count($args) > 1) {
			unset($args[0]);
			$this->args = $args;
		}
		$this->callBack = $callback;
	}
	
	/**
	 * ��filter���ж�̬��ɾ��һ��filter
	 * @param string $filterName
	 */
	public function deleteFilter($filterName) {
		if (!in_array($filterName, $this->filters))
			return false;
		$deleteIndex = 0;
		foreach ($this->filters as $key => $value) {
			if ($value[0] == $filterName) {
				$deleteIndex = $key;
				unset($this->filters[$key]);
			}
		}
		if ($deleteIndex == $this->index)
			$this->index++;
	}
	
	/**
	 * ��filter���ж�̬�����һ��filter����beforΪ��ʱ����ӵ������β��
	 * @param string $filterName
	 * @param string $path
	 * @param string $beforFilter
	 */
	public function addFilter($filterName, $path, $beforFilter = '') {
		$addIndex = count($this->filters);
		if ($beforFilter) {
			$exchange = null;
			foreach ($this->filters as $key => $value) {
				if ($key > $addIndex)  $this->filters[$key] = $exchange;
				if ($value[0] == $beforFilter) $addIndex = $key;
				$exchange = $value;
			}
			$exchange != null && $this->filters[] = $exchange;
		}
		$this->filters[$addIndex] = array(
			$filterName, 
			$path
		);
	}
	
	/**
	 * ��õ�ǰ������״̬���Ƿ��Ѿ�����ʼ����
	 * @return string
	 */
	public function getState() {
		return $this->state;
	}
	
	/**
	 * ��ʼ��һ��������
	 * @param WSystemConfig $config
	 */
	public function initFilters($configObj) {
		$this->index = 0;
		$this->filters = array();
		$config = $configObj->getFiltersConfig();
		foreach ((array) $config as $key => $value) {
			if (($pos = strrpos($value, '.')) === false)
				$filterName = $value;
			else
				$filterName = substr($value, $pos + 1);
			$this->filters[] = array(
				$filterName, 
				$value,
				$key
			);
		}
		$this->configs = $config;
	}
	
	/**
	 * @return WFilterFactory
	 */
	static function getFactory() {
		if (self::$instance === null) {
			$class = new ReflectionClass(__CLASS__);
			$args = func_get_args();
			self::$instance = call_user_func_array(array(
				$class, 
				'newInstance'
			), (array) $args);
		}
		return self::$instance;
	}
}