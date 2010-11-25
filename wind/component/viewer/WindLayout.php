<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * ���ֶ���
 * ͨ������һ�����ֶ��󣬻��߲��������ļ����������ò��ֱ�����ʵ��ҳ�沼��
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindLayout {
	/**
	 * ������Ϣ
	 * @var $_segments
	 */
	private $segments = array();
	private $layout = '';
	
	/**
	 * ����layout�����ļ�
	 * ����Ϊһ�������ļ����߼����ƣ��磺layout.mainLayout
	 * ��������ģ��·������Ѱ��layoutĿ¼�µ�mainLayout�����ļ�����׺����ģ��ĺ�׺������һ��
	 * 
	 * @param string $layout
	 */
	public function setLayoutFile($layout) {
		$this->layout = $layout;
	}
	
	/**
	 * ����ģ���ļ������ļ�
	 * ����Ϊһ�������ļ����߼����ƣ��磺segments.header
	 * ��������ģ��·������Ѱ��segmentsĿ¼�µ�header�����ļ�����׺����ģ��ĺ�׺������һ��
	 * 
	 * @param string $fileName
	 */
	private function includeFile($fileName) {
		$this->setSegments($fileName);
	}
	
	private function setSegments($segment) {
		$this->segments[] = $segment;
	}
	
	private function setContent($key = 'current') {
		$this->setSegments('key_' . $key);
	}
	
	/**
	 * ����layout�����ļ�
	 */
	public function parserLayout($dirName = '', $ext = '') {
		if ($this->layout) {
			$file = L::getRealPath($dirName . '.' . $this->layout, false, $ext);
			if (!$file) throw new WindException('cant find layout file.');
			@include $file;
		}
		return $this->segments;
	}

}