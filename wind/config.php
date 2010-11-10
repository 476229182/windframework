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
	'filters' => array(
		'testFilter' => 'filter.TestFilter', 
		'test1Filter' => 'filter.Test1Filter'
	), 
	
	/* Ӧ������ */
	'apps' => array(), 
	
	/* ·�ɲ������� */
	'router' => array(
		'parser' => 'url'
	), 
	/* URL·�ɹ�������  */
	'urlRule' => array(
		'action' => 'run', 
		'controller' => 'index', 
		'app1' => 'controller1', 
		'app2' => ''
	), 
	/* ·�ɽ��������� */
	'routerParser' => array(
		'url' => 'core.WUrlRouter'
	)
);
