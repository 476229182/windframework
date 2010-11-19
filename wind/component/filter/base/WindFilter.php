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
abstract class WindFilter {
	/**
	 * ����ù�������������Ϣ
	 * @var mixed $filterName
	 */
	protected $filterConfig = '';
	/**
	 * ��ʼ�������������øù�������������Ϣ
	 * @param WSystemConfig $configObj
	 */
	public function init($configObj = null) {}
	
	/**
	 * @param WRequest $request
	 * @param WResponse $response
	 */
	public function doFilter($request, $response) {
		$this->doBeforeProcess($request, $response);
		$filter = WFilterFactory::getFactory()->create();
		if ($filter != null) {
			if (!in_array(__CLASS__, class_parents($filter))) throw new WException(get_class($filter) . ' is not extend a filter class!');
			
			$filter->doFilter($request, $response);
		} else
			WFilterFactory::getFactory()->execute();
		$this->doAfterProcess($request, $response);
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
	abstract protected function doBeforeProcess($request, $response);
	
	/**
	 * �û���Ҫʵ��
	 * ���ò���
	 * @param WHttpRequest $httpRequest
	 */
	abstract protected function doAfterProcess($request, $response);
}