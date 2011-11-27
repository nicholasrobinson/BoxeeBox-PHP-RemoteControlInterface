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
	* @param  $command		 Command for stringification
	* @param  $parameters	 Array of parameters
	*
	* @return string
	*/
	protected function stringify($command, $parameters = array())
	{
		return $command . '(' . implode(',', $parameters) . ')';
	}
	
	/** 
	* Commands
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
	* @param	percentage		percentage to set
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
	* Toggles the sound on/off.
	*
	* @return string
	*/
	public function Mute()
	{
		return self::stringify(__FUNCTION__);
	}
	
	/** 
	* Sets the playing position of the currently playing media as a percentage of the media’s length.
	*
	* @param	percentage		percentage to seek
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
	* @param	relativePercentage		relative percentage to seek
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
	
/*
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
	
}

?>