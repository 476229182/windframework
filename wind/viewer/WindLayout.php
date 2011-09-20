<?php
/**
 * 视图布局对象
 * 
 * 通过设置布局模板文件来实现对页面的布局管理,和WindView以及WindViewerResolver配合使用实现对页面的布局管理.
 * 布局文件的路径设置方式与WindView中的模板路径设置方式相同,支持命名空间方式<code>
 * $layout = layoutFile;	//return $templateDir/layoutFile
 * $layout = nameSpace:layoutFile;	//return nameSpace:layoutFile</code>
 * 布局文件例子<code>
 * <!doctype html>
 * <html>
 * <template source='head'/>	//布局文件中有基础的编译支持
 * <body>
 * <div class="wrap">
 * <section class="main">
 * <!--#$this->segment('head');#-->	//布局切片
 * <!--#$this->content();#-->	//调用当前的模板内容
 * </section>
 * </div>
 * </body>
 * </html></code>
 * 组件定义:<code>
 * 'layout' => array(
 * 'path' => 'WIND:viewer.WindLayout',
 * 'scope' => 'prototype',
 * )</code>
 * <note><b>注意:</b>框架默认布局组件</note>
 *
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright Copyright &copy; 2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind.viewer
 */
class WindLayout extends WindModule {
	/**
	 * 布局文件
	 * 
	 * @var string
	 */
	private $layout;
	/**
	 * 视图渲染器对象
	 * 
	 * @var WindViewerResolver
	 */
	private $viewer = null;
	private $script;
	private $css;
	private $segments = array();

	/**
	 * @param string $layoutFile 布局文件
	 */
	public function __construct($layoutFile = '') {
		$this->setLayoutFile($layoutFile);
	}

	/**
	 * 解析布局文件
	 * 
	 * @param WindViewerResolver $viewer
	 * @return void
	 */
	public function parser($viewer) {
		$this->viewer = $viewer;
		ob_start();
		if ($this->layout) {
			$__theme = $this->viewer->getWindView()->theme;
			$themeUrl = $__theme ? $__theme : $this->getRequest()->getBaseUrl(true);
			unset($__theme);
			list($tpl) = $this->viewer->compile($this->layout);
			if (!@include ($tpl)) {
				throw new WindViewException('[component.viewer.WindLayout.parser] layout file ' . $tpl, WindViewException::VIEW_NOT_EXIST);
			}
		} else
			$this->content();
		$__content = ob_get_clean();
		foreach ($this->segments as $__key => $__value) {
			if ($__key) $__content = str_replace("<pw-wind key='" . $__key . "' />", $__value[1], $__content);
		}
		$this->script && $__content = preg_replace('/(<\/body>)/i', $this->script . '\\1', $__content);
		$this->css && $__content = preg_replace('/<\/head>/i', $this->css . '</head>', $__content);
		return $__content;
	}

	/**
	 * 输出模板切片内容
	 * 
	 * @param string $template 模板切片名称
	 * @return void
	 */
	private function segment($template) {
		if ($this->viewer === null) return '';
		$this->segments[$template] = $this->viewer->compile($template, '', true);
		echo "<pw-wind key='" . $template . "' />";
	}

	/**
	 * 输出当前模板的内容
	 * 
	 * @return void
	 */
	private function content() {
		if ($this->viewer === null) return '';
		$template = $this->viewer->getWindView()->templateName;
		$this->segment($template);
	}

	/**
	 * 设置模板布局文件
	 * 
	 * @param string $layout 布局文件
	 * @return void
	 */
	public function setLayout($layout) {
		$this->layout = $layout;
	}

	/**
	 * 设置将JavaScript脚本输出到页脚
	 * 
	 * 将内容中的javascript脚本，按照顺序移动到<b><body>...js定义</body></b>
	 * 
	 * @param string $script
	 * @return void
	 */
	public function setScript($script) {
		$this->script .= $script;
	}

	/**
	 * 设置Css定义输出到页头
	 * 
	 * 将内容中的css定义，按照顺序移动到<b><head>...css定义</head></b>
	 * 
	 * @param string $css
	 * @return void
	 */
	public function setCss($css) {
		$this->css .= $css;
	}
}