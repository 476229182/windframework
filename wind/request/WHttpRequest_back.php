<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * ����http����
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
class WHttpRequest_back  {
	
	public function getCookie() {

	}
	
	/**
	 * ��ȡ HTTP POST��ֵ
	 * @param string $key 
	 * @return mixed
	 */
	public function getPost($key = '') {
		return $key ? $_POST[$key] : $_POST;
	}
	
	/**
	 * ��ȡ HTTP GET��ֵ
	 * 
	 * @param string $key 
	 * @return mixed
	 */
	public function getGet($key = '') {
		return $key ? $_GET[$key] : $_GET;
	}
	
	/**
	 * ��ȡ HTTP SERVER��ֵ
	 * 
	 * @param string $key 
	 * @return mixed
	 */
	public function getServer($key = '') {
		return $key ? $_SERVER[$key] : $_SERVER;
	}
	
	/**
	 * ��ȡ HTTP REQUEST��ֵ
	 * 
	 * @param string $key 
	 * @return mixed
	 */
	public function getRequest($key = '') {
		return $key ? $_REQUEST[$key] : $_REQUEST;
	}
	
	/**
	 * ȡ�ÿͻ���ʹ�õ� HTTP ���ݴ��䷽��
	 * 
	 * @return string
	 */
	public function getHttpMethod() {
		return ($httpMethod = $this->getServer('REQUEST_METHOD')) ? $httpMethod : 'GET';
	}
	
	/**
	 * ȡ��http����� MIME �������͡�
	 * 
	 * @return string
	 */
	public function getAcceptTypes() {
		return $this->getServer('HTTP_ACCEPT');
	}
	
	/**
	 * ȡ�ÿͻ����������ԭʼ�û�������Ϣ��
	 * 
	 * @return string
	 */
	public function getUserAgent() {
		return $this->getServer('HTTP_USER_AGENT');
	}
	
	/**
	 *��֤HTTP�������Ƿ�ʹ�ð�ȫ�׽��� (ssl��ȫ����)
	 *
	 *@return boolean
	 */
	public function IsSecureConnection() {
		if (isset($this->_request['IS_SSL']))
			return $this->_request['IS_SSL'];
		return $this->_request['IS_SSL'] = !strcasecmp($this->getServer('HTTPS'), 'on');
	}
	
	public function isAjaxRequest() {

	}
	
	/**
	 * ȡ������ҳ���URI
	 * 
	 * @return string ����http����ҳ���URI
	 */
	public function getRequestUri() {
		if (isset($this->_request['REQUEST_URI']))
			return $this->_request['REQUEST_URI'];
		$requestUri = '';
		if ($uri = $this->getServer('HTTP_X_ORIGINAL_URL')) {
			$requestUri = $uri; //IIS7+Rewrite Module
		} elseif ($uri = $this->getServer('HTTP_X_REWRITE_URL')) {
			$requestUri = $uri; //IIS6 + ISAPI Rewite
		} elseif ($uri = $this->getServer('ORIG_PATH_INFO')) {
			$requestUri = $uri . (($queryString = $this->getQuery()) ? '?' . $queryString : ''); //IIS 5.0 CGI
		} elseif ($uri = $this->getServer('REQUEST_URI')) {
			$requestUri = $uri; //nginx+apache2
		} elseif ($uri = $this->getServer('REDIRECT_URL')) {
			$requestUri = $uri; //apache2
		} else {
			$requestUri = $this->getServer('PHP_SELF') . (($queryString = $this->getQuery()) ? '?' . $queryString : '');
		}
		return $this->_request['REQUEST_URI'] = $requestUri;
	}
	
	/**
	 * ȡ�� HTTP���� ��ѯ�ַ���
	 */
	public function getQuery() {
		return $this->getServer('QUERY_STRING');
	}
	
	/**
	 * ȡ��http����ǰ�ű��ļ����ڵ�Ŀ¼
	 * @return string;
	 */
	public function getFilePath() {
		if (isset($this->_request['FILEPATH']))
			return $this->_request['FILEPATH'];
		return $this->_request['FILEPATH'] = dirname($this->getServer('SCRIPT_FILENAME'));
	}
	
	/**
	 * ȡ��http����ǰ�ű��ļ�����ʵ·��
	 * @return string
	 */
	public function getFile() {
		if (isset($this->_request['FILE']))
			return $this->_request['FILE'];
		return $this->_request['FILE'] = realpath($this->getServer('SCRIPT_FILENAME'));
	}
	
