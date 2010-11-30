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
L::import('WIND:component.router.WindRouterFactory');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindWebApplication implements IWindApplication {
	protected $process = '';
	
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
		list($className, $method) = WindDispatcher::getInstance()->getActionHandle();
		$this->checkReprocess($className . '_' . $method);
		if ($className === null || $method === null) {
			throw new WindException('can\'t create action handle.');
		}
		$action = new $className($request, $response);
		return array($action, $method);
	}
	
	/**
	 * �ж��Ƿ����ظ��ύ����һ�������У������������ظ��������λ���������ĳ������
	 * @param string $key
	 */
	protected function checkReprocess($key = '') {
		if ($this->process && $this->process === $key) {
			//TODO
			echo 'Duplicate request \'' . $key . '\'';
			exit();
		}
		$this->process = $key;
	}
	
	/**
	 * ����ҳ��������ض���
	 * 
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @param WindModelAndView $mav
	 */
	protected function processDispatch($request, $response, $mav) {
		WindDispatcher::getInstance()->setMav($mav)->dispatch();
	}
	
	public function destory() {
		WindDispatcher::getInstance()->clear();
	}
}