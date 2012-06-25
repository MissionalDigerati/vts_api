Install the Video Translation Service API
-----------------------------------------
1.  Get a copy of the code: `git clone https://github.com/MissionalDigerati/video_translator_service.git`
2.  Upload the code your your web server
3.  Copy *Config/core.php.default* to *Config/core.php*, and change settings to fit your environment. Some things to change:
	* Change *debug* to 0 to hide errors
	* Change *Security.salt* and *Security.cipherSeed* to create stronger passwords
	* Change the path at the end of file to match where your PEAR library is installed.  Login to your webhosting via command line, and type `which pear` to find this path.
4.  Setup a database for the app according to your hosting provider's instruction
5.  Copy *Config/database.php.default* to *Config/database.php*, and change settings to match your database settings
