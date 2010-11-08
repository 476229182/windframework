<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-4
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * ��������ʵ��
 * 
 * ��������ʵ�֣�ͨ��addFilter��̬��������ӹ�������doFilter�Ǹù���������ڵ�ַ��doPreProcessing��ִ�й�����
 * �е�����ǰ�ò�����doPostProcessing��ִ�й����������к��ò�����
 * ��doFilter����Ҫ�û����ûص�����$callBack���ûص�������������ʽ���£�
 * array('$controller', '$action')����ʽ��
 * $controller�Ǹ�$action���ڵĿ���������
 * $action �Ǿ����û���Ҫ�Ĳ�����
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WFilterChain extends WFilter {
	private $filters = array();
	
	private $filterChain = array();
	
	/**
	 * @param WSystemConfig $configObj
	 * @param WRouter $router
	 */
	public function __construct($configObj, $router = null) {
		$this->init($configObj, $router);
	}
	
	/**
	 * @param WSystemConfig $configObj
	 * @param WRouter $router
	 */
	public function init($configObj, $router) {
		$filterConfig = $configObj->getFilterChainConfig();
		$action = $router->getAction();
		foreach ((array) $filterConfig as $key => $value) {
			$path = $configObj->getFiltersConfig($key);
			//TODO �������
			if ($path)
				$filterChain[$key] = array(
					'path' => $path, 
					'rule' => $value
				);
		}
	}
	
	/**
	 * ������������һ��������
	 * @param WFilter $filter
	 */
	private function addFilter($filter) {
		if ($filter instanceof WFilter)
			$this->filters[] = $filter;
		else
			throw new WException('this is not a filter object!!!');
	}
	
	public function getFilter($filter) {
		if (!$this->filters[$filter]) {
			W::import($this->filterChain[$filter]['path']);
			$this->addFilter(new $filter());
		}
		return $this->filters[$filter];
	}
	
	/**
	 * �������ĵ������
	 * @param array $callBack | array('className', 'action')
	 * @param WHttpRequest $httpRequest
	 */
	public function doFilter($callBack, $httpRequest) {
		$this->doPreProcessing($httpRequest);
		$callBack = $this->parseCallBack($callBack);
		call_user_func_array($callBack, array(
			$httpRequest
		));
		$this->doPostProcessing($httpRequest);
		$this->destory();
	}
	
	/**
	 * ִ�й������е�Ԥ����
	 */
	public function doPreProcessing($httpRequest) {
		foreach ($this->filterChain as $filter) {
			$this->getFilter($filter)->doPreProcessing($httpRequest);
		}
	}
	
	/**
	 * ִ�й������еĺ��ò���
	 */
	public function doPostProcessing($httpRequest) {
		$count = count($this->filterChain);
		for ($i = $count; $i > 0; $i--) {
			$this->getFilter($this->filterChain[$i - 1])->doPostProcessing($httpRequest);
		}
	}
	
	/**
	 * �����ص�����
	 * @param array $callBack
	 * @return array
	 */
	private function parseCallBack($callBack) {
		if (!is_array($callBack))
			return array(
				'FrontController', 
				'excute'
			);
		list($className, $action) = $callBack;
		if (!class_exists($className, true)) {
			echo $className . '������<br/>';
			//throw new ClassNoExitsException($className . '������', 100);
		//return false;
		}
		if ($action && !is_callable(array(
			$className, 
			$action
		))) {
			echo $className . '��,������' . $action . '<br/>';
			//throw new NoOperatorExitsException($className . '��,������' . $action, 100);
		//return false;
		}
		return $callBack;
	}
	
	private function destory() {
		$this->filterChain = array();
		$this->filters = array();
	}

}