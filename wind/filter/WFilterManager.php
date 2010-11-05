<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-4
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
WBasic::import('filter.WFilterChain');
/**
 * ʵ��filter�Ĺ�����
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WFilterManager {
	/**
	 * ������������Ķ���
	 * @var WFilterChain $filterChain
	 */
	private $filterChain;
	/**
	 * �����������������Ϣ
	 * @var array
	 */
	private $filterConfig;
	/**
	 * ����������Ĺ�����
	 * @param WConfigParser $configParser
	 */
	public function __construct($configParser) {
		$this->filterChain = new WFilterChain();
		$this->filterConfig = $configParser->filterConfig;
		$this->addFilters();
	}
	
	/**
	 * ���������ļ�װ�ع�����
	 * @param WConfigParser $configParser
	 */
	private function addFilters() {
		foreach ($this->filterConfig as $filter) {
			if (!class_exists($filter, true) ) {
				echo ('������' . $filter . '������<br/>');
			  // throw new Exception('������' . $filter . '������');
			   continue;
			}
			$this->filterChain->addFilter(new $filter());
		}
	}
	
	/**
	 * ������ת�����
	 * @param array $callBack  �ص�����
	 * @param WHttpRequest $httpRequest
	 */
	public function filterProcessing($callBack, $httpRequest) {
		$this->filterChain->doFilter($callBack, $httpRequest);
	}
}

