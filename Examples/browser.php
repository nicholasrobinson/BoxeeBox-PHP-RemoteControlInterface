<?php
/*
 *  File:			browser.php
 *  Description:	BoxeeBox Remote Control Interface API Browser
 *  Author:			Nicholas Robinson 11/27/2011
 */

/************************************************
*					EXECUTION					*
************************************************/

# Process discover query
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'discover')
{
	# Include UDPXML connection class for API DEBUG purposes (not needed in normal execution)
	require_once('../Connections/UDPXML.class.php');
	# Attempt to execute
	try 
	{
		# Initialize UDP server handle
		$udpServer = new UDPXML();
		# Execute UDP discovery with debug flag asserted
		$response = $udpServer->discover(true);
	}
	# Catch exceptions
	catch (Exception $error)
	{
		$response = 'CONNECTION ERROR';
	}
	# Return json encoded output
	die($response);
}

# Process browser queries
if (isset($_REQUEST['hostname']) && isset($_REQUEST['port']) && isset($_REQUEST['method']))
{
	# Initialise payload array
	$payload = array();

	# Normalize hostname
	if (isset($_REQUEST['hostname']))
		$payload['hostname']		= $_REQUEST['hostname'];
	# Normalize port
	if (isset($_REQUEST['port']))
		$payload['port']			= $_REQUEST['port'];
	# Normalize method
	if (isset($_REQUEST['method']))
		$payload['method']			= $_REQUEST['method'];
		
	# If JSON Encoded parameters are detected
	if (isset($_REQUEST['parameters']) && $_REQUEST['parameters'] != 'null' && $_REQUEST['parameters'] != '')
	{
		$payload['parameters'] = json_decode($_REQUEST['parameters'], true);
		# Throw exception if invalid JSON
		if ($payload['parameters'] === null)
			throw new Exception('Invalid JSON');
		# Normalize parameters
		if (!is_array($payload['parameters']))
			$payload['parameters'] = array($payload['parameters']);
	}
	# Otherwise set parameters to null
	else
		$payload['parameters'] = array();

	# If a payload is present
	if (!empty($payload))
	{
		# Validate Payload
		if (!isset($payload['method']))
			$response['error'] = 'METHOD_MISSING';
		elseif (!isset($payload['parameters']))
			$response['error'] = 'PARAMETERS_MISSING';
		else
		{
			# Include BoxeeBoxPHPRCI class
			require_once('../BoxeeBoxPHPRCI.class.php');
			# Attempt to execute
			try 
			{
				# Connect to BoxeeBox
				$bb = new BoxeeBoxPHPRCI($payload['hostname'], $payload['port']);
				# Issue Remote Control Interface Query
				$output = call_user_func_array(array($bb, $payload['method']), $payload['parameters']);
			}
			# Catch exceptions
			catch (Exception $error)
			{
				$output = 'CONNECTION ERROR';
			}
		}
	}
	# Otherwise indicate error
	else
	{
		$response['error'] = 'PAYLOAD_MISSING';
	}

	# Return output
	die($output);
}

/************************************************
*					BROWSER 					*
************************************************/

# Initialise variables
$services = array();
$method_blacklist = array('__construct');

