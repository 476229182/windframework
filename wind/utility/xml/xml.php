<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * xml�����Ĺ���
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class XML {
	/**
	 *  ��Ҫ����������
	 *  
	 * @var string
	 */
	protected $XMLData; 
	/**
	 * ���������Ķ���
	 * 
	 * @var SimpleXMLElement
	 */
	protected $object;
	/**
	 * ��������ı���
	 * 
	 * @var string
	 */
	protected $outputEncoding;
	
	/**
	 * ���캯��,��ʼ������
	 * 
	 * @param string $data
	 * @param string $encoding
	 */
	public function __construct($data = '', $encoding = 'gbk') {
		$this->setXMLData($data);
		$this->setOutputEncoding($encoding);
	}
	
	/**
	 * ������Ҫ������xml����
	 * 
	 * @param string $data
	 */
	public function setXMLData($data) {
		if (!$data) return false;
		if ($this->isXMLFile($data)) {
			$this->XMLData = trim($data);
		} else {
			throw new Exception('�������������Ч��xml��ʽ');
		}
	}
	/**
	 * ���ý�������ı���
	 * 
	 * @param string $encoding
	 */
	public function setOutputEncoding($encoding) {
		if ($encoding) $this->outputEncoding = strtoupper(trim($encoding));
	}
	
	/**
	 * ����ָ���ļ�·����ȡXML����
	 *
	 * @param string $filePath
	 */
	public function setXMLFile($filePath) {
		$filePath = realpath($filePath);
		if (!is_file($filePath) || strtolower(substr($filePath, -4)) != '.xml') throw new Exception("The file which your put is not a well-format xml file!");
		$this->setXMLData(file_get_contents($filePath));
	}
	
	/**
	 * �Ƿ�Ϊxml��ʽ�ļ�
	 * 
	 * @access private
	 * @return boolean
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
	
	/**
	 * ������������
	 */
	public function ceateParser() {
   		$this->object = simplexml_import_dom(DOMDocument::loadXML($this->XMLData));
	}
	
	/**
	 * ���ؽ�������
	 * 
	 * @return SimpleXMLElement
	 */
	public function getXMLDocument() {
		return $this->object;
	}
	
	/**
	 * ���ݱ�ǩ��·����øñ�ǩ�Ķ���
	 * 
	 * ���¸�ʽ<pre/>:
	 * <WIND>
	 *    <app>
	 *       <appName>Test</appName>
	 *    </app>
	 * </WIND>
	 * 1���������·�����ã�
	 * 		��Ҫ���app�µ���������˵��ã�  $xmlObject->getElementByXPath('app');
	 * 		��Ҫ���app�µ�appName����������˵��ã�$xmlObject->getElementByXPath('app/appName');
	 * 2��������ȫ·�����ã�
	 * 		��Ҫ���app�µ���������˵��ã�  $xmlObject->getElementByXPath('/WIND/app');
	 * 		��Ҫ���app�µ�appName����������˵��ã�$xmlObject->getElementByXPath('/WIND/app/appName');
	 * 
	 * @param string $tagPath
	 * @return array SimpleXMLElement objects 
	 */
	public function getElementByXPath($tagPath) {
		if ($tagPath) return $this->object->xpath($tagPath);
	}
	
	/**
	 * ����ͨ��getElementByXPath��õĶ��󼯺�,���������Ӧ������
	 * 
	 * ÿ��Ԫ�ض��и�ʽ
	 * $array = array('tagName' => '�ñ�ǩ������',
	 * 				  'value' => '��Ӧ��ǩ������',
	 * 				  'attributes' => array('��ǩ���Ե�����' => '�����Զ�Ӧ��ֵ', ...),
	 *                'children' => array(child1, child2,....);
	 * 
	 * 
	 * @param array SimpleXMLElement objects   $elements
	 * @return array
	 */
    public function getContentsList($elements) {
    	(!is_array($elements)) && $elements = array($elements);
    	$_result = array();
    	foreach ($elements as $key => $element) {
    		$_result[] = self::getTagContents($element);
    	}
    	return $_result;
    }
    
    /**
	 * ������SimpleXMLElement����,���������Ӧ�����ݼ����ӱ�ǩ
	 * 
	 * ÿ��Ԫ�ض��и�ʽ
	 * $array = array('tagName' => '�ñ�ǩ������',
	 * 				  'value' => '��Ӧ��ǩ������',
	 * 				  'attributes' => array('��ǩ���Ե�����' => '�����Զ�Ӧ��ֵ', ...),
	 *                'children' => array(child1, child2,....);
	 * 
	 * @param SimpleXMLElement object   $element
	 * @return array
	 */
	public function getTagContents($element) {
		$_array = array();
		$_array['tagName'] = $element->getName();
		$_array['value'] = self::getValue($element);
		$_array['attributes'] = self::getAttributes($element);
		$_array['children'] = self::getChildren($element);
		return $_array;
	}
	
	/**
	 * ��õ�ǰ���������
	 * ������SimpleXMLElement����,����������Ӧ�����ݣ��������ӱ�ǩ��
	 * 
	 * ÿ��Ԫ�ض��и�ʽ
	 * $array = array('tagName' => '�ñ�ǩ������',
	 * 				  'value' => '��Ӧ��ǩ������',
	 * 				  'attributes' => array('��ǩ���Ե�����' => '�����Զ�Ӧ��ֵ', ...),
	 * 			)
	 * 
	 * @param SimpleXMLElement object   $element
	 * @return array
	 */
	public function getCurrent($element) {
		$_array = array();
		$_array['tagName'] = $element->getName();
		$_array['value'] = self::getValue($element);
		$_array['attributes'] = self::getAttributes($element);
		return $_array;
	}
	
	/**
	 * ��øñ�ǩ������
	 * @param SimpleXMLElement $element
	 * @return string
	 */
	public function getValue($element) {
		if ($element[0]) return self::escape($element[0]);
		return '';
	}
	
	/**
	 * ��øñ�ǩ������
	 * @param SimpleXMLElement $element
	 * @return string
	 */
	public function getTagName($element) {
		return $element->getName();
	}
	
	/**
	 * �жϸ�Ԫ���Ƿ�������
	 * 
	 * @param SimpleXMLElement $element
	 * @return boolean
	 */
	public function hasAttributes($element) {
		if ($element->attributes()) return true;
		return false;
	}
	
	/**
	 * ���ؽڵ������
	 * ʹ��XML::getAttributes($element);
	 * ���صĸ�ʽΪ��
	 * $array = array('��������' => '����ֵ', ... );
	 * 
	 * @param SimpleXMLElement $element
	 * @return array  ���ظýڵ������
	 */
	public function getAttributes($element) {
		$_attributes = array();
		$attributes = $element->attributes();
		if (!$attributes) return $_attributes;
		foreach ($attributes as $key => $value) {
			$_attributes[$key] = self::escape($value);
		}
		return $_attributes;
	}
	
	/**
	 * �жϸ�Ԫ���Ƿ����ӱ�ǩ
	 * 
	 * @param SimpleXMLElement $element
	 * @return boolean
	 */
	public function hasChildren($element) {
		if ($element->children()) return true;
		return false;
	}
	
	/**
	 * ���ָ����ǩ�µ������ӱ�ǩ
	 * 
	 * @param SimpleXMLElement $element
	 * @return array 
	 */
	public function getChildren($element) {
		$_childs = array();
		$childs = $element->children();
		if (!$childs) return $_childs;
		foreach ($childs as $key => $value) {
			$_childs[] = self::getTagContents($value);
		}
		return $_childs;
	}
	
	/**
	 * ������������ת�루�������õ�����������ת����
	 * 
	 * @access private
	 * @param string $param
	 * @return string
	 */
	public function escape($param) {
		return self::dataConvert(strval($param));
	}
		
	/**
	 * ����������ݽ���ת�����
	 * 
	 * @param string $data
	 * @param string $from_encoding
	 * @param string $to_encoding
	 * @return string
	 */
	protected function dataConvert($data, $from_encoding = 'UTF-8', $to_encoding = '') {
		if (!$to_encoding) $to_encoding = $this->outputEncoding;
		if (function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($data, $to_encoding, $from_encoding);
		} else {
			/*L::loadClass('Chinese', 'utility/lang', false);
			$chs = new Chinese($db_charset, $to_encoding);
			return $chs->Convert($data);*/
		}
		return $data;
	}
	
	/**
	 * �Ӹ�����һ����ַ�л��xml����
	 * 
	 * @access private
	 * @param string $host
	 * @param string $data
	 * @param string $method
	 * @param string $showagent
	 * @param string $port
	 * @param integer $timeout
	 * @return string 
	 */
	private function PostHost($host, $data = '', $method = 'GET', $showagent = null, $port = null, $timeout = 30) {
		//Copyright (c) 2003-2103 phpwind
		$parse = @parse_url($host);
		if (empty($parse)) return false;
		if ((int)$port > 0) {
			$parse['port'] = $port;
		} elseif (!$parse['port']) {
			$parse['port'] = '80';
		}
		$parse['host'] = str_replace(array('http:\/\/', 'https:\/\/'), array('', 'ssl:\/\/'), $parse['scheme'] . ":\/\/") . $parse['host'];
		if (!$fp = @fsockopen($parse['host'],$parse['port'],$errnum,$errstr,$timeout)) return false;
		$method = strtoupper($method);
		$wlength = $wdata = $responseText = '';
		$parse['path'] = str_replace(array('\\', '\/\/'), '/', $parse['path']) . "?" . $parse['query'];
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
