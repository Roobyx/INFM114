<?php
	use Spatie\CalendarLinks\Link;

	$from = DateTime::createFromFormat('Y-m-d H:i', '2018-02-01 09:00');
	$to = DateTime::createFromFormat('Y-m-d H:i', '2018-02-01 18:00');
	
	$link = Link::create('Php\'s test', $from, $to)
		->description('Manager test event!')
		->address('Online');
	
	// Generate a link to create an event on Google calendar
	echo $link->google();

	echo "<br>--------------------------------<br>";
	
	// Generate a link to create an event on Yahoo calendar
	echo $link->yahoo();

	echo "<br>--------------------------------<br>";

	
	// Generate a link to create an event on outlook.com calendar
	echo $link->webOutlook();

	echo "<br>--------------------------------<br>";

	
	// Generate a data uri for an ics file (for iCal & Outlook)
	echo $link->ics();

	echo "<br>--------------------------------<br>";

	
	// Generate a data uri using arbitrary generator:
	// echo $link->formatWith(new \Your\Generator());
?>