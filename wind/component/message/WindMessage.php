<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-25
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

class WindMessage {
	
	private $message = array();
	
	/**
	 * ���һ��message��Ϣ
	 * 
	 * @param string $message
	 * @param string $key
	 */
	public function addMessage($message, $key = '') {
		if (empty($message)) return;
		if (is_array($message)) {
			foreach ($message as $key => $value) {
				$this->addMessage($value, $key);
			}
		} else {
			if ($key)
				$message[$key] = $message;
			else
				$message[] = $message;
		}
	}
	
	/**
	 * ���һ��message��Ϣ
	 * 
	 * @param string $key
	 * @return string|array
	 */
	public function getMessage($key = '') {
		return $key ? $this->message[$key] : $this->message;
	}
	
	/**
	 * ������ķ�ʽ����message��Ϣ
	 * 
	 * @param string $key
	 * @return array
	 */
	public function getMessageWithArray($key = '') {
		return (array) $this->getMessage($key);
	}
	
	/**
	 * ���ַ�����ʽ����message
	 * @return string
	 */
	public function getMessageWithString($key = '') {
		$message = implode(',', (array) $this->getMessage($key));
		return trim($message, ' ,');
	}
	
	/**
	 * ����message
	 * 
	 * @param string $key
	 */
	public function clear($key = '') {
		if ($key)
			unset($this->message[$key]);
		else
			$this->message = array();
	}

}