<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 
 * �����ǰ�˿������ӿڣ�ͨ�����ɸýӿڿ���ʵ������ְ��
 * 
 * ְ���壺
 * ���ܿͻ�����
 * ��������
 * ��ͻ��˷�����Ӧ
 * 
 * the last known user to change this file in the repository  
 * <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
abstract class WBaseFrontController {
	
	/**
	 * ������ĺ��Ĵ�����
	 * ͨ��ʵ�ָ÷������������ĺ��Ĵ�����
	 * 
	 * @param WHttpRequestContext $request
	 */
	abstract protected function processRequest($request);
	
	function __construct() {
		$this->init();
	}
	
	private function init() {
		
	}
	
	/**
	 * ���ܿͻ������������ĳ�ʼ������
	 * ����������н������û�������ͷ����Ϣ��
	 * ����������ͷ����Ϣ�����������
	 * 
	 * @param WRequestContext $request
	 */
	function run() {
		$request = Null;
		$this->service($request);
	}
	
	/**
	 * ����HTTP�����GET����
	 * @param WHttpRequestContext $request
	 * 
	 */
	protected function doGet($request) {
		$this->processRequest($request);
	}
	
	/**
	 * ����HTTP�����POST����
	 * @param WHttpRequestContext $request
	 */
	protected function doPost($request) {
		$this->processRequest($request);
	}
	
	/**
	 * ����ת����������Ȩ��Ӧ��ϵͳ��һ����ת������һ������
	 * �˴�ͨ��ʵ��dispatch�����������������ת������ͼ�������
	 * @param WHttpRequestContext $request
	 * @param $viewModel
	 */
	protected function dispatch($request, $viewModel) {
		//TODO
	}
	
	/**
	 * ����ͻ�������
	 * �÷������ݿͻ��˵��������ͣ�������Ӧ�ķ������д���
	 * @param WHttpRequestContext $request
	 * @access private
	 */
	private function service($request) {
		$this->doGet($request);
	}
}