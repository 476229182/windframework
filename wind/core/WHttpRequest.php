<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-7
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WHttpRequest extends WModule implements WRequest {
	
	private $_port = null;
	
	private $_language = null;
	private $_pathInfo = null;
	private $_scriptUrl = null;
	private $_requestUri = null;
	private $_baseUrl = null;
	
	/**
	 * �������ƻ�÷�������ִ�л�����Ϣ,������Ʋ������򷵻�NULL
	 * 
	 * @param string $name
	 */
	function getAttribute($name) {
		return isset($_REQUEST[$name]) ? $_REQUEST[$name] : null;
	}
	
	/**
	 * ���postֵ
	 * 
	 * @param string $name
	 * @param string $defaultValue
	 * @return string
	 */
	public function getPost($name = '', $defaultValue = null) {
		return !$name ? $defaultValue : isset($_POST[$name]) ? $_POST[$name] : $defaultValue;
	}
	
	/**
	 * ���getֵ
	 * 
	 * @param string $name
	 * @param string $defaultValue
	 * @return string
	 */
	public function getGet($name = '', $defaultValue = null) {
		return !$name ? $defaultValue : isset($_GET[$name]) ? $_GET[$name] : $defaultValue;
	}
	
	/**
	 * @param unknown_type $name
	 */
	public function getParameterValues($name, $defaultValue = null) {
		return isset($_GET[$name]) ? $_GET[$name] : (isset($_POST[$name]) ? $_POST[$name] : $defaultValue);
	}
	
	/**
	 * ��������ҳ��ʱͨ��Э������ƺͰ汾
	 * @return string
	 */
	public function getProtocol() {
		return isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
	}
	
	/**
	 * ���ص�ǰִ�нű��ľ���·��
	 * @return string
	 */
	public function getScriptUrl() {
		if ($this->_scriptUrl === null) {
			$scriptName = basename($_SERVER['SCRIPT_FILENAME']);
			if (basename($_SERVER['SCRIPT_NAME']) === $scriptName)
				$this->_scriptUrl = $_SERVER['SCRIPT_NAME'];
			else if (basename($_SERVER['PHP_SELF']) === $scriptName)
				$this->_scriptUrl = $_SERVER['PHP_SELF'];
			else if (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName)
				$this->_scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
			else if (($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false)
				$this->_scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $scriptName;
			else if (isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0)
				$this->_scriptUrl = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
			else
				throw new Exception('CHttpRequest is unable to determine the entry script URL.');
		}
		return $this->_scriptUrl;
	}
	
	/**
	 * ����Ҫ���ʵ�ҳ��
	 * @return string
	 */
	public function getRequestUri() {
		if ($this->_requestUri === null) {
			if (isset($_SERVER['HTTP_X_REWRITE_URL']))
				$this->_requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
			else if (isset($_SERVER['REQUEST_URI'])) {
				$this->_requestUri = $_SERVER['REQUEST_URI'];
				if (strpos($this->_requestUri, $_SERVER['HTTP_HOST']) !== false)
					$this->_requestUri = preg_replace('/^\w+:\/\/[^\/]+/', '', $this->_requestUri);
			} else if (isset($_SERVER['ORIG_PATH_INFO'])) {
				$this->_requestUri = $_SERVER['ORIG_PATH_INFO'];
				if (!empty($_SERVER['QUERY_STRING']))
					$this->_requestUri .= '?' . $_SERVER['QUERY_STRING'];
			} else
				throw new Exception('CHttpRequest is unable to determine the request URI.');
		}
		return $this->_requestUri;
	}
	
	/**
	 * ���ذ����ɿͻ����ṩ�ġ�������ʵ�ű�����֮�����ڲ�ѯ��䣨query string��֮ǰ��·����Ϣ
	 * @return string
	 */
	public function getPathInfo() {
		if ($this->_pathInfo === null) {
			$requestUri = urldecode($this->getRequestUri());
			$scriptUrl = $this->getScriptUrl();
			$baseUrl = $this->getBaseUrl();
			if (strpos($requestUri, $scriptUrl) === 0)
				$pathInfo = substr($requestUri, strlen($scriptUrl));
			else if ($baseUrl === '' || strpos($requestUri, $baseUrl) === 0)
				$pathInfo = substr($requestUri, strlen($baseUrl));
			else if (strpos($_SERVER['PHP_SELF'], $scriptUrl) === 0)
				$pathInfo = substr($_SERVER['PHP_SELF'], strlen($scriptUrl));
			else
				throw new Exception('CHttpRequest is unable to determine the path info of the request.');
			
			if (($pos = strpos($pathInfo, '?')) !== false)
				$pathInfo = substr($pathInfo, 0, $pos);
			$this->_pathInfo = trim($pathInfo, '/');
		}
		return $this->_pathInfo;
	}
	
	/**
	 * ���ø�·��
	 * @param boolean $absolute
	 * @return string
	 */
	public function getBaseUrl($absolute = false) {
		if ($this->_baseUrl === null)
			$this->_baseUrl = rtrim(dirname($this->getScriptUrl()), '\\/');
		return $absolute ? $this->getHostInfo() . $this->_baseUrl : $this->_baseUrl;
	}
	
	/**
	 * ���ص�ǰ���нű����ڵķ�������������������ű����������������У������������Ǹ��������������õ�ֵ����
	 */
	public function getServerName() {
		return isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
	}
	
	/**
	 * @return int
	 */
	public function getServerPort() {
		if ($this->_port === null) {
			$_default = $this->isSecure() ? 443 : 80;
			$this->_port = isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : $_default;
		}
		return $this->_port;
	}
	
	/**
	 * @param int $port
	 */
	public function setServerPort($port) {
		$this->_port = (int) $port;
	}
	
	/**
	 * ���������ǰҳ����û��� IP ��ַ
	 */
	public function getRemoteAddr() {
		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
	}
	
	/**
	 * ���������ǰҳ����û�����������DNS ����������������û��� REMOTE_ADDR
	 */
	public function getRemoteHost() {
		return isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : null;
	}
	
	/**
	 * �������ķ���
	 */
	public function getRequestMethod() {
		return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'POST';
	}
	
	/**
	 * Returns a boolean indicating whether this request was made using a
	 * secure channel, such as HTTPS.
	 * @return Boolean
	 */
	public function isSecure() {
		return isset($_SERVER['HTTPS']) && !strcasecmp($_SERVER['HTTPS'], 'on');
	}
	
	/**
	 * ���ظ������Ƿ�Ϊajax����
	 * @return Boolean
	 */
	public function getIsAjaxRequest() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
	}
	
	/**
	 * �������������Referer����ͷ�������÷������˽��׷�ٷ��������������ԴURL��ַ
	 * @return string or null 
	 */
	public function getUrlReferer() {
		return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
	}
	
	/**
	 * ����û����������ӵ� Web ��������ʹ�õĶ˿ں�
	 * @return number or null
	 */
	public function getRemotePort() {
		return isset($_SERVER['REMOTE_PORT']) ? (int) $_SERVER['REMOTE_PORT'] : null;
	}
	
	/**
	 * ����User-Agentͷ�ֶ�����ָ����������������ͻ��˳�������ͺ�����
	 * ����ͻ�����һ�������ֳ��նˣ��ͷ���һ��WML�ļ���������ֿͻ�����һ����ͨ�������
	 * �򷵻�ͨ����HTML�ļ�
	 * 
	 * @return string
	 */
	public function getUserAgent() {
		return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	}
	
	/**
	 * ���ص�ǰ����ͷ�� Accept: ������ݣ�
	 * Acceptͷ�ֶ�����ָ���ͻ��˳����ܹ������MIME���ͣ����� text/html,image/*
	 * 
	 * @return array
	 */
	public function getAcceptTypes() {
		if (isset($_SERVER['HTTP_ACCEPT']))
			return explode(',', $_SERVER['HTTP_ACCEPT']);
		return null;
	}
	
	/**
	 * ���ؿͻ��˳�������ܹ����н�������ݱ��뷽ʽ������ı��뷽ʽͨ��ָĳ��ѹ����ʽ
	 * 
	 * @return array or null
	 */
	public function getAcceptCharset() {
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
			return explode(',', $_SERVER['HTTP_ACCEPT_ENCODING']);
		return null;
	}
	
	/**
	 * ���ؿͻ��˳������������������ĸ����ҵ������ĵ� 
	 * Accept-Language: en-us,zh-cn
	 * 
	 * @return multitype:|NULL
	 */
	public function getAcceptLanguage() {
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$_language = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$this->_language = $_language[0] ? $_language[0] : 'zh-cn';
		}
		return $this->_language;
	}

}