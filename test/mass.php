<?php

require(dirname(__FILE__)."/../conf.php");
require(dirname(__FILE__)."/../Logbook.php");
require(dirname(__FILE__)."/../Logbook/Buffer.php");


$logb = new Logbook_Buffer(LOGBOOK_API_KEY);

for ($i=0; $i<10000; $i++) {
	$logb->add_entry(Logbook::SEVERITY_INFO, "test", array(
		"message" => "$i немного кириллицы",
		"somedata" => "Первая в мире отправка sms-сообщения была произведена в декабре 1992 года, а содержало данное смс сообщение короткое поздравление с наступающим Рождеством. Так появилась технология, которая вскоре изменила мобильную жизнь людей на всей планете.",
	));
	echo "$i: queue: ".$logb->length()."\n";
	usleep(100000);
}
$logb->flush();
