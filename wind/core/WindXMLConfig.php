<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
L::import('WIND:core.base.impl.WindConfigImpl');
L::import('WIND:utility.xml.xml');

/**
 * xml��ʽ�����ļ��Ľ�����
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author xiaoxia xu <x_824@sina.com>
 * @version $Id$ 
 * @package
 */
class WindXMLConfig extends XML implements WindConfigImpl {
	private $xmlArray;
    private $childConfig;
	/**
	 * ���캯��������������뼰������ʼ��
	 * @param string $data
	 * @param string $encoding
	 */
	public function __construct($data = '', $encoding = 'gbk') {
		parent::__construct($data, $encoding);
		$this->xmlArray = array();
		$this->setChildConfig();
	}
    
	/**
	 * ���ñ�ǩ�µ��ӱ�ǩ��
	 * 
	 */
	private function setChildConfig() {
		$_config = array();
		//����Ӧ�õ�����
		$_config[WindConfigImpl::APP] = array(
					WindConfigImpl::APPNAME, 
					WindConfigImpl::APPROOTPATH, 
					WindConfigImpl::APPCONFIG, 
					WindConfigImpl::APPAUTHOR);
		//���ڹ�����������
		/* 
		 * secondNodes: �����˸ñ�ǩ���Ӽ���ǩ
		 * keyNodes: �����˸ñ�ǩ�����ݽ���Ϊ������
		 * valueNodes: �����˸ñ�ǩ�����ݽ���Ϊֵ����
		 */			
		$_config[WindConfigImpl::FILTERS] = array(
		            'secondNodes' => array(WindConfigImpl::FILTER),
		            'keyNodes' => array(WindConfigImpl::FILTERNAME),
		            'valueNodes' => array(WindConfigImpl::FILTERPATH));
		//������ͼ���
		$_config[WindConfigImpl::TEMPLATE] = array(
					WindConfigImpl::TEMPLATEDIR, 
					WindConfigImpl::COMPILERDIR, 
					WindConfigImpl::CACHEDIR, 
					WindConfigImpl::TEMPLATEEXT, 
					WindConfigImpl::ENGINE);
		//����·�����
	    $_config[WindConfigImpl::URLRULE] = array(
	    			WindConfigImpl::ROUTERPASE);
	    			
		$this->childConfig = $_config;
	}
	/**
	 * ���ؽ����Ľ��
	 * @param boolean $isCheck �Ƿ���Ҫ�������
	 * @return array ���ؽ������������Ϣ
	 */
	public function getResult($isCheck = true) {
		return $this->fetchContents($isCheck);
	}
    
	/**
	 * ���ݽ���
	 * 
	 * ���ݵĽ��������������ļ���������ĸ�ʽ���У�ÿ���������Ӧ����WindConfigImpl�ж������ж�Ӧ�ĳ�������
	 * ��Ӧ�Ľ�����ʽ���ö�Ӧ�Ľ���������
	 * 
	 * @access private 
	 * @param boolean $isCheck �Ƿ���Ҫ�������
	 * @return array
	 */
	private function fetchContents($isCheck = true) {
		$app = $this->createParser()->getElementByXPath(WindConfigImpl::APP);
		if ($isCheck && !$app) throw new WindException('the app config must be setting');
		$this->xmlArray[WindConfigImpl::APP] = $this->getSecondChildTree(WindConfigImpl::APP, $this->childConfig[WindConfigImpl::APP]);
		if ($isCheck && empty($this->xmlArray[WindConfigImpl::APP][WindConfigImpl::APPCONFIG]))  throw new WindException('the "appconfig" of the "app" config must be setted!');

		$this->xmlArray[WindConfigImpl::ISOPEN] = $this->getNoChild(WindConfigImpl::ISOPEN);
		$this->xmlArray[WindConfigImpl::DESCRIBE] = $this->getNoChild(WindConfigImpl::DESCRIBE);

		$this->xmlArray[WindConfigImpl::FILTERS] = $this->getThirdChildTree(WindConfigImpl::FILTERS, 
																			$this->childConfig[WindConfigImpl::FILTERS]['secondNodes'], 
																			$this->childConfig[WindConfigImpl::FILTERS]['keyNodes'], 
																			$this->childConfig[WindConfigImpl::FILTERS]['valueNodes']);

		$this->xmlArray[WindConfigImpl::TEMPLATE] = $this->getSecondChildTree(WindConfigImpl::TEMPLATE, $this->childConfig[WindConfigImpl::TEMPLATE]);
		$this->xmlArray[WindConfigImpl::URLRULE] = $this->getSecondChildTree(WindConfigImpl::URLRULE, $this->childConfig[WindConfigImpl::URLRULE]);
		return $this->xmlArray;
	}
    
