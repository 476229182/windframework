<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-12
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
W::import('WIND:utilities.container.WModule');

/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WActionForm extends WModule {
	protected $_isValidate = false;
	
	/**
	 * @param WHttpRequest $request
	 * @param WHttpResponse $response
	 */
	public function __construct($request, $response) {
		$this->_setProperties($request);
	}
	
	/**
	 * ��֤���������ø÷������������֤����
	 * ִ���ڼ̳�WActionForm���actionForm�У�������validate��β�ĺ���
	 */
	public function validation() {
		$object = new ReflectionClass(get_class($this));
		$validationMethods = $object->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach ($validationMethods as $_value) {
			if (strtolower(substr($_value->name, -8)) == 'validate')
				call_user_func(array($this, $_value->name));
		}
	}
	
	private function _addError() {
		
	}
	
	/**
	 * ��������ֵ
	 * �ڼ̳�WActionForm���actionForm�У�������Ҫ���õ�����Ӧ����ʾ��������setter��������������������
	 * @param WHttpRequest $request
	 */
	private function _setProperties($request) {
	   $_params = array();
	   if ($request->isGet()) $_params = $request->getGet();
	   elseif ($request->isPost()) $_params = $request->getPost();
	   if (!$_params) return false;
	   foreach ($_params as $_key => $_value) {
	   	  //�Ƿ�������setter���������еĿռ�����form�е�������һһ��Ӧ
	   	   $this->$_key = $_value;
	   }
	}
	
	/**
	 * �Ƿ�����֤
	 * @return string
	 */
	public function getIsValidation() {
		return $this->_isValidate;
	}

}