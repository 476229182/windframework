<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-6
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * ������Ϣ����
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
class WindSystemConfig {
	private $config = array();
	private static $instance = null;
	
	/**
	 * ��ʼ�������ļ�����
	 * @param array $configSystem
	 */
	public function __construct($configSystem) {
		if (empty($configSystem)) throw new Exception('system config file is not exists!!!');
		$this->config = $configSystem;
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
	 * ������������·��ȡ����Ӧ��������Ϣ
	 * 
	 * $var = array(
	 * 'templates' => array(
	 * 'template' => array(
	 * 'templateDir' => '/date';
	 * 'templateCache'  => '/cache';
	 * )
	 * ))
	 * �������templateDir�µ�ֵ��
	 * �����µ���-WindSystemConfig::getConfigPath('templates', 'template', 'templateDir')
	 * �����·����ĳһ���ڵ㲻���ڣ��򷵻�''
	 * @param mixed
	 * @return mixed
	 */
	public function getConfigPath() {
		$vars = func_get_args();
		$current = $this->config;
		foreach ($vars as $name) {
			if (isset($current[$name])) $current = $current[$name];
			return '';
		}
		return $current;
	}
	
	/**
	 * ���ع�����
	 * @param string $name
	 */
	public function getFiltersConfig($name = '') {
		if (!$this->config[IWindConfig::FILTERS]) return array();
		if ($name == '') return $this->config[IWindConfig::FILTERS];
		$filters = $this->config[IWindConfig::FILTERS];
		foreach ($filters as $one) {
			if ($one[IWindConfig::FILTERNAME] == $name) return $one;
		}
	}
	
	/**
	 * ����Ӧ��������Ϣ��û���κ�Ӧ��������Ϣ�򷵻�''
	 * @param string $name
	 * @return string
	 */
	public function getModulesConfig($name = '', $default = null) {
		if (!isset($this->config['app'])) return $default;
		if (!$name) return $this->config['app'];
		
		return $this->config['app'][$name] ? $this->config['app'][$name] : $default;
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
	 * @return -WindSystemConfig
	 */
	static public function getInstance($config = '') {
		if (self::$instance === null) {
			$class = __CLASS__;
			self::$instance = new $class($config);
		}
		return self::$instance;
	}

}