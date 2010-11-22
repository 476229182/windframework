<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
class XML {
	private $XMLData;
	private $root;
	private $object;
	
	public function __construct($data) {
		$this->setXMLData($data);
	}
	
	public function setXMLData($data) {
		if (!$data) return false;
		if ($this->isXMLFile($data)) {
			$this->XMLData = trim($data);
		} else {
			throw new Exception('�������������Ч��xml��ʽ');
		}
	}
	/**
	 * ����ָ���ļ�·����ȡXML����
	 *
	 * @param string $filePath
	 */
	public function setXMLFile($filePath) {
		$filePath = realpath($filePath);
		if (!is_file($filePath) || strtolower(substr($filePath, -4)) != '.xml') throw new Exception("�������xml�ļ�������Ч��xml�ļ�");
		$this->setXMLData(file_get_contents($filePath));
	}
	/**
	 * �Ƿ�Ϊxml��ʽ�ļ�
	 *
	 * @return unknown
	 */
	private function isXMLFile($data) {
		if (strpos(strtolower($data), '<?xml') === false) {
			return false;
		}
		return true;
	}
	
	/**
	 * ����ָ��URL��ȡXML����
	 *
	 * @param string $url
	 */
	public function setXMLUrl($url) {
		$this->setXMLData(XML::PostHost($url));
	}
	
	public function doParser() {
   		$this->object = simplexml_import_dom(DOMDocument::loadXML($this->XMLData));
	}
	
	public function getXMLDocument() {
		return $this->object;
	}
	
	public function getElementByXPath($tagPath) {
		if ($tagPath) {
			return $this->object->xpath($tagPath);
		}
	}
    static public function getContentsList($elements) {
    	(!is_array($elements)) && $elements = array($elements);
    	$_result = array();
    	foreach ($elements as $key => $element) {
    		$_result[] = XML::getTagContents($element);
    	}
    	return $_result;
    }
    
	static public function getTagContents($element) {
		$_array = array();
		$_array['tagName'] = $element->getName();
		$_array['value'] = strval($element[0]);
		$_array['attributes'] = XML::getAttributes($element);
		$_array['children'] = XML::getChilds($element);
		return $_array;
	}
	
	/**
	 * ���ؽڵ������
	 * ʹ��XML::getAttributes($element);
	 * @param SimpleXMLElement $element
	 * @return array  ���ظýڵ������
	 */
	static public function getAttributes($element) {
		$_attributes = array();
		$attributes = $element->attributes();
		if (!$attributes) return $_attributes;
		
		foreach ($attributes as $key => $value) {
			$_attributes[$key] = strval($value);
		}
		return $_attributes;
	}
	
	static public function getChilds($element) {
		$_childs = array();
		$childs = $element->children();
		if (!$childs) return $_childs;
		foreach ($childs as $key => $value) {
			$_childs[] = XML::getTagContents($value);
		}
		return $_childs;
	}
	static private function PostHost($host, $data = '', $method = 'GET', $showagent = null, $port = null, $timeout = 30) {
		//Copyright (c) 2003-2103 phpwind
		$parse = @parse_url($host);
		if (empty($parse)) return false;
		if ((int)$port > 0) {
			$parse['port'] = $port;
		} elseif (!$parse['port']) {
			$parse['port'] = '80';
		}
		$parse['host'] = str_replace(array('http://', 'https://'), array('', 'ssl://'), $parse['scheme'] . "://") . $parse['host'];
		if (!$fp = @fsockopen($parse['host'],$parse['port'],$errnum,$errstr,$timeout)) return false;
		$method = strtoupper($method);
		$wlength = $wdata = $responseText = '';
		$parse['path'] = str_replace(array('\\', '//'), '/', $parse['path']) . "?" . $parse['query'];
		if ($method == 'GET') {
			$separator = $parse['query'] ? '&' : '';
			substr($data,0,1) == '&' && $data = substr($data,1);
			$parse['path'] .= $separator.$data;
		} elseif ($method == 'POST') {
			$wlength = "Content-length: " . strlen($data) . "\r\n";
			$wdata = $data;
		}
		$write = "{$method} $parse[path] HTTP/1.0\r\nHost: $parse[host]\r\nContent-type: application/x-www-form-urlencoded\r\n{$wlength}Connection: close\r\n\r\n{$wdata}";
		@fwrite($fp, $write);
		while ($data = @fread($fp, 4096)) {
			$responseText .= $data;
		}
		@fclose($fp);
		empty($showagent) && $responseText = trim(stristr($responseText, "\r\n\r\n"), "\r\n");
		return $responseText;
	}
}
