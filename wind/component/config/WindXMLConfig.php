<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
L::import('WIND:core.base.IWindConfig');
L::import('WIND:component.config.base.IWindParser');
L::import('WIND:utility.xml.xml');

/**
 * xml��ʽ�����ļ��Ľ�����
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$
 * @package
 */
class WindXMLConfig extends XML implements IWindParser {
	private $xmlArray;
    private $childConfig;
    private $isCheck;
    private $GAM;
	/**
	 * ���캯��������������뼰������ʼ��
	 * @param string $data
	 * @param string $encoding
	 */
	public function __construct($encoding = 'gbk') {
		$this->setOutputEncoding($encoding);
		$this->GAM = array();
	}
	
	/**
	 * ������Ҫ�������ļ�
	 * @param unknown_type $filename
	 */
	public function loadFile($filename) {
		$this->setXMLFile($filename);
	}

	/**
	 * ���ݽ���
	 *
	 * ���ݵĽ��������������ļ���������ĸ�ʽ���У�ÿ���������Ӧ����IWindConfig�ж������ж�Ӧ�ĳ�������
	 * ��Ӧ�Ľ�����ʽ���ö�Ӧ�Ľ���������
	 *
	 * @return boolean
	 */
	public function parser() {
		$this->ceateParser();
		$parseArray = trim(IWindConfig::PARSERARRAY, ',');
		$_parseTags = (strpos($parseArray, ',') === false) ? array($parseArray) : explode(',', $parseArray);
		$_array = array();
		foreach($_parseTags as $tag) {
			$elements = $this->getElementByXPath($tag);
			$this->isCheck = true;
			foreach($elements as $element) {
				list($key, $value) = $this->getContent($element);
				if (($value = $this->isEmpty($value)) !== true)
					$_array[$key] = $value;
			}
		}
		$this->xmlArray = $_array;
		return true;
	}

	/**
	 * ���ݱ�ǩ����ʽ���зַ�
	 *
	 * @param SimpleXMLElement $element
	 * @return array
	 */
	private function getContent($element) {
		$attributes = self::hasAttributes($element);
		$child = self::hasChildren($element);
		if ($attributes && $child) {
			return $this->getContentHasAttAndChild($element);
		}
		if ($attributes) {
			return $this->getContentHasAttributes($element);
		}
		if ($child) {
			return $this->getContentHasChildren($element);
		}
		return $this->getContentNone($element);
	}

	/**
	 * �õ����¹���ı�ǩ���ݣ�
	 * <tag>value</tag>
	 * ���ҷ�����ʽΪarray(tag, value)
	 *
	 * @param SimpleXMLElement $element
	 * @return array
	 */
	private function getContentNone($element) {
		$tagName = $element->getName();
		return array($tagName, trim(self::getValue($element)));
	}

	/**
	 * ��ú����ӱ�ǩ�ı�ǩ���ݣ�
	 * <AA>
	 *    <BB>Bvalue</BB>
	 *    <CC>Cvalue</CC>
	 * </AA>
	 * ���ؽ��array(AA, array(BB => Bvalue, CC => Cvalue))
	 *
	 * @param SimpleXMLElement $element
	 * @param array
	 */
	private function getContentHasChildren($element) {
		$tag = $element->getName();
		$childs = $element->Children();
		$childArray = array();
		foreach ($childs as $child) {
			list($childTag, $childValue) = $this->getContent($child);
			if (($value = $this->isEmpty($childValue)) !== true)
					$childArray[$childTag] = $value;
		}
		if (count($childArray) == 0) {
			(trim(self::getValue($element)) != '' ) && $childArray = trim(self::getValue($element));
		} else {
			(trim(self::getValue($element)) != '' ) && $childArray[] = trim(self::getValue($element));
		}		
		return array($tag, $childArray);
	}
    