# Fetch list of PHP class filenames in the services directory
$services_filenames = glob('../Services/*.class.php');
# Iterate through class filenames
foreach ($services_filenames as $services_filename)
{
	# Declare class
	require_once($services_filename);
	# Extract class name from filename
	$class_name = str_replace('.class.php', '', str_replace('../Services/', '', $services_filename));
	# Ignore Base Class
	if (strpos($class_name, 'Base') === false)
	{
		# Create reflection class
		$reflection_class = new ReflectionClass($class_name);
		# Extract Doc comments
		$class_description = trim(str_replace("\0", '', str_replace("\t", '', str_replace("\r", '', str_replace("\n", '', str_replace('*', '', str_replace('*/', '', str_replace('/*', '', $reflection_class->getDocComment()))))))));
		# Add class to services array
		$services[$class_name] = array();
		$services[$class_name]['methods'] = array();
		$services[$class_name]['description'] = $class_description;
		# Get methods
		$reflection_methods = $reflection_class->getMethods(ReflectionMethod::IS_PUBLIC);
		# Iterate through reflection methods
		foreach ($reflection_methods as $reflection_method)
		{
			# If method is not blacklisted
			if (!in_array($reflection_method->getName(), $method_blacklist))
			{
				# Extract Doc comments
				$method_comments = str_replace('*', '', str_replace('*/', '', str_replace('/*', '', $reflection_method->getDocComment())));
				$method_comments_array_1 = explode('@return', $method_comments);
				$method_comments_array_2 = explode('@param', $method_comments_array_1[0]);
				$method_description = trim(str_replace("\0", '', str_replace("\t", '', str_replace("\r", '', str_replace("\n", '', $method_comments_array_2[0])))));
				$method_return_description = trim(str_replace("\0", '', str_replace("\t", '', str_replace("\r", '', str_replace("\n", '', $method_comments_array_1[1])))));
				# Add method to services array
				$services[$class_name]['methods'][$reflection_method->getName()] = array();
				$services[$class_name]['methods'][$reflection_method->getName()]['parameters'] = array();
				$services[$class_name]['methods'][$reflection_method->getName()]['description'] = $method_description;
				$services[$class_name]['methods'][$reflection_method->getName()]['return_description'] = $method_return_description;
				# Get parameters
				$reflection_parameters = $reflection_method->getParameters();
				# Iterate through reflection parameters
				foreach ($reflection_parameters as $index => $reflection_parameter)
				{
					# Extract Doc comments
					$parameters_comments = trim(str_replace("\0", '', str_replace("\t", '', str_replace("\r", '', str_replace("\n", '', str_replace('$' . $reflection_parameter->getName(), '', $method_comments_array_2[(isset($method_comments_array_2[$index + 1]) ? $index + 1 : $index)]))))));
					# Add parameter to services array
					$services[$class_name]['methods'][$reflection_method->getName()]['parameters'][$reflection_parameter->getName()] = array();
					$services[$class_name]['methods'][$reflection_method->getName()]['parameters'][$reflection_parameter->getName()]['description'] = $parameters_comments;
				}
			}
		}
	}
}
?>

