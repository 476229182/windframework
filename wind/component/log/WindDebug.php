<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */
defined('RUNTIME_START') or define('RUNTIME_START', microtime(true));
defined('USEMEM_START') or define('USEMEM_START', memory_get_usage());
/**
 * ���Թ���
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
class WindDebug {
	
	private static $breakpoint = array();
	public static function setBreakPoint($point = '') {
		if (isset(self::$breakpoint[$point]))
			return false;
		self::$breakpoint[$point]['time'] = microtime(true);
		self::$breakpoint[$point]['mem'] = memory_get_usage();
		return true;
	}
	/**
	 * ���õ��Ե�
	 * @param string $point ���Ե�
	 */
	public static function removeBreakPoint($point = '') {
		if ($point) {
			if (isset(self::$breakpoint[$point]))
				unset(self::$breakpoint[$point]);
		} else {
			self::$breakpoint = array();
		}
	}
	
	/**
	 * ȡ��ϵͳ���������ڴ�
	 */
	public static function getMemUsage() {
		$useMem = memory_get_usage() - USEMEM_START;
		return $useMem ? round($useMem / 1024, 4) : 0;
	}
	
	/**
	 * ȡ��ϵͳ��������ʱ��
	 */
	public static function getExecTime() {
		$useTime = microtime(true) - RUNTIME_START;
		return $useTime ? round($useTime, 4) : 0;
	}
	
	/**
	 * ��ȡ���Ե�
	 * @param $point
	 * @param $label
	 */
	public static function getBreakPoint($point, $label = '') {
		if (!isset(self::$breakpoint[$point]))
			return array();
		return $label ? self::$breakpoint[$point][$label] : self::$breakpoint[$point];
	}
	
	/**
	 * ���Ե�֮��ϵͳ���������ڴ�
	 * @param string $beginPoint ��ʼ���Ե�
	 * @param string $endPoint   �������Ե�
	 * @return float 
	 */
	public static function getMemUsageOfp2p($beginPoint, $endPoint = '') {
		if (!isset(self::$breakpoint[$beginPoint]))
			return 0;
		$endMemUsage = isset(self::$breakpoint[$endPoint]) ? self::$breakpoint[$endPoint]['mem'] : memory_get_usage();
		$useMemUsage = $endMemUsage - self::$breakpoint[$beginPoint]['mem'];
		return round($useMemUsage / 1024, 4);
	}
	
	/**
	 * ���Ե�֮���ϵͳ��������ʱ��
	 * @param string $beginPoint ��ʼ���Ե�
	 * @param string $endPoint   �������Ե�
	 * @return float 
	 */
	public static function getExecTimeOfp2p($beginPoint, $endPoint = '') {
		if (!isset(self::$breakpoint[$beginPoint]))
			return 0;
		$endTime = self::$breakpoint[$endPoint] ? self::$breakpoint[$endPoint]['time'] : microtime(true);
		$useTime = $endTime - self::$breakpoint[$beginPoint]['time'];
		return round($useTime, 4);
	}
	
	/**
	 * ��ջ���
	 * @param array $trace ��ջ���ã����쳣
	 * @return array 
	 */
	public static function trace($trace = array()) {
		$debugTrace = $trace ? $trace : debug_backtrace();
		$traceInfo = array();
		foreach ($debugTrace as $info) {
			$info['args'] = self::traceArgs($info['args']);
			$str = '[' . date("Y-m-d H:i:m") . '] ' . $info['file'] . ' (line:' . $info['line'] . ') ';
			$str .= $info['class'] . $info['type'] . $info['function'] . '(';
			$str .= implode(', ', $info['args']);
			$str .= ")";
			$traceInfo[] = $str;
		}
		return $traceInfo;
	}
	/**
	 * ��ȡϵͳ�����ص��ļ�
	 */
	public static function loadFiles() {
		return get_included_files();
	}
	
	public static function debug($message = '', $trace = array(), $begin = '', $end = '') {
		$runtime = self::getExecTime();
		$useMem = self::getMemUsage();
		$separate = "<br/>";
		$trace = implode("{$separate}", self::trace($trace));
		$debug .= "{$message}{$separate}";
		$debug .= "Runtime:{$runtime}s{$separate}";
		$debug .= "Memory consumption:{$useMem}byte{$separate}";
		$debug .= "Stack conditions:{$separate}{$trace}{$separate}";
		if ($begin && $end) {
			$PointUseTime = self::getExecTimeOfp2p($begin, $end);
			$PointUseMem = self::getMemUsageOfp2p($begin, $end);
			$debug .= "Between points {$begin} and {$end} debugging system conditions:{$separate}";
			$debug .= "Runtime:{$PointUseTime}s{$separate}";
			$debug .= "Memory consumption:{$PointUseMem}byte{$separate}";
		}
		return $debug;
	}
	
	private static function traceArgs($args = array()) {
		foreach ($args as $key => $arg) {
			if (is_array($arg))
				$args[$key] = 'array(' . implode(',', $arg) . ')';
			elseif (is_object($arg))
				$args[$key] = 'class ' . get_class($arg);
			else
				$args[$key] = $arg;
		}
		return $args;
	}

}
?>