<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-7
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
class WWebApplicationController implements WApplicationController {
	private $router = NULL;
	
	function processRequest($request) {

	}
	
	/**
	 * ���һ��·��ʵ��
	 * @param WSystemConfig $configObj
	 */
	function createRouter($configObj) {
		$router = WRouterFactory::getFactory()->create($configObj);
		if ($router === null)
			throw new WException('create router failed!!');
		return $router;
	}
}