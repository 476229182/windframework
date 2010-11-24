<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * ������ͼ�����׼��������������ͼ�����ύ��ĳһ���������ͼ������
 * �����ͼ������һ���ض������󣬻�����������һ������
 * �򷵻�һ��forward����
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindView {
	
	/* ��ͼ������Ϣ  */
	const VIEW_CONFIG = 'view';
	const VIEW_CONFIG_TEMPLATE_PATH = 'templatePath';
	const VIEW_CONFIG_TEMPLATE_NAME = 'templateName';
	const VIEW_CONFIG_TEMPLATE_EXT = 'templateExt';
	const VIEW_CONFIG_RESOLVER = 'resolver';
	const VIEW_CONFIG_CACHE_DIR = 'cacheDir';
	const VIEW_CONFIG_COMPILE_DIR = 'compileDir';
	
	const VIEWER_RESOLVER = 'viewerResolver';
	
	private $templatePath = 'template';
	private $templateName = 'index';
	private $templateExt = 'htm';
	
	private $templateCacheDir = '';
	private $templateCompileDir = '';
	
	/**
	 * Ĭ�ϵ���ͼ������
	 * 
	 * @var string
	 */
	private $reolver = 'default';
	private $viewerResolvers = array();
	private $config = array();
	
	/**
	 * @var $this->mav WindModelAndView
	 */
	private $mav = null;
	
	/**
	 * @param string $templateName
	 * @param WindModelAndView $mav
	 */
	public function __construct($templateName = '', $mav = null) {
		$this->initConfig();
		if ($templateName) $this->templateName = $templateName;
		$this->mav = $mav ? $mav : new WindModelAndView($templateName);
	}
	
	/**
	 * ͨ��modelandview��ͼ��Ϣ����view
	 * @param WindModelAndView $mav
	 */
	public function setViewWithObject(&$mav) {
		if ($mav instanceof WindModelAndView) {
			$this->mav = $mav;
			$this->templateName = $this->mav->getViewName();
		}
	}
	
	/**
	 * ����ַ�
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
		if ($this->mav === null || !$this->mav->getRedirect()) throw new WindException('redirect error.');
		$response->sendRedirect($this->mav->getRedirect());
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
		$viewer = $this->createViewerResolver();
		$viewer->windAssign($this->mav->getModel());
		$response->setBody($viewer->windFetch());
	}
	
	/**
	 * ������ͼ����������
	 * 
	 * @return WindViewer
	 */
	public function &createViewerResolver() {
		$viewerResolver = $this->viewerResolvers[$this->reolver];
		list(, $className, , $viewerResolver) = L::getRealPath($viewerResolver, true);
		L::import($viewerResolver);
		if (!class_exists($className)) {
			throw new WindException('viewer resolver ' . $className . ' is not exists in ' . $viewerResolver);
		}
		$object = new $className();
		$object->initViewerResolverWithView($this);
		return $object;
	}
	
	/**
	 * ��ʼ�������ļ������ģ��·����Ϣ
	 */
	private function initConfig() {
		$configObj = WindSystemConfig::getInstance();
		if ($configObj == null) throw new WindException('config object is null.');
		
		$this->viewerResolvers = $configObj->getConfig(self::VIEWER_RESOLVER);
		
		if (isset($this->config[self::VIEW_CONFIG_TEMPLATE_PATH])) {
			$this->templatePath = $this->config[self::VIEW_CONFIG_TEMPLATE_PATH];
		}
		if (isset($this->config[self::VIEW_CONFIG_TEMPLATE_EXT])) {
			$this->templateExt = $this->config[self::VIEW_CONFIG_TEMPLATE_EXT];
		}
		if (isset($this->config[self::VIEW_CONFIG_TEMPLATE_NAME])) {
			$this->templateName = $this->config[self::VIEW_CONFIG_TEMPLATE_NAME];
		}
		if (isset($this->config[self::VIEW_CONFIG_CACHE_DIR])) {
			$this->templateCacheDir = $this->config[self::VIEW_CONFIG_CACHE_DIR];
		}
		if (isset($this->config[self::VIEW_CONFIG_COMPILE_DIR])) {
			$this->templateCompileDir = $this->config[self::VIEW_CONFIG_COMPILE_DIR];
		}
		if (isset($this->config[self::VIEW_CONFIG_RESOLVER])) {
			$this->reolver = $this->config[self::VIEW_CONFIG_RESOLVER];
		}
	}
	
	/**
	 * @return the $templatePath
	 */
	public function getTemplatePath() {
		return $this->templatePath;
	}
	
	/**
	 * @return the $templateName
	 */
	public function getTemplateName() {
		return $this->templateName;
	}
	
	/**
	 * @return the $templateExt
	 */
	public function getTemplateExt() {
		return $this->templateExt;
	}
	
	/**
	 * @return the $reolver
	 */
	public function getReolver() {
		return $this->reolver;
	}
	
	/**
	 * @return the $viewerResolvers
	 */
	public function getViewerResolvers() {
		return $this->viewerResolvers;
	}
	
	/**
	 * @return the $config
	 */
	public function getConfig() {
		return $this->config;
	}
	
	/**
	 * @param $templatePath the $templatePath to set
	 * @author Qiong Wu
	 */
	public function setTemplatePath($templatePath) {
		$this->templatePath = $templatePath;
	}
	
	/**
	 * @param $templateName the $templateName to set
	 * @author Qiong Wu
	 */
	public function setTemplateName($templateName) {
		$this->templateName = $templateName;
	}
	
	/**
	 * @param $templateExt the $templateExt to set
	 * @author Qiong Wu
	 */
	public function setTemplateExt($templateExt) {
		$this->templateExt = $templateExt;
	}
	/**
	 * @return the $templateCacheDir
	 */
	public function getTemplateCacheDir() {
		return $this->templateCacheDir;
	}
	
	/**
	 * @return the $templateCompileDir
	 */
	public function getTemplateCompileDir() {
		return $this->templateCompileDir;
	}
	
	/**
	 * @param $templateCacheDir the $templateCacheDir to set
	 * @author Qiong Wu
	 */
	public function setTemplateCacheDir($templateCacheDir) {
		$this->templateCacheDir = $templateCacheDir;
	}
	
	/**
	 * @param $templateCompileDir the $templateCompileDir to set
	 * @author Qiong Wu
	 */
	public function setTemplateCompileDir($templateCompileDir) {
		$this->templateCompileDir = $templateCompileDir;
	}
	
	/**
	 * @return WindModelAndView $mav
	 */
	public function getMav() {
		return $this->mav;
	}

}