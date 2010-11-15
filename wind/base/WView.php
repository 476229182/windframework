<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-12
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
/**
 * ��ͼ���������
 * ������������е����ñ�����$config�����а������ĸ��ؼ����ã��ݶ�������
 * $config = array('cachePath' => '', //ָ���û���Ҫ�ı��뻺��Ŀ¼--�����PHP���棨������PHP��html�ķ�ʽ��������������� 
 *					'templateExt' => 'phtml',//ģ��ʹ�õĺ�׺���ƣ�Ĭ����phtml���ݶ���
 *					'engine' => 'php', //ģ��ʹ�õ����棬Ĭ����php(�ݶ�),�û����Ը����Լ�����Ҫ���ò��Ҳ����Լ������棬��smarty
 *					'templatePath' => '',//ģ���ļ���ȡ��·��
 *					'charset' => '', //ģ��������ַ���
 *            );
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WView {
	private $config = array ();
	private static $instance = null;
	private $viewContents = ''; //���������
	private $var; //ģ��������
	

	/**
	 * ���ظ���ľ�̬ʵ��
	 * @param array $config //��ʼ��������Ϣ
	 * @return WView $instance
	 */
	public function getInstance($config = NULL) {
		if (self::$instance == null) {
			$class = new ReflectionClass(__CLASS__);
			self::$instance = call_user_func_array(array(
				$class, 
				'newInstance'
			), array());
			self::$instance ->_initView($config);
		}
		return self::$instance;
	}
	/**
	 * ����ģ���ļ�·��
	 * @param string $path
	 */
	public function setTemplatePath($path) {
		($path && file_exists($path)) && $this->config['templatePath'] = $path;
	}
	/**
	 * ����ģ����뻺��·��
	 * @param string $path
	 */
	public function setCachePath($path) {
		($path && file_exists($path)) && $this->config['cachePath'] = $path;
	}
	/**
	 * ����ģ��ʹ�õ�����
	 * @param string $engine
	 */
	public function setEngine($engine) {
		($engine) && $this->config['engine'] = $engine;
	}
	/**
	 * ����ģ��ĺ�׺
	 * @param string $ext
	 */
	public function setTemplateExt($ext) {
		($ext) && $this->config['templateExt'] = $ext;
	}
	//TODO ����������Ϣ
	private function _initView($config) {
		if (!is_array($config)) {
			$this->config = array('cachePath' => R_P . '/cache/', 
							'templateExt' => 'phtml',
							'charset' => 'gbk',
							'engine' => 'php',
							'templatePath' => R_P . '/template/');
		} else {
			foreach ($config as $key => $value) {
				(trim($value)) && $this->config[trim($key)] = trim($value);
			}
		}
		$this->viewContent = '';
		$this->var = array();
	}
	/**
	 * ����ģ���еı�����
	 * ���ֻ��һ�����������Ҵ�����ǹ������飬����ע��
	 * ����������һ�������򽫰�������ĸ�ʽת���ö����еı�����
	 *      ��ʹ�õ�ʱ���裺$var['�������']�ķ�ʽ������
	 * �����Ҫ����һ��������Ӧ��ʹ�÷��� $this->assignByRef();������
	 * @param string $val ��������
	 * @param string $value ����ֵ
	 */
	public function assign($var, $value = null) {
		if (is_array($var)) {
            foreach ($var as $_key => $_val) {
               (trim($_key) != '') && $this->var[$_key] = trim($_val);
            }
		} elseif (is_object($value)) {
			$this->var[$var] = get_object_vars($value);
		} else {
			$this->var[$var] = $value;
		}
	}
	/**
	 * ����ģ���еı���Ϊ����
	 * ʹ�õ�ʱ��$var->�������ķ�ʽ;���
	 * @param string $val ��������
	 * @param string $value ����ֵ
	 */
	public function assignByRef($var, &$value) {
		(is_object($value)) && $this->var[$var] = $value;
	}
	/**
	 * ���ñ���
	 * @param string $var ��������
	 * @param string $value ����ֵ
	 */
	public function __set($var, $value) {
		$this->assign($var, $value);
	}
	/**
	 * ���ģ�����
	 * @param string $var ��������
	 * @return string  ������ֵ
	 */
	public function __get($name) {
		if (isset($this->var[$name])) return $this->var[$name];
		return null;
	}
	
	/**
	 * ������ת
	 * @param string $url ��ת��Ŀ��url
	 * @param array $params ��ת���ݵĲ���
	 * @param integer $delayTime ��ת�ӳٵ�ʱ��
	 * @param string $msg ��ʾ����Ϣ
	 */
	//TODO url��Ч���жϣ��˴��Ƿ���Խ������ʵ��ת�����������У��Ա������ط�����
	public function redirect($url, $params = array(), $delayTime = 0, $msg = '') {
		$url = str_replace(array("\n", "\r" ), '', $url);
		$parse = '';
		foreach ((array)$params as $key => $value) {
			($value != '') && $parse .= "{$key}={$value}&";
		}
		(strpos($url, '?') === false) ? $url .= "?{$parse}" : "&{$parse}";
		
		($msg == '') && $msg = "ϵͳ����{$delayTime}��֮���Զ���ת��!";
		$delayTime = intval($delayTime);
		if (!headers_sent()) {
			if ($delayTime === 0) {
				header('Location:' . $url);
				exit();
			} else {
				header("refresh:{$delayTime}; url={$url}");
				exit($msg);
			}
		}
		$jumpStr = "<meta http-equiv='Refresh' content='{$delayTime};URL={$url}'>";
		($delayTime > 0) && $jumpStr .= $msg;
		exit($jumpStr );
	}
	
	/**
	 * ��ʾģ��
	 * @param string $templateFile ģ������
	 * @param string $charset ������ַ�����Ĭ��Ϊϵͳ�ģ�
	 * @param string $contentType ���������
	 */
	public function display($templateFile = '', $charset = '', $contentType = '') {
		$this->fetch($templateFile, $charset, $contentType, false );
	}
	
	/**
	 * ���ģ������
	 * @param string $templateFile ģ������
	 * @param string $charset ������ַ�����Ĭ��Ϊϵͳ�ģ�
	 * @param string $contentType ���������
	 * @param boolean $return �Ƿ񷵻ػ���������ʾ
	 */
	//TODO ���ģ������
	public function fetch($template = '', $charset = '', $contentType = 'text/html', $return = true) {
		if ($template == '') return;
		$templateFile = $this->config['templatePath'] . $template . '.' . $this->config['templateExt'];
		
		(!$charset) && $charset = $this->config ['charset'];
		(!$contentType) && $contentType = 'text/html';
		if(!headers_sent()) {
			header("Content-Type:" . $contentType . "; charset=" . $charset);
			header("Cache-control: private"); //֧��ҳ�����
		}
		(extension_loaded('zlib')) ? ob_start('ob_gzhandler') : ob_start();
		switch (strtolower($this->config['engine'])) {
			case 'php':
				extract($this->var, EXTR_OVERWRITE );
				if (!file_exists($templateFile) || !is_readable($templateFile)) return 'ERR_TEMPLATE:' . $templateFile;
				include $templateFile;
				$this->viewContent = ob_get_contents();
				break;
			case 'phpwind':
			default:
				$tmplangfile2 = $this->config['cachePath'] . $template . '.' . $this->config['templateExt'];;
				$this->viewContent = WTemplate::fetch($templateFile, $tmplangfile2, $this->var);
				echo $this->viewContent;
				break;
		}
		if ($return) {
			ob_end_clean();
			return $this->viewContent;
		} else {
			ob_end_flush();
		}
	}
	/**
	 * ���� ajax����ķ�����Ϣ��ʾ
	 * @param mixed $data  ��ʾ������
	 * @param string $type  ���ص����ͣ�Ĭ��ΪJSON����
	 */
	public function ajaxReturn($data='', $type='JSON') {
		(!$data) && $data = $this->var;
		$type = strtoupper(trim($type));
		switch ($type) {
			case 'JSON':
				header("Content-Type:text/html; charset=utf-8");
				if (is_array($data)) $data = json_encode($data);
				elseif (is_object($data)) $data = json_encode(get_object_vars($data));
				exit($data);
				break;
			case 'XML':				
				header("Content-Type:application/xml; charset=utf-8");
				//TODO xml�������
				exit(WView::xml_encode($data));
				break;
			case 'HTML':
				header("Content-Type:text/html; charset=utf-8");
				exit(serialize($data));
				break;
			default:
				exit($data);
		}
	}
	
	/**
	 * ��������װ����ȷ��xml���
	 * @param mixed $data ��Ҫ����������
	 * @return string �����������
	 */
	//TODO ����xml���Ƿ���ȫ�������
	public function xml_encode($data) {
		$xml = new DOMDocument();
	    $xml->formatOutput = true;
	    $root = $xml->createElement('phpwind');
	    $xml->appendChild(WView::data_format($xml, $root, $data));
		echo $xml->saveXML();
	}
	/**
	 * ������������xml�����ڵ�
	 * �������Ĳ������飺
	 *    �����ɵĵ��ڵ�����ͳһΪitem
	 * ���������Ƕ���
	 *    ת��Ϊ���顣
	 * �������������飺
	 *    �������飺���Լ���Ϊ�ڵ㣬��ֵΪ�ı��ڵ�
	 *    �������飺����item-$keyΪ�ڵ㣬��ֵΪ�ı��ڵ�
	 * @param DOMDocument $xml;
	 * @param DOMElement $root ���ڵ�
	 * @param mixed $data ����
	 */
	//����xml
	public function data_format(&$xml, &$root, $data) {
		if (is_object($data)) $data = get_object_vars($data);
		if (!is_array($data)) {
			$note = $xml->createElement('item');
			$note->appendChild($xml->createTextNode($data));
			$root->appendChild($note);
			return $root;
		}
		foreach ($data as $key => $value) {
			(is_numeric($key)) && $key = 'item-' . $key;
			$note = $xml->createElement($key);
			(!is_array($value)) ? $note->appendChild($xml->createTextNode($value)) : $note = WView::data_format($xml, $note, $value);
			$root->appendChild($note);
		}
		return $root;
	}
}