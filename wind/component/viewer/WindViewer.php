<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::register('viewer', dirname(__FILE__));
L::import('viewer:base.impl.WindViewerImpl');
/**
 * Ĭ����ͼ����
 * ����URL����ͼ���棬��ͼ����ģ�����Ʊ���һ��
 * 
 * ����ͼ�����һ��modelAndView����ͨ�������ö�����һ���߼���ͼ����
 * �������߼���ͼ���ƣ�ӳ�䵽�������ͼ��Դ��
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindViewer implements WindViewerImpl {
	
	/**
	 * @var WindView
	 */
	protected $view = null;
	
	/**
	 * ��ͼ����
	 * @var $viewContainer
	 */
	protected $viewContainer = '';
	
	protected $template = '';
	
	/**
	 * ��ͼ������Ϣ
	 * @var $vars
	 */
	protected $vars = array();
	protected $isCache = false;
	
	public function windDisplay($template = '') {
		//TODO
		$this->windFetch($template);
		return $this->viewContainer;
	}
	
	/**
	 * ��ȡģ����Ϣ
	 */
	public function windFetch($template) {
		$template = $this->getViewTemplate($template);
		if ($this->vars) extract($this->vars, EXTR_REFS);
		ob_start();
		@include $template;
		$this->viewContainer = ob_get_clean();
	}
	
	/**
	 * ����ģ�������Ϣ
	 * 
	 * @param object|array|string $vars
	 * @param string $key
	 */
	public function windAssign($vars = '', $key = '') {
		if (is_array($vars))
			$this->windAssignWithArray($vars, $key);
		elseif (is_object($vars))
			$this->windAssignWithObject($vars, $key);
		else
			$this->windAssignWithSimple($vars, $key);
	}
	
	/**
	 * ����ģ�����
	 * 
	 * @param $vars
	 * @param string $key
	 */
	public function windAssignWithSimple($vars, $key = '') {
		if ($key) $this->vars[$key] = $vars;
	}
	
	/**
	 * ����ģ�����
	 * 
	 * @param object $vars
	 * @param string $key
	 */
	public function windAssignWithObject($vars, $key = '') {
		if (!is_object($vars)) return;
		if ($key) $this->vars[$key] = $vars;
		$this->vars += get_class_vars($vars);
	}
	
	/**
	 * ����ģ�����
	 * 
	 * @param array $vars
	 * @param string $key
	 */
	public function windAssignWithArray($vars, $key = '') {
		if (!is_array($vars)) return;
		if ($key) $this->vars[$key] = $vars;
		foreach ($vars as $key => $value) {
			$this->vars[$key] = $value;
		}
	}
	
	/**
	 * ���ģ���ļ�
	 * 
	 * @param string $templateName
	 * @param string $templateExt
	 * @return array()
	 */
	public function getViewTemplate($templateName = '', $templateExt = '') {
		if (!$templateName) $templateName = $this->view->getTemplateName();
		if (!$templateExt) $templateExt = $this->view->getTemplateExt();
		$templatePath = $this->view->getTemplatePath();
		$templatePath = $this->_getViewTemplate($templateName, $templatePath, $templateExt);
		return $templatePath;
	}
	
	private function _getViewCache() {

	}
	
	/**
	 * ����ģ�����ƻ��ģ���ļ�
	 * 
	 * @param string $viewName
	 * @return array()
	 */
	private function _getViewTemplate($templateName, $templatePath, $templateExt = '') {
		if (!$templateName) throw new WindException('template file is not exists.');
		
		$filePath = $templatePath . '.' . $templateName;
		return W::getRealPath($filePath, false, $templateExt);
	}
	
	/**
	 * ������ͼ��Ϣ
	 * 
	 * @param WindView $view
	 */
	public function setView($view) {
		$this->view = $view;
	}
}