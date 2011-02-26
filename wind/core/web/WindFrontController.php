<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-10-27
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license 
 */

L::import('WIND:core.AbstractWindServer');
/**
 * 抽象的前端控制器接口，通过集成该接口可以实现以下职责
 * 
 * 职责定义：
 * 接受客户请求
 * 处理请求
 * 向客户端发送响应
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindFrontController extends AbstractWindServer {

	const WIND_APPLICATION = 'WIND:core.web.WindWebApplication';

	const WIND_CONFIG = 'WIND:core.config.WindSystemConfig';

	const WIND_FACTORY = 'WIND:core.factory.WindComponentFactory';

	const WIND_ERRORHANDLER = 'WIND:core.web.WindErrorHandler';

	protected $windSystemConfig = null;

	protected $windFactory = null;

	protected $windErrorHandler = null;

	/**
	 * Enter description here ...
	 * 
	 * @param WindConfig $windConfig
	 * @param WindFactory $windFactory
	 */
	public function __construct($appName, $config = array()) {
		parent::__construct();
		$this->initWindConfig($appName, $config);
		$this->initWindFactory();
	}

	/**
	 * Enter description here ...
	 */
	protected function initWindFactory() {
		$classesDefinition = $this->getWindConfig()->getFactory(WindSystemConfig::WEB_APP_FACTORY_CLASS_DEFINITION);
		$classesDefinitions = $this->getWindConfig()->getConfig($classesDefinition);
		$factoryClass = L::import($this->getWindConfig()->getFactory(WindSystemConfig::CLASS_PATH));
		if (!class_exists($factoryClass)) {
			throw new WindException($factoryClass, WindException::ERROR_CLASS_NOT_EXIST);
		}
		
		$this->windFactory = new $factoryClass($classesDefinitions);
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $appName
	 * @param string $config
	 */
	protected function initWindConfig($appName, $config) {
		L::import('WIND:core.config.parser.WindConfigParser');
		$configParser = new WindConfigParser();
		$this->windSystemConfig = new WindSystemConfig($config, $configParser, ($appName ? $appName : 'default'));
		L::register($this->getWindConfig()->getRootPath(), $this->getWindConfig()->getAppName());
	}

	/* (non-PHPdoc)
	 * @see wind/core/base/WindServer#process()
	 */
	protected function process(WindHttpRequest $request, WindHttpResponse $response) {
		try {
			$this->getWindFactory()->request = $request;
			$this->getWindFactory()->response = $response;
			$request->setAttribute(self::WIND_CONFIG, $this->windSystemConfig);
			$request->setAttribute(self::WIND_FACTORY, $this->windFactory);
			
			$appName = $this->getWindConfig()->getAppClass();
			$application = $this->getWindFactory()->getInstance($appName);
			if (null === $application) {
				throw new WindException('application', WindException::ERROR_CLASS_NOT_EXIST);
			}
			
			$this->getWindFactory()->application = $application;
			
			//TODO change args of application->processRequest as an component object
			$filterChain = $this->getFilterChain();
			$filterChain->setCallBack(array($application, 'processRequest'), array($request, $response));
			$filterChain->getHandler()->handle($request, $response);
		
		} catch (WindException $exception) {
			echo $exception->getMessage();
		}
	}

	/**
	 * @return WindFilterChain
	 */
	private function getFilterChain() {
		$filterChainPath = $this->getWindConfig()->getFilters(WindSystemConfig::CLASS_PATH);
		return $this->getWindFactory()->createInstance($filterChainPath, array($this->getWindConfig()->getFilters()));
	}

	/* (non-PHPdoc)
	 * @see AbstractWindServer::beforeProcess()
	 */
	protected function beforeProcess(WindHttpRequest $request, WindHttpResponse $response) {
		L::import('WIND:core.web.WindErrorHandler');
		set_error_handler(array(new WindErrorHandler(), 'errorHandle'));
	}

	/* (non-PHPdoc)
	 * @see AbstractWindServer::afterProcess()
	 */
	protected function afterProcess(WindHttpRequest $request, WindHttpResponse $response) {
		restore_error_handler();
	}

	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @throws Exception
	 */
	protected function doPost(WindHttpRequest $request, WindHttpResponse $response) {
		$this->process($request, $response);
	}

	/**
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 * @throws Exception
	 */
	protected function doGet(WindHttpRequest $request, WindHttpResponse $response) {
		$this->process($request, $response);
	}

	/**
	 * @return WindSystemConfig $windConfig
	 */
	public function getWindConfig() {
		if ($this->windSystemConfig instanceof WindConfig)
			return $this->windSystemConfig;
		else
			throw new WindException(get_class($this) . '->windSystemConfig', WindException::ERROR_CLASS_TYPE_ERROR);
	}

	/**
	 * @return WindFactory $windFactory
	 */
	public function getWindFactory() {
		if ($this->windFactory instanceof WindFactory)
			return $this->windFactory;
		else
			throw new WindException(get_class($this) . '->windFactory', WindException::ERROR_CLASS_TYPE_ERROR);
	}

}