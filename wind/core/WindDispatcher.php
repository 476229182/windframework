<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-24
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindDispatcher {
	private $action = 'run';
	private $controller = 'index';
	private $module = 'apps';
	
	private $mav = null;
	private $mavs = array();
	
	private static $instance = null;
	
	/**
	 * @param WindModelAndView $mav
	 */
	public function __construct() {}
	
	/**
	 * ����ַ�����
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function dispatch($request, $response) {
		if ($this->getMav() === null) throw new WindException('dispatch error.');
		if (($redirect = $this->getMav()->getRedirect()) !== '')
			$this->_dispatchWithRedirect($redirect, $request, $response);
		
		elseif (($action = $this->getMav()->getAction()) !== '')
			$this->_dispatchWithAction($action, $request, $response);
		
		else
			$this->_dispatchWithTemplate($request, $response);
		return;
	}
	
	/**
	 * ����ַ�һ���ض�������
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	private function _dispatchWithRedirect($redirect, $request, $response) {
		$response->sendRedirect($redirect);
	}
	
	/**
	 * ����ַ�һ����������
	 * @param String $action
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	private function _dispatchWithAction($action, $request, $response) {
		if (!$action) throw new WindException('action handled is empty.');
		$this->initWithModelAndView($this->getMav());
		WindFrontController::getInstance()->getApplicationHandle()->processRequest($request, $response);
	}
	
	/**
	 * ����ַ�һ��ģ������
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	private function _dispatchWithTemplate($request, $response) {
		while ($mav = array_pop($this->mavs)) {
			$viewer = $mav->getView()->createViewerResolver();
			$viewer->windAssign($response->getData());
			$response->setBody($viewer->windFetch(), $mav->getViewName());
		}
	}
	
	/**
	 * ����һ��ModelAndView����
	 * @return WindModelAndView $mav
	 */
	public function getMav() {
		return $this->mav;
	}
	
	/**
	 * @param WindModelAndView $mav the $mav to set
	 * @return WindDispatcher
	 * @author Qiong Wu
	 */
	public function setMav($mav) {
		if ($mav instanceof WindModelAndView) {
			$this->mavs[] = $mav;
			$this->mav = $mav;
		} else
			throw new WindException('The type of object error.');
		
		return $this;
	}
	
	/**
	 * @param WindModelAndView $mav
	 */
	public function initWithModelAndView($mav) {
		$this->action = $mav->getAction();
		$path = $this->getMav()->getActionPath();
		if (!$path) return;
		if (($pos = strpos($path, ':')) !== false) {
			$path = substr($path, $pos + 1);
		}
		if (($pos = strrpos($path, '.')) !== false) {
			$this->controller = substr($path, $pos + 1);
			$this->module = substr($path, 0, $pos);
		} else
			$this->controller = $path;
	}
	
	/**
	 * @param WindRouter $router
	 * @return WindDispatcher
	 */
	public function initWithRouter($router) {
		if ($router instanceof WindRouter) {
			$this->module = $router->getModule();
			$this->controller = $router->getController();
			$this->action = $router->getAction();
		}
		return $this;
	}
	
	/**
	 * ���ش���������
	 * @return array($className,$method)
	 */
	public function getActionHandle() {
		$moduleConfig = C::getModules($this->module);
		$module = $moduleConfig ? $moduleConfig[IWindConfig::MODULE_PATH] : $this->module;
		$path = $module . '.' . $this->controller;
		if (!preg_match("/Controller$/i", $path)) {
			$path .= 'Controller';
		}
		$method = $this->action;
		list(, $className, , $realPath) = L::getRealPath($path, true);
		if (!$realPath) {
			$path .= $this->action;
			if (!preg_match("/Action$/i", $path)) {
				$path .= 'Action';
			}
			list(, $className, , $realPath) = L::getRealPath($path, true);
			$method = 'run';
		}
		L::import($realPath);
		if (!class_exists($className) || !in_array($method, get_class_methods($className))) {
			return array(null, null);
		}
		return array($className, $method);
	}
	
	/**
	 * @return WindDispatcher
	 */
	static public function getInstance() {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}
	
	/**
	 * @return the $action
	 */
	public function getAction() {
		return $this->action;
	}
	
	/**
	 * @return the $controller
	 */
	public function getController() {
		return $this->controller;
	}
	
	/**
	 * @return the $module
	 */
	public function getModule() {
		return $this->module;
	}

}