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
	'filterChain' => array(
		'filter1' => array()
	), 
	'filters' => array(
		'filter1' => 'path'
	), 
	
	'controllers' => array(
		'controller1' => 'www.app.controller', 
		'controller2' => 'www.app.controller.subcont'
	), 
	
	'router' => array(
		'parser' => 'url'
	), 
	'urlRule' => array(
		'action' => 'run', 
		'controller' => 'index', 
		'app1' => 'controller1', 
		'app2' => ''
	), 
	'routerParser' => array(
		'url' => 'router.parser.WUrlRouteParser.php'
	)
);
