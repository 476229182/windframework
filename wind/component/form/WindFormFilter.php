<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-30
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import("WIND:component.filter.base.WindFilter");

/**
 * form�����һ���������ʵ��
 * �û���Ҫ����form�����ʱ��ֻҪ�������ļ���<filters>�������������ø�formfilter�����Զ�ʹ���û������form
 * �����ļ��е��������£�
 * <filters>
 *		<filter name="WindFormFilter">
 *		   <filterName>WindFormFilter</filterName> 
 *		   <filterPath>WIND:component.form.WindFormFilter</filterPath> 
 *		</filter>
 *	</filters>
 *
 *form���������Դ���������У������û����õ�formName��Ӧ��form�������õı�������һ����ֵ��
 *����û�Ҳ��������ص���֤��������Ҳ��ִ����֤������
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WindFormFilter extends WindFilter {
	/**
	 * �����û���Ҫʹ��form�����֣��ṩ���û�������ʶ���ִ�
	 * @var string
	 */
	const FORMNAME = 'formName';
	
	public function doAfterProcess($request, $response) {
	}
	/**
	 * ִ��ǰ�ò���
	 * 
	 * ��ִ���û�actionǰ����ִ���û���form����������form, ��form��ֵ��ִ��form����֤�������form����
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	public function doBeforeProcess($request, $response) {
		$formObject = $this->getFormHandle($request, $response);
		if ($formObject === null) return;
	    $formObject->setProperties($request->getGet());
	    $formObject->setProperties($request->getPost());
	    $this->validation($formObject);
	    $formObject->save();
	}
	/**
	 * ִ���û�����form�е���֤����
	 * ����д�����Ϣ��װ������Ϣ���
	 * @param WindActionForm $formObject
	 */
	private function validation($formObject) {
		//TODO
	    ($formObject->getIsValidation()) &&  $formObject->validation();
	}
	
	/**
	 * ��ö�Ӧform�ľ��
	 * �����û������formName������form,
	 * �����û������form
	 * @param WindHttpRequest $request
	 * @param WindHttpResponse $response
	 */
	private function getFormHandle($request, $response) {
		$formName = $request->getGet(self::FORMNAME) ? $request->getGet(self::FORMNAME) : $request->getPost(self::FORMNAME);
	    if (!$formName) return null;
	    $module = C::getConfig('modules', $response->getDispatcher()->getModule());
		L::import($module['path'] . ".actionForm." . $formName);
	    if (!class_exists($formName)) {
	    	throw new WindException('Class \'' . $formName . '\' is not exists!');
	    }
	    $formObject = new $formName();
	    if (!$formObject instanceof WindActionForm) {
	    	throw new WindException('The class \'' . $formName . '\' must extend WindActionForm!');
	    }
	    return $formObject;
	}
}