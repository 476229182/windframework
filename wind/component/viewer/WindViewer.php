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
	
	protected $template = '';
	protected $templatePath = '';
	protected $view = null;
	
	/**
	 * ��ͼ������Ϣ
	 * @var $vars
	 */
	protected $vars = array();
	
	/**
	 * ��ȡģ����Ϣ
	 */
	public function windFetch($template = '') {
		$template = $this->getViewTemplate($template);
		if ($this->vars) extract($this->vars, EXTR_REFS);
		
		ob_start();
		@include $template;
		
		return ob_get_clean();
	}
	
	/**
	 * ����ģ�������Ϣ
	 * 
	 * @param object|array|string $vars
	 * @param string $key
	 */
	public function windAssign($vars, $key = '') {
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
		$this->vars += get_object_vars($vars);
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
		$templatePath = $this->templatePath;
		$templatePath = $this->_getViewTemplate($templateName, $templatePath, $templateExt);
		return $templatePath;
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
		return L::getRealPath($filePath, false, $templateExt);
	}
	
	/**
	 * ������ͼ��Ϣ
	 * 
	 * @param WindView $view
	 */
	public function initViewerResolverWithView($view) {
		$this->template = $view->getTemplateName();
		$this->templatePath = $view->getTemplatePath();
		$this->view = $view;
	}
}