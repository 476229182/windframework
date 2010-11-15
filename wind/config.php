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
	//�������·��
	'filters' => array(),
	
	/* Ӧ��·������ */
	'modules' => array(
		'default' => 'actionControllers'
	),
	
	/*ģ��·������*/
	'view' => array(
		'viewPath' => 'template',  //ģ���ļ�·��
		'ext' => 'htm' //ģ���ļ���׺��
	),
	
	/*
	 * �������ѡ��������ֵ�����ò����ṹ�ǻ���WActionController���߻���WAction
	 * ����WAction����Ŀ¼�ṹ��actionControllers/controller/action.php
	 * ����WActionController����Ŀ¼�ṹ��actionControllers/controller.php
	 * */
	'baseController' => 'WActionController',
	
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
		'url' => 'WIND:core.WUrlRouter'
	)
);
