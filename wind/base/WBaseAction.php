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
	protected function setView($value, $key = '') {
		if ($key) {
			$this->view->$key = $value;
		} else {
			if (is_string($value)) {
				$this->view->default = $value;
				return;
			} elseif (is_object($value))
				$value = get_object_vars($value);
			foreach ($value as $k => $v) {
				if ($k)
					$this->view->$k = $v;
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
		if (!$request->getIsAjaxRequest()) {
			$viewer->setLayout($this->layout);
		}
		$viewer->assign($this->view);
	}

}