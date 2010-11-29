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
	const PARSERARRAY = 'app, applications, modules, filters, template, viewerResolvers, router, routerParsers, debug, log';
	
	/**
	 * Ӧ��������Ϣ
	 */
	const APP = 'app';
	const APP_NAME = 'name';
	const APP_ROOTPATH = 'rootPath';
	const APP_CONFIG = 'configPath';
	
	const APPLICATIONS = 'applications';
	const APPLICATIONS_NAME = 'name';
	const APPLICATIONS_CLASS = 'class';
	
	/**
	 * ģ���O��
	 */
	const MODULES = 'modules';
	const MODULE_NAME = 'name';
	const MODULE_PATH = 'path';
	/**
	 * ��������
	 */
	const FILTERS = 'filters';
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
	const TEMPLATE = 'template';
	const TEMPLATE_PATH = 'path';
	const TEMPLATE_NAME = 'name';
	const TEMPLATE_EXT = 'ext';
	const TEMPLATE_RESOLVER = 'resolver';
	const TEMPLATE_ISCACHE = 'isCache';
	const TEMPLATE_CACHE_DIR = 'cacheDir';
	const TEMPLATE_COMPILER_DIR = 'compileDir';
	
	/**
	 * ģ������������Ϣ
	 */
	const VIEWER_RESOLVERS = 'viewerResolvers';
	
	/**
	 * ·�ɲ�������
	 */
	const ROUTER = 'router';
	const ROUTER_PARSER = 'parser';
	
	/**
	 * ·�ɽ���������
	 */
	const ROUTER_PARSERS = 'routerParsers';
	const ROUTER_PARSERS_RULE = 'rule';
	const ROUTER_PARSERS_PATH = 'path';
	
	/**
	 * ���Ժ���־����
	 */
	const DEBUG = 'debug';
	const LOG = 'log';
	/**
	 * ��������ӵ�е�����
	 * name: ���Զ���һЩ�е�item��ÿһ��item������������ÿһ��
	 * isGlobal: �������ϸ����ԣ���ñ�ǩ���ڽ������֮�����������ȫ�ֻ����� -----ֻ������һ����ǩ
	 * isMerge: �������ϸ����ԣ���ñ�ǩ�����ڽ�������кϲ� -----ֻ������һ����ǩ
	 */
	const ATTRNAME = 'name';
	const ISGLOBAL = 'isGlobal';
	const ISMERGE = 'isMerge';
}