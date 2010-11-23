<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-22
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import("WIND:core.base.impl.WindConfigImpl");
/**
 * ���������������ļ�ͬʱ���ɻ���
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WindConfigParser implements WindConfigImpl {
	private $defaultPath = '';//ȱʡ�������ļ�·��
	private $userAppPath = '';//�û������ļ�·��
	private $globalAppsPath = '';//ȫ��Ӧ������·��
	private $globalAppsConfig = '';//ȫ��Ӧ�������ļ�
	private $parserEngine = '';//����ʹ�õĽ�������
	private $configExt = array('php' , 'xml', 'properpoties', 'ini');//���õ����û����ø�ʽ
	private $encoding = 'gbk';
	/**
	 * ��ʼ��
	 * 
	 * @param $globalAppsPath
	 * @param $userAppPath
	 * @param $defaultPath
	 */
	public function __construct($globalAppsPath, $userAppPath, $defaultPath, $outputEncoding = 'gbk') {
		$this->setGlobalAppsPath($globalAppsPath);
		$this->setUserAppPath(realpath($userAppPath));
		$this->setDefaultPath(realpath($defaultPath));
		$outputEncoding && $this->encoding = $outputEncoding;
	}
	
	/**
	 * ����Ĭ�������ļ�·��
	 * @param $defaultPath the $defaultPath to set
	 * @author xiaoxia xu
	 */
	public function setDefaultPath($defaultPath) {
		(file_exists($defaultPath)) && $this->defaultPath = rtrim(rtrim($defaultPath, '/'), '\\');
	}

	/**
	 * �����û�Ӧ�õ������ļ�·��
	 * @param $userAppPath the $userAppPath to set
	 * @author xiaoxia xu
	 */
	public function setUserAppPath($userAppPath) {
		(file_exists($userAppPath)) && $this->userAppPath = rtrim(rtrim($userAppPath, '/'), '\\');
	}

	/**
	 * ����ȫ��Ӧ�������ļ�
	 * @param $globalAppsPath the $globalAppsPath to set
	 * @author xiaoxia xu
	 */
	public function setGlobalAppsPath($globalAppsPath) {
		$this->globalAppsConfig = $globalAppsPath;//���������ļ���λ��
		$this->globalAppsPath = rtrim(rtrim(dirname($globalAppsPath), '/'), '\\');
		
	}
	
	/**
	 * ��������ļ���
	 * ���Ƚ���ȫ��Ӧ�������ļ�����鵱ǰ������Ӧ���Ƿ��Ѿ�������
	 * ����Ѿ�������������ԭ�����ļ������ԭ�����ļ������ڣ����ȡĬ�������ļ�Ϊԭ�����ļ���
	 * 					���ԭ�����ļ��ѱ��޸ģ����ٴν���ԭ�����ļ���Ĭ�������ļ����ɻ��档
	 * 					���û���޸ģ���ֱ�ӷ��ػ����ļ�
	 * ��������ļ������ڣ����ȡȱʡ�������ļ���������Ƿ��ѱ��޸�
	 *        			���ԭ�����ļ��ѱ��޸ģ����ٴν���ԭ�����ļ���Ĭ�������ļ����ɻ��档
	 *        			���û���޸ģ���ֱ�ӷ��ػ����ļ�
	 */
	public function getConfig() {
		$oConfig = $this->isExist($this->userAppPath);
		$defaultConfig = $this->defaultPath . '/wind_config.xml';
		if ($oConfig == null) $oConfig = $defaultConfig;
		//��������ļ����ڲ���ԭ�ļ�û�и��£���ֱ�Ӷ�ȡ����
		if (($path = $this->isCached()) && is_file($path)) {
			if (is_file($oConfig) && (filemtime($oConfig) < filemtime($path)) && filemtime($defaultConfig) < filemtime($path)) {
				include($path);
				return $config;
			}
		}
		return $this->parserConfig();
	}
	
	/**
	 * ���������ļ�
	 * 
	 * @return array �������յ�������Ϣ
	 */
	public function parserConfig() {
		$uConfig = $dConfig = array();
		$defaultConfigP = $this->isExist($this->defaultPath);
		$default = $this->defaultPath . '/wind_config.xml';
		$this->parserEngine = 'XML';
		($default) && $dConfig = $this->switchParser($default, false);//���ȱʡ�������ļ�
		$oConfigP = $this->isExist($this->userAppPath);
		($oConfigP) && $uConfig = $this->switchParser($oConfigP);
		return $this->mergeConfig($dConfig, $uConfig);
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
		$globalConfigP = $this->globalAppsPath . '/config.php';
		$sysConfig = array();
		if (is_file($globalConfigP)) {
			include($globalConfigP);
		}
		$appName = isset($config[WindConfigImpl::APPNAME]) ? $config[WindConfigImpl::APPNAME] : $this->getAppName();
		$sysConfig[$appName] = $config;
		$this->writeover($globalConfigP, "<?php\r\n \$sysConfig = " . $this->varExport($sysConfig) . ";\r\n?>");
		return $sysConfig;
	}
	/**
	 * �ṩһ���ӿڣ����ڻ��ȫ��Ӧ�������ļ�����
	 * @return array 
	 */
	public function getAppsConfig() {
		$globalConfigP = $this->globalAppsPath . '/config.php';
		include($globalConfigP);
		return $sysConfig;
	}
	
	/**
	 * ���������ļ�
	 * ������WindConfigImpl�е����ö������������кϲ�/����
	 * ���Ӧ��������û���������ѡ���ʹ��Ĭ�������е�ѡ��
	 * �������Ҫ�ϲ������ȱʡ����û���������кϲ�
	 * 
	 * @param array $defaultConfig Ĭ�ϵ������ļ�
	 * @param array $appConfig Ӧ�õ������ļ�
	 * @return array ���ش����������ļ�
	 */
	public function mergeConfig($defaultConfig, $appConfig) {
		if (count($appConfig) == 0) $appConfig = $defaultConfig;
		$app = $appConfig[WindConfigImpl::APP];
		(!isset($app[WindConfigImpl::APPNAME]) || $app[WindConfigImpl::APPNAME] == '') && $app[WindConfigImpl::APPNAME] = $this->getAppName();
		(!isset($app[WindConfigImpl::APPROOTPATH]) || $app[WindConfigImpl::APPROOTPATH] == '') && $app[WindConfigImpl::APPROOTPATH] = $this->userAppPath;
		(isset($app[WindConfigImpl::APPCONFIG])) && $app[WindConfigImpl::APPCONFIG] = $this->userAppPath . $app[WindConfigImpl::APPCONFIG];
		$app[WindConfigImpl::APPCONFIG] = $app[WindConfigImpl::APPCONFIG]. '/' . $app[WindConfigImpl::APPNAME] . '_config.php';
		foreach ($defaultConfig as $key => $value) {
			$_merge = (strpos(WindConfigImpl::MERGEARRAY, ',') === false) ? array(WindConfigImpl::MERGEARRAY) : explode(',', WindConfigImpl::MERGEARRAY);
			if (in_array($key, $_merge) && $appConfig[$key]) {
				!is_array($value) && $value = array($value);
				!is_array($appConfig[$key]) && $appConfig[$key] = array($appConfig[$key]);
				$defaultConfig[$key] = array_merge($value, $appConfig[$key]);
			} else {
				($appConfig[$key]) && $defaultConfig[$key] = $appConfig[$key];
			}
		}
		$defaultConfig[WindConfigImpl::APP] = $app;
		$this->writeover($app[WindConfigImpl::APPCONFIG], "<?php\r\n \$config = " . $this->varExport($defaultConfig) . ";\r\n?>");
		$this->addAppsConfig($app);
		return $defaultConfig;
	}
	
	/**
	 * ѡ����ʵĽ�����
	 * 
	 * @param string $configPath
	 * @param boolean $isCheck �Ƿ���Ҫ�������ü��
	 * @return array ���ؽ����Ľ��
	 */
	public function switchParser($configPath, $isCheck = true) {
		switch($this->parserEngine) {
			case 'XML':
				return $this->parserXML($configPath, $this->encoding, $isCheck);
				break;
			case 'PHP':
				include($configPath);
				return $config;
				break;
			default:
				throw new WindException('The Config ' . $configPath . ' cannot parsered because of the error format!');
				break;
		}
	}
	
	/**
	 * ����XML��ʽ�������ļ�
	 * 
	 * @param string $configPath  �������ļ���·��
	 * @param boolean $isCheck �Ƿ���Ҫ�������
	 * @param string $encoding  ����ı���
	 */
	public function parserXML($configPath, $encoding, $isCheck = true) {
		L::import('WIND:core.WindXMLConfig');
	    $xml = new WindXMLConfig();
	    $xml->setXMLFile($configPath);
	    $xml->setOutputEncoding($encoding);
	    return $xml->getResult($isCheck);
	}
	
	public function parserProperties() {
		
	}
	public function parserIni() {
		
	}
	
	/**
	 * ��õ�ǰӦ�õ����֣�����·�������һ���ļ���
	 * 
	 * @return string ���ط��ϵ���
	 */
	private function getAppName() {
		$path = trim($this->userAppPath, '/');
		$path = trim($this->userAppPath, '\\');
		$_tmp = explode('/', $this->userAppPath);
		(!$_tmp) && $_tmp = explode('\\', $this->userAppPath);
		$pos = count($_tmp)-1;
		if ($_tmp[$pos]) return strtoupper($_tmp[$pos]);
	}
	
	/**
	 * �ж��ļ��Ƿ���ڣ���������򷵻ظ������ļ�������·�������������ý������������ͣ���������ڷ���NULL
	 * 
	 * @param string $path
	 * @return mixed null | string 
	 */
	private function isExist($path) {
		foreach ($this->configExt as $ext) {
			$_temp = realpath($path . '/' . 'config.' . $ext);
			if (is_file($_temp)) {
				$this->parserEngine = strtoupper($ext);
				return $_temp;
			} 
		}
		return null;
	}
	
	/**
	 * �ж��Ƿ��Ѿ�������
	 * 
	 * @return string ���ػ���·�� | '';
	 */
	private function isCached() {
		if (!is_file($this->globalAppsConfig)) return '';
		include($this->globalAppsConfig);
		$appConfig = array();
		foreach ($sysConfig as $appName => $config) {
			if (isset($config[WindConfigImpl::APPROOTPATH]) && $config[WindConfigImpl::APPROOTPATH] == $this->userAppPath) {
				$appConfig = $sysConfig[$appName];
				$appConfig[WindConfigImpl::APPNAME] = $appName;
				break;
			}
		}
		if (isset($appConfig[WindConfigImpl::APPCONFIG])) return $appConfig[WindConfigImpl::APPCONFIG];
		else return '';
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
			case 'string' :
				return "'" . str_replace(array("\\", "'"), array("\\\\", "\'"), $input) . "'";
			case 'array' :
				$output = "array(\r\n";
				foreach ($input as $key => $value) {
					$output .= $indent . "\t" . $this->varExport($key, $indent . "\t") . ' => ' . $this->varExport($value, $indent . "\t");
					$output .= ",\r\n";
				}
				$output .= $indent . ')';
				return $output;
			case 'boolean' :
				return $input ? 'true' : 'false';
			case 'NULL' :
				return 'NULL';
			case 'integer' :
			case 'double' :
			case 'float' :
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
		$tmparray = array('://',"\0");
		$tmparray[] = '..';
		if (str_replace($tmparray, '', $tmpname) != $tmpname) exit('forbidden');
		
		if (!touch($fileName)) throw WindException('The path "' . $fileName . '" is unwritable!');
		$handle = fopen($fileName, $method);
		$ifLock && flock($handle, LOCK_EX);
		$writeCheck = fwrite($handle, $data);
		$method == 'rb+' && ftruncate($handle, strlen($data));
		fclose($handle);
		$ifChmod && @chmod($fileName, 0777);
		return $writeCheck;
	}
}