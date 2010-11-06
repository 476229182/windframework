<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-6
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WSystemConfig extends WConfig implements WContext {
	private $systemConfig;
	private $config;
	
	/**
	 * 
	 * @param array $sytemConfig	//��ܵ�Ĭ������
	 * @param array $config			//Ӧ������
	 */
	function parse($sytemConfig, $config = NULL) {
		
		$args = func_get_args();
		$obj = isset($args[2]) ? $args[2] : $this;
		if (empty($args[2])) {
			$systemConfig = $this->getSystemConfig();
			$config = array_merge($systemConfig, $config);
		}
		foreach ($config as $key => $value) {
			if ($ifRecursion && is_array($value)) {
				$obj->{$key} = new stdClass();
				$obj->parse($value, $ifRecursion, $this->{$key});
			} else {
				$obj->{$key} = $value;
			}
		}
	}
	
	/**
	 * xml ��ʽ���ý���
	 */
	function parse1() {

	}
	
	function getRouterConfig() {
		return '';
	}
	
	/**
	 * 
	 */
	function getConfig($configName) {
		return '';
	}
	
	/**
	 * @return WSystemConfig
	 */
	public static function getInstance() {
		return W::getInstance(__CLASS__);
	}

}

?>