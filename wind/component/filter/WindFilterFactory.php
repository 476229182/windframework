<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:utility.factory.IWindFactory');

/**
 * ����������
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package
 */
class WindFilterFactory implements IWindFactory {
	private $index = 0;
	private $filters = array();
	private $state = false;
	
	private $callBack = null;
	private $args = array();
	
	private static $instance = null;
	
	/**
	 * ����һ��Filter
	 * 
	 * @param WSystemConfig $config
	 * @return WFilter 
	 */
	public function create() {
		if (empty($this->filters)) {
			$this->_initFilters();
		}
		return $this->createFilter();
	}
	
	/**
	 * ����һ��filter
	 * 
	 * @return WFilter 
	 */
	public function createFilter() {
		if ((int) $this->index >= count($this->filters)) {
			$this->state = true;
			return null;
		}
		list($filterName, $path) = $this->filters[$this->index++];
		L::import($path);
		if ($filterName && class_exists($filterName) && in_array('WFilter', class_parents($filterName))) {
			return new $filterName();
		}
		$this->createFilter();
	}
	
	/**
	 * ִ�����������ִ�и÷����Ļص�
	 */
	public function execute() {
		if ($this->callBack === null) $this->callBack = array('WFrontController', 'process');
		if (is_array($this->callBack)) {
			list($className, $action) = $this->callBack;
			if (!class_exists($className, true)) throw new WException($className . ' is not exists!');
			if (!in_array($action, get_class_methods($className))) throw new WException('method ' . $action . ' is not exists in ' . $className . '!');
		} elseif (is_string($this->callBack))
			if (!function_exists($this->callBack)) throw new WException($this->callBack . ' is not exists!');
		
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
	 * ˼·����¼ɾ����λ�ã����Ҵӱ�ɾ����Ԫ�ؿ�ʼ�����к����Ԫ�ض���ǰ�ƣ�����֮�����һ��Ԫ��ɾ��
	 * 
	 * @param string $filterName
	 */
	public function deleteFilter($filterName) {
		$deleteIndex = -1;
		foreach ($this->filters as $key => $value) {
			if ($key > $deleteIndex && $deleteIndex >= 0) $this->filters[$key - 1] = $value;
			if ($value[0] == $filterName) $deleteIndex = $key;
		}
		if ($deleteIndex >= 0) {
			array_pop($this->filters);
		}
	}
	
	/**
	 * ��filter���ж�̬�����һ��filter
	 * ��beforΪ��ʱ����ӵ������β��
	 * ���befor��ֵ����������飬�ҵ�befor��λ�ã����µĹ�������ӵ�befor���棬
	 * ��������ԭbeforλ�ú�Ĺ�����������һλ
	 * 
	 * @param string $filterName
	 * @param string $path
	 * @param string $beforFilter
	 */
	public function addFilter($filterName, $path, $beforFilter = '') {
		$addIndex = count($this->filters);
		if ($beforFilter) {
			$exchange = null;
			foreach ($this->filters as $key => $value) {
				if ($key > $addIndex) {
					$this->filters[$key] = $exchange;
					$exchange = $value;
				}
				if ($value[0] == $beforFilter) {
					$addIndex = $key + 1;
					if (!isset($this->filters[$key + 1])) break;
					$exchange = $this->filters[$key + 1];
				}
			}
			$exchange != null && $this->filters[] = $exchange;
		}
		$this->filters[$addIndex] = array($filterName, $path);
	}
	
	/**
	 * ��õ�ǰ������״̬���Ƿ��Ѿ�����ʼ����
	 * 
	 * @return boolean 
	 */
	public function getState() {
		return $this->state;
	}
	
	/**
	 * ��ʼ��һ��������
	 * 
	 * @param WSystemConfig $config
	 */
	private function _initFilters() {
		$this->index = 0;
		$this->filters = array();
		$filters = C::getConfig('filters');
		foreach ((array) $filters as $key => $value) {
			$path = $value[IWindConfig::FILTER_NAME];
			$name = $value[IWindConfig::FILTER_PATH];
			if (($pos = strrpos($path, '.')) === false)
				$filterName = $path;
			else
				$filterName = substr($path, $pos + 1);
			$this->filters[] = array($filterName, $path, $name);
		}
	}
	
	/**
	 * ����һ������
	 * 
	 * @return WindFilterFactory
	 */
	static function getFactory() {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}
}