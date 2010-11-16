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
abstract class WRouter {
	protected $routerRule = '';
	protected $routerName = '';
	
	protected $action = 'run';
	protected $controller = 'index';
	protected $module = '';
	
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
		if ($configObj === null)
			throw new WException('Config object is null.');
		
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
	abstract public function getActionHandle();
	
	/**
	 * �����������,����һ�����飬array('$className','$method')
	 * 
	 * @return array
	 */
	abstract public function getControllerHandle();
	
	/**
	 * �����������,����һ�����飬array('$className','$method')
	 * 
	 * @return WActionform
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
		return $this->controller;
	}
	
	/**
	 * ���һ��Ӧ�����Ŀ¼��
	 */
	public function getModule() {
		return $this->module;
	}

}