<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$ 
 * @package 
 */
class WindWebApplication extends WindModule implements IWindApplication {
	/**
	 * @var WindDispatcher
	 */
	protected $dispatcher = null;
	/**
	 * @var WindUrlBasedRouter
	 */
	protected $handlerAdapter = null;

	/* (non-PHPdoc)
	 * @see IWindApplication::processRequest()
	 */
	public function processRequest() {
		try {
			if (IS_DEBUG && IS_DEBUG <= WindLogger::LEVEL_DEBUG) {
				Wind::log('[core.web.WindWebApplication.processRequest]', WindLogger::LEVEL_DEBUG, 'wind.core');
			}
			$handler = $this->getHandler();
			call_user_func_array(array($handler, 'preAction'), array($this->handlerAdapter));
			$forward = call_user_func_array(array($handler, 'doAction'), array($this->handlerAdapter));
			call_user_func_array(array($handler, 'postAction'), array($this->handlerAdapter));
			$this->doDispatch($forward);
		} catch (WindActionException $actionException) {
			$this->sendErrorMessage($actionException);
		} catch (WindDbException $dbException) {
			$this->sendErrorMessage($dbException->getMessage());
		} catch (WindViewException $viewException) {
			//TODO
			throw new Exception($viewException->getMessage());
		}
	}

	/* (non-PHPdoc)
	 * @see IWindApplication::doDispatch()
	 */
	public function doDispatch($forward) {
		if ($forward === null) {
			Wind::log('[core.web.WindWebApplication.doDispatch] Forward is null, dispatch abort.', 
				WindLogger::LEVEL_DEBUG, 'wind.core');
			return;
		}
		$this->getDispatcher()->dispatch($this, $forward, $this->handlerAdapter);
	}

	/**
	 * 获得Action处理句柄
	 * 
	 * @param WindHttpRequest $request
	 */
	protected function getHandler() {
		$handler = $this->getHandlerAdapter()->doParse();
		if (IS_DEBUG && IS_DEBUG <= WindLogger::LEVEL_DEBUG) {
			Wind::log('[core.web.WindWebApplication.getHandler] router result:' . $handler, WindLogger::LEVEL_DEBUG, 
				'wind.core');
		}
		if (!$this->getSystemFactory()->checkAlias($handler)) {
			$definition = new WindClassDefinition();
			$definition->setPath($handler);
			$definition->setScope(WindClassDefinition::SCOPE_SINGLETON);
			$definition->setAlias($handler);
			$definition->setProxy('true');
			$definition->setProperties(
				array('errorMessage' => array('ref' => COMPONENT_ERRORMESSAGE), 
					'forward' => array('ref' => COMPONENT_FORWARD), 'urlHelper' => array('ref' => COMPONENT_URLHELPER)));
			$this->getSystemFactory()->addClassDefinitions($definition);
		}
		return $this->getSystemFactory()->getInstance($handler);
	}

	/**
	 * 异常处理请求
	 * 
	 * @param WindActionException|string actionException
	 * @return
	 */
	protected function sendErrorMessage($actionException) {
		$_tmp = is_object($actionException) ? $actionException->getError() : $actionException;
		if (is_string($_tmp)) $_tmp = new WindErrorMessage($_tmp);
		$forward = $this->getSystemFactory()->getInstance(COMPONENT_FORWARD);
		$forward->forwardAnotherAction($_tmp->getErrorAction(), $_tmp->getErrorController());
		$this->getRequest()->setAttribute('error', $_tmp->getError());
		$this->doDispatch($forward);
	}

	/**
	 * @return WindUrlBasedRouter
	 */
	protected function getHandlerAdapter() {
		return $this->_getHandlerAdapter();
	}

	/**
	 * @return WindDispatcher
	 */
	protected function getDispatcher() {
		return $this->_getDispatcher();
	}

}