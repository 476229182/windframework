<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-4
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * �������������
 * 
 * �û�Ҫ����Լ��Ĺ������࣬�����̳иó�����,�����û�ͨ��ʵ�֣�
 * doPreProcessing��doPostProcessing������������С�
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
abstract class WFilter {
	/**
	 * ����ù�������������Ϣ
	 * @var mixed $filterName 
	 */
	protected $filterConfig = '';
	/**
	 * ��ʼ�������������øù�������������Ϣ
	 * @param mixed $filterName
	 */
	public function init($filterConfig) {
		if ($filterConfig) $this->filterConfig = $filterConfig;
	}
	/**
	 * ��ù�����������Ϣ
	 * @return mixed
	 */
	public function getFilterConfig() {
		return $this->filterConfig;
	}
	/**
	 * �û���Ҫʵ��
	 * Ԥ���������û���ʵ���������û��ĸò�����Ԥ�������Ǻ��ò���
	 * @param WHttpRequest $httpRequest
	 */
	public abstract function doPreProcessing($httpRequest);
	/**
	 * �û���Ҫʵ��
	 * ���ò���
	 * @param WHttpRequest $httpRequest
	 */
	public abstract function doPostProcessing($httpRequest);
}