<html>
	<head>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
		<script type="text/javascript">
			// Copy services to javascript
			<?php
				echo 'var services = new Array();' . PHP_EOL;
				foreach ($services as $service_name => $service_content)
				{
					echo '			services[\'' . $service_name . '\'] = new Array();' . PHP_EOL;
					echo '			services[\'' . $service_name . '\'][\'methods\'] = new Array();' . PHP_EOL;
					echo '			services[\'' . $service_name . '\'][\'description\'] = \'' . htmlspecialchars($service_content['description'], ENT_QUOTES) . '\';' . PHP_EOL;
					foreach ($service_content['methods'] as $method_name => $method_content)
					{
						echo '			services[\'' . $service_name . '\'][\'methods\'][\'' . $method_name . '\'] = new Array();' . PHP_EOL;
						echo '			services[\'' . $service_name . '\'][\'methods\'][\'' . $method_name . '\'][\'parameters\'] = new Array();' . PHP_EOL;
						echo '			services[\'' . $service_name . '\'][\'methods\'][\'' . $method_name . '\'][\'description\'] =  \'' . htmlspecialchars($method_content['description'], ENT_QUOTES) . '\';' . PHP_EOL;
						echo '			services[\'' . $service_name . '\'][\'methods\'][\'' . $method_name . '\'][\'return_description\'] =  \'' . htmlspecialchars($method_content['return_description'], ENT_QUOTES) . '\';' . PHP_EOL;
						foreach ($method_content['parameters'] as $parameter_name => $parameter_content)
						{
							echo '			services[\'' . $service_name . '\'][\'methods\'][\'' . $method_name . '\'][\'parameters\'][\'' . $parameter_name . '\'] = new Array();' . PHP_EOL;
							echo '			services[\'' . $service_name . '\'][\'methods\'][\'' . $method_name . '\'][\'parameters\'][\'' . $parameter_name . '\'][\'description\'] =  \'' . htmlspecialchars($parameter_content['description'], ENT_QUOTES) . '\';' . PHP_EOL;
						}
					}
				}
			?>
			// Ready Behaviour
			$(document).ready(function()
			{
				// Populate Service Drop-down
				$("#service").html('');
				$('<option>Select Service</option>').appendTo("#service");
				for (var service in services)
				{
					$('<option>' + service + '</option>').appendTo("#service");
				}
				
				// Hide dependent inputs
				$(".method").hide();
				$(".parameters").hide();
				$(".execute").hide();
				
				// Create service select change handler
				$("#service").live('change', function(event)
				{
					// Hide execute
					$(".execute").hide();
					// Hide arguments
					$(".parameters").hide();
					$("#parameters").html('');
					// Hide prototype
					$("#prototype").html('');
					// Populate Service Drop-down
					$("#method").html('');
					$('<option>Select Method</option>').appendTo("#method");
					var service = $("#service").val();
					if (service == "Select Service")
					{
						$(".method").hide();
						return false;
					}
					for (var method in services[service]['methods'])
					{
						$('<option>' + method + '</option>').appendTo("#method");
					}
					$(".method").show();
				});
				
				// Create method select change handler
				$("#method").live('change', function(event)
				{
					// Hide prototype
					$("#prototype").html('');
					// Populate Service Drop-down
					$("#parameters").html('This method takes no parameters...');
					$("#descriptions").html('');
					var service = $("#service").val();
					var method = $("#method").val();
					if (method == "Select Method")
					{
						$(".parameters").hide();
						$(".execute").hide();
						return false;
					}
					if (typeof services[service]['methods'][method]['description'] != "undefined")
						$('<em>' + services[service]['methods'][method]['description'] + '</em><br />').appendTo("#prototype");
					$('<strong>' + service + '::' + method + '(' + '</strong>').appendTo("#prototype");
					$("#parameters").html('');
					if (!$.isEmptyObject(services[service]['methods'][method]['parameters']))
					{
						for (var parameter in services[service]['methods'][method]['parameters'])
						{
							// Prep-populate Device service's deviceid parameter for convenience
							if (service == 'Device' && parameter == 'deviceid' && typeof window.deviceid != 'undefined')
							{
								$('<input type="text" class="parameter" name="' + parameter + '" value="' + window.deviceid + '" /> <em>' + parameter + '</em><br />').appendTo("#parameters");
							}
							else
							{
								$('<input type="text" class="parameter" name="' + parameter + '" /> <em>' + parameter + '</em><br />').appendTo("#parameters");
							}
							$('<strong>$' + parameter + ', ' + '</strong>').appendTo("#prototype");
							$('<em>' + services[service]['methods'][method]['parameters'][parameter]['description'] + '</em><br />').appendTo("#descriptions");
						}
						$("#prototype").html($("#prototype").html().substr(0, $("#prototype").html().length - 11));
					}
					else
						$('<em>This method takes no parameters</em>').appendTo("#parameters");
					$('<strong>);</strong><br />').appendTo("#prototype");
					$('<em>returns ' + services[service]['methods'][method]['return_description'] + '</em>').appendTo("#prototype"); 
					$(".execute").show();
					$(".parameters").show();
				});
				
				// Create discover click handler
				$("#discover").live('click', function(event)
				{
					discover();
				});
				
				// Create execute click handler
				$("#execute").live('click', function(event)
				{
					execute();
				});
				
				// Create form submit handler
				$("#ui").live('submit', function(event)
				{
					execute();
					return false;
				});
				
				// Create clearQuery click handler
				$("#clearQuery").live('click', function(event)
				{
					$("#method").change();
				});
				
				// Create clearResults click handler
				$("#clearResults").live('click', function(event)
				{
					$("#results").html('');
				});
				
				// Create hostname keypress handler
				$('.hostname').live('keypress', function (e) {
					var code = (e.keyCode ? e.keyCode : e.which);
					if (code == 13) 
						return false;
				});
				
				// Create port keypress handler
				$('.port').live('keypress', function (e) {
					var code = (e.keyCode ? e.keyCode : e.which);
					if (code == 13) 
						return false;
				});

				// Create parameter keypress handler
				$('.parameter').live('keypress', function (e) {
					var code = (e.keyCode ? e.keyCode : e.which);
					if (code == 13) 
						$('#execute').click();
				});
			});
			
			// Execute discovery broadcast
			function discover()
			{
				// Output Query
				var query = '<?xml version="1.0" ?\>\n<BDP1 cmd="discover" application="iphone_remote" version="1" challenge="BoxeeBoxPHPRemote" signature="c736b87f0ae19ce029b6f2bb91e1edf5" />';
				$('<p>Sent:<br /><span class="query">' + htmlEntities(query) + '</p><hr class="results" />').prependTo("#results");
				// POST to API with JSON encoded query object
				$.ajax({
					type: 'POST',
					url: '',
					data:	{
							'method' : 'discover'
							},
					success: 
						function(result) 
						{
							// Output result
							$('<p>Received:<br /><span class="' + (result.match(/error/i) ? 'error' : 'result') + '">' + htmlEntities(result) + '</p>').prependTo("#results");
							// Attempt tp extract hostname and port from response
							var regex = /name="(.*)" httpPort="(.*)" httpAuthRequired.*\/\>\n\nfrom (.*)/gi;
							var matches = regex.exec(result);
							// If successful, populate fields
							if (matches != null && $.isArray(matches) && matches.length == 4)
							{
								//$('#hostname').val(matches[1]);
								$('#port').val(matches[2]);
								$('#hostname').val(matches[3]); // Using UDP source ip as local DNS may not be trustworthy
							}
						},
					dataType: 'text'
				});
			}
			
			function execute()
			{
				// Extract Form Values
				var hostname = $("#hostname").val();
				var port = $("#port").val();
				var service = $("#service").val();
				var method = $("#method").val();
				var parameters = new Array();
				var parameterList = new Array();
				var parameterValue = '';
				// Extract form parameter values
				for (var parameter in services[service]['methods'][method]['parameters'])
				{
					parameterValue = $("[name=" + parameter + "]").val();
					parameters.push(parameterValue);
				}
				// Create JSON encoded parameters payload
				var jsonPayload = '[';
				// Encode each parameter dependent on type
				for (var i in parameters)
				{
					var parameter = parameters[i];
					// If parameter encapsulated by double quotes or is an Object or numeric
					if (	(parameter[0] == '"' && parameter[parameter.length - 1] == '"')
						||	(parameter[0] == '{') || (parameter[0] == '[')
						||	((parameter - 0) == parameter && parameter.length > 0)	)
					{
						parameterList[i] += parameter;
						jsonPayload = jsonPayload + parameter + ((i < parameters.length - 1) ? ',' : '');
					}
					// Otherwise treat as a string
					else
					{
						parameterList[i] += '"' + parameter + '"';
						jsonPayload = jsonPayload + '"' + parameter + '"' + ((i < parameters.length - 1) ? ',' : '');
					}
				}
				jsonPayload = jsonPayload + ']'; 
				// Output Query
				var query = 'http://' + hostname + ':' + port + '/xbmcCmds/xbmcHttp?command=' + method + '(' + parameters.join(', ') + ')';
				$('<p>Sent:<br /><span class="query">' + query + '</p><hr class="results" />').prependTo("#results");
				// POST to API with JSON encoded query object
				$.ajax({
					type: 'POST',
					url: '',
					data:	{
							'hostname' : hostname,
							'port' : port,
							'service' : service,
							'method' : method,
							'parameters' : jsonPayload,
							},
					success: 
						function(result) 
						{
							// Output result
							$('<p>Received:<br /><span class="' + (result.match(/error/i) ? 'error' : 'result') + '">' + htmlEntities(result) + '</p>').prependTo("#results");
						},
					dataType: 'text'
				});
			} 
			
			// Format xml string for human consumption
			function htmlEntities(str) 
			{
				return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/\n/g, '<br />');
			}
		</script>
		<style type="text/css">
		<!--
			strong {
				font-size:20px;
			}
			hr {
				border:1px solid;
			}
			table {
				border:none;
				border-spacing:0px;
				margin:0px;
				padding:0px;
			}
			td {
				margin:0px;
				padding:0px
			}
			th {
				text-align:right;
				vertical-align:top;
				padding-right:5px;
			}
			.query {
				color:#00f;
			}
			.result {
				color:#0c8;
			}
			.error {
				color:#f00;
			}
			hr.results {
				border:1px dashed;
			}
			.service {
				height:32px;
				vertical-align: top;
			}
			.method {
				height:28px;
				vertical-align: top;
			}
			.hostname {
				width:300px;
				height:29px;
				line-height:30px;
				border: #ccc 1px solid;
				background: #eee;
			}
			.port {
				width:230px;
				height:29px;
				line-height:30px;
				border: #ccc 1px solid;
				background: #eee;
			}
			.parameter {
				width:300px;
				height:29px;
				line-height:30px;
				border: #ccc 1px solid;
				background: #eee;
			}
			#prototype {
				vertical-align:middle;
			}
			#descriptions {
				vertical-align:top;
				line-height:30px;
			}
		-->
		</style>
	</head>
	<body>
		<h2>API Call:</h2>
		<form name="ui" id="ui">
			<table>
				<tr>
					<th width="100">Hostname: </th>
					<td width="500"><input name="hostname" class="hostname" id="hostname" value="boxeebox" /></td>
					<td rowspan="4" id="prototype"></td>
				</tr>
				<tr>
					<th>Port: </th>
					<td><input name="port" class="port" id="port" value="8800" /><button type="button" id="discover">Discover</button></td>
				</tr>
				<tr class="service">
					<th width="100">Service: </th>
					<td width="500"><select name="service" class="parameter" id="service"></select></td>
				</tr>
				<tr class="method">
					<th>Method: </th>
					<td><select name="method" class="parameter" id="method"></select></td>
				</tr>
				<tr class="parameters">
					<th>Parameters: </th>
					<td><div id="parameters"></div></td>
					<td id="descriptions"></td>
				</tr>
				<tr class="execute">
					<th>&nbsp;</th>
					<td><input type="button" name="execute" id="execute" value="Execute" /><input type="button" name="reset" id="clearQuery" value="Reset" /></td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</form>
		<hr />
		<h2>Results:</h2>
		<input type="button" name="clear" id="clearResults" value="Clear" />
		<div id="results"></div>
	</body>
</html>