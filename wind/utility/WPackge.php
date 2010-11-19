<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * ����������
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WPackge{
	 
	
	
	
	
	
	
	/**
	 * ȥ��ָ���ļ���ע�ͼ��հ�
	 * @param string $filename �ļ���
	 */
	public function  stripWhiteSpace($filename){
		return php_strip_whitespace($filename);
	}
	
	
	
	
	
	/**
	 * ȥ��ע��
	 * @param string $content Ҫȥ��������
	 * @param string $replace Ҫ�滻���ı�
	 * @return string
	 */
	public function stripComment($content,$replace = ''){
		return preg_replace("/(?:\/\*.*\*\/)*|(?:\/\/[^\r\n\'\"]*[\r\n])*/Us",$replace,$content);
	}
	
	/**
	 * ȥ������
	 * @param string $content Ҫȥ��������
	 * @param string $replace Ҫ�滻���ı�
	 * @return string
	 */
	public function stripNR($content,$replace = "\n"){
		return preg_replace("/[\n\r]+/",$replace,$content);
	}
	
	/**
	 * ȥ���ո��
	 * @param string $content Ҫȥ��������
	 * @param string $replace Ҫ�滻���ı�
	 * @return string
	 */
	public function stripSpace($content,$replace = ' '){
		return preg_replace("/[ ]+/",$replace,$content);
	}

	/**
	 * ȥ��php��ʶ
	 * @param string $content
	 * @param string $replace
	 * @return string
	 */
	public function stripPhpIdentify($content,$replace = ''){
		return preg_replace("/(?:<\?(?:php)*)|(\?>)/i",$replace,$content);
	}
	
	/**
	 *���ļ���ȡ����
	 * @param string $filename �ļ���
	 * @return string
	 */
	public function readContentFromFile($filename){
		if($this->isFile($filename)){
			$fp = fopen($filename, "r");
			while(!feof($fp)){
				$line = fgets($fp);
				if(in_array(strlen($line),array(2,3)) && in_array(ord($line),array(9,10,13)) )
					continue;
				$content .= $line;
			}
			fclose($fp);
			return $content;
		}
		return false;
	}
	
	/**
	 * �����ݴ�����ļ�
	 * @param string $filename �ļ�����
	 * @param string $content  Ҫ�����ָ���ļ�������
	 * @return string
	 */
	public function writeContentToFile($filename,$content){
		$fp = fopen($filename, "w");
		fwrite($fp,$content);
		fclose($fp);
		return true;
	}
	/**
	 * �����ļ���׺��ȡ��Ӧ��mime����
	 * @param string $content Ҫ�������������
	 * @param string $mime �ļ���׺����
	 * @return string
	 */
	public function getContentByMime($content,$mime = 'php'){
		switch($mime){
			case 'php' : $content = '<?php'.$content.'?>';
			default: $content = $content;
		}
		return $content;
	}
	
	/**
	 * �Ӹ���Ŀ¼��ȡ�ö�Ӧ��ÿ���ļ������� 
	 * @param string $dir Ŀ¼��
	 * @param array $ndir ����Ҫȡ���ļ����ݵ�Ŀ¼
	 * @return array
	 */
	public function readContentFromDir($dir,$ndir = array('.','..','.svn')){
		static $content = array();
		if($this->isDir($dir)){
			$handle = dir($dir);
			while(false != ($tmp = $handle->read())){
				$name = $this->realDir($dir).$tmp;
				if($this->isDir($name) && !in_array($tmp,$ndir)){
					$this->readContentFromDir($name);
				}
				if($this->isFile($name)){
					$content[$dir] = $this->readContentFromFile($name);
				}
			}
			$handle->close();
		}
		return $content;
	}
	
	/**
	 * ȡ����ʵ��Ŀ¼
	 * @param string $path ·����
	 * @return string
	 */
	public function realDir($path){
		if(($pos = strrpos($path,DIRECTORY_SEPARATOR)) === strlen($path) - 1){
			return $path;
		}
		return $path.DIRECTORY_SEPARATOR;
	}
	
	/**
	 * �ж��Ƿ���һ���ļ�
	 * @param string $filename �ļ���
	 * @return boolean
	 */
	public function isFile($filename){
		return is_file($filename);
	}
	
	/**
	 * �ж��Ƿ���һ��Ŀ¼
	 * @param string $dir Ŀ¼��
	 * @return boolean
	 */
	public function isDir($dir){
		return is_dir($dir);
	}
	
	/**
	 * ��ָ��Ŀ¼�µ������ļ����ݴ����һ���ļ�
	 * @param string $dir Ҫ�����Ŀ¼
	 * @param sgring $dst �ļ���
	 * @param array $ndir ����Ҫ�����Ŀ¼
	 * @return string
	 */
	public function packge($dir,$dst,$ndir = array('.','..','.svn')){
		if(empty($dst)){
			return false;
		}
		if(!($content = $this->readContentFromDir($dir,$ndir))){
			return false;
		}
		$mime = substr($dst,strrpos($dst,'.')+1);
		$content = implode("\n\r",$content);
		$content = $this->stripComment($content);
		$content = $this->stripPhpIdentify($content);
		$content = $this->stripNR($content);
		$content = $this->stripSpace($content);
		$content = $this->getContentByMime($content,$mime);
		$this->writeContentToFile($dst,$content);
		return true;
		
	}
	
	public function packgeByDir($dir){
		
	}
	
	public function packgeByTime($dir){
	}
	
	
}




$dir =  substr(__FILE__,0,strrpos(__FILE__,DIRECTORY_SEPARATOR));
echo $dir;

$pack = new WPackge();
//echo php_strip_whitespace(__FILE__);
$content = $pack->packge("E:\www\bbs\phpwind_wind\wind",'test.php');

