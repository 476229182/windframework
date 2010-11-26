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
class WindPack{
	
	private $packList = array();
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
		return preg_replace('/(?:\/\*.*\*\/)*|(?:\/\/[^\r\n]*[\r\n])*/Us',$replace,$content);
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
	 * ����ָ�������滻ָ����������Ӧ������
	 * @param string $content
	 * @param string $rule
	 * @param string $replace
	 * @return string
	 */
	public function stripStrByRule($content,$rule,$replace = ''){
		return preg_replace("/$rule/",$replace,$content);
	}
	
	/**
	 * ȥ��������ļ�������Ϣ
	 * @param string $content
	 * @param string $replace
	 * @return string
	 */
	public function stripImport($content,$replace = ''){
		$str = preg_match_all('/[\t\n\r]+L[\t ]*::[\t ]*import[\t ]*\([\t ]*[\'\"]([^$].+)[\"\'][\t ]*\)[\t ]*/',$content,$matchs);
		if($matchs[1]){
			foreach($matchs[1] as $key=>$value){
				$name = substr($value,strrpos($value,'.')+1);
				if(preg_match("/(abstract[\t ]*|class|interface)[\t ]+$name/i",$content)){
					$strip = str_replace(array('(',')'),array('\(','\)'),addslashes($matchs[0][$key])).'[\t ]*;';
					$content = $this->stripStrByRule($content,$strip,$replace);
				}
			}
		}
		return $content;
	}
	
	/**
	 * ȡ�ñ�������ļ��б�
	 * @return array:
	 */
	public function getPackList(){
		return $this->packList;
	}
	
	public function formatPackList($comment = false,$pack = array(),$samekey = ''){
		$sep = $comment ? "\r\n*" : "\r\n";
		$format = '';
		$pack = $pack && is_array($pack) ? $pack : $this->getPackList();
		foreach($pack as $key=>$value){
			if(is_array($value)){
				$format .= $this->formatPackList($comment,$value,$key);
			}else{
				$key = $samekey ? $samekey : $key;
				$format .= $key.'=>'.$value.$sep;
			}
		}
		return $format;
		
	}
	
	public function convertPackList($pack = array(),$samekey = ''){
		static $list = array();
		$pack = $pack && is_array($pack) ? $pack : $this->getPackList();
		foreach($pack as $key=>$value){
			if(is_array($value)){
				$this->convertPackList($value,$key);
			}else{
				$key = $samekey ? $samekey : $key;
				array_push($list,$key.'='.$value);
			}
		}
		return $list;
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
	 * @param string $suffix �ļ���׺����
	 * @return string
	 */
	public function getContentBySuffix($content,$suffix){
		switch($suffix){
			case 'php' : $content = '<?php'.$content.'?>';
			default: ;
		}
		return $content;
	}
	
	/**
	 * @param string $content
	 * @param string $suffix
	 * @param string $other
	 * @return string
	 */
	public function getCommentBySuffix($content,$suffix,$other= ''){
		switch($suffix){
			case 'php' : $content = "\r\n/**$other\r\n*".$content."\r\n*/\r\n";
			default: ;
		}
		return $content;
	}
	
	/**
	 * �����ļ��������ע��
	 * @param string $content
	 * @param string $suffix
	 * @return string
	 */
	public function getPackComment($content,$suffix){
		return $this->getCommentBySuffix($this->formatPackList(true),$suffix,'Your pack list').$content;
	}
	
	/**
	 * ��ָ��������ת�����ַ�����ʽ
	 * @param array $pack
	 * @return string
	 */
	public function getPackListAsString($pack = array()){
		$str = '';
		$packs = $pack ? $pack : $this->getPackList();
		foreach($packs as $key =>$value){
			$str .= (is_string($key) ? '"'.$key.'"' : $key ).'=>';
			if(is_array($value)){
				$str .='array(';
				$str .= $this->getPackListAsString($value);
				$str .= ')';
			}else{
				$str .= (is_string($value) ? '"'.$value.'"' : $value ).',';
			}
		}
		return empty($pack) ? 'array('.$str.')' : $str;
	}
	public function getPackImport($content = ''){
		$packlist = $this->getPackListAsString();
		return "\r\nL::setImports(".$packlist.");\r\n".$content;
	}
	
	/**
	 * �Ӹ���Ŀ¼��ȡ�ö�Ӧ��ÿ���ļ������� 
	 * @param mixed $dir Ŀ¼��
	 * @param array $ndir ����Ҫ������ļ���
	 * @param array $suffix ����Ҫ������ļ�����
	 * @param array $nfile ����Ҫ������ļ�
	 * @return array
	 */
	public function readContentFromDir($dir,$ndir = array('.','..','.svn'),$suffix = array(),$nfile = array()){
		static $content = array();
		$dir = is_array($dir) ? $dir : array($dir);
		foreach($dir as $_dir){
			if($this->isDir($_dir)){
				$handle = dir($_dir);
				while(false != ($tmp = $handle->read())){
					$name = $this->realDir($_dir).$tmp;
					if($this->isDir($name) && !in_array($tmp,$ndir)){
						$this->readContentFromDir($name,$ndir,$suffix,$nfile);
					}
					if($this->isFile($name) && !in_array($this->getFileSuffix($name),$suffix) && !in_array($file = $this->getFileName($name),$nfile)){
						$content[] = $this->readContentFromFile($name);
						$this->setPackList($file,$this->getRealName($name));
					}
				}
				$handle->close();
			}
		}
		return $content;
	}
	
	/**
	 * ��ӱ�������ļ����б�
	 * @param  string $key
	 * @param  string $value
	 */
	private function setPackList($key,$value){
		if(isset($this->packList[$key])){
			if(is_array($this->packList[$key])){
				array_push($this->packList[$key],$value);
			}else{
				$tmp_name = $this->packList[$key];
				$this->packList[$key] = array($tmp_name,$value);
			}
		}else{
			$this->packList[$key] = $value;
		}
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
	 * @param mixed $dir Ҫ�����Ŀ¼
	 * @param sgring $dst �ļ���
	 * @param array $ndir ����Ҫ�����Ŀ¼
	 * @param array $suffix �����������ļ�����
	 * @return string
	 */
	public function pack($dir,$dst,$ndir = array('.','..','.svn'),$suffix = array(),$nfile = array()){
		if(empty($dst)){
			return false;
		}
		$suffix = is_array($suffix) ? $suffix : array($suffix);
		if(!($content = $this->readContentFromDir($dir,$ndir,$suffix,$nfile))){
			return false;
		}
		$fileSuffix = $this->getFileSuffix($dst);
		$content = implode("\n\r",$content);
		$content = $this->stripComment($content);
		$content = $this->stripPhpIdentify($content);
		$content = $this->stripNR($content);
		$content = $this->stripSpace($content);
		$content = $this->getPackImport($content,$fileSuffix);
		$content = $this->stripImport($content);
		$content = $this->getContentBySuffix($content,$fileSuffix);
		$this->writeContentToFile($dst,$content);
		return true;
		
	}
	
	function strInContent($content,$findStr){
		return strstr($content,$findStr);
	}
	
	
	public function getFileSuffix($filename){
		return substr($filename,strrpos($filename,'.')+1);
	}
	
	public function getFileName($path,$ifsuffix = false){
		$filename = substr($path, strrpos($path,DIRECTORY_SEPARATOR)+1);
		return  $ifsuffix ? $filename : substr($filename,0,strrpos($filename,'.'));
	}
	
	private function getRealName($name){
		return WIND_PATH.$name;
	}
	
}
