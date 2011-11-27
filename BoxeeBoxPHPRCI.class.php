<?php
/*
 *	File:			BoxeeBoxPHPRCI.class.php
 *	Description:	Communicate with BoxeeBox via Remote Control Interface
 *	Author:			Nicholas Robinson 11/21/2011
 */
 
# TODO: Add Authentication

# Constants
define('DEBUG', false);

# Includes
define('ROOT', dirname(__FILE__));
require_once(ROOT . '/Connections/UDPXML.class.php');
require_once(ROOT . '/Connections/HTTPGET.class.php');
require_once(ROOT . '/Services/RemoteControlInterface.class.php');

/** 
* Communicate with BoxeeBox via Remote Control Interface
*/
class BoxeeBoxPHPRCI
{
	# Properties
	private $udpServer;
	private $httpServerAddress;
	private $httpServerPort;
	
	/** 
	* Private methods
	*/
	
	/** 
	* Configure HTTP server and port
	*
	* @param  $hostname		BoxeeBox Server Hostname or IP 
	* @param  $hostname		BoxeeBox Server HTTP port 
	*
	* @return null
	*/
	private function setHttpServer($hostname, $hostPort)
	{
		# Initialize http server address
		$this->httpServerAddress = gethostbyname($hostname);
		# Initialize http server port
		$this->httpServerPort = $hostPort;
	}
	
	/** 
	* Public methods
	*/	
	
	/** 
	* Create BoxeeBoxPHPRCI connection
	*
	* @param  $hostname		BoxeeBox Server Hostname or IP 
	* @param  $hostname		BoxeeBox Server HTTP port 
	*
	* @return null
	*/
	public function __construct($hostname = '', $hostPort = 8800)
	{
		# Initialize UDP server handle
		$this->udpServer = new UDPXML();
		# Initialize http server address and port
		self::setHttpServer(gethostbyname($hostname), $hostPort);
	}
	
	/** 
	* Automatically discover BoxeeBox
	*
	* @return null
	*/
	public function discover()
	{
		# Execute UDP discovery
		$this->udpServer->discover();
		# Store http server address and port
		self::setHttpServer($this->udpServer->getServerAddress(), $this->udpServer->getServerPort());
	}
	
	/** 
	* Handle API Services
	*
	* @param  $command		Remote Control Interface command
	* @param  $parameters	Remote Control Interface command parameters array
	*
	* @return null
	*/
	public function __call($command, $parameters) 
	{
		# Instanciate Remote Control Interface
		$rciAPI = new RemoteControlInterface();
		# Validate command
		if (!method_exists($rciAPI, $command))
		{
			throw new Exception('ERROR: Invalid command specified');
		}
		# Fetch formatted query
		$query = call_user_func_array(array($rciAPI, $command), $parameters);
		# Validate api call
		if ($query == false)
		{
			throw new Exception('ERROR: Invalid parameters specified');
		}
		# Send query to api
		$httpGetTransaction = new HTTPGET($this->httpServerAddress, $this->httpServerPort, $query);
		$response = $httpGetTransaction->execute();
		# Return response
		return (DEBUG ? $query . "\n\n" : '') . $response;
	}
	
}