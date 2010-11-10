<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-10-27
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license 
 */

/**
 * 
 * �����ǰ�˿������ӿڣ�ͨ�����ɸýӿڿ���ʵ������ְ��
 * 
 * ְ���壺
 * ���ܿͻ�����
 * ��������
 * ��ͻ��˷�����Ӧ
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WFrontController extends WActionServlet {
	private $config = null;
	
	/**
	 * ϵͳ��ʼ������
	 * @param array $config
	 * @return null
	 */
	function init($config = array()) {
		parent::init();
		$this->_initConfig($config);
	}
	
	function run() {
		if ($this->config === null)
			throw new WException('init system config failed!');
		$this->beforProcess();
		if (!class_exists('WFilterFactory'))
			parent::run();
		else
			$this->_initFilter();
		$this->afterProcess();
	}
	
	protected function beforProcess() {

	}
	
	function process($request, $response) {
		$config = W::getInstance('WSystemConfig');
		/* ��ʼ��һ��Ӧ�÷����� */
		$applicationController = new WWebApplicationController();
		$router = $applicationController->createRouter($config);
		$router->doParser($response, $request);
		
		
		$applicationController->destory();
	}
	
	protected function afterProcess() {
		if (defined('LOG_RECORD'))
			WLog::flush();
		restore_exception_handler();
	}
	
	protected function doPost($request, $response) {
		$this->process($request, $response);
	}
	
	protected function doGet($request, $response) {
		$this->process($request, $response);
	}
	
	/**
	 * ��ʼ������������������ִ�о��ָ��һ�����������
	 */
	private function _initFilter() {
		WFilterFactory::getFactory()->setExecute(array(
			get_class($this), 
			'process'
		), $this->reuqest, $this->response);
		$filter = WFilterFactory::getFactory()->create($this->config);
		if (is_object($filter))
			$filter->doFilter($this->reuqest, $this->response);
	}
	
	/**
	 * ��ʼ��ϵͳ������Ϣ
	 * @param array $config
	 */
	private function _initConfig($config) {
		$realPath = W::getSystemConfigPath();
		if (!file_exists($realPath))
			throw new Exception('SYS Excetion ��config file ' . $realPath . ' is not exists!!!');
		
		W::import($realPath);
		$sysConfig = W::getVar('sysConfig');
		$configObj = W::getInstance('WSystemConfig');
		$configObj->parse($sysConfig, (array) $config);
		$this->config = $configObj;
	}

}