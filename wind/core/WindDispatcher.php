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
	
	private $mav = null;
	private static $instance = null;
	
	public function __construct($mav) {
		$this->setMav($mav);
	}
	
	/**
	 * ����ַ�����
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function dispatch($request, $response) {
		if ($this->mav === null) throw new WindException('dispatch error.');
		if ($this->mav->isRedirect())
			$this->_dispatchWithRedirect($request, $response);
		elseif ($this->mav->getPath())
			$this->_dispatchWithAction($request, $response);
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
	private function _dispatchWithRedirect($request, $response) {	

	//TODO 
	}
	
	/**
	 * ����ַ�һ����������
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	private function _dispatchWithAction($request, $response) {	

	//TODO
	}
	
	/**
	 * ����ַ�һ��ģ������
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	private function _dispatchWithTemplate($request, $response) {
		$viewer = $this->getMav()->getView()->createViewerResolver();
		$viewer->windAssign($this->mav->getModel());
		$response->setBody($viewer->windFetch());
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
	 * @author Qiong Wu
	 */
	public function setMav($mav) {
		if ($mav instanceof WindModelAndView)
			$this->mav = $mav;
		else
			throw new WindException('The type of object error.');
	}
	
	/**
	 * @return WindDispatcher
	 */
	static public function getInstance($mav = null) {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class($mav);
		}
		return self::$instance;
	}

}