Turn bloody mess entered by users during registration into a standard phone number representation.

Example:
* "(231) 8180078" becomes "+12318180078"
* "516661666" becomes "+48516661666"

Usage:

	$sanitizer = new PhoneNumberSanitizer(false);
	echo $sanitizer->Sanitize('US', '(231) 8180078');

outputs:

	+12318180078

This class loads a list of country prefixes from a serialized file and, when given a country code (like: "US") and a free-form country number on the input, returns a phone number in standard +XXYYYYYYYYY format, where XX is country dialing prefix and YYYYYY is the rest of the number.

Before first use, run convert.php to turn a CSV country table into its serialized representation for faster sanitization.
