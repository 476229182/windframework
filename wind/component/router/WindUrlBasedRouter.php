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
	protected $routerName = 'url';
	
	/**
	 * ���ø÷���ʵ��·�ɽ���
	 * ��õ� request �ľ�̬���󣬵õ�request��URL��Ϣ
	 * ��� config �ľ�̬���󣬵õ�URL�ĸ�ʽ��Ϣ
	 * ����URL��������RouterContext����
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function doParser($request, $response) {
		if (!$this->routerRule) throw new WindException('The url parser rule is empty.');
		$this->_setValues($request, $response);
	}
	
	/**
	 * ���������ActionForm��������δ�����򷵻�null
	 */
	public function getActionFormHandle() {
		if (!$this->modulePath) throw new WindException('The path of module is not exists.');
		
		try {
			$formPath = $this->modulePath . '.' . 'actionForm';
			$className = $this->controller . $this->action . 'Form';
			if (!is_file($formPath . '.' . $className . '.php')) return null;
			W::import($formPath . '.' . $className);
			if (class_exists($className)) return $className;
		} catch (Exception $exception) {
			return null;
		}
		return null;
	}
	
	/**
	 * ���������ActionForm��������δ�����򷵻�null
	 */
	public function getDefaultViewHandle() {
		if (!$this->modulePath) throw new WindException('The path of module is not exists.');
		return $this->controller . '_' . $this->action;
	}
	
	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	private function _setValues($request, $response) {
		$keys = array_keys($this->routerRule);
		$this->action = $request->getGet($keys[0], $this->action);
		$this->controller = $request->getGet($keys[1], $this->controller);
		$this->module = $request->getGet($keys[2], $this->module);
		$response->setRouter($this);
	}

}