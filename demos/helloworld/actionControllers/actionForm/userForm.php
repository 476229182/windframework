<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-30
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
L::import('WIND:component.form.base.WindActionForm');

class UserForm extends WindActionForm {
	protected $_isValidate = true;
	protected $username = 'xxx123';
	protected $password = 'xxx123';
	protected $birth = '1987-1-1';
	
	public function usernameValidate() {
		if (strlen($this->username) < 5) {
			$this->addError('');
		}
		return true;
	}
	
	public function __tostring() {
		echo '<br/>';
		echo '�����û���Ϊ��' . $this->username . '<br/>';
		echo '��������Ϊ��' . $this->password . '<br/>';
		echo '��������Ϊ��' . $this->birth . '<br/>';
		return '';
	}
}