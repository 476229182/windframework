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
	/**
	 * ���������---�������������
	 * @var  array $filters
	 */
	private $filters = array();
	/**
	 * ���������������Ϣ---������������Ϣ����
	 * Ԫ�ر�������Ϊ: 'filter' => array('path' => 'path', 'rule' => 'rule')
	 * @var  array $filterChain
	 */
	private $filterChain = array();
	
	/**
	 * ���캯����
	 * ������ʼ��������Ϣ�����ҽ���������Ϣ����
	 * @param WSystemConfig $configObj   ���ö���ʵ��
	 * @param WRouter $router   �������·��ʵ��
	 */
	public function __construct($configObj, $router = null) {
		$this->init($configObj, $router);
	}
	
	/**
	 * ����·��������Ϣ
	 * @param WSystemConfig $configObj  ������Ϣ����
	 * @param WRouter $router    �������·��ʵ��
	 */
	public function init($configObj, $router) {
		$filterConfig = $configObj->getFilterChainConfig();//��ù�������Ϣ--���й��˹���
		$action = $router->getAction();
		foreach ((array) $filterConfig as $key => $value) {
			$path = $configObj->getFiltersConfig($key);
			//TODO �������
			if ($path)
				$this->filterChain[$key] = array(
					'name' => $key,
					'path' => $path, 
					'rule' => $value
				);
		}
	}
	
	/**
	 * ������������һ��������
	 * ����ù��������� WFilter��ʵ�����󣨸ù�������ʵ��û�м̳�WFilter���ࣩ�����׳�һ���쳣
	 * @access private
	 * @param WFilter $filter ��ӵľ��������ʵ������
	 */
	private function addFilter($filter) {
		if ($filter instanceof WFilter) 
			$this->filters[get_class($filter)] = $filter;
		else
			throw new WException('This is not a WFilter object!!!');
	}
	
	/**
	 * ��ù�����ʵ��
	 * @param string $filter  ��Ҫ��ȡ�Ĺ���������
	 */
	public function getFilter($filter) {
		if (!isset($this->filters[$filter])) {
			W::import($this->filterChain[$filter]['path']);
			$this->addFilter(new $filter());
		}
		return $this->filters[$filter];
	}
	
	/**
	 * �������ĵ������
	 * ���ȵ��õ��ǹ����������б����صĹ�������ǰ�ò�����Ȼ����ûص������������ù������ĺ��ò���
	 * @param array $callBack array('className', 'action')
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
		foreach ($this->filterChain as $filter => $info) {
			$this->getFilter($filter)->doPreProcessing($httpRequest);
		}
	}
	
	/**
	 * ִ�й������еĺ��ò���
	 */
	public function doPostProcessing($httpRequest) {
		$count = count($this->filterChain);
		for ($i = $count; $i > 0; $i--) {
			$filter = array_pop($this->filterChain);
			$this->getFilter($filter['name'])->doPostProcessing($httpRequest);
		}
	}
	
	/**
	 * �����ص�����
	 * @param array $callBack
	 * @return array array('className', 'action')
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
			//throw new WException($className . '������', 100);
		}
		if ($action && !is_callable(array(
			$className, 
			$action
		))) {
			echo $className . '��,������' . $action . '<br/>';
			//throw new WException($className . '��,������' . $action, 100);
		}
		return $callBack;
	}
	
	/**
	 * ��չ�����������������������
	 * @access private
	 */
	private function destory() {
		$this->filterChain = array();
		$this->filters = array();
	}

}