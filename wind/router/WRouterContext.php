<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
WBasic::import('base.WContext');

/**
 * ·�ɶ���
 * ͨ�����ʸö�����Ի�õ���ǰ�����Ӧ��ģ���Լ�����ľ������
 * 
 * ·�ɹ��򣬵����յ�һ�������·�ɹ����������·�����ó�ʼ��һ��·�ɽ���������������
 * �����󷵻�һ��WRouterContext����
 * 
 * �������ʹ���
 * 1. $action �������ƣ����������������ƣ��������õĲ�ͬ������仯��Ψһ������Ǹò���ָ��һ�������ҵ�����
 * 2. $controller Ӧ�ÿ��������ƣ������ƣ��ò���ָ��һ������ļ��ϣ�������ĳ��С��ҵ��ģ��
 * 3. $app1 һ��Ӧ�õ����ƣ��ò���ָ��һ��Ӧ�ã����� bbs/cms/house/dianpu ��
 * 4. $app2 һ��Ӧ�õ����ƣ����һ��Ӧ������������applicationController������Ҫ����ò��������� ǰ̨�ͺ�̨��Ӧ�÷ֿ�
 * 
 * ����˳�� ��
 * $app1 -> $app2 -> $controller -> $action
 * 
 * ���ϸ���������Ĭ��ֵ�����δ����ò�������ʹ��Ĭ��ֵ
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WRouterContext extends WModel implements WContext {
	
	/* ���� */
	private $action;
	
	/* Ӧ�ÿ����� */
	private $controller;
	
	/* һ��Ӧ�����1 */
	private $app1;
	
	/* һ��Ӧ�õ����2 */
	private $app2;
	
	private function _init() {
		$this->_config = array(
			'defaultAction' => 'run', 
			'defaultController' => 'index', 
			'defaultApp1' => 'bbs', 
			'defaultApp2' => 'admin'
		);
	}
	
	public function getDefaultAction() {
		if (!isset($this->_default_action)) $this->_default_action = $this->_config['defaultAction'];
		return $this->_default_action;
	}
	
	public function getDefaultController() {
		if (!isset($this->_default_controller)) $this->_default_action = $this->_config['defaultController'];
		return $this->_default_controller;
	}
	
	public function getDefaultApp1() {
		if (!isset($this->_default_app1)) $this->_default_action = $this->_config['defaultApp1'];
		return $this->_default_app1;
	}
	
	public function getDefaultApp2() {
		if (!isset($this->_default_app2)) $this->_default_action = $this->_config['defaultApp2'];
		return $this->_default_app2;
	}
	
	/**
	 * @return WRouterContext
	 */
	public static function getInstance() {
		return WBasic::getInstance(__CLASS__);
	}

}