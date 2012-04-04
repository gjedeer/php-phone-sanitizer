<?php
# License:
# wget -O - https://raw.github.com/avsm/openbsd-xen-sys/master/sys/timetc.h | head -n 8 |  sed 's/Poul-Henning Kamp/GDR!/g' | sed 's/phk@FreeBSD.ORG/gdr@go2.pl/g'

abstract class AbstractPhoneNumberSanitizerException extends Exception {};
class PhoneNumberSanitizerException extends AbstractPhoneNumberSanitizerException {};
class PhoneNumberSanitizerCountryException extends AbstractPhoneNumberSanitizerException {};


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

	function StripKnownNonAlpha($number)
	{
		$known = array(' ', '-', '(', ')', '.');
		$replace = array_fill(0, sizeof($known), '');
		return str_replace($known, $replace, $number);
		
	}

	function CountFirstZeros($number)
	{
		$nz = 0;
		for($i = 0; $i < strlen($number); $i++)
		{
			if($number[$i] == '0')
			{
				$nz++;
			}
			else
			{
				break;
			}
		}

		return $nz;
	}


	function StripPhonePrefix($countrycode, $number){
		
		$local_number=$this->Sanitize($countrycode, $number);
		$prefix = $this->GetPrefixes($countrycode);
		try{
			$strip_phone_prefix=substr($local_number, (strlen($prefix)+1));
		}
		catch (AbstractPhoneNumberSanitizerException $e){
			if($this->strict)
			{
				throw $e;
			}
			else
			{
				return $number;
			}
				
		}
		return $strip_phone_prefix;
		
	}


	function Sanitize($countrycode, $number)
	{
		$prefixes = $this->GetPrefixes($countrycode);

		$number = trim($number);
		$original_number = $number;

		if(sizeof($prefixes) == 1)
		{
			$prefix = $prefixes;
			/* case 1: no country prefix */
			if($number[0] != '+')
			{    
				/* Strip leading zeros */
				$zeros = $this->CountFirstZeros($number);
				if($zeros > 0 && $zeros < 4)
				{
					$number = substr($number, $zeros);
				}

				/* 48516661666 */
				if(!strncmp($prefix, $number, strlen($prefix)))
				{
					return '+' . $this->StripKnownNonAlpha($number);
				}
				/* 516661666 or (91)4640028 or 914640028 */
				else
				{
					return '+' . $prefix .  $this->StripKnownNonAlpha($number);
				}
			}
			else
			{
				$striped_number= $this->StripKnownNonAlpha($number);
				if(!strncmp("+".$prefix, $striped_number, (strlen($prefix)+1))){
					return $striped_number;
					
				}
				else{
					if($this->strict)
						{
							throw new PhoneNumberSanitizerException('Could not sanitize ' . $original_number);
						}
					else
						{
							return $striped_number;
						}
				}
			}
		}
		else
		{
			if($number[0] == '+')
			{
				return $this->StripKnownNonAlpha($number);
			}
			else
			{
				$matched_pfx = false;
				foreach($prefixes as $prefix)
				{
					if(!strncmp($prefix, $number, strlen($prefix)))
					{
						$matched_pfx = $prefix;
					}
				}
				if($matched_pfx)
				{
					return '+' . $this->StripKnownNonAlpha($number);
				}
				// pass further
			}
		}
		
		if($this->strict)
		{
			throw new PhoneNumberSanitizerException('Could not sanitize ' . $original_number);
		}
		else
		{
			return $original_number;
		}
	}
}

?>
