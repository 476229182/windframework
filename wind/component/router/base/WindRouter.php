<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * ·�ɽ������ӿ�
 * ְ��: ·�ɽ���, ����·�ɶ���
 * ʵ��·�ɽ���������ʵ�ָýӿڵ�doParser()����
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WindRouter {
	protected $routerRule = '';
	protected $routerName = '';
	protected $method = 'run';
	
	protected $action = 'run';
	protected $controller = 'index';
	protected $module = 'default';
	
	protected $modules = array();
	
	protected $modulePath = '';
	protected $actionForm = 'actionForm';
	
	public function __construct($configObj = null) {
		$this->init($configObj);
	}
	
	/**
	 * ��ʼ��·������
	 * 
	 * @param WSystemConfig $configObj
	 */
	protected function init($configObj) {
		if ($configObj === null) throw new WindException('Config object is null.');
		$this->modules = $configObj->getModulesConfig();
		$this->routerRule = $configObj->getRouterRule($this->routerName);
	}
	
	/**
	 * ͨ��ʵ�ָýӿ�ʵ��·�ɽ���
	 * 
	 * @return WRouterContext
	 */
	abstract function doParser($request, $response);
	
	/**
	 * �����������,����һ�����飬array('$className','$method')
	 * 
	 * @return array
	 */
	public function getActionHandle() {
		if (empty($this->modules)) throw new WindException('the modules is empty.');
		$module = $this->modules[$this->getModule()];
		$className = $this->getController();
		$method = $this->getAction();
		L::import($module . '.' . $className);
		if (!class_exists($className)) {
			$module .= $module . '.' . $className;
			$className = $this->getAction();
			L::import($module . '.' . $className);
			if (!class_exists($className)) return array(null, null);
			$method = $this->method;
		}
		if (!in_array($method, get_class_methods($className))) return array(null, null);
		$this->modulePath = $module;
		return array($className, $method);
	}
	
	/**
	 * �����������,����һ�����飬array('$className','$method')
	 * 
	 * @return array
	 */
	abstract public function getActionFormHandle();
	
	/**
	 * �����������,����һ�����飬array('$className','$method')
	 * 
	 * @return array
	 */
	abstract public function getDefaultViewHandle();
	
	/**
	 * ���ҵ�����
	 */
	public function getAction() {
		return $this->action;
	}
	
	/**
	 * ���ҵ�����
	 */
	public function getController() {
		return $this->controller . 'Controller';
	}
	
	/**
	 * ���һ��Ӧ�����Ŀ¼��
	 */
	public function getModule() {
		return $this->module;
	}

}