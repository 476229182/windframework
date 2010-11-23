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

	/* 
	 * ģ�����������Ϣ
	 * 1.ģ���ļ����·��
	 * 2.Ĭ�ϵ�ģ���ļ�����
	 * 3.ģ���ļ���׺��
	 * 4.��ͼ������
	 * 5.ģ���ļ��Ļ���·��
	 * 6.ģ�����·��
	 *  */
	'view' => array(
		'templatePath' => 'template',
		'templateName' => 'index',  
		'templateExt' => 'htm',
		'resolver' => 'default',
		'isCache' => 'false',
		'cacheDir' => 'cache',
		'compileDir' => 'compile',
	),
	/* ģ������������Ϣ */
	'viewerResolver' => array(
		'default' => 'WIND:component.viewer.WindViewer',
		'pw' => 'WIND:component.viewer.WindPWViewer',
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
		'url' => 'WIND:component.router.WindUrlBasedRouter'
	)
);
