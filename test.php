<?php

###############
#  DISCOVERY  #
###############

# Define constants
define('TIMEOUT', 1);
define('UDPXML_PORT', 2562);
define('BIND_ADDRESS', '0.0.0.0');
define('BROADCAST_ADDRESS', '255.255.255.255');
define('CLIENT_APPLICATION_NAME', 'iphone_remote');
define('SERVER_APPLICATION_NAME', 'boxee');
define('SHARED_KEY', 'b0xeeRem0tE!');

# Initialize variables
$timeout				= TIMEOUT;
$clientApplicationName	= CLIENT_APPLICATION_NAME;
$serverApplicationName	= SERVER_APPLICATION_NAME;
$applicationVersion		= '1';
$randomDigits			= 'BoxeeBoxPHPRemote';
$sharedKey				= SHARED_KEY;
$bb						= new stdClass();

# Initialize UDP Socket
$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
# Bind to UDPXML port
socket_bind($socket, BIND_ADDRESS, UDPXML_PORT);
# Enable Broadcast
socket_set_option($socket, SOL_SOCKET, SO_BROADCAST, 1);

#	Compose discovery packet
#
#	Example:
#	<?xml version="1.0" ?\>
#	<BDP1 cmd="discover" application="iphone_remote" version="1" challenge="BoxeeBoxPHPRemote" 
#	signature="c736b87f0ae19ce029b6f2bb91e1edf5" />
#
$buf =	'<?xml version="1.0"?>';
$buf .= '<BDP1 cmd="discover" application="' . $clientApplicationName . '" version="' . $applicationVersion . '" challenge="' . $randomDigits . '" signature="' . md5($randomDigits . $sharedKey) . '" />';

# Broadcast discovery packet
socket_sendto($socket, $buf, strlen($buf), 0, BROADCAST_ADDRESS, UDPXML_PORT);
# Set socket to blocking
socket_set_block($socket);
# Set socket timeout
socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $timeout, 'usec' => 0));


# Set loop timeout
$timeout += time();

# Loop fetching reponses (Assume one BoxeeBox)
#
# Example:
# <?xml version="1.0"?\>
# <BDP1 cmd="found" application="boxee" version="1.2.2" name="boxeebox" httpPort="8800" httpAuthRequired="false" response="0F7106058AABB82D337EDF784097B91E" signature="93F823EA481B6E1F4059914B79B52EF9"/>
#
while (time() < $timeout) 
{
	# If no error
	if ($responseLength = @socket_recvfrom($socket, $reponse, 2048, 0, $serverAddress, $sourcePort) !== false)
	{
		# If response matches expected format
		if (preg_match('/\<bdp1 cmd="(?P<cmd>.*)" application="(?P<application>.*)" version="(?P<version>.*)" name="(?P<name>.*)" httpPort="(?P<httpPort>.*)" httpAuthRequired="(?P<httpAuthRequired>.*)" response="(?P<response>.*)" signature="(?P<signature>.*)"\/\>/i', $reponse, $matches) == 1)
		{
			$bb->address	= $serverAddress;
			$bb->hostname	= $matches['name'];
			$bb->port		= $matches['httpPort'];
		}
	}
}

# Set socket to non-blocking
socket_set_nonblock($socket);

# Close the socket
socket_close($socket);

###############
#  COMMANDS   #
###############

/*
Commands
- GetVolume Retrieves the current volume setting as a percentage of the maximum possible value.
- SetVolume(percent) Sets the volume as a percentage of the maximum possible.
- Mute Toggles the sound on/off.
- Pause Pauses the currently playing media.
- Stop Stops the currently playing media.
- PlayNext Starts playing/showing the next media/image in the current playlist or, if currently showing a slidshow, the slideshow playlist.
- PlayPrev Starts playing/showing the previous media/image in the current playlist or, if currently showing a slidshow, the slideshow playlist.
- Mute Toggles the sound on/off.
- SeekPercentage(percent) Sets the playing position of the currently playing media as a percentage of the media’s length.
- SeekPercentageRelative(relative-percentage) Adds/Subtracts the current percentage on to the current postion in the song
- GetPercentage Retrieves the current playing position of the currently playing media as a percentage of the media’s length.

Inputs (partial list)
- SendKey(270) Click on UP button
- SendKey(271) Click on DOWN button
- SendKey(272) Click on LEFT button
- SendKey(273) Click on RIGHT button
- SendKey(275) Click on BACK button
- SendKey(61704) Sends an backspace key (used in keyboard)
- SendKey(<ASCII value + 61696>) Sends an ASCII key (used in keyboard)

Below is a list of commands added by boxee:
- getKeyboardText Returns whether a virtual keyboard is active, whether it has hidden text and the actual text in the keyboard.
*/

# Define constants
define('PROTOCOL', 'http');
define('API_END_POINT', '/xbmcCmds/xbmcHttp?command=');

# Define a list of commands
$commands = array(
						'SendKey(270)',
						'SendKey(272)',
						'SendKey(272)'
				);

# Loop through each command
foreach ($commands as $command)
{
	# Initialize curl handler
	$curlHandler = curl_init();

	# Configure curl target
	curl_setopt ($curlHandler, CURLOPT_URL, PROTOCOL . '://' . $bb->address . ':' . $bb->port . API_END_POINT . $command);

	# Execute curl request (supressing output)
	ob_start();
	$response = curl_exec($curlHandler);
	ob_end_clean();

	# Close curl handler
	curl_close($curlHandler);
}

?>