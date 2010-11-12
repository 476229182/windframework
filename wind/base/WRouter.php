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
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WRouter {
	protected $routerRule = '';
	protected $routerName = 'url';
	
	protected $action = 'run';
	protected $controller = 'index';
	protected $app1 = 'actionControllers';
	protected $app2;
	
	protected $configObj = null;
	
	function __construct($configObj = null) {
		$this->init($configObj);
	}
	
	/**
	 * ��ʼ��·������
	 * @param WSystemConfig $configObj
	 */
	protected function init($configObj) {
		if ($configObj === null)
			throw new WException('Config object is null!!!');
		$this->routerRule = $configObj->getRouterRule($this->routerName);
		$this->configObj = $configObj;
	}
	
	/**
	 * ͨ��ʵ�ָýӿ�ʵ��·�ɽ���
	 * @return WRouterContext
	 */
	abstract function doParser($request, $response);
	
	abstract function &getActionHandle();
	abstract function &getControllerHandle();
	
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
	public function getApp1() {
		return $this->app1;
	}
	
	/**
	 * ���һ��Ӧ����ڶ���Ŀ¼��
	 */
	public function getApp2() {
		return $this->app2;
	}

}