	/**
	 * �ж��Ƿ�Ϊ�գ����Ϊ�շ���true,���򷵻�false
	 * @param mixed $value
	 * @return mixed boolean | 
	 */
	private function isEmpty($value) {
		if (is_array($value)) {
			return (count($value) == 0) ? true : $value;
		}
		if (is_string($value)) {
			return (trim($value) == '') ? true : trim($value);
		}
	}
	
	/**
	 * ��ú����ӱ�ǩ�ı�ǩ���ݣ�
	 * <AA>
	 *    <BB name='key1' value='key1Value' attri3='attribute1'/>
	 *    <BB value='key2Value' attri3='attribute2'/>
	 * </AA>
	 * �����������name���򽫸�name��Ϊkey
	 * ���ؽ��array(AA, array(key1 => array(tagName = BB, name => key1, value=>key1Value, attri3 => attribute1),
	 * 						  BB => array(tagName => BB, value=>key2Value, attri3 => attribute2)
	 * 					))
	 *
	 * @param SimpleXMLElement $element
	 * @param array
	 */
	private function getContentHasAttributes($element) {
		$tag = $element->getName();
		$attributes = self::getAttributes($element);
		$attributes['tagName'] = $tag;
		(isset($attributes[IWindConfig::ATTRNAME])) && $tag = $attributes[IWindConfig::ATTRNAME];
		$this->setGAM($attributes);
		return array($tag, array());
	}
	
	/**
	 * ����ȫ�ֵı�ǩ����Ҫ�ϲ��ı�ǩ
	 * 
	 * @param array $attributes
	 * @return boolean; 
	 */
	private function setGAM($attributes) {
		if (!$this->isCheck) return false;
		$tag = $attributes['tagName'];
		$name = isset($attributes[IWindConfig::ATTRNAME]) ? $attributes[IWindConfig::ATTRNAME] : $tag;
		(isset($attributes[IWindConfig::ISGLOBAL]) && $attributes[IWindConfig::ISGLOBAL] == 'true') && $this->GAM[IWindConfig::ISGLOBAL][$name] = $tag;
		(isset($attributes[IWindConfig::ISMERGE]) && $attributes[IWindConfig::ISMERGE] == 'true') && $this->GAM[IWindConfig::ISMERGE][$name] = $tag;
		$this->isCheck = false;
		return true;
	}
	
	/**
	 * ��ú������Ժ��ӱ�ǩ�ı�ǩ���ݣ���������<pre/>:
	 * <bbbb name='aaa1' attrib1='dddd'>
  	 * 	  <filterName>windFilter1</filterName>
  	 *	  <filterPath>/filter1</filterPath>
  	 * </bbbb>
	 * �÷������������������Σ������������������Ľ���ǣ�
	 * return array(aaa1,
	 *       	       array(name => aaa1,
	 *       				 attrib1 => dddd,
	 *      	       		 filterName => windFilter1,
	 *      	       		 filterPath => /filter1,
	 *      				 tagName = bbbb,
	 *      			)
	 *         )
	 *         
	 * @access private
	 * @param SimpleXMLElement $element
	 * @return array
	 */
	private function getContentHasAttAndChild($element) {
		list($tag, $atttrValue) = $this->getContentHasAttributes($element);
		list($tag1, $childValue) = $this->getContentHasChildren($element);
		return array($tag, $childValue);
	}
	
    /*
	 * ���ؽ����Ľ��
	 * @param boolean $isCheck �Ƿ���Ҫ�������
	 * @return array ���ؽ������������Ϣ
	 */
	public function getResult() {
		if (!$this->xmlArray) $this->parser();
		return $this->xmlArray;
	}
	
    
	/**
	 * ������Ҫ����ȫ�ֵı�ǩ
	 * 
	 * @return array; 
	 */
	public function getGAM($key = '') {
		if (in_array($key, array_keys($this->GAM))) return $this->GAM[$key];
		return $this->GAM;
	}
}
