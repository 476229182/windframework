<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @link WRouteParser
 * @package 
 */
class WUrlRouter extends WRouter {
	protected $_parserName = 'url';
	
	/**
	 * ���ø÷���ʵ��·�ɽ���
	 * ��õ� request �ľ�̬���󣬵õ�request��URL��Ϣ
	 * ��� config �ľ�̬���󣬵õ�URL�ĸ�ʽ��Ϣ
	 * ����URL��������RouterContext����
	 * @param WSystemConfig $configObj
	 * @return WRouterContext
	 */
	public function doParser($request, $response) {
		
	}
	
}