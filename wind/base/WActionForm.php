<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-12
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

abstract class WActionFrom extends WModule {
	private $_isValidate = false;
	
	/**
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 */
	public function __construct($request, $response) {

	}
	
	/**
	 * ��֤���������ø÷������������֤����
	 */
	public function validation() {
		$class = new ReflectionClass(get_class($this));
		$validationMethod = $class->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach ($validationMethod as $key => $value) {

		}
	}
	
	private function _addError() {

	}
	
	/**
	 * ��������ֵ
	 * @param WHttpRequest $request
	 */
	private function _setProperties($request) {
		
	}
	
	/**
	 * �Ƿ�����֤
	 * @return string
	 */
	public function getIsValidate() {
		return $this->_isValidate;
	}

}