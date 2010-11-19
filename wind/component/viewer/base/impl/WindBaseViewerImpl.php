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
interface WindBaseViewerImpl {
	
	public function setTpl($tpl = '');
	
	public function setLayout($layout = null);
	
	public function windDisplay($tpl = '');
	
	public function windAssign($vars = '', $key = null);
	
	public function windFetch();
	
	public function setCacheDir($cacheDir); //���û���·��
	
	public function setCompileDir($compileDir);//���ñ���·��
	
	public function setTemplateDir($templateDir);//����ģ��·��
}