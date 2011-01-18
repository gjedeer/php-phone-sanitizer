<?php

class PhoneNumberSanitizerException extends Exception {};
class PhoneNumberSanitizerCountryException extends Exception {};

class PhoneNumberSanitizer
{
	private $strict;
	private $countryToPrefix = NULL;
	private $prefixToCountry = NULL;

	function __construct($strict = false)
	{
		$this->strict = $strict;
	}

	function LoadCountryToPrefixTable()
	{
		if($this->countryToPrefix)
		{
			return;
		}

		$this->countryToPrefix = unserialize(file_get_contents('country_to_prefix.serialized'));
	}

	function LoadPrefixToCountryTable()
	{
		if($this->prefixToCountry)
		{
			return;
		}

		$this->prefixToCountry = unserialize(file_get_contents('prefix_to_country.serialized'));
	}

	/* Return array of prefixes for a given country code, or NULL if country code unknown */
	function GetPrefixes($countrycode)
	{
		$this->LoadCountryToPrefixTable();
		if(isset($this->countryToPrefix[$countrycode]))
		{
			return $this->countryToPrefix[$countrycode];
		}

        throw new PhoneNumberSanitizerCountryException('Wrong country code: ' . $countrycode);
	}

	function Sanitize($countrycode, $number)
	{
		$prefixes = $this->GetPrefixes($countrycode);

		$number = trim($number);

		if(sizeof($prefixes) == 1)
		{
			$prefix = $prefixes[0];
			/* case 1: no country prefix, 516661666 or (91)4640028 or 914640028 */
			if($number[0] != '+')
			{

			}
		}
		else
		{
		}
		

		return '666!';
	}
}

?>
