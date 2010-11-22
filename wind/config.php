<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-6
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */

/*
 * ��ܺ��������ļ� <·�����ã�Ӧ�����ã�����������...>
 *
 * */
$sysConfig = array(
	/* �������·�� */
	'filters' => array(),

	/* Ӧ��·������ */
	'modules' => array(
		'default' => 'actionControllers'
	),

	/* ģ�����������Ϣ */
	'view' => array(
		'viewPath' => 'template',  //ģ���ļ�·��
		'tpl' => 'index',  //Ĭ�ϵ�ģ���ļ�
		'engine' => 'default',  //default,smarty
		'ext' => 'htm', //ģ���ļ���׺��
		'cacheDir' => 'cache',//ģ���ļ��Ļ���·�� 
		'compileDir' => 'compile',//ģ�����·��
	),

	/* ģ������������Ϣ */
	'viewEngine' => array(
		'default' => 'WIND:core.WViewer',
		'pw' => 'WIND:core.WPWViewer',
		'smarty' => 'libs.WSmarty',
	),

	/* ·�ɲ������� */
	'router' => array(
		'parser' => 'url'
	),

	/* URL·�ɹ�������  */
	'urlRule' => array(
		'action' => 'run',
		'controller' => 'index',
		'module' => ''
	),

	/* ·�ɽ��������� */
	'routerParser' => array(
		'url' => 'WIND:component.router.WindUrlRouter'
	)
);
