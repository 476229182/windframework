<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WViewFactory {
	
	private $viewPath = 'template';
	private $ext = 'htm';
	private $config = array();
	
	private $forward = '';
	
	const VIEW_CONFIG = 'view';
	
	private static $instance = null;
	
	protected function __construct($configObj = null) {
		$this->initConfig($configObj);
	}
	
	public function setForward($forward) {
		$this->forward;
	}
	
	/**
	 * ��ʼ�������ļ������ģ��·����Ϣ
	 * 
	 * @param WSystemConfig $configObj
	 */
	private function initConfig(WSystemConfig $configObj) {
		if ($configObj == null)
			return;
		
		$this->config = $configObj->getConfig(self::VIEW_CONFIG);
		if (isset($this->config['viewPath']))
			$this->viewPath = $this->config['viewPath'];
		
		if (isset($this->config['ext']))
			$this->ext = $this->config['ext'];
	}
	
	/**
	 * ����ģ�����ƻ��ģ���ļ�
	 * 
	 * @param string $viewName
	 * @return string
	 */
	private function getViewTemplate($viewName) {
		return '';
	}
	
	/**
	 * ������ͼforward���߼���ͼ���ƻ�����ǵ���ͼ�ļ���
	 * @return string
	 */
	private function getViewFileName() {
		if (!$this->forward)
			$this->forward = 'index';
		
		return $this->forward . $this->ext;
	}
	
	/**
	 * @param WSystemConfig $configObj
	 * @return WViewFactory
	 */
	static public function getInstance(WSystemConfig $configObj = null) {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class($configObj);
		}
		return self::$instance;
	}
}