<?php

require_once('PhoneNumberSanitizer.php');

class SanitizeTest extends PHPUnit_Framework_TestCase
{
	/* Array of test cases:
		* (Country, Number, ExpectedOutput)
		* if ExpectedOutput is NULL, an exception is expected
		*/
	var $cases = array(
		array('PL', '516661505', '+48516661505'),
		array('PL', '48516661505', '+48516661505'),
		array('PL', '+48516661505', '+48516661505'),
		array('PL', '0048516661505', '+48516661505'),
		array('PL', '00048516661505', '+48516661505'),
		/* Dominican Rep has 2 country prefixes and they need to be supplied explicitely */
		array('DO', '+180912345678', '+180912345678'),
		array('DO', '12345678', NULL),
		array('DO', '182912345678', '+182912345678'),
	);
	public function testSanitize()
	{
		$sanitizer = new PhoneNumberSanitizer(false);
		foreach($this->cases as $case)
		{
			$number = $sanitizer->Sanitize($case[0], $case[1]);
			$this->assertEquals($number, $case[2]);
		}
	}

	public function testStrictSanitize()
	{
		$sanitizer = new PhoneNumberSanitizer(true);
		foreach($this->cases as $case)
		{
			if($case[2] === NULL)
			{
				try
				{
					$number = $sanitizer->Sanitize($case[0], $case[1]);
					$this->fail('Exception not raised for ('.$case[0].','.$case[1].'), instead returned ' . $number);
				}
				catch PhoneNumberSanitizerException
				{
				}
			}
			else
			{
				$number = $sanitizer->Sanitize($case[0], $case[1]);
				$this->assertEquals($number, $case[2]);
			}
		}
	}
}
