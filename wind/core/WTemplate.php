<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-15
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * phpwindģ���������
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WTemplate {
	/**
	 * ���ģ����Ϣ
	 * @param string $from ģ����λ��
	 * @param string $to ����õ�ģ��λ��
	 * @param array $vars ����ģ��ı���
	 * @param integer $time ģ�����Чʱ��
	 * @return string ���������
	 */ 
	public function fetch($from, $to, $vars, $time = null) {
		if (!$this->checkCache($from, $to, $time))  $this->compile($from, $to);
		extract($vars, EXTR_OVERWRITE );
		include $to;
		return $this->filterOutPut(ob_get_contents());
	}
	/**
	 * �������������
	 * @param string $_output
	 * @return string $_output
	 */
	//TODO �˴�������Ӷ����filter
	private function filterOutPut($_output) {
		//$_output = str_replace(array("\r", '<!--<!---->-->', '<!---->-->', '<!--<!---->', "<!---->\n", '<!---->', '<!-- -->', "<!--\n-->", "\t\t", '    ', "\n\t", "\n\n"), array('', '', '', '', '', '', '', '', '', '',"\n", "\n"), $_output);
		$_output = str_replace(array('<!--<!---->-->','<!---->-->', '<!--<!---->', "<!---->\r\n", '<!---->', '<!-- -->', "\t\t\t"), '', $_output);
		return $_output;
	}
	/**
	 * ����ģ������ģ�建���ļ�
	 * @param string templateFile
	 * @param string $cacheFile
	 */
	private function compile($templateFile, $cacheFile) {
		include (R_P . '/all_lang.php');		//ģ���ڵ�����
		$content = preg_replace("/{#([\w]+?)}/eis",'$lang[\\1]', readover($templateFile));
		$this->createLangForder($cacheFile);
		if (readover($cacheFile) != $content) {
			writeover($cacheFile, $content);
		}
	}
	private function createLangForder($file) {
		$to_dir = substr($file, 0, strrpos($file,'/'));
		if (!is_dir($to_dir)) {
			$this->createFile(dirname($to_dir));
			@mkdir($to_dir);
			@chmod($to_dir,0777);
			@fclose(@fopen($to_dir.'/index.html','w'));
			@chmod($to_dir.'/index.html',0777);
		}
	}
	private function createFile($path) {
		if (!is_dir($path)) {
			$this->createFile(dirname($path));
			@mkdir($path);
			@chmod($path,0777);
		}
	}
	/**
	 * �ж��Ƿ��Ѿ�����
	 * @param string $template
	 * @param string $cache
	 * @param integer $time �������ʱ��
	 */
	private function checkCache($template, $cache, $time = null) {
		if (!file_exists($cache)) return false; 
		if (filemtime($cache) < filemtime($template)) return false;//���ģ���Ƿ����
		if ($time && time() > (filemtime($cache)+intval($time))) return false;
		return true;
	}
}