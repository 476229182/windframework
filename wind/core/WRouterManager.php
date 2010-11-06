<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * ����������Ϣ������������Ϣ����·�ɽ�����
 * 
 * ����·�ɵ��������
 * ·�ɽ��������ã�ʹ����һ��·�ɽ�����
 * ·��Ĭ��ֵ�����ã�·�ɵ�Ĭ��ֵ
 * 
 * $config = array(
 * 'router' => array(
 * 'parser' => 'url',
 * 'defaultAction' => 'run', 
 * 'defaultController' => 'index', 
 * 'defaultApp1' => 'cms',       // ��Ӧ��ģ��, �������Ϊ�����ʾû����Ӧ��ģ�飻��Ӧ��Ŀ¼Ϊ��Ŀ¼ mode.cms
 * 'defaultApp2' => 'front',     // admin/front,�������Ϊ����Ϊfront; ��Ӧ��Ŀ¼/��Ӧ��Ŀ¼Ϊ��Ŀ¼ protected.controller ����  admin.controller
 * ),
 * 'app' => array(
 * 'cms' => 'xxx.xxx.xxx',
 * ),
 * 'module' => array(
 * 'front' => '',
 * 'admin' => '',
 * ),
 * );
 * 
 * ·�ɹ��򣬵����յ�һ�������·�ɹ����������·�����ó�ʼ��һ��·�ɽ���������������
 * �����󷵻�һ��WRouterContext����
 * 
 * �������ʹ���
 * 1. $action �������ƣ����������������ƣ��������õĲ�ͬ������仯��Ψһ������Ǹò���ָ��һ�������ҵ�����
 * 2. $controller Ӧ�ÿ��������ƣ������ƣ��ò���ָ��һ������ļ��ϣ�������ĳ��С��ҵ��ģ��
 * 3. $app1 һ��Ӧ�ÿ�����Ŀ¼
 * 4. $app2 ����Ӧ�ÿ�����Ŀ¼
 * 
 * ����˳�� ��
 * $app1/$app2/$controller -> $action
 * 
 * ���ϸ���������Ĭ��ֵ�����δ����ò�������ʹ��Ĭ��ֵ
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WRouterManager extends WRouter implements WContext {
	
	/**
	 * ���������ļ�����ʼ��·����Ϣ
	 */
	static function init() {
		$config = array(
			'router' => array(
				'parser' => 'url'
			)
		);
		$routerContext = & self::createRouterParser($config['router'])->doParser();
		
		$rootPath = ''; //ͨ�������ļ����Ӧ�ó����Ŀ¼��·����Ϣ
		$path = '';
		if ($routerContext->app1) {
			$path .= $config['app'][$routerContext->app1];
		}
		if ($routerContext->app2) {
			$path .= $config['module'][$routerContext->app2];
		}
		if ($routerContext->controller) {
			$path .= $routerContext->controller;
		}
	
	}
	
	/**
	 * ����·�������ļ���Ϣ
	 * @param array $config
	 */
	function _parserConfig($config) {

	}
	
	/**
	 * ���·�ɽ�������·����Ϣ
	 * ϵͳ��һ��Ĭ�ϵ����ã���ܼ�����ṩ��·�ɽ���·��
	 * 
	 * @param string $key
	 * @return string
	 */
	private function _getParserPath($key, $config = '') {
		$parser = array(
			'url' => 'router.parser.WUrlRouteParser'
		);
		if (!key_exists($key, $parser) && $config && $config['routerParser']) {
			$parser = (array) $config['routerParser'];
		}
		return key_exists($key, $parser) ? $parser[$key] : '';
	}
	
	/**
	 * ����������Ϣ����·�ɽ�����
	 * @param array $config
	 * @return WRouteParser
	 */
	private function _createRouterParser($config) {
		$className = '';
		if (!$config['parser'])
			$config['parser'] = self::$_defaultParser;
		$path = self::_getParserPath($config['parser'], $config);
		if (file_exists($path)) {
			if (($pos = strpos($path, '.')) === false) {
				$className = $path;
			} else
				$className = substr($path, $pos + 1);
			WBasic::import($path);
		}
		return new $className();
	}
	
	/**
	 * ��ø���ľ�̬��������
	 */
	public static function getInstance() {
		return WBasic::getInstance(__CLASS__);
	}

}