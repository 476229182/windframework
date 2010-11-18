<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

abstract class WBaseAction {
	/**
	 * ��ͼ��Ϣ
	 * 
	 * @var $forward
	 */
	protected $forward = '';
	
	/**
	 * ҳ�沼����Ϣ
	 * 
	 * @var $layout
	 */
	protected $layout = '';
	
	/**
	 * ���������Ϣ
	 * 
	 * @var $viewer
	 */
	protected $view = null;
	
	public function __construct() {
		$this->view = new stdClass();
		$this->layout = new WLayout();
	}
	
	public function beforeAction() {}
	
	public function afterAction() {}
	
	/**
	 * ������ͼ��ת��Ϣ
	 * 
	 * @param string $forward
	 */
	protected function setForward($forward) {
		$this->forward = $forward;
	}
	
	/**
	 * ������ͼ����
	 * 
	 * @param  $view
	 */
	protected function setView($key, $value = '') {
		if (is_string($key)) {
			($value == '') ? $this->view->default = $key : $this->view->$key = $value;
		} elseif (is_object($key)) {
			$value = get_object_vars($key);
			foreach ($value as $k => $v) {
				($k) && $this->view->$k = $v;
			}
		}
		return;
	}
	
	/**
	 * ����ҳ�沼����Ƭģ��
	 * 
	 * @param string|array $segment
	 */
	protected function setSegment($segment) {
		if ($this->layout == null)
			return;
		$this->layout->setSegments($segment);
	}
	
	/**
	 * ������ͼ�Ĳ�����Ϣ
	 * 
	 * @param string $layout
	 */
	protected function setLayout($layout) {
		$this->layout->setLayout($layout);
	}
	
	/**
	 * ������ͼ����
	 * @param WhttpRequest $request
	 * @param WHttpResponse $response
	 * @param WRouter $router
	 */
	public function actionForward($request, $response, $router) {
		if (!$this->forward)
			$this->forward = $router->getDefaultViewHandle();
		
		$viewer = WViewFactory::getInstance()->create($this->forward);
		if (!$request->getIsAjaxRequest() && $this->layout instanceof WLayout) {
			$viewer->setLayout($this->layout);
		}
		$viewer->windAssign($this->view);
	}

}