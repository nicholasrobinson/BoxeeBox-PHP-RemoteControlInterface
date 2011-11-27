<?php
/*
 *	File:			HTTPGET.class.php
 *	Description:	Handle HTTP GET communication with BoxeeBox Remote Control Interface
 *	Author:			Nicholas Robinson 11/26/2011
 */
 
# TODO: Add authentication

# Define constants
define('PROTOCOL', 'http');
define('API_END_POINT', '/xbmcCmds/xbmcHttp?command=');

/** 
* Handle HTTP GET communication with BoxeeBox Remote Control Interface
*/
class HTTPGET
{
	# Socket Properties
	private $curlHandler;
	
	/** 
	* Configure cURL handler
	*
	* @param	$serverAddress		Server IP Address or Hostname
	* @param	$action				string command for BoxeeBox Remote Control Interface
	*
	* @return null
	*/
	public function __construct($serverAddress, $serverPort, $command)
	{
		# Initialize curl handler
		$this->curlHandler = curl_init();
		# Verify successful initialization
		if ($this->curlHandler === false) 
		{
			throw new Exception('curl_init() failed.');
		}	
		# Configure curl target
		$optionSuccess = curl_setopt($this->curlHandler, CURLOPT_URL, PROTOCOL . '://' . $serverAddress . ':' . $serverPort . API_END_POINT . $command);
		# Verify successful operation
		if ($optionSuccess == false) 
		{
			throw new Exception('curl_setopt() failed.');
		}
	}
	
	/** 
	* Destroy cURL Handler
	*
	* @return null
	*/
	public function __destruct()
	{
		curl_close($this->curlHandler);
	}
	
	/** 
	* Execute cURL transaction
	*
	* @return string response or FALSE for failure
	*/
	public function execute()
	{
		# Execute curl request
		ob_start();
		$curlSuccess = curl_exec($this->curlHandler);
		$response = ob_get_contents();
		ob_end_clean();
		# Verify success execution
		if ($curlSuccess === false) 
		{
			throw new Exception('curl_exec() failed.');
		}
		# Return response
		return $response;
	}
	
}

?>