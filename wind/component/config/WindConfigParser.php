<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-22
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import("WIND:core.base.IWindConfig");

/**
 * �����ļ�������
 * �����ļ���ʽ������3�и�ʽ��xml,properties,ini
 * 
 * ����Ĭ�Ϸ���Ӧ�ó����·�����棬�������ɵ����û����ļ�Ĭ�Ϸ��ڡ�COMPILE_PATH������
 * �����$userAppConfig���ļ����ж����˽������ɵ������ļ����·��������ڸ�·������
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WindConfigParser implements IWindConfig {
	private $defaultPath = WIND_PATH;
	private $defaultConfig = 'wind_config.xml';
	
	private $userAppConfig = 'config.xml';
	
	private $globalAppsPath = COMPILE_PATH;
	private $globalAppsConfig = 'config.php';
	
	private $parser = null;
	private $parserEngine = 'xml';
	private $configExt = array('xml', 'properpoties', 'ini');
	
	private $encoding = 'gbk';
	private $defaultGAM = array();
	private $userGAM = array();
	
	private $currentApp = '';
	
	/**
	 * ��ʼ��
	 * @param String $outputEncoding	//������Ϣ
	 */
	public function __construct($outputEncoding = 'gbk') {
		$this->currentApp = W::getCurrentApp();
		if ($outputEncoding) $this->encoding = $outputEncoding;
	}
	
	/**
	 * @param WindHttpRequest $request  //������Ϣ
	 */
	public function parser($request) {
		$rootPath = dirname($request->getServer('SCRIPT_FILENAME'));
		if ($this->isCompiled()) {
			
		} else {
			$userConfigPath = $rootPath . D_S . $this->userAppConfig;
			$userConfig = $this->_parser($userConfigPath);
			
			$defaultConfigPath = $this->defaultPath . D_S . $this->defaultConfig;
			$defaultConfig = $this->_parser($defaultConfigPath);
		}
		
	}
	
	/**
	 * ����һ�������ļ�·��������·����Ϣ��ʼ�����ý�������������������
	 * �������ʽ�������ý������
	 * 
	 * @param string $configFile
	 * @return array
	 */
	private function _parser($configFile) {
		list(, $fileName, $ext, $realPath) = L::getRealPath($configFile, true);
		if (!$realPath) return array();
		if ($this->parser === null) {
			$this->initParser($ext);
			$this->parser->setOutputEncoding($this->encoding);
		}
		$this->parser->setXMLFile($realPath);
		$this->parser->parser();
		return $this->parser->getResult();
	}
	
	/**
	 * �����Ƿ���Ҫִ�н�������
	 * ���Compile�ļ���Ϊ������򲻿�д�򷵻�false
	 * ���config.php�ļ��������򷵻�false
	 * �������app��Ϣ�������򷵻�false
	 * �����ǰapp�������ļ��������򷵻�false
	 */
	private function isCompiled() {

	}
	
	/**
	 * ���������ļ�
	 * ������IWindConfig�е����ö������������кϲ�/����
	 * ���Ӧ��������û���������ѡ���ʹ��Ĭ�������е�ѡ��
	 * �������Ҫ�ϲ������ȱʡ����û���������кϲ�
	 * 
	 * @param array $defaultConfig Ĭ�ϵ������ļ�
	 * @param array $appConfig Ӧ�õ������ļ�
	 * @return array ���ش����������ļ�
	 */
	public function mergeConfig($defaultConfig, $appConfig) {
		if (count($appConfig) == 0) $appConfig = $defaultConfig;
		$app = $appConfig[IWindConfig::APP];
		(!isset($app[IWindConfig::APP_NAME]) || $app[IWindConfig::APP_NAME] == '') && $app[IWindConfig::APP_NAME] = $this->getAppName();
		(!isset($app[IWindConfig::APP_ROOTPATH]) || $app[IWindConfig::APP_ROOTPATH] == '') && $app[IWindConfig::APP_ROOTPATH] = realpath($this->userAppPath);
		$_file = '/' . $app[IWindConfig::APP_NAME] . '_config.php';
		if (!isset($app[IWindConfig::APP_CONFIG])) {
			$app[IWindConfig::APP_CONFIG] = $this->globalAppsPath . $_file;
		} else {
			$app[IWindConfig::APP_CONFIG] = $this->getRealPath($app[IWindConfig::APP_NAME], $app[IWindConfig::APP_ROOTPATH], $app[IWindConfig::APP_CONFIG]) . $_file;
		}
		$appConfig[IWindConfig::APP] = $app;
		
		$_merge = $this->getGAM(IWindConfig::MERGEATTR);
		$hasInDefaultConfigKeys = array();
		foreach ($appConfig as $key => $value) {
			if (in_array($key, $_merge) && isset($defaultConfig[$key])) {
				!is_array($value) && $value = array($value);
				!is_array($defaultConfig[$key]) && $defaultConfig[$key] = array($defaultConfig[$key]);
				$appConfig[$key] = array_merge($value, $defaultConfig[$key]);
			}
			(!isset($defaultConfig[$key])) && $hasInDefaultConfigKeys[] = $key;
		}
		//��Ӧ�������в�ȱʡ������䵽Ӧ�������У�
		$appConfigKeys = array_keys($appConfig);
		$_notInAppConfig = array_diff(array_keys($defaultConfig), $hasInDefaultConfigKeys);
		foreach ($_notInAppConfig as $key) {
			if (in_array($key, $appConfigKeys)) continue;
			$appConfig[$key] = $defaultConfig[$key];
		}
		$this->writeover($app[IWindConfig::APP_CONFIG], "<?php\r\n return " . $this->varExport($appConfig) . ";\r\n?>");
		$this->addGlobalArray($appConfig);
		return $appConfig;
	}
	
	private function getGAM($key) {
		$_tmp1 = isset($this->userGAM[$key]) ? $this->userGAM[$key] : array();
		$_tmp2 = isset($this->defaultGAM[$key]) ? $this->defaultGAM[$key] : array();
		if ($_tmp1 && $_tmp2) return array_merge($_tmp1, $_tmp2);
		if ($_tmp1) return $_tmp1;
		return $_tmp2;
	}
	
	/**
	 * ��ȫ�����ݴ��������ҳ�������ӵ������ļ���
	 * @param array $config
	 */
	private function addGlobalArray($config) {
		$_global = $this->getGAM(IWindConfig::GLOBALATTR);
		$_globalArray = array();
		foreach ($_global as $key) {
			isset($config[$key]) && $_globalArray[$key] = $config[$key];
		}
		$this->addAppsConfig($_globalArray);
		return true;
	}
	
	/**
	 * ����Ӧ�õ��������merge��ȫ��Ӧ��������
	 * ��ǰӦ�ã����û������Ӧ�õ����֣��򽫵�ǰ���ʵ����һ��λ������ΪӦ������
	 * ����ʹ�����������úõ�Ӧ�����֡�
	 * ��ӻ���
	 * 
	 * @param array $config  ��ǰӦ�õ�Ӧ��������Ϣ
	 * @return array �����޸ĺ��Ӧ��������Ϣ
	 */
	public function addAppsConfig($config) {
		$sysConfig = array();
		if (is_file($this->globalAppsConfig)) {
			include ($this->globalAppsConfig);
		}
		//�����ڣ��򴴽�
		$appName = isset($config[IWindConfig::APP_NAME]) ? $config[IWindConfig::APP_NAME] : $this->getAppName();
		$sysConfig = array($sysConfig, $config);
		$this->writeover($this->globalAppsConfig, "<?php\r\n return " . $this->varExport($sysConfig) . ";\r\n?>");
		return $sysConfig;
	}
	
	/**
	 * ��ʼ�������ļ�������
	 * @param string $parser
	 */
	private function initParser($parser = 'xml') {
		switch ($parser) {
			case 'XML':
				L::import("WIND:component.config.WindXMLConfig");
				$this->parser = new WindXMLConfig();
				break;
			default:
				throw new WindException('init config parser error.');
				break;
		}
	}
	
	/**
	 * ��õ�ǰӦ�õ����֣�����·�������һ���ļ���
	 * 
	 * @return string ���ط��ϵ���
	 */
	private function getAppName() {
		if ($this->currentApp != '') return $this->currentApp;
		$path = rtrim(rtrim($this->userAppPath, '\\'), '/');
		$pos = (strrpos($path, '\\') === false) ? strrpos($path, '/') : strrpos($path, '\\');
		return substr($path, -(strlen($path) - $pos - 1));
	}
	
	/**
	 * ��������Ϊ�ַ���
	 *
	 * @param mixed $input ����
	 * @param string $indent ����
	 * @return string
	 */
	public function varExport($input, $indent = '') {
		switch (gettype($input)) {
			case 'string':
				return "'" . str_replace(array("\\", "'"), array("\\\\", "\'"), $input) . "'";
			case 'array':
				$output = "array(\r\n";
				foreach ($input as $key => $value) {
					$output .= $indent . "\t" . $this->varExport($key, $indent . "\t") . ' => ' . $this->varExport($value, $indent . "\t");
					$output .= ",\r\n";
				}
				$output .= $indent . ')';
				return $output;
			case 'boolean':
				return $input ? 'true' : 'false';
			case 'NULL':
				return 'NULL';
			case 'integer':
			case 'double':
			case 'float':
				return "'" . (string) $input . "'";
		}
		return 'NULL';
	}
	
	/**
	 * д�ļ�
	 *
	 * @param string $fileName �ļ�����·��
	 * @param string $data ����
	 * @param string $method ��дģʽ
	 * @param bool $ifLock �Ƿ����ļ�
	 * @param bool $ifCheckPath �Ƿ����ļ����еġ�..��
	 * @param bool $ifChmod �Ƿ��ļ����Ը�Ϊ�ɶ�д
	 * @return bool �Ƿ�д��ɹ�
	 */
	public function writeover($fileName, $data, $method = 'rb+', $ifLock = true, $ifCheckPath = true, $ifChmod = true) {
		$tmpname = strtolower($fileName);
		$tmparray = array(':\/\/', "\0");
		$tmparray[] = '..';
		if (str_replace($tmparray, '', $tmpname) != $tmpname) return false;
		
		@touch($fileName);
		if (!$handle = @fopen($fileName, $method)) return false;
		$ifLock && flock($handle, LOCK_EX);
		$writeCheck = fwrite($handle, $data);
		$method == 'rb+' && ftruncate($handle, strlen($data));
		fclose($handle);
		$ifChmod && @chmod($fileName, 0777);
		return $writeCheck;
	}
}