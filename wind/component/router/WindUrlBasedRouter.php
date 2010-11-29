<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:component.router.base.WindRouter');
L::import('WIND:component.exception.WindException');
/**
 * ����URL��·�ɽ�����.
 * �ý�����ͨ������һ��Http�����Request���������URL�Ĳ�����Ϣ
 * ��������������Ѷ����·�ɹ�����н���.
 * ͨ���÷�����getActionHandle��������һ������������ľ����Ϣ
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @link WRouteParser
 * @package 
 */
class WindUrlBasedRouter extends WindRouter {
	/**
	 * ���ø÷���ʵ��·�ɽ���
	 * ��õ� request �ľ�̬���󣬵õ�request��URL��Ϣ
	 * ��� config �ľ�̬���󣬵õ�URL�ĸ�ʽ��Ϣ
	 * ����URL��������RouterContext����
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function doParser($request, $response) {
		$this->_setValues($request, $response);
	}
	
	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	private function _setValues($request, $response) {
		$rule = $this->routerConfig[IWindConfig::ROUTER_PARSERS_RULE];
		$keys = array_keys($rule);
		$this->action = $request->getGet($keys[0], $this->action);
		$this->controller = $request->getGet($keys[1], $this->controller);
		$this->module = $request->getGet($keys[2], $this->module);
		$response->setDispatcher($this);
	}

}