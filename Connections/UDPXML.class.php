<?php
/*
 *	File:			UDPXML.class.php
 *	Description:	Handle XML communication via a UDP Socket
 *	Author:			Nicholas Robinson 11/21/2011
 */

# UDP Socket Constants
define('TIMEOUT', 1);
define('UDPXML_PORT', '2562');
define('BROADCAST_ADDRESS', '255.255.255.255');
define('BIND_ADDRESS', '0.0.0.0');
define('READ_LENGTH', 2048);

# XML Discovery Packet constants
define('XML_VERSION', '1.0');
define('CLIENT_APPLICATION_NAME', 'iphone_remote');
define('SERVER_APPLICATION_NAME', 'boxee');
define('SHARED_KEY', 'b0xeeRem0tE!');

/** 
* Handle XML communication via a UDP Socket
*/
class UDPXML
{
	# Socket Properties
	private $timeout;
	private $clientApplicationName;
	private $serverApplicationName;
	private $applicationVersion;
	private $randomDigits;
	private $sharedKey;
	private $socket;
	
	# Server Properties
	private $serverAddress		= '';
	private $serverHostname		= '';
	private $serverPort			= 0;
	
	/** 
	* Create UDP Socket
	*
	* @return null
	*/
	public function __construct()
	{
		# Initialize variables
		$this->timeout					= TIMEOUT;
		$this->clientApplicationName	= CLIENT_APPLICATION_NAME;
		$this->serverApplicationName	= SERVER_APPLICATION_NAME;
		$this->applicationVersion		= '1';
		$this->randomDigits				= 'BoxeeBoxPHPRemote';
		$this->sharedKey				= SHARED_KEY;
		
		# Initialize UDP Socket
		$this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		# Verify successful socket creation
		if ($this->socket === false) 
		{
			throw new Exception('socket_create() failed: reason: ' . socket_strerror(socket_last_error()));
		}
		
		# Bind to UDPXML port
		$bindSuccess = socket_bind($this->socket, BIND_ADDRESS, UDPXML_PORT);
		# Verify successful socket bind
		if ($bindSuccess == false) 
		{
			throw new Exception('socket_bind() failed: reason: ' . socket_strerror(socket_last_error()));
		}
		
		# Enable Broadcast
		$optionSuccess = socket_set_option($this->socket, SOL_SOCKET, SO_BROADCAST, 1);	
		# Verify successful socket option
		if ($optionSuccess == false) 
		{
			throw new Exception('socket_set_option() failed: reason: ' . socket_strerror(socket_last_error()));
		}	

	}
	
	/** 
	* Destroy UDP Socket
	*
	* @return null
	*/
	public function __destruct()
	{
		# Close the socket
		socket_close($this->socket);
	}
	
	/** 
	* Discover BoxeeBox via UDP XML broadcast
	*
	* @return bool success
	*/
	public function discover()
	{
		#	Compose discovery packet
		#
		#	Example:
		#	<?xml version="1.0" ?\>
		#	<BDP1 cmd="discover" application="iphone_remote" version="1" challenge="BoxeeBoxPHPRemote" 
		#	signature="c736b87f0ae19ce029b6f2bb91e1edf5" />
		#
		$discoveryPacket =	'<?xml version="1.0"?>';
		$discoveryPacket .= '<BDP1 cmd="discover" application="' . $this->clientApplicationName . '" version="' . $this->applicationVersion . '" challenge="' . $this->randomDigits . '" signature="' . md5($this->randomDigits . $this->sharedKey) . '" />';

		# Broadcast discovery packet
		$numBytesSent = socket_sendto($this->socket, $discoveryPacket, strlen($discoveryPacket), 0, BROADCAST_ADDRESS, UDPXML_PORT);
		# Verify successful socket option
		if ($numBytesSent === false) 
		{
			throw new Exception('socket_sendto() failed: reason: ' . socket_strerror(socket_last_error()));
		}
		
		# Set socket to blocking
		$blockSucess = socket_set_block($this->socket);
		# Verify successful socket block
		if ($blockSucess == false) 
		{
			throw new Exception('socket_set_block() failed: reason: ' . socket_strerror(socket_last_error()));
		}
		
		# Set socket timeout
		$optionSuccess = socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $this->timeout, 'usec' => 0));
		# Verify successful socket option set
		if ($optionSuccess == false) 
		{
			throw new Exception('socket_set_option() failed: reason: ' . socket_strerror(socket_last_error()));
		}

		# Set loop timeout
		$timeout = $this->timeout + time();
		# Loop fetching reponses (Assume only one BoxeeBox)
		$discoverSuccess = false;
		while (time() < $timeout) 
		{
			# If no error
			if ($responseLength = @socket_recvfrom($this->socket, $reponse, READ_LENGTH, 0, $serverAddress, $sourcePort) !== false)
			{
				# If response matches expected format
				#
				# Example:
				# <?xml version="1.0"?\>
				# <BDP1 cmd="found" application="boxee" version="1.2.2" name="boxeebox" httpPort="8800" httpAuthRequired="false" response="0F7106058AABB82D337EDF784097B91E" signature="93F823EA481B6E1F4059914B79B52EF9"/>
				#
				if (preg_match('/\<bdp1 cmd="(?P<cmd>.*)" application="(?P<application>.*)" version="(?P<version>.*)" name="(?P<name>.*)" httpPort="(?P<httpPort>.*)" httpAuthRequired="(?P<httpAuthRequired>.*)" response="(?P<response>.*)" signature="(?P<signature>.*)"\/\>/i', $reponse, $matches) == 1)
				{
					# Extract key information
					$this->serverAddress	= $serverAddress;
					$this->serverHostname		= $matches['name'];
					$this->serverPort			= $matches['httpPort'];
					# Indicate success
					$discoverSuccess = true;
				}
			}
		}

		# Set socket to non-blocking
		socket_set_nonblock($this->socket);
		
		# Return success
		return $discoverSuccess;
	}
	
	/** 
	* Get server address
	*
	* @return string
	*/
	public function getServerAddress()
	{
		return $this->serverAddress;
	}

	/** 
	* Get server hostname
	*
	* @return string
	*/
	public function getServerHostname()
	{
		return $this->serverHostname;
	}
	
	/** 
	* Get server port
	*
	* @return integer
	*/
	public function getServerPort()
	{
		return $this->serverPort;
	}
	
}

?>