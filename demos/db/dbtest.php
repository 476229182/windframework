<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-18
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
require '../../wind/WBase.php';
echo '<pre/>';
/**
 * mysql ����ʹ��
 */
$phpwind = array ('charset'=>'gb2312','dbtype' => 'mysql','dbname'=>'phpwind_8', 'dbuser'=>'root','dbpass'=>'suqian0512h','dbhost'=>'localhost','dbport'=>3306 );
$phpwind_beta = array ('charset'=>'gb2312','dbtype' => 'mysql','dbname'=>'phpwind_8beta', 'dbuser'=>'root','dbpass'=>'suqian0512h','dbhost'=>'localhost','dbport'=>'3306','force'=>1 );
$config = array ('phpwind' =>  $phpwind,'beta'=>$phpwind_beta);
$mysql = new WMySql ($config);

//
$option['table'] = 'pw_members a';
$option['where'] = array('lt'=>array('a.uid',10));
$option['field'] =  array('a.uid'=>'ids','a.username');
$option['join'] =  array('pw_posts'=>array('left','a.uid=b.authorid','b'));
//��ָ��db����
$mysql->select($option);
//ָ��db����
//$mysql->select($option,'phpwind');
$result = $mysql->getAllResult();
//��������
$option['set'] = array('username'=>"test");
$option['table'] = 'pw_members';
$option['where'] = array('eq'=>array('uid',1));
$mysql->update($option,'phpwind');

//��������
$option['data'] = array("asfafafafaf");
$option['table'] = 'pw_members';
$option['field'] = array('username');
$option['where'] = array('eq'=>array('uid',1));
$mysql->insert($option,'phpwind');

//ɾ������

$option['table'] = 'pw_members';
$option['where'] = array('eq'=>array('uid',22));
$mysql->delete($option,'phpwind');

/**
 * sql serverl����ʹ��
 */
$phpwind_beta = array ('charset'=>'gb2312','dbtype' => 'mssql','dbname'=>'phpwind', 'dbuser'=>'sa','dbpass'=>'151@suqian','dbhost'=>'localhost','dbport'=>'','force'=>1 );
$config = array ('betat'=>$phpwind_beta);
$mssql = new WMsSql ($config);





$option['table'] = 'pw_members a';
$option['where'] = array('lt'=>array('a.uid',10));
$option['field'] =  array('a.uid'=>'ids','a.username');
$option['join'] =  array('pw_posts'=>array('left','a.uid=b.authorid','b'));
$mssql->select($option);
$result = $mssql->getAllResult();
print_r($result);