<?php
/*
 *  File:			keypress.php
 *  Description:	jQuery KeyPress Example building on javascript.php example
 *					This example only requires PHP to render the javascript.php file
 *					Once rendered the contents of the file can be used without PHP (just copy and paste the source to a new HTML/js document).
 *  Author:			Nicholas Robinson 11/27/2011
 */
?>
<html>
	<head>
		<?php 
		# Include javascript SDK
		require_once('javascript.php');
		?>
		<script type="text/javascript">
			// Bind text fields to global server parameters
			$(document).ready(function() 
			{
				$('#server_address').val(window.serverAddress);
				$('#server_address').change(function() 
				{ 
					window.serverAddress = $('#server_address').val(); 
				});
				$('#server_port').val(window.serverPort);
				$('#server_port').change(function() 
				{ 
					window.serverPort = $('#server_port').val(); 
				});
			});
			// Handle key-presses
			$(window).keydown(function(e)
			{
				switch ( e.keyCode ) {
					// <shift>
					case 13:
						SendKey(256);
						break;
					// <shift>
					case 16:
						break;
					// Escape / Back
					case 27:
						SendKey(275);
						break;
					// Up
					case 38:
						SendKey(270);
						break;
					// Down
					case 40:
						SendKey(271);
						break; 
					// Left
					case 37:
						SendKey(272);
						break; 
					// Right
					case 39:
						SendKey(273);
						break;
					// Keyboard
					default:
						// <shift> modifier
						if (e.shiftKey)
						{
							// Upper-case alphabetic
							if (e.keyCode >= 'A'.charCodeAt(0) && e.keyCode <= 'Z'.charCodeAt(0))
							{
								SendKey(e.keyCode + 61696);
							}
							// Tilda
							else if (e.keyCode == '`'.charCodeAt(0))
							{
								SendKey('~'.charCodeAt(0) + 61696);
							}
							// 'At' symbol
							else if (e.keyCode == '2'.charCodeAt(0))
							{
								SendKey('@'.charCodeAt(0) + 61696);
							}
							// Hash symbol
							else if (e.keyCode == '3'.charCodeAt(0))
							{
								SendKey('#'.charCodeAt(0) + 61696);
							}
							// Dollar symbol
							else if (e.keyCode == '4'.charCodeAt(0))
							{
								SendKey('$'.charCodeAt(0) + 61696);
							}
							// Caret symbol
							else if (e.keyCode == '6'.charCodeAt(0))
							{
								SendKey('^'.charCodeAt(0) + 61696);
							}
							// Asterix symbol
							else if (e.keyCode == '8'.charCodeAt(0))
							{
								SendKey('*'.charCodeAt(0) + 61696);
							}
							// Right bracket symbol
							else if (e.keyCode == '0'.charCodeAt(0))
							{
								SendKey(')'.charCodeAt(0) + 61696);
							}
						}
						else
						{
							// Lower-case alphabetic
							if (e.keyCode >= 'A'.charCodeAt(0) && e.keyCode <= 'Z'.charCodeAt(0))
							{
								// Lower-case 'l' hack-fix (why boxee?)
								if (e.keyCode == 'L'.charCodeAt(0))
								{
									SendKey('L'.charCodeAt(0) + 61696);
								}
								else
								{
									SendKey(e.keyCode + 61696 + 32);
								}
							}
							// Numeric
							else if (e.keyCode >= '0'.charCodeAt(0) || e.keyCode <= '9'.charCodeAt(0))
							{
								SendKey(e.keyCode + 61696);
							}
							// Space, Backspace
							else if (e.keyCode == ' '.charCodeAt(0) || e.keyCode == 8)
							{
								SendKey(e.keyCode + 61696);
							}							
						}
						break;
				}
				return;
			});
		</script>
	</head>
	<body>
		<h2>Use your Keyboard to Control your BoxeeBox</h2>
		<hr />
		<p>Ensure you configure your device first by editing Examples/javascript.php</a>.
		<hr />
		Hostname/IP: <input type="text" id="server_address" />
		Port: <input type="text" id="server_port" />
		<hr />
		<p>&lt;up&gt; = Up</p>
		<p>&lt;down&gt; = Down</p>
		<p>&lt;left&gt; = Left</p>
		<p>&lt;right&gt; = Right</p>
		<p>&lt;escape&gt; = Back</p>
	</body>
</html>