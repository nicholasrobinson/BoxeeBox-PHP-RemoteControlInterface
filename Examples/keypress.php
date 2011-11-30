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
				// <shift> modifier
				if (e.shiftKey)
				{
					switch ( e.keyCode ) 
					{
						// Exclamation Point
						case '1'.charCodeAt(0):
							SendUnicodeChar('!'.charCodeAt(0));
							break;
						// 'At'
						case '2'.charCodeAt(0):
							SendUnicodeChar('@'.charCodeAt(0));
							break;
						// Hash
						case '3'.charCodeAt(0):
							SendUnicodeChar('#'.charCodeAt(0));
							break;
						// Dollar
						case '4'.charCodeAt(0):
							SendUnicodeChar('$'.charCodeAt(0));
							break;
						// Percent
						case '5'.charCodeAt(0):
							SendUnicodeChar('%'.charCodeAt(0));
							break;
						// Caret
						case '6'.charCodeAt(0):
							SendUnicodeChar('^'.charCodeAt(0));
							break;
						// Ampersand
						case '7'.charCodeAt(0):
							SendUnicodeChar('&'.charCodeAt(0));
							break;
						// Asterisk
						case '8'.charCodeAt(0):
							SendUnicodeChar('*'.charCodeAt(0));
							break;
						// Left bracket
						case '9'.charCodeAt(0):
							SendUnicodeChar('('.charCodeAt(0));
							break;
						// Right bracket
						case '0'.charCodeAt(0):
							SendUnicodeChar(')'.charCodeAt(0));
							break;
						// Underscore
						case 189:
							SendUnicodeChar('_'.charCodeAt(0));
							break;
						// Plus
						case 187:
							SendUnicodeChar('+'.charCodeAt(0));
							break;
						// Left curly bracket
						case 219:
							SendUnicodeChar('{'.charCodeAt(0));
							break;
						// Right curly bracket
						case 221:
							SendUnicodeChar('}'.charCodeAt(0));
							break;
						// Pipe
						case 220:
							SendUnicodeChar('|'.charCodeAt(0));
							break;
						// Colon
						case 186:
							SendUnicodeChar(':'.charCodeAt(0));
							break;
						// Double quote
						case 222:
							SendUnicodeChar('"'.charCodeAt(0));
							break;
						// Tilde
						case 192:
							SendUnicodeChar('~'.charCodeAt(0));
							break;
						// Less than
						case 188:
							SendUnicodeChar('<'.charCodeAt(0));
							break;
						// Greater than
						case 190:
							SendUnicodeChar('>'.charCodeAt(0));
							break;
						// Question mark
						case 191:
							SendUnicodeChar('?'.charCodeAt(0));
							break;
						// Default
						default:
							break;
					}	
				}
				else
				{
					switch ( e.keyCode ) 
					{
						// <enter>
						case 13:
							SendKey(256);
							break;
						// <shift>
						case 16:
						// <command>
						case 91:
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
						// Space
						case ' '.charCodeAt(0):
						// Backspace
						case 8:
						// Tab
						case 9:
							SendKey(e.keyCode + 61696);
							break;
						// Slant
						case 192:
							SendUnicodeChar('`'.charCodeAt(0));
							break;
						// Hyphen
						case 189:
							SendUnicodeChar('-'.charCodeAt(0));
							break;
						// Equals
						case 187:
							SendUnicodeChar('='.charCodeAt(0));
							break;
						// Left square bracket
						case 219:
							SendUnicodeChar('['.charCodeAt(0));
							break;
						// Right square bracket
						case 221:
							SendUnicodeChar(']'.charCodeAt(0));
							break;
						// Backslash
						case 220:
							SendUnicodeChar('\\'.charCodeAt(0));
							break;
						// Semi-colon
						case 186:
							SendUnicodeChar(';'.charCodeAt(0));
							break;
						// Apostrophe
						case 222:
							SendUnicodeChar('\''.charCodeAt(0));
							break;
						// Comma
						case 188:
							SendUnicodeChar(','.charCodeAt(0));
							break;
						// Full stop
						case 190:
							SendUnicodeChar('.'.charCodeAt(0));
							break;
						// Forward slash
						case 191:
							SendUnicodeChar('/'.charCodeAt(0));
							break;
						// Default
						default:
							// lower-case alphabetic
							if (e.keyCode >= 'A'.charCodeAt(0) && e.keyCode <= 'Z'.charCodeAt(0))
							{
								SendUnicodeChar(e.keyCode + 32);
							}
							// Numeric
							else if (e.keyCode >= '0'.charCodeAt(0) && e.keyCode <= '9'.charCodeAt(0))
							{
								SendUnicodeChar(e.keyCode);
							}
							break;
					}
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
		Type Here: <input type="text" />
		<p>&lt;up&gt; = Up</p>
		<p>&lt;down&gt; = Down</p>
		<p>&lt;left&gt; = Left</p>
		<p>&lt;right&gt; = Right</p>
		<p>&lt;escape&gt; = Back</p>
		<p>&lt;enter&gt; = Mouse Click</p>
	</body>
</html>