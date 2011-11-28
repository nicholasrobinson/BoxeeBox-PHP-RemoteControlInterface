<?php
/*
 *  File:			javascript.php
 *  Description:	SDK example of rendering all API calls into a static javascript SDK. 
 *					Once rendered the contents of the file can be used without PHP (just copy and paste the source to a new HTML/js document).
 *					Note: Macro functions from the PHP-SDK are ommited from the javascript SDK
 *  Author:			Nicholas Robinson 11/27/2011
 */
 
# Define constants
define('DEBUG', false);
define('PROTOCOL', 'http');
define('API_END_POINT', '/xbmcCmds/xbmcHttp?command=');
define('SERVER_ADDRESS', 'boxeebox');
define('SERVER_PORT', 8800);
 
# Render all BoxeeBox Remote Control Interface functions to javascript
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
			# If method is not blacklisted and not a Macro
			if (!in_array($reflection_method->getName(), $method_blacklist) && strpos($reflection_method->getName(), 'Macro') === false)
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

# Include jQuery
echo '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>' . PHP_EOL;

# Output javascript
echo '		<script type="text/javascript">' . PHP_EOL;

# Create command iframe on load
echo '			// Create command iframe' . PHP_EOL;
echo '			$(document).ready(function(){ $(\'<iframe ' . (DEBUG ? '' : 'style="display: none; height:1px; width:1px;" ') . 'id="command" />\').appendTo(\'body\'); });' . PHP_EOL;
echo PHP_EOL;

# Define server address and port as globals
echo '			// Define default server parameters' . PHP_EOL;
echo '			window.serverAddress = "' . SERVER_ADDRESS . '";' . PHP_EOL;
echo '			window.serverPort = "' . SERVER_PORT . '";' . PHP_EOL;
echo PHP_EOL;

# Define BBRCI function
echo '			/**' . PHP_EOL; 
echo '			* Execute BoxeeBox Remote Control Interface command' . PHP_EOL; 
echo '			* Log and return destination url (access to contents forbidden to javascript by same-origin policy)' . PHP_EOL; 
echo '			* Note: jsonp could overcome this, however this requires a valid json response' . PHP_EOL; 
echo '			*'. PHP_EOL; 
echo '			* @param  command		 Command for execution'  . PHP_EOL; 
echo '			*' . PHP_EOL; 
echo '			* @return null' . PHP_EOL;
echo '			*/' . PHP_EOL;
echo '			function BBRCI(command)' . PHP_EOL;
echo '			{' . PHP_EOL;
echo '				$(\'#command\').attr("src", "' . PROTOCOL . '://" + window.serverAddress + "' . ':" + window.serverPort + "' . API_END_POINT . '" + command);' . PHP_EOL;
echo '				console.log($(\'#command\').attr("src"));' . PHP_EOL;
echo '				return $(\'#command\').attr("src");' . PHP_EOL;
echo '			}' . PHP_EOL;
echo PHP_EOL;

# Iterate over available services
foreach ($services as $service)
{
	# Iterate over available methods
	foreach ($service['methods'] as $methodName => $methodObject)
	{
		# Fom array for parameters/descriptions
		$parameterNames = array();
		$parameterDescriptions = array();
		foreach ($methodObject['parameters'] as $parameterName => $parameterObject)
		{
			$parameterNames[] = $parameterName;
			$parameterDescriptionsParts = explode(' ', $parameterObject['description'], 2);
			$parameterDescriptions[] = $parameterDescriptionsParts[1];
		}
		# Output function header
		echo '			/**' . PHP_EOL; 
		echo '			* Flatten command into BoxeeBox Remote Control Interface command' . PHP_EOL; 
		if (count($parameterNames) > 0)
		{
			echo '			*'. PHP_EOL; 
			for ($i = 0; $i < count($parameterNames) && $i < count($parameterDescriptions); $i++)
			{
				echo '			* @param  ' . $parameterNames[$i] . '		 ' . $parameterDescriptions[$i] . PHP_EOL;
			}
		}
		echo '			*' . PHP_EOL; 
		echo '			* @return string' . PHP_EOL;
		echo '			*/' . PHP_EOL;
		# Output function
		echo '			function ' . $methodName . '(' . implode(', ', $parameterNames) . ')' . PHP_EOL;
		echo '			{' . PHP_EOL;
		echo '				return BBRCI("' . $methodName . (empty($parameterNames) ? '()' : '(" + ' . implode('+ "," + ', $parameterNames) . ' + ")') . '");' . PHP_EOL;
		echo '			}' . PHP_EOL;
		echo PHP_EOL;
	}
}

# End javascript
echo '		</script>' . PHP_EOL;

?>