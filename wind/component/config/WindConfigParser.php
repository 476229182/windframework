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
	 * ���compile�ļ���δ������򲻿�д�򷵻�false
	 * ���config.php�ļ��������򷵻�false
	 * �����ǰapp��Ϣ�������򷵻�false
	 * �����ǰapp�������ļ��������򷵻�false
	 */
	private function isCompiled() {
		if (!W::ifCompile()) return false;
		if (!W::getApps()) return false;
		$app = W::getApps();
		if (!is_file($app[IWindConfig::APP_CONFIG])) return false;
		$config = $this->fetchConfigExit($app[IWindConfig::APP_ROOTPATH]);
		if ($config == '') return false;
		
		$_configLastT = filemtime($config);
		$_cacheLastT = filemtime($app[IWindConfig::APP_CONFIG]);
		$defaultConfig = $this->defaultPath . D_S . $this->defaultConfig . '.' . $this->parserEngine;
		$_defaultConfigLastT = filemtime($defaultConfig);
		if ($_configLastT >= $_cacheLastT  || $_defaultConfigLastT >= $_cacheLastT) return false;
		return true;
	}

	/**
	 * 
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
	 * @param WindHttpRequest $request  //������Ϣ
	 */
	public function parser($request) {
		$rootPath = dirname($request->getServer('SCRIPT_FILENAME'));
		if ($this->isCompiled()) {
			$app = W::getApps();
			return @include $app[IWindConfig::APP_CONFIG];
		}
		$userConfigPath = $this->fetchConfigExit($rootPath);
		$defaultConfigPath = $this->defaultPath . D_S . $this->defaultConfig . '.' . $this->parserEngine;
		list($defaultConfig, $this->defaultGAM) = $this->execuseParser(realpath($defaultConfigPath));
		list($userConfig, $this->userGAM) = $this->execuseParser($userConfigPath);
		$empty = false;
		if (count($userConfig) == 0) {
			$userConfig = $defaultConfig;
			$empty = true;
		}
		if (isset($userConfig[IWindConfig::APP])) {
			$app = $userConfig[IWindConfig::APP];
			if (!isset($app[IWindConfig::APP_NAME]) || $app[IWindConfig::APP_NAME] == '' || $app[IWindConfig::APP_NAME] == 'default') {
			     $app[IWindConfig::APP_NAME] = $this->getAppName($rootPath);
			}
			if (!isset($app[IWindConfig::APP_ROOTPATH]) || $app[IWindConfig::APP_ROOTPATH] == '' || $app[IWindConfig::APP_ROOTPATH] == 'default') {
				$app[IWindConfig::APP_ROOTPATH] = realpath($rootPath);
			}
			$_file = '/' . $app[IWindConfig::APP_NAME] . '_config.php';
			if (!isset($app[IWindConfig::APP_CONFIG]) || $app[IWindConfig::APP_CONFIG] == '' ) {
				$app[IWindConfig::APP_CONFIG] = $this->globalAppsPath . $_file;
			} else {
				$app[IWindConfig::APP_CONFIG] = $this->getRealPath($app[IWindConfig::APP_NAME], $app[IWindConfig::APP_ROOTPATH], $app[IWindConfig::APP_CONFIG]) . $_file;
			}
			$userConfig[IWindConfig::APP] = $app;
		}
		return $this->mergeConfig($defaultConfig, $userConfig, $empty);
	}
	
	/**
	 * ����һ�������ļ�·��������·����Ϣ��ʼ�����ý�������������������
	 * �������ʽ�������ý������
	 * 
	 * @param string $configFile
	 * @return array
	 */
	private function execuseParser($configFile) {
		//list(, $fileName, $ext, $realPath) = L::getRealPath($configFile, true);
		if (!$configFile) return array();
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
	private function mergeConfig($defaultConfig, $appConfig, $flag = false) {
		if ($flag === false) {
			$_merge = $this->getGAM(IWindConfig::ISMERGE);
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
		}
		if (!isset($appConfig[IWindConfig::APP])) return $appConfig;
		Common::writeover($appConfig[IWindConfig::APP][IWindConfig::APP_CONFIG], "<?php\r\n return " . Common::varExport($appConfig) . ";\r\n?>");
		$this->updateGlobalCache($appConfig);
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
	 * ����Ӧ�õ��������merge��ȫ��Ӧ��������
	 * ��ǰӦ�ã����û������Ӧ�õ����֣��򽫵�ǰ���ʵ����һ��λ������ΪӦ������
	 * ����ʹ�����������úõ�Ӧ�����֡�
	 * ��ӻ���
	 * @param array $config
	 */
	private function updateGlobalCache($config) {
		if (!W::ifCompile()) return false;
		$_global = $this->getGAM(IWindConfig::ISGLOBAL);
		if (count($_global) == 0 ) return false;
		$_globalArray = array();
		foreach ($_global as $key) {
			isset($config[$key]) && $_globalArray[$key] = $config[$key];
		}
		$globalConfigPath = $this->globalAppsPath . D_S . $this->globalAppsConfig;
		$sysConfig = array();
		if (is_file($globalConfigPath)) {
			$sysConfig = @include ($globalConfigPath);
		}
		$sysConfig = (count($sysConfig) > 0) ? array_merge($sysConfig, $_globalArray) : $_globalArray;
		Common::writeover($globalConfigPath, "<?php\r\n return " . Common::varExport($sysConfig) . ";\r\n?>");
		return true;
	}
	
	/**
	 * ͨ�������ռ䷵����ʵ·��
	 * @param string $nameSpace Ĭ�ϵ������ռ�
	 * @param string $oPah ·��
	 * @param string $file ��Ҫ���ҵ��ļ�·��
	 */
	private function getRealPath($nameSpace, $rootPath, $oPah) {
		if (strpos(':', $oPah) === false) {
			return L::getRealPath($nameSpace . ':' . $oPah . '.*', '', '', $rootPath);
		} else {
			return L::getRealPath($oPah . '.*', '', '', $rootPath);
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