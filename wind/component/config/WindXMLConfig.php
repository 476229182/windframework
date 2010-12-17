<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
L::import('WIND:component.xml.xml');

/**
 * xml格式配置文件的解析类
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$
 * @package
 */
class WindXMLConfig extends XML {
	/**
	 * 定义允许拥有的属性
	 * name: 可以定义一些列的item中每一个item的名字以区分每一个
	 * isGlobal: 如果添加上该属性，则该标签将在解析完成之后被提出放置在全局缓存中 -----只作用于一级标签
	 * isMerge: 如果添加上该属性，则该标签将被在解析后进行合并 -----只作用于一级标签
	 */
	const NAME = 'name';
	
	private $xmlArray;
	/**
	 * 构造函数，设置输出编码及变量初始化
	 * @param string $data
	 * @param string $encoding
	 */
	public function __construct($encoding = 'UTF-8') {
		$this->setOutputEncoding($encoding);
	}
	
	/**
	 * 加载需要解析的文件
	 * @param unknown_type $filename
	 */
	public function loadFile($filename) {
		$this->getXMLFromFile($filename);
	}
	
	/**
	 * 内容解析
	 *
	 * 内容的解析依赖于配置文件中配置项的格式进行，每个配置项对应的在IWindConfig中都必须有对应的常量声明
	 * 对应的解析格式调用对应的解析函数。
	 *
	 * @return boolean
	 */
	public function parser($filename = '') {
	    ($filename != '') && $this->getXMLFromFile($filename);
		$this->ceateParser();
		$children = $this->getXMLDocument()->children();
		$result = array();
		foreach ($children as $node => $child) {
			$elements = (array) $this->getElementByXPath($node);
			foreach ($elements as $element) {
				list($key, $value) = $this->getContents($element);
				if (($value = $this->checkValue($value)) !== false) $result[$key] = $value;
			}
		}
		$this->xmlArray = $result;
		return $result;
	}
	
	/**
	 * 根据返回节点内容
	 *
	 * 获得含有属性和子标签的标签内容，规则如下<pre/>:
	 * <bbbb name='aaa1' attrib1='dddd'>
	 * 		<filterName>windFilter1</filterName>
	 * 		<filterPath>/filter1</filterPath>
	 * </bbbb>
	 * 该方法对上述的这种情形，根据需求会解析出最后的结果是：
	 * return array(aaa1,
	 * 		        array(name => aaa1,
	 * 			 		attrib1 => dddd,
	 * 		     		filterName => windFilter1,
	 *           		filterPath => /filter1,
	 *           		tagName = bbbb,
	 *       		)
	 * 			)
	 *
	 *<tag>value</tag>
	 * 并且返回形式为array(tag, value)
	 * 
	 * @param SimpleXMLElement $element
	 * @return array
	 */
	private function getContents($node) {
		$hasAttr = $this->haveAttributes($node);
		$hasChild = $this->haveChildren($node);
		if ($hasAttr && $hasChild) {
			list($tag, $attributes) = $this->getAttributesList($node);
			list(, $childValue) = $this->getChildrenList($node);
			$childValue = (count($attributes) == 0) ? $childValue : array_merge($childValue, $attributes);
			return array($tag, $childValue);
		}
		if ($hasChild) {
			return $this->getChildrenList($node);
		}
		if ($hasAttr) {
			return $this->getAttributesList($node);
		}
		return array($node->getName(), trim($this->getValue($node)));
	}
	
	/**
	 * 获得含有子标签的标签内容：
	 * <AA>
	 * <BB name='key1' value='key1Value' attri3='attribute1'/>
	 * <BB value='key2Value' attri3='attribute2'/>
	 * </AA>
	 * 如果含有属性name，则将该name作为key
	 * 返回结果array(AA, array(key1 => array(tagName = BB, name => key1, value=>key1Value, attri3 => attribute1),
	 * BB => array(tagName => BB, value=>key2Value, attri3 => attribute2)
	 * ))
	 *
	 * @param SimpleXMLElement $element
	 * @param array
	 */
	private function getAttributesList($node) {
		$tag = $node->getName();
		$attributes = $this->getAttributes($node);
		if (isset($attributes[self::NAME])) {
			$tag = $attributes[self::NAME]; 
			unset($attributes[self::NAME]);
		}
		return array($tag, $attributes);
	}
	
	/**
	 * 获得含有子标签的标签内容：
	 * <AA>
	 * <BB>Bvalue</BB>
	 * <CC>Cvalue</CC>
	 * </AA>
	 * 返回结果array(AA, array(BB => Bvalue, CC => Cvalue))
	 *
	 * @param SimpleXMLElement $element
	 * @param array
	 */
	private function getChildrenList($node) {
		$tag = $node->getName();
		$childArray = array();
		foreach ($node->children() as $child) {
			list($childTag, $childValue) = $this->getContents($child);
			if (($value = $this->checkValue($childValue)) !== false) $childArray[$childTag] = $value;
		}
		if (($value = $this->getValue($node)) != '') {
			(count($childArray) == 0) ? $childArray = $value : $childArray[] = $value;
		}
		return array($tag, $childArray);
	}

	/**
	 * 检查是否为空，如果为空返回false 否则返回格式化后的值
	 * 
	 * @param mixed $value
	 * @return mixed boolean | 
	 */
	private function checkValue($value) {
		if (is_string($value)) {
			return (trim($value) == '') ? false : trim($value);
		}
		if (is_array($value)) {
			return (count($value) == 0) ? false : $value;
		}
	}
}
