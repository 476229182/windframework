<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-10-27
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license 
 */


/**
 * 
 * �����ǰ�˿������ӿڣ�ͨ�����ɸýӿڿ���ʵ������ְ��
 * 
 * ְ���壺
 * ���ܿͻ�����
 * ��������
 * ��ͻ��˷�����Ӧ
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WFrontController extends WBaseFrontController {
	
	/**
	 * @param unknown_type $request
	 */
	protected function processRequest($request) {
		WRouterManager::init();
		echo "hello world";
	}
	
	/**
	 * @param unknown_type $request
	 */
	protected function dispatch($request) {

	}

}