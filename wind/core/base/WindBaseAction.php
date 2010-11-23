<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

L::import('WIND:core.WindModelAndView');
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WindBaseAction {
	/**
	 * ҳ����ת��Ϣmodel and view����
	 * 
	 * @var $mav WindModelAndView
	 */
	protected $mav = null;
	
	/**
	 * ���������Ϣ
	 * 
	 * @var $viewer
	 */
	protected $view = null;
	
	public function __construct() {
		$this->view = new stdClass();
		$this->mav = new WindModelAndView();
	}
	
	public function beforeAction() {}
	
	public function afterAction() {}
	
	/**
	 * ����Ĭ��ģ��
	 */
	public function setDefaultViewTemplate($default) {
		$this->getModelAndView()->setViewName($default);
	}
	
	/**
	 * @return WindModelAndView $mav
	 */
	protected function getModelAndView() {
		return $this->mav;
	}
	
	/**
	 * ������ͼ����
	 * 
	 * @param WRouter $router
	 * @return WindForward
	 */
	public function getModulAndView() {
		return $this->mav;
	}

}