<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::register('viewer', dirname(__FILE__));
L::import('viewer:base.IWindViewer');
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
class WindViewer implements IWindViewer {
	
	protected $template = '';
	protected $templatePath = '';
	protected $templateExt = '';
	
	protected $view = null;
	protected $layout = null;
	protected $layoutMapping = array();
	
	/**
	 * ��ͼ������Ϣ
	 * @var $vars
	 */
	protected $vars = array();
	
	/**
	 * ��ȡģ����Ϣ
	 */
	public function windFetch($template = '') {
		if ($this->vars) extract($this->vars, EXTR_REFS);
		ob_start();
		if (($segments = $this->parserLayout()) == null) {
			$template = $this->getViewTemplate($template);
			if ($template) include $template;
		} else {
			foreach ($segments as $value) {
				if (isset($this->layoutMapping[$value])) {
					$value = $this->layoutMapping[$value];
				}
				$template = $this->getViewTemplate($value);
				if (is_file($template)) @include $template;
			}
		}
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
	 * ������ڲ����ļ������������Ϣ
	 * @return array()
	 */
	public function parserLayout() {
		if ($this->layout === null) return null;
		return $this->layout->parserLayout($this->templatePath, $this->templateExt);
	}
	
	/**
	 * ģ��·������
	 * ����ģ����߼����ƣ�����ģ��ľ���·����Ϣ
	 * 
	 * @param string $templateName
	 * @param string $templateExt
	 * @return string | false
	 */
	public function getViewTemplate($templateName = '', $templateExt = '') {
		if (!$templateName) $templateName = $this->template;
		if (!$templateExt) $templateExt = $this->templateExt;
		if (strrpos($templateName, ':') === false) {
			$templateName = $this->templatePath . '.' . $templateName;
		}
		return L::getRealPath($templateName, false, $templateExt);
	}
	
	/**
	 * ������ͼ��Ϣ
	 * 
	 * @param WindView $view
	 */
	public function initWithView($view) {
		$this->template = $view->getTemplateName();
		$this->templatePath = $view->getTemplatePath();
		$this->templateExt = $view->getTemplateExt();
		$this->layout = $view->getMav()->getLayout();
		$this->layoutMapping = $view->getMav()->getLayoutMapping();
		$this->view = $view;
	}
	
	/**
	 * @return WindView
	 */
	public function getView() {
		return $this->view;
	}
	
	/**
	 * @param string $actionHandle
	 */
	public function doAction($actionHandle = '') {
		if ($this->view instanceof WindView) $this->getView()->doAction($actionHandle);
	}

}