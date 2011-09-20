<?php
/**
 * 视图处理器接口类
 * 
 * <i>WindView</i>是基础的视图处理器,职责：进行视图渲染.<br>
 *
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright Copyright &copy; 2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind.viewer
 */
interface IWindView {

	/**
	 * 视图渲染方法
	 * 
	 * 通过该方法进行视图渲染,并将视图内容设置到response对象中以备输出<code>
	 * $this->getResponse()->setBody($viewContent, $templateName);
	 * </code>
	 * 
	 * @return void
	 * @throws WindViewException
	 */
	public function render();

}

?>