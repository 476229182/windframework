<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.WindModelAndView');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WindBaseAction {
	private $request;
	private $response;
	/**
	 * ҳ����ת��Ϣmodel and view����
	 * 
	 * @var $mav WindModelAndView
	 */
	protected $mav = null;
	
	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function __construct(WindHttpRequest $request, WindHttpResponse $response) {
		$this->mav = new WindModelAndView();
		$this->request = $request;
		$this->response = $response;
		$this->setDefaultViewTemplate();
	}
	
	public function beforeAction() {}
	abstract public function run();
	public function afterAction() {}
	
	/**
	 * ����Ĭ��ģ��
	 */
	public function setDefaultViewTemplate($default = '') {
		if (!$default && $this->response) {
			$default = $this->response->getDispatcher()->getController() . '_' . $this->response->getDispatcher()->getAction();
		}
		$this->mav->setViewName($default);
	}
	
	/**
	 * ����ģ������
	 * @param string|array|object $data
	 * @param string $key
	 */
	public function setViewData($data, $key = '') {
		$this->response->setData($data, $key);
	}
	
	/**
	 * ����ҳ��ģ��
	 * @param string $template
	 */
	public function setTemplate($template = '') {
		if ($template) $this->getModelAndView()->setViewName($template);
	}
	
	/**
	 * ����ҳ�沼��
	 * @param WindLayout $layout
	 */
	public function setLayout($layout = '') {
		if ($layout instanceof WindLayout) $this->getModelAndView()->setLayout($layout);
	}
	
	/**
	 * ��Ӵ�����Ϣ
	 * 
	 * @param string $message
	 * @param string $key
	 */
	public function addError($message, $key = '') {
		WindErrorMessage::getInstance()->addError($message, $key);
	}
	
	/**
	 * @param string $message
	 * @param string $key
	 */
	public function showError($message = '', $key = '') {
		$this->addError($message, $key);
	}
	
	/**
	 * @return WindModelAndView $mav
	 */
	public function getModelAndView() {
		return $this->mav;
	}

}