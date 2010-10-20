<?php
require(dirname(__FILE__)."/../conf.php");
require(dirname(__FILE__)."/../Logbook.php");

$log = new Logbook(LOGBOOK_API_KEY);

foreach(Logbook::$SEVERITY_TYPES as $severity) {
	$log->send($severity, "test", array(
		"message" => "severity '$severity'"
	));
	echo "$severity added..\n";
}
