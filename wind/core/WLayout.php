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
class WLayout {
	/* layout �߼����� */
	private $layout = '';
	
	/* ģ���ļ�·����Ϣ */
	private $tpl = '';
	
	private $dirName = '';
	private $ext = '';
	private $tplName = '';
	
	/**
	 * ������Ϣ
	 * array('fileName' => 'filePath')
	 * @var $_segments
	 */
	private $segments = array();
	
	/**
	 * ����layout�����ļ�
	 * 
	 * @param string $layout
	 */
	public function setLayout($layout) {
		$this->layout = $layout;
	}
	
	/**
	 * ����ģ���·����Ϣ
	 * @param string $tpl
	 */
	public function setTpl($tpl) {
		$pathInfo = @pathinfo($tpl);
		$this->dirName = $pathInfo['dirname'];
		$this->ext = $pathInfo['extension'];
		$this->tplName = substr($pathInfo['basename'], 0, strrpos($pathInfo['basename'], '.'));
		$this->tpl = $tpl;
	}
	
	/**
	 * ͨ�����������ļ�����ò�����Ϣ
	 * @param string $config
	 */
	public function parser() {
		if ($this->layout)
			$this->_parserLayoutFile();
	}
	
	/**
	 * ����ҳ��Ƭ��
	 * 
	 * @param array|string $segment
	 */
	public function setSegments($segment) {
		if (is_array($segment))
			$this->segments += $segment;
		else
			$this->segments[] = $segment;
	}
	
	public function getSegments() {
		foreach ($this->segments as $key => $value) {
			$file = $this->dirName . W::getSeparator() . $value . '.' . $this->ext;
			if (file_exists($file))
				$this->segments[$value] = $file;
		}
		return $this->segments;
	}
	
	/**
	 * ����layout�����ļ�
	 */
	private function _parserLayoutFile() {
		$file = $this->dirName . W::getSeparator() . $this->layout . '.' . $this->ext;
		if (file_exists($file))
			include $file;
	}
}