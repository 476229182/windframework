<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
class indexController extends WActionController {
	private $a = 2;
	public $b = 3;
	protected $c;
	public function run() {
		$view = WViewFactory::getInstance()->create('body.phtml');
		$view->windAssign('content', 'Hello World!');
		$view->windAssign('name', '��ȵ�š�С��');
		$view->windAssign('title', 'Smarty����֮��Ĳ���');
		$view->windAssign('count', '8888888');
	}
	
	public function show() {
		$this->setForward('foot.phtml');
		$this->setView('content', 'welcome');
		$this->setView('name', '��ȵ�š�С��');
		$this->setView('count', '1000');
	}
}