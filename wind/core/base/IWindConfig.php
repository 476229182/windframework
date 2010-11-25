<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-11-19
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
interface IWindConfig {
	/**
	 * ����ָ���ڳ����������Ҫ����һ����ǩ
	 */
	const PARSERARRAY = 'app, filters, view, viewerResolver, router, urlRule, routerParser';
	
	/**
	 * Ӧ��������Ϣ
	 */
	const APP = 'app';
	const APP_NAME = 'name';
	const APP_ROOTPATH = 'rootPath';
	const APP_CONFIG = 'configPath';
    
	/**
	 * ��������
	 */
	const FILTERS = 'filters';
	const FILTER = 'filter';
	const FILTER_NAME = 'filterName';
	const FILTER_PATH = 'filterPath';
    
	/**
	 * ģ�����������Ϣ
	 * 1.ģ���ļ����·��
	 * 2.Ĭ�ϵ�ģ���ļ�����
	 * 3.ģ���ļ���׺��
	 * 4.��ͼ������
	 * 5.ģ���ļ��Ļ���·��
	 * 6.ģ�����·��
	 */
	const TEMPLATE = 'view';
	const TEMPLATE_DIR = 'templatePath';
	const TEMPLATE_NAME = 'templateName';
	const TEMPLATE_EXT = 'templateExt';
	const RESOLVER = 'resolver';
	const ISCACHE = 'isCache';
	const CACHE_DIR = 'cacheDir';
	const COMPILER_DIR = 'compileDir';
	
    /**
     * ģ������������Ϣ
     */
	const VIEWER_RESOLVER = 'viewerResolver';
	const RESOLVER_DEFAULT = 'default';
	const PHPWIND = 'pw';

	/**
	 * ·�ɲ�������
	 */
	const ROUTER = 'router';
	const PARSER = 'parser';
	
	/**
	 * URL·�ɹ�������
	 */
	const URLRULE = 'urlRule';
	const ACTION = 'action';
	const CONTROLLER = 'controller';
	const MODULE = 'module';
	
	/**
	 * ·�ɽ���������
	 */
	const ROUTER_PARSER = 'routerParser';
	const URL = 'url';
	
	/**
	 * ��������ӵ�е�����
	 * name: ���Զ���һЩ�е�item��ÿһ��item������������ÿһ��
	 * isGlobal: �������ϸ����ԣ���ñ�ǩ���ڽ������֮�����������ȫ�ֻ����� -----ֻ������һ����ǩ
	 * isMerge: �������ϸ����ԣ���ñ�ǩ�����ڽ�������кϲ� -----ֻ������һ����ǩ
	 */
	const ATTRNAME = 'name';
	const GLOBALATTR = 'isGlobal';
	const MERGEATTR = 'isMerge';
}