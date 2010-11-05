<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-4
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
WBasic::import('filter.WFilter');
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
class WFilterChain extends WFilter{
	/**
	 * ���������
	 * @var array
	 */
	private $filterChain = array();
	
	/**
	 * ����������й������ĸ���
	 * @var integer
	 */
	private $count = 0;
	
	/**
	 * ������
	 */
	public function __construct() {
		$this->filterChain =  array();
		$this->count = 0;
	}
	/**
	 * ������������һ��������
	 * @param WFilter $filter
	 */
	public function addFilter($filter) {
		if ($filter instanceof WFilter) {
			$this->filterChain[] = $filter;
			$this->count ++;
		} else {
			echo $filter . '������Ч�Ĺ�����!�����Ƿ�̳��˻���Filter!<br/>';
		}
	}
	
	/**
	 * �������ĵ������
	 * @param array $callBack | array('className', 'action')
	 * @param WHttpRequest $httpRequest
	 */
	public function doFilter($callBack, $httpRequest) {
		$this->doPreProcessing($httpRequest);
		$callBack = $this->parseCallBack($callBack);
		call_user_func_array($callBack, array($httpRequest));
		$this->doPostProcessing($httpRequest);
	}
	
	/**
	 * ִ�й������е�Ԥ����
	 */
	public function doPreProcessing($httpRequest) {
		foreach ($this->filterChain as $filter) {
			$filter->doPreProcessing($httpRequest);	
		}
	}
	
	/**
	 * ִ�й������еĺ��ò���
	 */
	public function doPostProcessing($httpRequest) {
		for ($i = 0; $i < $this->count; $i++) {
			$filter = array_pop($this->filterChain);
			$filter->doPostProcessing($httpRequest);
		}
	}
	
	/**
	 * �����ص�����
	 * @param array $callBack
	 * @return array
	 */
	private function parseCallBack($callBack) {
		if (!is_array($callBack)) return array('FrontController', 'excute');
		list($className, $action) = $callBack;
		if (!class_exists($className, true)) {
			echo $className . '������<br/>';
			//throw new ClassNoExitsException($className . '������', 100);
			//return false;
		}
		if ($action && !is_callable(array($className,	$action))) {
			echo $className . '��,������' . $action .'<br/>';
			//throw new NoOperatorExitsException($className . '��,������' . $action, 100);
			//return false;
		}
		return $callBack;
	}
	
	public function  __destruct(){
		$this->filterChain = NULL;
		$this->count = NULL;
		unset($this->filterChain);
		unset($this->count);
	}
}