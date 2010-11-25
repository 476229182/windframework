<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-7
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.base.IWindApplication');
L::import('WIND:component.exception.WindException');
L::import('WIND:component.viewer.WindViewFactory');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindWebApplication implements IWindApplication {
	
	/**
	 * ��ʼ��������Ϣ
	 * @param WSystemConfig $configObj
	 */
	public function init() {}
	
	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @param WSystemConfig $configObj
	 */
	public function processRequest($request, $response) {
		$router = $this->createRouter();
		$router->doParser($request, $response);
		
		/* ��ò������ */
		list($action, $method) = $this->getActionHandle($request, $response);
		$action->beforeAction();
		$action->$method($request, $response);
		$action->afterAction();
		
		/* ���������ת��Ϣ */
		$mav = $action->getModelAndView();
		$this->processDispatch($request, $response, $mav);
	}
	
	/**
	 * ����action��
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @return array(WindAction,string)
	 */
	protected function getActionHandle($request, $response) {
		list($className, $method) = $response->getRouter()->getActionHandle();
		if ($className === null || $method === null) {
			throw new WindException('can\'t create action handle.');
		}
		$class = new ReflectionClass($className);
		$action = call_user_func_array(array($class, 'newInstance'), array($request, $response));
		return array($action, $method);
	}
	
	/**
	 * ����ҳ��������ض���
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @param WindModelAndView $mav
	 */
	protected function processDispatch($request, $response, $mav) {
		WindDispatcher::getInstance($mav)->dispatch($request, $response);
	}
	
	/**
	 * ���һ��·��ʵ��
	 * @param WSystemConfig $configObj
	 * @return WRouter
	 */
	public function &createRouter() {
		$configObj = WindSystemConfig::getInstance();
		$parser = $configObj->getRouterConfig('parser');
		$parserPath = $configObj->getRouterParser($parser);
		list(, $className, , $parserPath) = L::getRealPath($parserPath, true);
		L::import($parserPath);
		if (!class_exists($className)) throw new WindException('The router ' . $className . ' is not exists.');
		$router = new $className($configObj);
		return $router;
	}
	
	public function destory() {}
}