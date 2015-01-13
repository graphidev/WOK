<?php
	
	/**
	 * Get a formated destination time from a local one
	 * @param 	mixed		$timezone		Destination timezone (string or DateTimeZone object)
	 * @param	string		$format			Destination date format (see native date format parameter)
	 * @param	mixed		$datetime		Reference local date time (timestamp, ; current date time by default)
	 * @return 	string		Formated destination date 
	 * @see http://php.net/manual/fr/timezones.php
	 * @see http://php.net/manual/en/function.date.php
	 * @see http://php.net/manual/en/datetime.formats.php
	 * @use datezone('Europe/Paris', 'H:i:s', time());
	 * @package Libraries
	**/
	function datezone($timezone, $format, $datetime = null) {
		
		// Timezone formating
		if(is_string($timezone))
			$timezone = new DateTimeZone($timezone);
		
		// Datetime formating
		if(!empty($datetime) && is_numeric($datetime) && ctype_digit($datetime))
			$datetime = date('r', $datetime);
		
		// Undefined datetime : set now
		elseif(!$datetime)
			$datetime = 'now';
		
		// Set context to current timezone
		$localtmz = new DateTimeZone(date_default_timezone_get());
		$datetime = new DateTime($datetime, $localtmz);
		
		// Move context to destination timezone
		$datetime->setTimezone($timezone);
		return $datetime->format($format);
			
	}