<?php
require(dirname(__FILE__)."/../conf.php");
require(dirname(__FILE__)."/../Logbook.php");
require(dirname(__FILE__)."/../Logbook/Buffer.php");

$logb = new Logbook_Buffer(LOGBOOK_API_KEY);

foreach(Logbook::$SEVERITY_TYPES as $severity) {
	$logb->add_entry($severity, "test", array(
		"message" => "severity '$severity'"
	));
	echo "$severity added..\n";
}
$logb->flush();
