<?php
/*
 *	File:			RemoteControlInterface.class.php
 *	Description:	RemoteControlInterface Services class
 *	Author:			Nicholas Robinson 11/26/2011
 */

/** 
* RemoteControlInterface Services class
*/
class RemoteControlInterface
{

/** 
* Auxiliary methods
*/
	
	/** 
	* Flatten command into BoxeeBox Remote Control Interface command
	*
	* @param  command		  Command for stringification
	* @param  parameters	  Array of parameters
	*
	* @return string
	*/
	protected function stringify($command, $parameters = array())
	{
		return $command . '(' . implode(',', $parameters) . ')';
	}
	
/** 
* XBMC Commands
*/
	
	/** 
	* Retrieves the current volume setting as a percentage of the maximum possible value.
	*
	* @return string
	*/
	public function GetVolume()
	{
		return self::stringify(__FUNCTION__);
	}
	
	/** 
	* Sets the volume as a percentage of the maximum possible.
	*
	* @param	percentage		 percentage to set
	*
	* @return string
	*/
	public function SetVolume($percent)
	{
		return self::stringify(__FUNCTION__, array($percent));
	}
	
	/** 
	* Mute Toggles the sound on/off.
	*
	* @return string
	*/
	public function Mute()
	{
		return self::stringify(__FUNCTION__);
	}
	
	/** 
	* Pause Pauses the currently playing media.
	*
	* @return string
	*/
	public function Pause()
	{
		return self::stringify(__FUNCTION__);
	}
	
	/** 
	* Stops the currently playing media.
	*
	* @return string
	*/
	public function Stop()
	{
		return self::stringify(__FUNCTION__);
	}
	
	/** 
	* Starts playing/showing the next media/image in the current playlist or, if currently showing a slidshow, 
	* the slideshow playlist.
	*
	* @return string
	*/
	public function PlayNext()
	{
		return self::stringify(__FUNCTION__);
	}
	
	/** 
	* Starts playing/showing the previous media/image in the current playlist or, if currently showing a slidshow, 
	* the slideshow playlist.
	*
	* @return string
	*/
	public function PlayPrev()
	{
		return self::stringify(__FUNCTION__);
	}
	
	/** 
	* Sets the playing position of the currently playing media as a percentage of the media’s length.
	*
	* @param	percentage			 percentage to seek
	*
	* @return string
	*/
	public function SeekPercentage($percentage)
	{
		return self::stringify(__FUNCTION__, array($percentage));
	}
	
	/** 
	* Adds/Subtracts the current percentage on to the current postion in the song
	*
	* @param	relativePercentage	 relative percentage to seek
	*
	* @return string
	*/
	public function SeekPercentageRelative($relativePercentage)
	{
		return self::stringify(__FUNCTION__, array($relativePercentage));
	}
	
	/** 
	* Retrieves the current playing position of the currently playing media as a percentage of the media’s length.
	*
	* @return string
	*/
	public function GetPercentage()
	{
		return self::stringify(__FUNCTION__);
	}
	
	/** 
	* Send specified key
	*
	* @param	key		 key to send
	*
	* @return string
	*/
	public function SendKey($key)
	{
		return self::stringify(__FUNCTION__, array($key));
	}
	
	/** 
	* Send specified unicode character
	*
	* @param	key		 key to send
	*
	* @return string
	*/
	public function SendUnicodeChar($char)
	{
		return self::stringify(__FUNCTION__, array($char));
	}
	
	
/** 
* BoxeeBox Commands
*/
	
	/** 
	* Returns whether a virtual keyboard is active, whether it has hidden text and the actual text in the keyboard.
	*
	* @return string
	*/
	public function getKeyboardText()
	{
		return self::stringify(__FUNCTION__);
	}
	
/** 
* Convenience wrapper macro commands
*/
	
	/** 
	* Sends an ASCII key (used in keyboard)
	*
	* @param	key		 key to send
	*
	* @return string
	*/
	public function SendASCIIKeyMacro($key)
	{
		return self::SendKey(ord($key) + 61696);
	}
	
	/** 
	* Click on UP button
	*
	* @return string
	*/
	public function SendUpKeyMacro()
	{
		return self::SendKey(270);
	}
	
	/** 
	* Click on DOWN button
	*
	* @return string
	*/
	public function SendDownKeyMacro()
	{
		return self::SendKey(271);
	}
	
	/** 
	* Click on LEFT button
	*
	* @return string
	*/
	public function SendLeftKeyMacro()
	{
		return self::SendKey(272);
	}
	
	/** 
	* Click on RIGHT button
	*
	* @return string
	*/
	public function SendRightKeyMacro()
	{
		return self::SendKey(273);
	}
	
	/** 
	* Click on BACK button
	*
	* @return string
	*/
	public function SendBackKeyMacro()
	{
		return self::SendKey(275);
	}
	
	/** 
	* Sends an backspace key (used in keyboard)
	*
	* @return string
	*/
	public function SendBackspaceKeyMacro()
	{
		return self::SendKey(61704);
	}
	
	/** 
	* Hide Cursor
	*
	* @return string
	*/
	public function SendHideCursorMacro()
	{
		return self::SendKey(274);
	}
	
	/** 
	* Show shutdown menu
	*
	* @return string
	*/
	public function SendPowerButtonMacro()
	{
		return self::SendKey(277);
	}
	
	/** 
	* Send OK button
	*
	* @return string
	*/
	public function SendOKButtonMacro()
	{
		return self::SendKey(256);
	}
	
}

?>