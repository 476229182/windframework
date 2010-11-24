<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-6
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
//L::import('WIND:');

/**
 * ������Ϣ����
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
class WindSystemConfig extends WindConfig {
	private $globalConfig = array();
	private $config = array();
	private static $instance = null;
	
	/**
	 * ���������ʽ�Ľ���
	 * @param array $configSystem
	 * @param array $configCustom
	 */
	public function _parse($configSystem, $configCustom = array()) {
		if (!is_array($configSystem) || !is_array($configCustom)) throw new Exception('the format of config file is error!!!');
		
		if (empty($configSystem)) throw new Exception('system config file is not exists!!!');
		
		$this->config = array_merge($configSystem, $configCustom);
		$this->system = $configSystem;
		$this->custom = $configCustom;
	}
	
	/**
	 * ��������ʽ�Ľ���
	 * @param array $configSystem  ȫ�ֻ�������
	 * @param string $current  ��ǰӦ�õ�����
	 */
	public function parse($configSystem, $current) {
		if (!is_array($configSystem) || !$current) 
			throw new Exception('the format of config file is error!!!');
		$this->system = $configSystem;
		if (!$configSystem[$current]) throw new Exception('the current app name is error!!!');
		if (is_file($configSystem[$current]['appConfig'])) {
			include ($configSystem[$current]['appConfig']);
			$this->config = $config;
		} else {
			include (L::getRealPath($configSystem[$current]['appConfig']));
			$this->config = $config;
		}
	}
	
	/**
	 * ����������ȡ����Ӧ������
	 * @param string $configName
	 * @return string
	 */
	public function getConfig($configName) {
		if ($configName && isset($this->config[$configName])) return $this->config[$configName];
	}
	
	/**
	 * ���ع�����
	 * @param string $name
	 */
	public function getFiltersConfig($name = '') {
		if (isset($this->config['filters'])) return !$name ? $this->config['filters'] : ($this->config['filters'][$name] ? $this->config['filters'][$name] : '');
	}
	
	/**
	 * ����Ӧ��������Ϣ��û���κ�Ӧ��������Ϣ�򷵻�''
	 * @param string $name
	 * @return string
	 */
	public function getModulesConfig($name = '', $default = null) {
		if (!isset($this->config['modules'])) return $default;
		if (!$name) return $this->config['modules'];
		
		return $this->config['modules'][$name] ? $this->config['modules'][$name] : $default;
	}
	
	/**
	 * ���·��������Ϣ
	 * 
	 * @param string $name
	 * @return string|null|array
	 */
	public function getRouterConfig($name = '', $default = null) {
		if (!isset($this->config['router'])) return $default;
		if (!$name) return $this->config['router'];
		
		return isset($this->config['router'][$name]) ? $this->config['router'][$name] : $default;
	}
	
	/**
	 * ���·�ɽ�����������
	 * 
	 * @param string $name
	 * @return array|null
	 */
	public function getRouterRule($name = '', $default = null) {
		if ($name) {
			$name = $name . 'Rule';
			return isset($this->config[$name]) ? $this->config[$name] : $default;
		} else
			throw new WindException('');
	}
	
	/**
	 * ����·�ɽ���������
	 * 
	 * @return string
	 */
	public function getRouterParser($name = '', $default = null) {
		if (!isset($this->config['routerParser'])) return $default;
		if (!$name) return $this->config['routerParser'];
		
		return $this->config['routerParser'][$name] ? $this->config['routerParser'][$name] : $default;
	}
	
	/**
	 * @return WindSystemConfig
	 */
	static public function getInstance() {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}

}