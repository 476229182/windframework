<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-16
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WActionForward {
	/**
	 * ҳ����תĿ���߼���ͼ����
	 * 
	 * @var $_forward
	 */
	private $_forward;
	
	/**
	 * ���������Ϣ
	 * 
	 * @var $_module
	 */
	private $_module;
	
	/**
	 * ��ͼ�Ĳ�����Ϣ
	 * 
	 * @var $_layout
	 */
	private $_layout;
	
	public function getLayout() {
		return $this->_layout;
	}
	
	public function setLayout($layout) {
		$this->_layout = $layout;
	}
	
	public function setModule($module) {
		$this->_module = $module;
	}
	
	public function getModule() {
		return $this->_module;
	}
	
	public function setForward($forward) {
		$this->_forward = $forward;
	}
	
	public function getForward() {
		return $this->_forward;
	}

}