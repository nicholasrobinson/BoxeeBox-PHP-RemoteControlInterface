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
					case 27:
						SendKey(275);
						break;
					case 38:
						SendKey(270);
						break;
					case 40:
						SendKey(271);
						break; 
					case 37:
						SendKey(272);
						break; 
					case 39:
						SendKey(273);
						break; 
					default:
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