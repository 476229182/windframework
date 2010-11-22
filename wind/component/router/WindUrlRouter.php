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
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @link WRouteParser
 * @package 
 */
class WindUrlRouter extends WindRouter {
	protected $routerName = 'url';
	
	/**
	 * ���ø÷���ʵ��·�ɽ���
	 * ��õ� request �ľ�̬���󣬵õ�request��URL��Ϣ
	 * ��� config �ľ�̬���󣬵õ�URL�ĸ�ʽ��Ϣ
	 * ����URL��������RouterContext����
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 */
	public function doParser($request, $response) {
		if (!$this->routerRule) throw new WindException('The url parser rule is empty.');
		
		$this->_setValues($request, $response);
	}
	
	/**
	 * ͨ��ʵ��WAction�ӿڵĵ��ø÷���
	 * 
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 */
	public function getActionHandle() {
		if (empty($this->modules)) throw new WindException('the modules is empty.');
		$module = $this->modules[$this->getModule()];
		$module .= '.' . $this->getController();
		$className = $this->getAction();
		$method = 'run';
		L::import($module . '.' . $className);
		if (!class_exists($className)) return array(null, null);
		if (!in_array(get_class_methods($method, $className))) return array(null, null);
		$this->modulePath = $module;
		return array($className, $method);
	}
	/**
	 * ͨ��ʵ��WActionController�ӿڵĵ��ø÷���
	 */
	public function getControllerHandle() {
		if (empty($this->modules)) throw new WindException('the modules is empty.');
		$module = $this->modules[$this->getModule()];
		$className = $this->getController();
		$method = $this->getAction();
		L::import($module . '.' . $className);
		if (!class_exists($className)) return array(null, null);
		if (!in_array($method, get_class_methods($className))) return array(null, null);
		$this->modulePath = $module;
		return array($className, $method);
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
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 */
	private function _setValues($request, $response) {
		$keys = array_keys($this->routerRule);
		$this->action = $request->getGet($keys[0], $this->action);
		$this->controller = $request->getGet($keys[1], $this->controller);
		$this->module = $request->getGet($keys[2], $this->module);
	}

}