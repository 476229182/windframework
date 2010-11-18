<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-6
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
W::import('WIND:utilities.container.WModule');

/**
 * ������Ϣ
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WConfig extends WModule {
	
	/**
	 * ������Ϣ��������
	 * @param array $configSystem
	 * @param array $configCustom
	 */
	public function parse($configSystem, $configCustom) {}

	
	/**
	 * ������Ϣ��������
	 * @param xml $configSystem
	 * @param xml $configCustom
	 */
	public function parseXML($configSystem, $configCustom) {}

	
	/**
	 * �����������ƻ��������Ϣ
	 * @param string $configName
	 */
	public function getConfig($configName) {}


}