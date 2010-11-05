<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

WBasic::import('router.WRouterContext');

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @link WRouteParser
 * @package 
 */
class WUrlRouteParser implements WRouteParser {
	
	private $_urlRule;
	
	private $routerContext;
	
	public function __construct() {
		$this->routerContext = WRouterContext::getInstance();
	}
	
	/**
	 * ���ø÷���ʵ��·�ɽ���
	 * ��õ� request �ľ�̬���󣬵õ�request��URL��Ϣ
	 * ��� config �ľ�̬���󣬵õ�URL�ĸ�ʽ��Ϣ
	 * ����URL��������RouterContext����
	 * @return WRouterContext
	 */
	public function doParser() {
		$this->_init();
		
		return $this->routerContext;
	}
	
	/**
	 * ·�ɽ�������ʼ������
	 */
	private function _init() {
		$config = array();
		$routeChina = $this->_parserUrl($config);
//		$this->routerContext->
	}
	
	/**
	 * �������ù������Url, �����ؽ��������
	 * 
	 * @param array $config
	 * @return array('����','Ӧ��','Ӧ�ü���1','Ӧ�ü���2');
	 */
	private function _parserUrl($config) {
		
		return array('a','c');
	}

}