	/**
	 * ��õ�����������
	 * @param string $node
	 * @return string
	 */
	private function getNoChild($node) {
		$dom = $this->getElementByXPath($node);
		if (!isset($dom[0])) return '';
		$contents = $this->getTagContents($dom[0]);
		return $contents['value'];
	}
    
	/**
	 * ��������������������
	 * ������������������е���������
	 * @param string $parentNode  ��Ҫ���ҵ�������
	 * @param array $nodes   ���������µ�������������
	 */
	private function getSecondChildTree($parentNode, $nodes) {
		if (!$nodes || !$parentNode) return array();
		(!is_array($nodes)) && $nodes = array($nodes);
		$dom = $this->getElementByXPath($parentNode);
		if (!$dom) return array();
		$childs = $this->getChilds($dom[0]);
		$_result = array();
		foreach ($childs as $child) {
			(in_array($child['tagName'], $nodes)) && $_result[$child['tagName']] = $child['value'];
		}
		return $_result;
	}
    
	/**
	 * ��ú������������������������
	 * ���ҵ��������������е�һ������������Ϊkey���ڶ�������������Ϊvalue������xml��filters������
	 * <pre>
	 * <filters>
	 *    <filter>
	 *    	 <filtername>filte1</filtername>
	 *       <filterpath>/filter1.php</filtername>
	 *    </filter>
	 *    <filter>
	 *       <filtername>filter2</filtername>
	 *       <filterpath>/filter2.php</filterpath>
	 *    </filter>
	 * </filters>
	 * </pre>
	 * �÷������������������Σ������������������Ľ���ǣ�
	 * $filters = array(
	 *       'filte1' => '/filter1.php',
	 *       'filter2' => '/filter2.php',
	 * )
	 *  
	 * @access private
	 * @param string $parentNode   ��ǰ������
	 * @param array $secondeParentNode  ���������µ���������
	 * @param array $keyNode  ����Ϊ����������
	 * @param array $valueNode ����Ϊֵ��������
	 * @return array 
	 */
	private function getThirdChildTree($parentNode, $secondeParentNode, $keyNode, $valueNode) {
		if (!$parentNode || !$secondeParentNode) return array();
		(!is_array($keyNode)) && $keyNode = array($keyNode);
		(!is_array($valueNode)) && $valueNode = array($valueNode);
		(!is_array($secondeParentNode)) && $secondeParentNode = array($secondeParentNode);
		$dom = $this->getElementByXPath($parentNode);
		if (!isset($dom[0])) return array(); 
		$childs = $this->getChilds($dom[0]);
		$_childs = array();
		foreach($childs as $child) {
			if (!in_array($child['tagName'], $secondeParentNode)) continue;
			$_secondeChild = $child['children'];
			$_keys = array();
			$_values = array();
			foreach ($_secondeChild as $_key => $_second) {
				if (!in_array($_second['tagName'], $keyNode) && !in_array($_second['tagName'], $valueNode)) continue;
				in_array($_second['tagName'], $keyNode) && $_keys[] = $_second['value'];
				in_array($_second['tagName'], $valueNode) && $_values[] = $_second['value'];
			}
			$_childs = array_merge($_childs, array_combine($_keys, $_values));
		}
		return $_childs;
	}
    
	/**
	 * ����������
	 * @access private
	 * @return XML object
	 */
	private function createParser() {
		if (is_object($this->object)) return $this;
		$this->ceateParser();
		return $this;
	}
}
