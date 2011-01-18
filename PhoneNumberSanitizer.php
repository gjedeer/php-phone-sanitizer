<?php

class PhoneNumberSanitizerException extends Exception {};

class PhoneNumberSanitizer
{
	private $strict;

	function __construct($strict = false)
	{
		$this->strict = $strict;
	}

	function Sanitize($countrycode, $number)
	{
		return '666!';
	}
}

?>
