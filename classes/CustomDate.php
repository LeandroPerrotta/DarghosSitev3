<?php
define("DATE_PATTERN_DEFAULT", "d/m/y - H:i");

class CustomDate
{
	private $_timestamp;
	
	public $_month = 0, $_year = 0, $_day = 0, $_min= 0, $_hour = 0, $_sec = 0;
	
	function __construct($timestamp = null)
	{
		$timestamp = ($timestamp == null) ? time() : $timestamp;
		$this->_timestamp = $timestamp;
	}
	
	//date
	function getDay($withzero = false){ return date(($withzero) ? "d" : "j", $this->_timestamp); }
	function getWeekDay(){	return date("w", $this->_timestamp); }
	function getYearDay(){	return date("z", $this->_timestamp); }
	function getMonth($withzero = false){ return date(($withzero) ? "m" : "n", $this->_timestamp); }
	function getYear($two_digits = false){ return date(($two_digits) ? "y" : "Y", $this->_timestamp); }
	
	//time
	function getHour24($withzero = false){ return date(($withzero) ? "H" : "G", $this->_timestamp); }
	function getHour12($withzero = false){ return date(($withzero) ? "h A" : "g A", $this->_timestamp); }
	function getHour($_12hFormat = false){ return (($_12hFormat) ? $this->getHour12() : $this->getHour24()); }
	function getMins(){ return date("i", $this->_timestamp); }
	function getSecs(){ return date("s", $this->_timestamp); }
	
	function getFormated($pattern = DATE_PATTERN_DEFAULT){ return date($pattern, $this->_timestamp); }

	function makeDate()
	{
		$this->_timestamp = mktime(
			$this->_hour || 0,
			$this->_min || 0,
			$this->_sec || 0,
			$this->_month || 0,
			$this->_day || 0,
			$this->_year || 0
		);
		
		return $this->_timestamp;
	}
}