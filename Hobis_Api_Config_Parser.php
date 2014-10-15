<?php

class Hobis_Api_Config_Parser
{
	/**
	 * Container for config uri
	 * 
	 * @var string
	 */	
	protected $configUri;
	
	/**
	 * Container for settings
	 * 
	 * @var array
	 */
	protected $settings;
	
	/**
	 * Setter for config uri
	 * 
	 * @param string
	 */
	public function setConfigUri($configUri)
	{
		$this->configUri = $configUri;
	}
	
	/**
	 * Getter for individual setting
	 * 
	 * @param string
	 * @return mixed
	 */
	public function getSetting($settingKey)
	{
		return (true === isset($this->getSettings()[$settingKey])) ? $this->getSettings()[$settingKey] : null;
	}
	
	/**
	 * Getter for settings
	 * 
	 * @return array
	 * @throws Exception
	 */
	public function getSettings()
	{
		// We are assuming a 1:1 relationship with each object instantiated and corresponding config file
		//	This saves us on having to stat the same file over and over again in case multiple calls to this method are made
		if (true === isset($this->settings)) {
			return $this->settings;
		}
		
		// Need a valid config uri
		if ((false === isset($this->configUri)) || (mb_strlen($this->configUri) < 1)) {
			throw new Exception(sprintf('Invalid configUri: %s', serialize($this->configUri)));
		}
		
		$config = new SplFileObject($this->configUri);
		
		if (false === $config->isReadable()) {
			throw new Exception(sprintf('Config is unreadable: %s', $this->configUri));
		}
		
		while (false === $config->eof()) {
			
			$currentLine = rtrim($config->fgets());
			
			// Rather than add unnecessary logic for lines we don't care about, lets just focus on lines we do care about, which are
			//	indicated with an =
			if (false !== stripos($currentLine, '=')) {
				
				list($key, $value) = explode('=', $currentLine, 2);
				
				// We need both key and value for pair to be valid
				if ((mb_strlen($key) < 1) || (mb_strlen($value) < 1)) {
					continue;
				}
				
				$settings[trim($key)] = $this->sanitizeValue($value);
			}
		}
		
		$this->settings = $settings;
		
		return $this->settings;
	}
	
	/**
	 * Wrapper method for sanitizing value
	 * 
	 * @param mixed
	 * @return mixed
	 * @throws Exception
	 */
	protected function sanitizeValue($value)
	{
		// Validate
		if (mb_strlen($value) < 1) {
			throw new Exception(sprintf('Invalid $value: %s', serialize($value)));
		}
		
		$value = trim($value);
		
		if (true === is_numeric($value)) {
			
			if (false !== stripos($value, '.')) {
				return (float) $value;
			}
			
			return (int) $value;
		}
		
		switch (strtolower($value)) {
			
			case 'on':				
			case 'true':
			case 'yes':
				return true;			
			case 'false':
			case 'no':
			case 'off':
				return false;
		}
		
		return $value;
	}
}