	/**
	 * ȡ��http������ԭʼ��URL
	 * @return string
	 */
	public function getRequestUrl() {
		if (isset($this->_request['REQUEST_URL']))
			return $this->_request['REQUEST_URL'];
		return $this->_request['REQUEST_URL'] = $this->getHost() . $this->getRequestUri();
	}
	
	public function getBaseUrl() {

	}
	/**
	 * ȡ�ÿͻ����ϴ������ URL��ַ
	 * @return string
	 */
	public function getReferUrl() {
		return $this->getServer('HTTP_REFERER');
	}
	
	/**
	 * ȡ��http�����еĵ�ǰ�ű��ļ���
	 * @return string
	 */
	public function getScript() {
		if (isset($this->_request['SCRIPT']))
			return $this->_request['SCRIPT'];
		return $this->_request['SCRIPT'] = basename($this->getServer('SCRIPT_FILENAME'));
	}
	
	/**
	 * ȡ�÷�����DNS
	 * @param $schema string
	 * @return string
	 */
	public function getHost($schema = '') {
		if (isset($this->_request['HOST']))
			return $this->_request['HOST'];
		$schema = $schema ? $schema : $ssl = $this->IsSecureConnection() ? 'https' : 'http';
		if ($host = $this->getUserHost()) {
			$host = $schema . '://' . $host;
		} else {
			$host = $schema . '://' . $this->getServerName();
			$port = $this->getServerPort();
			$host .= (($port != 80 && !$ssl) || ($port != 443 && $ssl)) ? ':' . $port : '';
		}
		return $this->_request['HOST'] = $host;
	
	}
	/**
	 * ȡ��http�����е�����������ַ
	 * @return string
	 */
	public function getUserHost() {
		return $this->getServer('HTTP_HOST');
	}
	/**
	 * ȡ��http����ͻ����е�IP��ַ
	 * @return string
	 */
	public function getUserHostAddr() {
		if (isset($this->_request['REMODE_ADDR']))
			return $this->_request['REMODE_ADDR'];
		return $this->_request['REMODE_ADDR'] = $this->getServer("HTTP_X_FORWARDED_FOR") || $this->getServer("HTTP_CLIENT_IP") || $this->getServer("REMOTE_ADDR");
	}
	/**
	 * ȡ��http�����з�������������ַ
	 * @return string
	 */
	public function getServerName() {
		return $this->getServer('SERVER_NAME');
	}
	/**
	 * ȡ��http�����з�������������ַ�Ķ˿ں�
	 * @return string
	 */
	public function getServerPort() {
		return $this->getServer('SERVER_PORT');
	}
	/**
	 * ��ȡHTTP����Ŀͻ��˵�����������Ϣ
	 * @param string $userAgent �ͻ����������ԭʼ�û�������Ϣ
	 * @return array
	 */
	public function getUserBrowser($userAgent = null) {
		if (isset($this->_request['USER_BROWSER']))
			return $this->_request['USER_BROWSER'];
		return $this->_request['USER_BROWSER'] = get_browser($userAgent, true);
	}
	
	/**
	 * ����httpͷ��Ϣ
	 * @return  array;
	 */
	public function getHeaders() {
		if (isset($this->_request['headers']))
			return $this->_request['headers'];
		return $this->_request['headers'] = function_exists('getallheaders') ? getallheaders() : $this->getAllHeaders();
	}
	private function getAllHeaders() {
		$headers = array();
		$servers = $this->getServer();
		foreach ($servers as $key => $value) {
			$key = strtoupper($key);
			if ('HTTP_' == substr($key, 0, 5)) {
				$headers[$this->makeHeaderKey($key)] = $value;
			}
			if (in_array($key, array(
				'CONTENT_LENGTH', 
				'CONTENT_TYPE'
			))) {
				$headers[$key] = $value;
			}
			if ('PHP_AUTH_DIGEST' == $key && $atthorization = $this->getServer($key)) {
				$headers['AUTHORIZATION'] = $atthorization;
			} elseif ('PHP_AUTH_USER' == $key && $user = $this->getServer($key) && $pwd = $this->getServer('PHP_AUTH_PW')) {
				$headers['AUTHORIZATION'] = base64_encode($user . ':' . $pwd);
			}
		}
		return $headers;
	}
	private function makeHeaderKey($key) {
		$newKey = '';
		$key = str_replace('_', '-', substr(strtolower($key), 5));
		foreach (explode('-', $key) as $value) {
			$newKey .= $newKey ? '-' . ucfirst($value) : ucfirst($value);
		}
		return $newKey;
	}
}

