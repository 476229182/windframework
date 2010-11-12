<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-9
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */

class WViewFactory {
	private static $configs = array();
	private static $instance;
	private $viewContents = '';
	private $var;//ģ��������
	
	//TODO ��ͼ������Ϣ����
	public function getInstance($config = NULL) {
		$config = array('engine' => 'php', 'cache_path' => '');
	}
	
	public function assign($val, $value) {
		if (is_object($val)) {
			$this->var[$val] = get_object_vals($val);
		} else {
			$this->var[$val] = $value;
		}
	}
	public function __set($val, $value) {
		$this->assign($val, $value);
	}
	public function __get($val) {
		if (isset($this->val[$val])) return $this->val[$val];
		return null;
	}
	
	//TODO ��Ч���жϣ��˴��Ƿ���Խ������ʵ��ת�����������У��Ա������ط�����
	public function redirect($url, $params=array(), $delayTime=0, $msg='') {
		$url = str_replace(array("\n", "\r"), '', $url);
		$parse = '';
		foreach ((array)$params as $key => $value) {
			if ($value != '') $parse .= "{$key}={$value}&";
		}
		(strpos($url, '?') === false) ? $url .= "?{$parse}" : "&{$parse}";
		if ($msg == '') {
			$msg = "ϵͳ����{$delayTime}��֮���Զ���ת��!";
		}
		$delayTime = intval($delayTime);
		if (!headers_sent()) {
			if ($delayTime === 0) {
				header('Location:' . $url);
				exit();
			} else {
				header("refresh:{$delayTime};url={$url}");
            	exit($msg);
			}
		}
		$jumpStr = "<meta http-equiv='Refresh' content='{$delayTime};URL={$url}'>";
		($delayTime > 0) && $jumpStr .= $msg;
		exit($jumpStr);
	}
	
	public function display($templateFile='', $charset='', $contentType='') {
		
	}
	
	//TODO ���ģ������
	public function fetch($templateFile='', $charset='', $contentType='text/html', $return=true){
		
	}
	//TODO  cache���棬ͬʱ�ܵ��ý�����ͬģ������
}