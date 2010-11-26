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
	private static $instance = null;
	
	protected function __construct() {
		parent::__construct();
		$this->_initConfig();
	}
	
	public function run() {
		$this->beforProcess();
//		$filters = C::getConfig(IWindConfig::FILTERS);
		if (!empty($filters)) {
			$this->_initFilter();
		} else
			parent::run();
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
		L::import('WIND:component.filter.WindFilterFactory');
		WindFilterFactory::getFactory()->setExecute(array(get_class($this), 'process'), $this->request, $this->response);
		$filter = WindFilterFactory::getFactory()->create();
		if (is_object($filter)) $filter->doFilter($this->request, $this->response);
	}
	
	/**
	 * ��ʼ��ϵͳ������Ϣ
	 * 
	 * @param array $config
	 */
	private function _initConfig() {
		L::import('WIND:component.config.WindConfigParser');
		$configParser = new WindConfigParser();
		$appConfig = $configParser->parser($this->request);
		//TODO
		$currentApp = $appConfig[IWindConfig::APP];
		W::setApps($currentApp[IWindConfig::APP_NAME], $currentApp);
		W::setCurrentApp($currentApp[IWindConfig::APP_NAME]);
		
		C::init($appConfig);
	}
	
	/**
	 * @return WindFrontController
	 */
	static public function &getInstance() {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}
}