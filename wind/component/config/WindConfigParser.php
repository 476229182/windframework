<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-22
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import("WIND:component.config.base.IWindConfig");
L::import('WIND:utility.Common');
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
	private $defaultConfig = 'wind_config';
	
	private $userAppConfigPath;
	private $userAppConfig = 'config';
	
	private $globalAppsPath = COMPILE_PATH;
	private $globalAppsConfig = 'config.php';
	
	private $configParser = null;
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
	 * ��ʼ�������ļ�������
	 * @access private
	 * @param string $parser
	 */
	private function initParser() {
		switch (strtoupper($this->parserEngine)) {
			case 'XML':
				L::import("WIND:component.config.WindXMLConfig");
				$this->configParser = new WindXMLConfig($this->encoding);
				break;
			default:
				throw new WindException('init config parser error.');
				break;
		}
	}
	
	/**
	 * �����Ƿ���Ҫִ�н�������
	 * 
	 * �����debugģʽ���򷵻�false,����ÿ�ζ����н���
	 * �������debugģʽ�������ж��Ƿ������˻���ģʽ
	 *    ���û�����û����򷵻�false,���н�����
	 * ��������˻���ģʽ�����жϸ�Ӧ���Ƿ��Ѿ�������
	 *    �����ǰ����Ӧ��û�б����������򷵻�false,���н���
	 * �����ǰӦ�ý����������жϽ����������ļ��Ƿ����
	 *    ����ý����������ļ������ڣ��򷵻�false,ִ�н���
	 * ���򷵻�true,ֱ�Ӷ�ȡ����
	 * 
	 * @return boolean false:��Ҫ���н����� true������Ҫ���н�����ֱ�Ӷ�ȡ�����ļ�
	 */
	private function isCompiled() {
		if (IS_DEBUG) return false;
		if (!W::ifCompile()) return false;
		if (!($app = W::getApps())) return false;
		if (!is_file($app[IWindConfig::APP_CONFIG])) return false;
		return true;
	}
	
	/**
	 * @return mixed boolean |multitype:
	 */
	private function fetchConfigExit($rootPath) {
		$rootPath = realpath($rootPath);
		foreach ($this->configExt as $ext) {
			if (is_file($rootPath . D_S . $this->userAppConfig . '.' . $ext)) {
				$this->parserEngine = $ext;
				return realpath($rootPath . D_S . $this->userAppConfig . '.' . $ext);
			}
		}
		return '';
	}
	
	/**
	 * @param WindHttpRequest $request
	 */
	public function parser($request) {
		$rootPath = dirname($request->getServer('SCRIPT_FILENAME'));
		if ($this->isCompiled()) {
			$app = W::getApps();
			return @include $app[IWindConfig::APP_CONFIG];
		}
		$defaultConfigPath = $this->defaultPath . D_S . $this->defaultConfig . '.' . $this->parserEngine;
		list($defaultConfig, $this->defaultGAM) = $this->executeParser(realpath($defaultConfigPath));
		list($userConfig, $this->userGAM) = $this->executeParser($this->userAppConfigPath);
		$userConfig = $this->mergeConfig($defaultConfig, $userConfig);
		$userConfig[IWindConfig::APP] = $this->getAppInfo($rootPath, $userConfig);
		
		W::setApps($userConfig[IWindConfig::APP][IWindConfig::APP_NAME], $userConfig[IWindConfig::APP]);
		W::setCurrentApp($userConfig[IWindConfig::APP][IWindConfig::APP_NAME]);
		$this->updateGlobalCache($userConfig);
		
		Common::writeover($userConfig[IWindConfig::APP][IWindConfig::APP_CONFIG], "<?php\r\n return " . Common::varExport($userConfig) . ";\r\n?>");
		return $userConfig;
	}
	
	/**
	 * @param rootPath
	 * @param userConfig
	 */
	private function getAppInfo($rootPath, $userConfig) {
		$app = isset($userConfig[IWindConfig::APP]) ? $userConfig[IWindConfig::APP] : array();
		if (!isset($app[IWindConfig::APP_NAME]) || $app[IWindConfig::APP_NAME] == '' || $app[IWindConfig::APP_NAME] == 'default') {
			$app[IWindConfig::APP_NAME] = $this->getAppName($rootPath);
		}
		if (!isset($app[IWindConfig::APP_ROOTPATH]) || $app[IWindConfig::APP_ROOTPATH] == '' || $app[IWindConfig::APP_ROOTPATH] == 'default') {
			$app[IWindConfig::APP_ROOTPATH] = realpath($rootPath);
		}
		$_file = D_S . $app[IWindConfig::APP_NAME] . '_config.php';
		if (!isset($app[IWindConfig::APP_CONFIG]) || $app[IWindConfig::APP_CONFIG] == '') {
			$app[IWindConfig::APP_CONFIG] = $this->globalAppsPath . $_file;
		} else {
			$app[IWindConfig::APP_CONFIG] = $this->getRealPath($app[IWindConfig::APP_ROOTPATH], $app[IWindConfig::APP_CONFIG]) . $_file;
		}
		return $app;
	}
	
	/**
	 * ����һ�������ļ�·��������·����Ϣ��ʼ�����ý�������������������
	 * �������ʽ�������ý������
	 * 
	 * @param string $configFile
	 * @return array
	 */
	private function executeParser($configFile) {
		if (!$configFile) return array(null, null);
		if ($this->configParser === null) {
			$this->initParser();
		}
		$this->configParser->loadFile($configFile);
		$this->configParser->parser();
		return array($this->configParser->getResult(), $this->configParser->getGAM());
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
	private function mergeConfig($defaultConfig, $appConfig) {
		if (!$appConfig) return $defaultConfig;
		$_merge = $this->defaultGAM[IWindConfig::ISMERGE];
		$hasInDefaultConfigKeys = array();
		foreach ($appConfig as $key => $value) {
			if (in_array($key, $_merge) && isset($defaultConfig[$key])) {
				!is_array($value) && $value = array($value);
				!is_array($defaultConfig[$key]) && $defaultConfig[$key] = array($defaultConfig[$key]);
				$appConfig[$key] = array_merge($value, $defaultConfig[$key]);
			}
			(!isset($defaultConfig[$key])) && $hasInDefaultConfigKeys[] = $key;
		}
		$appConfigKeys = array_keys($appConfig);
		$_notInAppConfig = array_diff(array_keys($defaultConfig), $hasInDefaultConfigKeys);
		foreach ($_notInAppConfig as $key) {
			if (in_array($key, $appConfigKeys)) continue;
			$appConfig[$key] = $defaultConfig[$key];
		}
		return $appConfig;
	}
	
	/**
	 * ��ȫ�����ݴ��������ҳ�������ӵ������ļ���
	 * ����Ӧ�õ��������merge��ȫ��Ӧ��������
	 * ��ǰӦ�ã����û������Ӧ�õ����֣��򽫵�ǰ���ʵ����һ��λ������ΪӦ������
	 * ����ʹ�����������úõ�Ӧ�����֡�
	 * ��ӻ���
	 * @param array $config
	 */
	private function updateGlobalCache($config) {
		$_global = $this->defaultGAM[IWindConfig::ISGLOBAL];
		if (count($_global) == 0) return false;
		$_globalArray = array();
		foreach ($_global as $key) {
			if (!isset($config[$key])) continue;
			$_temp = $config[$key];
			if ($_temp['name']) $key = $_temp['name'];
			$_globalArray[$key] = $_temp;
		}
		$globalConfigPath = $this->globalAppsPath . D_S . $this->globalAppsConfig;
		$sysConfig = array();
		if (is_file($globalConfigPath)) {
			$sysConfig = @include ($globalConfigPath);
		}
		$sysConfig = array_merge($sysConfig, $_globalArray);
		Common::writeover($globalConfigPath, "<?php\r\n return " . Common::varExport($sysConfig) . ";\r\n?>");
		return true;
	}
	
	/**
	 * ͨ�������ռ䷵����ʵ·��
	 * @param string $rootPath ·��
	 * @param string $oPath ��Ҫ���ҵ��ļ�·��
	 */
	private function getRealPath($rootPath, $oPath) {
		if (strpos(':', $oPath) === false) {
			return L::getRealPath($oPath . '.*', '', '', $rootPath);
		} else {
			return L::getRealPath($oPath . '.*');
		}
	}
	
	/**
	 * ��õ�ǰӦ�õ����֣�����·�������һ���ļ���
	 * 
	 * @return string ���ط��ϵ���
	 */
	private function getAppName($rootPath) {
		if ($this->currentApp != '') return $this->currentApp;
		$path = rtrim(rtrim($rootPath, '\\'), '/');
		$pos = (strrpos($path, '\\') === false) ? strrpos($path, '/') : strrpos($path, '\\');
		return substr($path, -(strlen($path) - $pos - 1));
	}
}