<?php
class API
{
	const VERSION_CODE = 2;

	/**
	 * @var string The XML request string.
	 */
	protected $requestXML = '';

	/**
	 * @var object The SimpleXML representation of the supplied XML.
	 */
	public $request = null;

	/**
	 * @var array Array of details of the user accessing the API.
	 */
	protected $authenticatedUser = array();

	/**
	 * The constructor. Setup and handle the XML request.
	 */
	public function __construct($request)
	{
		if(!$request) {
			$this->BadRequest('A valid XML request to the API was not supplied.');
			return;
		}

		// Store the request XML locally
		$this->requestXML = $request;

		// Attempt to parse the XML
		try {
			$this->request = new SimpleXMLElement($request);
		}
		catch(Exception $e) {
			$this->BadRequest('The XML supplied was not valid.');
			return;
		}

		$requiredNodes = array(
			'username',
			'usertoken',
			'requesttype',
			'requestmethod',
			'details'
		);
		foreach($requiredNodes as $node) {
			if(empty($this->request->$node)) {
				$this->BadRequest('The supplied XML does not contain the '.$node.' node. This is a required field.');
				return;
			}
		}

		if(!$this->AuthenticateUser($this->request->username, $this->request->usertoken)) {
			$this->BadRequest('The supplied username and usertoken were unable to be verified. Please ensure that the details are valid and the user has API access.');
		}

		if(!$this->RouteRequest($this->request->requesttype, $this->request->requestmethod)) {
			$this->BadRequest('The supplied requesttype or requestmethod are invalid. Please check that you are calling a valid API method and try again.');
		}
	}

	private function AuthenticateUser($username, $token)
	{
		$query = "
			SELECT pk_userid
			FROM [|PREFIX|]users
			WHERE
				username='".$GLOBALS['ISC_CLASS_DB']->Quote((string)$username)."' AND
				usertoken='".$GLOBALS['ISC_CLASS_DB']->Quote((string)$token)."' AND
				userstatus=1 AND
				userapi=1
		";
		$userId = $GLOBALS['ISC_CLASS_DB']->FetchOne($query);
		if(!$userId) {
			return false;
		}

		return true;
	}

	private function RouteRequest($controller, $action)
	{
		// Invalid controller or action supplied
		if(preg_match('#[^a-z0-9\_-]#i', $controller) || preg_match('#[^a-z0-9\_-]#i', $action)) {
			echo __LINE__;
			return false;
		}

		$action = 'Action_'.$action;
		$controllerFile = 'class.'.strtolower($controller).'.api.php';
		$controllerClass = 'API_'.strtoupper($controller);

		$controllerPath = ISC_BASE_PATH.'/includes/classes/api/'.$controllerFile;
		if(!file_exists($controllerPath)) {
			return false;
		}

		require_once ISC_BASE_PATH.'/includes/classes/api/class.base.api.php';
		require_once $controllerPath;

		if(!class_exists($controllerClass)) {
			return false;
		}

		if(!method_exists($controllerClass, $action)) {
			return false;
		}

		$instance = new $controllerClass($this);
		$result = call_user_func(array($instance, $action));
		if($result !== false) {
			$this->SendResponse($result);
		}
		return true;
	}

	protected function SendResponse($output)
	{
		header("Content-Type: text/xml");
		echo '<?xml version="1.0" encoding="'.GetConfig('CharacterSet').'" ?>';
		echo '<response>';
		echo '<status>SUCCESS</status>';
		echo '<version>' . self::VERSION_CODE . '</version>';
		echo '<data>';
		echo $this->CreateOutput($output);
		echo '</data>';
		echo '</response>';
		exit;
	}

	protected function BadRequest($message)
	{
		header("Content-Type: text/xml");
		echo '<?xml version="1.0" encoding="'.GetConfig('CharacterSet').'" ?>';
		echo '<response>';
		echo '<status>FAILED</status>';
		echo '<version>' . self::VERSION_CODE . '</version>';
		echo '<errormessage>';
		echo $message;
		echo '</errormessage>';
		echo '</response>';
		exit;
	}

	public function CreateOutput($output)
	{
		if($output === '') {
			return '';
		}
		else if (!is_array($output)) {
			return '<![CDATA['.$this->EscapeCData($output).']]>';
		}

		$xml_output = '';
		foreach ($output as $name => $data) {
			if (is_numeric($name)) {
				$name = 'item';
			}

			if($data === '') {
				$xml_output .= '<'.$name.' />';
				continue;
			}

			if (!is_array($data)) {
				$xml_output .= '<'.$name.'><![CDATA['.$this->EscapeCData($data).']]></'.$name.'>';
				continue;
			}

			$xml_output .= '<'.$name.'>';

			if (is_array($data)) {
				foreach ($data as $k => $v) {
					if (is_array($v)) {
						$xml_output .= '<item>'.$this->CreateOutput($v).'</item>';
						continue;
					}
					if (is_numeric($k)) {
						$k = 'item';
					}

					if($v === '') {
						$xml_output .= '<'.$k.' />';
						continue;
					}

					$xml_output .= '<'.$k.'><![CDATA['.$this->EscapeCData($v).']]></'.$k.'>';
				}
			}
			$xml_output .= '</'.$name.'>';
		}

		return $xml_output;
	}

	private function EscapeCData($output)
	{
		return str_replace(']]>', ']]]]>![CDATA[>', $output);
	}
}