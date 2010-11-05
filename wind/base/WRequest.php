<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-5
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * ��������������
 * ��http����
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
abstract class WRequest {
	protected static $_request = array();
	public abstract function getCookie();
	/**
	 * ��ȡPOST��ֵ
	 * @param string $key 
	 * @return mixed
	 */
	public abstract function getPost($key = '');
	/**
	 * ��ȡ HTTP GET��ֵ
	 * @param string $key 
	 * @return mixed
	 */
	public abstract function getGet($key = '');
	/**
	 * ��ȡ HTTP SERVER��ֵ
	 * @param string $key 
	 * @return mixed
	 */
	public abstract function getServer($key = '');
	/**
	 * ��ȡ HTTP REQUEST��ֵ
	 * @param string $key 
	 * @return mixed
	 */
	public abstract function getRequest($key = '');
	/**
	 * ȡ�ÿͻ���ʹ�õ� HTTP ���ݴ��䷽��
	 * @return string
	 */
	public abstract function getHttpMethod();
	/**
	 * ȡ��http����� MIME �������͡�
	 * @return string
	 */
	public abstract function getAcceptTypes();
	/**
	 * ȡ�ÿͻ����������ԭʼ�û�������Ϣ��
	 * @return string
	 */
	public abstract function getUserAgent();
	/**
	 *��֤HTTP�������Ƿ�ʹ�ð�ȫ�׽��� (ssl��ȫ����)
	 *@return boolean
	 */
	public abstract function IsSecureConnection();
	/**
	 * ȡ������ҳ���URI
	 * @return string ����http����ҳ���URI
	 */
	public abstract function getRequestUri();
	/**
	 * ȡ�� HTTP���� ��ѯ�ַ���
	 */
	public abstract function getQuery();
	/**
	 * ȡ��http����ǰ�ű��ļ����ڵ�Ŀ¼
	 * @return string;
	 */	
	public abstract function getFilePath();
	/**
	 * ȡ��http����ǰ�ű��ļ�����ʵ·��
	 * @return string
	 */
	public abstract function getFile();
	/**
	 * ȡ��http������ԭʼ��URL
	 * @return string
	 */
	public abstract function getRequestUrl();
	public abstract function getBaseUrl();
	/**
	 * ȡ�ÿͻ����ϴ������ URL��ַ
	 * @return string
	 */
	public abstract function getReferUrl();
	/**
	 * ȡ��http�����еĵ�ǰ�ű��ļ���
	 * @return string
	 */
	public abstract function getScript();
	/**
	 * ȡ�÷�����DNS
	 * @param $schema string
	 * @return string
	 */
	public abstract function getHost($schema = '');
	/**
	 * ȡ��http�����е�����������ַ
	 * @return string
	 */
	public abstract function getUserHost();
	/**
	 * ȡ��http����ͻ����е�IP��ַ
	 * @return string
	 */
	public abstract function getUserHostAddr();
	/**
	 * ȡ��http�����з�������������ַ
	 * @return string
	 */
	public abstract function getServerName();
	/**
	 *  ȡ��http�����з�������������ַ�Ķ˿ں�
	 * @return string
	 */
	public abstract function getServerPort();
	/**
	 * ��ȡHTTP����Ŀͻ��˵�����������Ϣ
	 * @param string $userAgent �ͻ����������ԭʼ�û�������Ϣ
	 * @return array
	 */
	public abstract function getUserBrowser();
	/**
	 * ����httpͷ��Ϣ
	 * @return  array;
	 */
	public abstract function getHeaders();
		
}




