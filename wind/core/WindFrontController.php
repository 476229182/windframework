<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-10-27
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license 
 */

L::import('WIND:core.base.WindServlet');
L::import('WIND:component.exception.WindException');
L::import('WIND:component.filter.WindFilterFactory');
L::import('WIND:core.WindSystemConfig');
L::import('WIND:core.WindWebApplication');
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
class WindFrontController extends WindServlet {
	private $config = null;
	private static $instance = null;
	
	protected function __construct($config = array()) {
		parent::__construct();
		echo '<pre/>';
		$this->_initConfig($config);
		exit();
	}
	
	public function run() {
		if ($this->config === null) throw new WindException('init system config failed!');
		$this->beforProcess();
		$filters = $this->config->getConfig('filters');
		if (!class_exists('WindFilterFactory') || empty($filters))
			parent::run();
		else
			$this->_initFilter();
		$this->afterProcess();
	}
	
	protected function beforProcess() {

	}
	
	function process($request, $response) {
		/* ��ʼ��һ��Ӧ�÷����� TODO�ع��˴��� */
		$applicationController = new WindWebApplication();
		$applicationController->init();
		
		$applicationController->processRequest($request, $response);
		
		$applicationController->destory();
	}
	
	protected function afterProcess() {
		if (defined('LOG_RECORD')) WindLog::flush();
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
		WindFilterFactory::getFactory()->setExecute(array(get_class($this), 'process'), $this->reuqest, $this->response);
		$filter = WindFilterFactory::getFactory()->create($this->config);
		if (is_object($filter)) $filter->doFilter($this->reuqest, $this->response);
	}
	
	/**
	 * ��ʼ��ϵͳ������Ϣ
	 * 
	 * @param array $config
	 */
	private function _initConfig($config) {
		$configParser = new WindConfigParser($this->request);
		$appName = $configParser->parser();//ִ�н���
		W::parserConfig();//����ȫ��apps
		W::setCurrentApp($appName);
		$configObj = WindSystemConfig::getInstance();
		$configObj->parse((array) W::getSystemConfig(), W::getCurrentApp()); 
//		$configObj->parse((array) W::getSystemConfig(), (array) $config);
		$this->config = $configObj;
	}
	
	/**
	 * @param array $config
	 * @return WindFrontController
	 */
	static public function &getInstance(array $config = array()) {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class($config);
		}
		return self::$instance;
	}
}