<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * ��ͼ�������
 * ͨ���̳и÷�������ʵ�ֶ���ͼģ��ĵ��ý���
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
interface WindViewerImpl {
	
	/**
	 * ������ͼ������Ϣ
	 * 
	 * @param array $vars
	 * @param string $key
	 */
	public function windAssign($vars, $key = '');
	
	/**
	 * ��ȡģ�������������Ϣ
	 */
	public function windFetch($template = '');
	
	/**
	 * ���һ����ͼ��Ϣ������ʼ��������
	 * @param WindView $view
	 */
	public function initWithView($view);
	
}