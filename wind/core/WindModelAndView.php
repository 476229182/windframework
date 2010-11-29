<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-22
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
class WindModelAndView {
	/* ģ����ͼ��Ϣ */
	private $viewName = '';
	private $path = '';
	
	/* ҳ���ض���������Ϣ */
	private $redirect = '';
	
	/* ������������ */
	private $action = '';
	private $actionPath = '';
	
	/**
	 * ��ͼԤ������
	 * @var WindView
	 */
	private $view = null;
	
	/* ������Ϣ */
	private $layoutMapping = array();
	private $layout = null;
	
	/**
	 * @param WindHttpRequest $request //path to which control should be forwarded or redirected
	 * @param WindHttpResponse $response //should we do a redirect
	 * @param string $module //module prefix
	 */
	public function __construct() {}
	
	/**
	 * ����һ��layout�����ʼ��ModelAndView
	 * @param WindLayout $layout
	 */
	public function setLayout($layout) {
		if ($layout instanceof WindLayout) {
			$this->layout = $layout;
		} else
			throw new WindException('object type error.');
	}
	
	/**
	 * @return WindLayout
	 */
	public function &getLayout() {
		return $this->layout;
	}
	
	public function getLayoutMapping() {
		return $this->layoutMapping;
	}
	
	
	/**
	 * ������ͼ���ض�����Ϣ
	 * 
	 * @param string $redirect
	 */
	public function setRedirect($redirect) {
		if (!$redirect) return;
		$this->redirect = $redirect;
	}
	
	public function getRedirect() {
		return $this->redirect;
	}
	
	/**
	 * ������ͼ���߼�����
	 * 
	 * @param string $name
	 */
	public function setViewName($viewName, $key = 'current') {
		if (!$viewName) return;
		$this->layoutMapping['key_' . $key] = $viewName;
		$this->viewName = $viewName;
	}
	
	/**
	 * ������ͼ���߼�����
	 * 
	 * @return string
	 */
	public function getViewName() {
		return $this->viewName;
	}
	
	/**
	 * ����view����
	 * 
	 * @param WindView $view
	 */
	public function setView($view = null) {
		$this->view = $view;
	}
	
	/**
	 * ����WindView����
	 * 
	 * @return WindView
	 */
	public function getView() {
		if ($this->view == null) {
			L::import('WIND:component.viewer.WindView');
			$this->view = new WindView();
			$this->view->setViewWithObject($this);
		}
		return $this->view;
	}
	
	/**
	 * ������ͼ��·����Ϣ
	 * 
	 * @param string $path
	 */
	public function setPath($path) {
		$this->path = $path;
	}
	
	/**
	 * ������ͼ��·����Ϣ
	 * 
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}
	
	/**
	 * @return the $action
	 */
	public function getAction() {
		return $this->action;
	}
	
	/**
	 * @param $action the $action to set
	 * @author Qiong Wu
	 */
	public function setAction($action) {
		$this->action = $action;
	}
	
	/**
	 * @return the $actionPath
	 */
	public function getActionPath() {
		return $this->actionPath;
	}

	/**
	 * @param $actionPath the $actionPath to set
	 * @author Qiong Wu
	 */
	public function setActionPath($actionPath) {
		$this->actionPath = $actionPath;
	}


}