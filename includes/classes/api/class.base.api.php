<?php
abstract class API_BASE
{
	/**
	* Instance of the API class (router)
	*
	* @var API
	*/
	protected $router = null;

	public function __construct($router)
	{
		$this->router = $router;
	}

	protected function SendResponse()
	{
		$arguments = func_get_args();
		call_user_func_array(array($this->router, 'SendResponse'), $arguments);
	}

	protected function BadRequest()
	{
		$arguments = func_get_args();
		call_user_func_array(array($this->router, 'BadRequest'), $arguments);
	}
}