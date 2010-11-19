<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-7
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.request.base.impl.WindRequestImpl');
L::import('WIND:component.exception.WindException');

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindHttpRequest implements WindRequestImpl {
	
	/**
	 * ���ʵĶ˿ں�
	 * @var int
	 */
	private $_port = null;
	/**
	 * �ͻ���IP
	 * @var string
	 */
	private $_clientIp = null;
	
	/**
	 * ������Ϣ
	 * @var string
	 */
	private $_language = null;
	
	/**
	 * ·����Ϣ
	 * @var string
	 */
	private $_pathInfo = null;
	
	/**
	 * @var string
	 */
	private $_scriptUrl = null;
	
	/**
	 * @var string
	 */
	private $_requestUri = null;
	
	/**
	 * ����·����Ϣ
	 * @var string
	 */
	private $_baseUrl = null;
	private $_hostInfo = null;
	
	private static $_instance = null;
	
	/**
	 * ���������Ϣ
	 * @var array
	 */
	private $_params = array();
	
	/**
	 * �����������ĵ���ʵ��
	 * 
	 * @return WindHttpRequest
	 */
	static public function &getInstance() {
		if (self::$_instance === null) {
			$class = __CLASS__;
			self::$_instance = new $class();
		}
		return self::$_instance;
	}
	
	/**
	 * �������ƻ�÷�������ִ�л�����Ϣ,������Ʋ������򷵻�NULL
	 * 
	 * @param string|null $name
	 */
	public function getAttribute($name) {
		if (isset($_GET[$name]))
			return $_GET[$name];
		else if (isset($_POST[$name]))
			return $_POST[$name];
		else if (isset($_COOKIE[$name]))
			return $_COOKIE[$name];
		else if (isset($_REQUEST[$name]))
			return $_REQUEST[$name];
		else if (isset($_ENV[$name]))
			return $_ENV[$name];
		else if (isset($_SERVER[$name]))
			return $_SERVER[$name];
		else
			return null;
	}
	
	/**
	 * ��query��ȡֵ
	 * 
	 * @param string $name
	 * @param string $default
	 * @return string|null
	 */
	public function getQuery($name = null, $defaultValue = null) {
		return $this->getGet($name, $defaultValue);
	}
	
	/**
	 * ���postֵ
	 * 
	 * @param string $name
	 * @param string $defaultValue
	 * @return string|null
	 */
	public function getPost($name = null, $defaultValue = null) {
		if ($name) return $_POST;
		return isset($_POST[$name]) ? $_POST[$name] : $defaultValue;
	}
	
	/**
	 * ���getֵ
	 * 
	 * @param string $name
	 * @param string $defaultValue
	 * @return string|null
	 */
	public function getGet($name = '', $defaultValue = null) {
		if ($name == null) return $_GET;
		return (isset($_GET[$name])) ? $_GET[$name] : $defaultValue;
	}
	
	/**
	 * ����cookie��ֵ�����$name=null�򷵻�����Cookieֵ
	 * 
	 * @param string $key
	 * @param string $defaultValue
	 * @return string|null|array
	 */
	public function getCookie($name = null, $defaultValue = null) {
		if ($name == null) return $_COOKIE;
		return (isset($_COOKIE[$name])) ? $_COOKIE[$name] : $defaultValue;
	}
	
	/**
	 * ����session��ֵ�����$name=null�򷵻�����Cookieֵ
	 * 
	 * @param string $key
	 * @param string $defaultValue
	 * @return string|null|array
	 */
	public function getSession($name = null, $defaultValue = null) {
		if ($name == null) return $_SESSION;
		return (isset($_SESSION[$name])) ? $_SESSION[$name] : $defaultValue;
	}
	
	/**
	 * ����Server��ֵ�����$nameΪ���򷵻�����Server��ֵ
	 * 
	 * @param string $name
	 * @param string $defaultValue
	 * @return string|null|array
	 */
	public function getServer($name = null, $defaultValue = null) {
		if ($name == null) return $_SERVER;
		return (isset($_SERVER[$name])) ? $_SERVER[$name] : $defaultValue;
	}
	
	/**
	 * ����env�е�ֵ�����$nameΪnull�򷵻�����env��ֵ
	 * 
	 * @param string|null $name
	 * @param string $defaultValue
	 * @return string|null|array
	 */
	public function getEnv($name = null, $defaultValue = null) {
		if ($name == null) return $_ENV;
		return (isset($_ENV[$name])) ? $_ENV[$name] : $defaultValue;
	}
	
	/**
	 * ��ȡЭ������
	 * 
	 * @return string
	 */
	public function getScheme() {
		return ($this->getServer('HTTPS') == 'on') ? 'https' : 'http';
	}
	
	/**
	 * ��������ҳ��ʱͨ��Э������ƺͰ汾
	 * @return string
	 */
	public function getProtocol() {
		return $this->getServer('SERVER_PROTOCOL', 'HTTP/1.0');
	}
	
	/**
	 * ���ط���IP
	 * 
	 * @return string|0.0.0.0
	 */
	public function getClientIp() {
		if (!$this->_clientIp) $this->_getClientIp();
		return $this->_clientIp;
	}
	
	/**
	 * �������ķ���
	 */
	public function getRequestMethod() {
		return $this->getServer('REQUEST_METHOD', 'POST');
	}
	
	/**
	 * ���ظ������Ƿ�Ϊajax����
	 * @return Boolean
	 */
	public function getIsAjaxRequest() {
		return $this->getServer('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest';
	}
	
	/**
	 * Returns a boolean indicating whether this request was made using a
	 * secure channel, such as HTTPS.
	 * @return Boolean
	 */
	public function isSecure() {
		return !strcasecmp($this->getServer('HTTPS'), 'on');
	}
	
	/**
	 * ���������Ƿ�ΪGET��������
	 * @return boolean 
	 */
	public function isGet() {
		return !strcasecmp($this->getRequestMethod(), 'GET');
	}
	
	/**
	 * ���������Ƿ�ΪPOST��������
	 * @return boolean
	 */
	public function isPost() {
		return !strcasecmp($this->getRequestMethod(), 'POST');
	}
	
	/**
	 * ���������Ƿ�ΪPUT��������
	 * @return boolean
	 */
	public function isPut() {
		return !strcasecmp($this->getRequestMethod(), 'PUT');
	}
	
	/**
	 * ���������Ƿ�ΪDELETE��������
	 * @return boolean
	 */
	public function isDelete() {
		return !strcasecmp($this->getRequestMethod(), 'Delete');
	}
	
	/**
	 * ��ʼ���������Դ��ʶ��
	 * �����uri��ȥ��Э��������������
	 * 
	 * @return string
	 */
	public function getRequestUri() {
		if (!$this->_requestUri) $this->initRequestUri();
		return $this->_requestUri;
	}
	
	/**
	 * ���ص�ǰִ�нű��ľ���·��
	 * 
	 * Example:
	 * http://www.phpwind.net/example/index.php?a=test
	 * $this->_scriptUrl = /example/index.php
	 * 
	 * @throws WindException
	 * @return string
	 */
	public function getScriptUrl() {
		if (!$this->_scriptUrl) $this->_initScriptUrl();
		return $this->_scriptUrl;
	}
	
	/**
	 * ��ȡHttpͷ��Ϣ
	 * @param string $header ͷ������
	 */
	public function getHeader($header) {
		$temp = strtoupper(str_replace('-', '_', $header));
		if (substr($temp, 0, 5) != 'HTTP_') $temp = 'HTTP_' . $temp;
		if (($header = $this->getServer($temp)) != null) return $header;
		if (function_exists('apache_request_headers')) {
			$headers = apache_request_headers();
			if (!empty($headers[$header])) return $headers[$header];
		}
		return false;
	}
	
	/**
	 * ���ذ����ɿͻ����ṩ�ġ�������ʵ�ű�����֮�����ڲ�ѯ��䣨query string��֮ǰ��·����Ϣ
	 * 
	 * @throws WindException
	 * @return string
	 */
	public function getPathInfo() {
		if (!$this->_pathInfo) $this->_initPathInfo();
		return $this->_pathInfo;
	}
	
	/**
	 * ��ȡ����URL,������ȥ���˽ű��ļ��Լ����ʲ�����Ϣ��URL��ַ��Ϣ
	 * 
	 * Example:
	 * http://www.phpwind.net/example/index.php?a=test
	 * $this->_baseUrl = example
	 * return absolute url address when absolute is true 
	 * 'example' will be return when absolute is false
	 * 'http://www.phpwind.net/example' will be return when absolute is true
	 * 'http://www.phpwind.net:80/example' will be return when absolute is true
	 * 'http://www.phpwind.net:443/example' will be return when absolute is true
	 * 
	 * @param boolean $absolute
	 * @return string
	 */
	public function getBaseUrl($absolute = false) {
		if ($this->_baseUrl === null) $this->_baseUrl = rtrim(dirname($this->getScriptUrl()), '\\/');
		return $absolute ? $this->getHostInfo() . $this->_baseUrl : $this->_baseUrl;
	}
	
	/**
	 * ���������Ϣ������Э����Ϣ�������������ʶ˿���Ϣ
	 * 
	 * @return string
	 */
	public function getHostInfo() {
		if ($this->_hostInfo === null) $this->_initHostInfo();
		return $this->_hostInfo;
	}
	
	/**
	 * ���ص�ǰ���нű����ڵķ���������������
	 * ����ű�����������������
	 * �����������Ǹ��������������õ�ֵ����
	 * 
	 * @return string|''
	 */
	public function getServerName() {
		return $this->getServer('SERVER_NAME', '');
	}
	
	/**
	 * ���ط���˿ں�
	 * https���ӵ�Ĭ�϶˿ں�Ϊ443
	 * http���ӵ�Ĭ�϶˿ں�Ϊ80
	 * 
	 * @return int
	 */
	public function getServerPort() {
		if (!$this->_port) {
			$_default = $this->isSecure() ? 443 : 80;
			$this->setServerPort($this->getServer('SERVER_PORT', $_default));
		}
		return $this->_port;
	}
	
	/**
	 * ���÷���˿ں�
	 * https���ӵ�Ĭ�϶˿ں�Ϊ443
	 * http���ӵ�Ĭ�϶˿ں�Ϊ80
	 * 
	 * @param int $port
	 */
	public function setServerPort($port) {
		$this->_port = (int) $port;
	}
	
	/**
	 * ���������ǰҳ����û���������
	 * DNS ����������������û��� REMOTE_ADDR
	 * 
	 * @return string|null
	 */
	public function getRemoteHost() {
		return $this->getServer('REMOTE_HOST');
	}
	
	/**
	 * �������������Referer����ͷ�������÷������˽��׷�ٷ��������������ԴURL��ַ
	 * 
	 * @return string|null 
	 */
	public function getUrlReferer() {
		return $this->getServer('HTTP_REFERER');
	}
	
	/**
	 * ����û����������ӵ� Web ��������ʹ�õĶ˿ں�
	 * 
	 * @return number|null
	 */
	public function getRemotePort() {
		return $this->getServer('REMOTE_PORT');
	}
	
	/**
	 * ����User-Agentͷ�ֶ�����ָ����������������ͻ��˳�������ͺ�����
	 * ����ͻ�����һ�������ֳ��նˣ��ͷ���һ��WML�ļ���������ֿͻ�����һ����ͨ�������
	 * �򷵻�ͨ����HTML�ļ�
	 * 
	 * @return string
	 */
	public function getUserAgent() {
		return $this->getServer('HTTP_USER_AGENT', '');
	}
	
	/**
	 * ���ص�ǰ����ͷ�� Accept: ������ݣ�
	 * Acceptͷ�ֶ�����ָ���ͻ��˳����ܹ������MIME���ͣ����� text/html,image/*
	 * 
	 * @return string|''
	 */
	public function getAcceptTypes() {
		return $this->getServer('HTTP_ACCEPT', '');
	}
	
	/**
	 * ���ؿͻ��˳�������ܹ����н�������ݱ��뷽ʽ������ı��뷽ʽͨ��ָĳ��ѹ����ʽ
	 * 
	 * @return string|''
	 */
	public function getAcceptCharset() {
		return $this->getServer('HTTP_ACCEPT_ENCODING', '');
	}
	
	/**
	 * ���ؿͻ��˳������������������ĸ����ҵ������ĵ� 
	 * Accept-Language: en-us,zh-cn
	 * 
	 * @return string
	 */
	public function getAcceptLanguage() {
		if (!$this->_language) {
			$_language = explode(',', $this->getServer('HTTP_ACCEPT_LANGUAGE', ''));
			$this->_language = $_language[0] ? $_language[0] : 'zh-cn';
		}
		return $this->_language;
	}
	
	/**
	 * ��÷�����Ϣ
	 * @return WindHttpResponse
	 */
	public function getResponse() {
		return WindHttpResponse::getInstance();
	}
	
	/**
	 * ���ط��ʵ�IP��ַ
	 * 
	 * Example:
	 * $this->_clientIp = 127.0.0.1
	 * 
	 * @return string 
	 */
	private function _getClientIp() {
		if (($ip = $this->getServer('HTTP_CLIENT_IP')) != null) {
			$this->_clientIp = $ip;
		} elseif (($_ip = $this->getServer('HTTP_X_FORWARDED_FOR')) != null) {
			$ip = strtok($_ip, ',');
			do {
				$ip = ip2long($ip);
				if (!(($ip == 0) || ($ip == 0xFFFFFFFF) || ($ip == 0x7F000001) || (($ip >= 0x0A000000) && ($ip <= 0x0AFFFFFF)) || (($ip >= 0xC0A8FFFF) && ($ip <= 0xC0A80000)) || (($ip >= 0xAC1FFFFF) && ($ip <= 0xAC100000)))) {
					$this->_clientIp = long2ip($ip);
					return;
				}
			} while (($ip = strtok(',')));
		} elseif (($ip = $this->getServer('HTTP_PROXY_USER')) != null) {
			$this->_clientIp = $ip;
		} elseif (($ip = $this->getServer('REMOTE_ADDR')) != null) {
			$this->_clientIp = $ip;
		} else {
			$this->_clientIp = "0.0.0.0";
		}
	}
	
	/**
	 * ��ʼ���������Դ��ʶ��
	 * �����uri��ȥ��Э��������������
	 * 
	 * Example:
	 * http://www.phpwind.net/example/index.php?a=test
	 * $this->_requestUri = /example/index.php?a=test
	 * 
	 * @throws WindException
	 * @return
	 */
	private function initRequestUri() {
		if (($requestUri = $this->getServer('HTTP_X_REWRITE_URL')) != null) {
			$this->_requestUri = $requestUri;
		} elseif (($requestUri = $this->getServer('REQUEST_URI')) != null) {
			$this->_requestUri = $requestUri;
			if (strpos($this->_requestUri, $this->getServer('HTTP_HOST')) !== false) $this->_requestUri = preg_replace('/^\w+:\/\/[^\/]+/', '', $this->_requestUri);
		} elseif (($requestUri = $this->getServer('ORIG_PATH_INFO')) != null) {
			$this->_requestUri = $requestUri;
			if (($query = $this->getServer('QUERY_STRING')) != null) $this->_requestUri .= '?' . $query;
		} else
			throw new WindException(__CLASS__ . ' is unable to determine the request URI.');
		
		$this->_requestUri = $requestUri;
	}
	
	/**
	 * ��ʼ����ǰִ�нű��ľ���·��
	 * 
	 * Example:
	 * http://www.phpwind.net/example/index.php?a=test
	 * $this->_scriptUrl = /example/index.php
	 * 
	 * @throws WindException
	 * @return
	 */
	private function _initScriptUrl() {
		if (($scriptName = $this->getServer('SCRIPT_FILENAME')) == null) throw new WindException(__CLASS__ . ' determine the entry script URL failed!!');
		
		$scriptName = basename($scriptName);
		if (($_scriptName = $this->getServer('SCRIPT_NAME')) != null && basename($_scriptName) === $scriptName) {
			$this->_scriptUrl = $_scriptName;
		} elseif (($_scriptName = $this->getServer('PHP_SELF')) != null && basename($_scriptName) === $scriptName) {
			$this->_scriptUrl = $_scriptName;
		} elseif (($_scriptName = $this->getServer('ORIG_SCRIPT_NAME')) != null && basename($_scriptName) === $scriptName) {
			$this->_scriptUrl = $_scriptName;
		} elseif (($pos = strpos($this->getServer('PHP_SELF'), '/' . $scriptName)) !== false) {
			$this->_scriptUrl = substr($this->getServer('SCRIPT_NAME'), 0, $pos) . '/' . $scriptName;
		} elseif (($_documentRoot = $this->getServer('DOCUMENT_ROOT')) != null && ($_scriptName = $this->getServer('SCRIPT_FILENAME')) != null && strpos($_scriptName, $_documentRoot) === 0) {
			$this->_scriptUrl = str_replace('\\', '/', str_replace($_documentRoot, '', $_scriptName));
		} else
			throw new WindException(__CLASS__ . ' determine the entry script URL failed!!');
	}
	
	/**
	 * ���������Ϣ������Э����Ϣ�������������ʶ˿���Ϣ
	 * 
	 * Example:
	 * http://www.phpwind.net/example/index.php?a=test
	 * $this->_hostInfo = http://www.phpwind.net/
	 * $this->_hostInfo = http://www.phpwind.net:80/
	 * $this->_hostInfo = https://www.phpwind.net:443/
	 * 
	 * @throws WindException
	 * @return 
	 */
	private function _initHostInfo() {
		$http = $this->isSecure() ? 'https' : 'http';
		if (($httpHost = $this->getServer('HTTP_HOST')) != null)
			$this->_hostInfo = $http . '://' . $httpHost;
		elseif (($httpHost = $this->getServer('SERVER_NAME')) != null) {
			$this->_hostInfo = $http . '://' . $httpHost;
			if (($port = $this->getServerPort()) != null) $this->_hostInfo .= ':' . $port;
		} else
			throw new WindException(__CLASS__ . ' determine the entry script URL failed!!');
	}
	
	/**
	 * ���ذ����ɿͻ����ṩ�ġ�������ʵ�ű�����֮�����ڲ�ѯ��䣨query string��֮ǰ��·����Ϣ
	 * 
	 * @throws WindException
	 * @return
	 */
	private function _initPathInfo() {
		$requestUri = urldecode($this->getRequestUri());
		$scriptUrl = $this->getScriptUrl();
		$baseUrl = $this->getBaseUrl();
		if (strpos($requestUri, $scriptUrl) === 0)
			$pathInfo = substr($requestUri, strlen($scriptUrl));
		elseif ($baseUrl === '' || strpos($requestUri, $baseUrl) === 0)
			$pathInfo = substr($requestUri, strlen($baseUrl));
		elseif (strpos($_SERVER['PHP_SELF'], $scriptUrl) === 0)
			$pathInfo = substr($_SERVER['PHP_SELF'], strlen($scriptUrl));
		else
			throw new WindException('');
		
		if (($pos = strpos($pathInfo, '?')) !== false) $pathInfo = substr($pathInfo, 0, $pos);
		
		$this->_pathInfo = trim($pathInfo, '/');
	}

}