<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-12
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
class WView {
	private static $configs = array ();
	private static $instance = null;
	private $viewContents = ''; //���������
	private $var; //ģ��������
	

	//TODO ��ͼ������Ϣ����
	public function getInstance($config = NULL) {
		if (self::$instance == null) {
			$this->_initView ( $config );
			self::$instance = new WView ();
		}
		return self::$instance;
	}
	//TODO
	private function _initView($config) {
		if (! $config)
			$config = array ('engine' => 'php', 'cache_path' => '', 'tmpExt' => '.phtml' );
		$this->config = $config;
	}
	public function assign($val, $value) {
		if (is_object ( $val )) {
			$this->var [$val] = get_object_vals ( $val );
		} else {
			$this->var [$val] = $value;
		}
	}
	public function __set($val, $value) {
		$this->assign ( $val, $value );
	}
	public function __get($val) {
		if (isset ( $this->val [$val] ))
			return $this->val [$val];
		return null;
	}
	
	//TODO ��Ч���жϣ��˴��Ƿ���Խ������ʵ��ת�����������У��Ա������ط�����
	public function redirect($url, $params = array(), $delayTime = 0, $msg = '') {
		$url = str_replace ( array ("\n", "\r" ), '', $url );
		$parse = '';
		foreach ( ( array ) $params as $key => $value ) {
			if ($value != '')
				$parse .= "{$key}={$value}&";
		}
		(strpos ( $url, '?' ) === false) ? $url .= "?{$parse}" : "&{$parse}";
		if ($msg == '') {
			$msg = "ϵͳ����{$delayTime}��֮���Զ���ת��!";
		}
		$delayTime = intval ( $delayTime );
		if (! headers_sent ()) {
			if ($delayTime === 0) {
				header ( 'Location:' . $url );
				exit ();
			} else {
				header ( "refresh:{$delayTime};url={$url}" );
				exit ( $msg );
			}
		}
		$jumpStr = "<meta http-equiv='Refresh' content='{$delayTime};URL={$url}'>";
		($delayTime > 0) && $jumpStr .= $msg;
		exit ( $jumpStr );
	}
	
	/**
	 * 
	 * @param string $templateFile ģ������
	 * @param string $charset ������ַ�����Ĭ��Ϊϵͳ�ģ�
	 * @param string $contentType ���������
	 */
	public function display($templateFile = '', $charset = '', $contentType = '') {
		$this->fetch ( $templateFile, $charset, $contentType, false );
	}
	
	/**
	 * @param string $templateFile ģ������
	 * @param string $charset ������ַ�����Ĭ��Ϊϵͳ�ģ�
	 * @param string $contentType ���������
	 * @param boolean $return �Ƿ񷵻ػ���������ʾ
	 */
	//TODO ���ģ������
	public function fetch($templateFile = '', $charset = '', $contentType = 'text/html', $return = true) {
		$templateFile = $this->config ['cache_path'] . $templateFile . $this->config ['tmpExt'];
		if (! file_exists ( $templateFile ))
			return;
		(! $charset) && $charset = $this->config ['charset'];
		(! $contentType) && $contentType = 'text/html';
		header ( "Content-Type:" . $contentType . "; charset=" . $charset );
		header ( "Cache-control: private" ); //֧��ҳ�����
		if (extension_loaded ( 'zlib' ))
			ob_start ( 'ob_gzhandler' ); //�����������
		else
			ob_start ();
		extract ( $this->val, EXTR_OVERWRITE );
		include $templateFile;
		$this->viewContent = ob_get_contents ();
		if ($return) {
			ob_end_clean();
			return $this->viewContent;
		} else {
			ob_end_flush();
		}
	}
	//TODO  cache���棬ͬʱ�ܵ��ý�����ͬģ������
}