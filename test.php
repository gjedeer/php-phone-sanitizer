<?php
/* $ phpunit SanitizeTest test.php */

require_once('PhoneNumberSanitizer.php');

class SanitizeTest extends PHPUnit_Framework_TestCase
{
	/* Array of test cases:
		* (Country, Number, ExpectedOutput)
		* if ExpectedOutput is NULL, an exception is expected
		*/
	var $cases = array(
		array('PL', '516661666', '+48516661666'),
		array('PL', '48516661666', '+48516661666'),
		array('PL', '+48516661666', '+48516661666'),
		array('PL', '0048516661666', '+48516661666'),
		array('PL', '00048516661666', '+48516661666'),
		/* Dominican Rep has 2 country prefixes and they need to be supplied explicitely */
		array('DO', '+180912345678', '+180912345678'),
		array('DO', '12345678', NULL),
		array('DO', '182912345678', '+182912345678'),
		array('US', '(231) 8180078', '+12318180078'),
		array('US', '+1-231-844-4053', '+12318444053'),
	);
	public function testSanitize()
	{
		$sanitizer = new PhoneNumberSanitizer(false);
		foreach($this->cases as $case)
		{
			$number = $sanitizer->Sanitize($case[0], $case[1]);
			if($case[2])
			{
				$this->assertEquals($case[2], $number);
			}
			else
			{
				$this->assertEquals($case[1], $number);
			}
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
				catch(PhoneNumberSanitizerException $ex)
				{
				}
			}
			else
			{
				$number = $sanitizer->Sanitize($case[0], $case[1]);
				$this->assertEquals($case[2], $number);
			}
		}
	}

	/**
	 * @expectedException PhoneNumberSanitizerCountryException
	 */
	function testInvalidCountry()
	{
		$sanitizer = new PhoneNumberSanitizer(true);
		$sanitizer->Sanitize('SXXXX', '3456788765');
	}

	function testCountZeros()
	{
		$sanitizer = new PhoneNumberSanitizer(true);
		$this->assertEquals(0, $sanitizer->CountFirstZeros('3wfsf'));
		$this->assertEquals(1, $sanitizer->CountFirstZeros('0532423'));
		$this->assertEquals(2, $sanitizer->CountFirstZeros('0032re23r'));
		$this->assertEquals(3, $sanitizer->CountFirstZeros('000fuisng'));
	}
}
