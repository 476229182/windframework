<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-7
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
class WWebApplicationController implements WApplicationController {
	
	/**
	 * ��ʼ��������Ϣ
	 * @param WSystemConfig $configObj
	 */
	public function init() {}
	
	/**
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 * @param WSystemConfig $configObj
	 */
	public function processRequest($request, $response, $configObj) {
		$router = $this->createRouter($configObj);
		$router->doParser($request, $response);
		
		if (($base = $configObj->getConfig('baseController')) == 'WActionController')
			list($className, $method) = $router->getControllerHandle($request, $response);
		elseif (($base = $configObj->getConfig('baseController')) == 'WAction')
			list($className, $method) = $router->getActionHandle($request, $response);
		else
			throw new WException('determine the baseController is failed in config.php.');
		
		$class = new ReflectionClass($className);
		$action = call_user_func_array(array(
			$class, 
			'newInstance'
		), array(
			$request, 
			$response
		));
		
		if (($formHandle = $router->getActionFormHandle()) != null) {
			$actionForm = W::getInstance($formHandle, array(
				$request, 
				$response
			));
			
			//TODO ��֤������������
			if ($actionForm->getIsValidate()) {
				$this->validateProcessActionForm($actionForm);
			}
		}
		
		$forward = $action->$method();
		
		if (!$forward)
			$forward = $router->getDefaultViewHandle();
		
		$this->processActionForward($request, $response, $forward);
	}
	
	/**
	 * @param WActionForm $actionForm
	 */
	protected function validateProcessActionForm($actionForm) {
		$result = $actionForm->validation();
	}
	
	/**
	 * ������Ӧ
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 * @param WviewFactory $forward
	 */
	protected function processActionForward($request, $response, $forward) {
		ob_start();
		W::import('template.hello');
		
		echo ob_get_clean();
		$response->sendResponse();
	}
	
	/**
	 * ���һ��·��ʵ��
	 * @param WSystemConfig $configObj
	 * @return WRouter
	 */
	public function &createRouter($configObj) {
		$parser = 'url';
		$parserPath = 'router.parser.WUrlRouteParser';
		if (($_parser = $configObj->getRouterConfig('parser')) != null)
			$parser = $_parser;
		if (($_parserPath = $configObj->getRouterParser($parser)) != null)
			$parserPath = $_parserPath;
		if (($pos = strrpos($parserPath, '.')) === false)
			$className = $parserPath;
		else
			$className = substr($parserPath, $pos + 1);
		W::import($parserPath);
		if (!class_exists($className))
			throw new WException('The router ' . $className . ' is not exists.');
		
		$class = new ReflectionClass($className);
		$router = call_user_func_array(array(
			$class, 
			'newInstance'
		), array(
			$configObj
		));
		return $router;
	}
	
	public function destory() {}
}