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
	protected $routerRule;
	protected $routerName = 'url';
	
	protected $action = 'run';
	protected $controller = 'index';
	protected $app1 = 'default';
	protected $app2;
	
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
	}
	
	/**
	 * ͨ��ʵ�ָýӿ�ʵ��·�ɽ���
	 * @return WRouterContext
	 */
	abstract function doParser($request, $response);
	
	/**
	 * ���ҵ�����
	 */
	public function getAction() {
		return $this->_action;
	}
	
	/**
	 * ���ҵ�����
	 */
	public function getController() {
		return $this->_controller;
	}
	
	/**
	 * ���һ��Ӧ�����Ŀ¼��
	 */
	public function getApp1() {
		return $this->_app1;
	}
	
	/**
	 * ���һ��Ӧ����ڶ���Ŀ¼��
	 */
	public function getApp2() {
		return $this->_app2;
	}

}