<?php
require_once "vendor/autoload.php";

$vCalendar = new \Eluceo\iCal\Component\Calendar('www.example.com');

$vEvent = new \Eluceo\iCal\Component\Event("122");

$vEvent->setDtStart(new \DateTime('2013-12-24 19:00'));
$vEvent->setDtEnd(new \DateTime('2013-12-24 22:00'));
$vEvent->setSummary('Christmas');

$vCalendar->addEvent($vEvent);

$vEvent = new \Eluceo\iCal\Component\Event("123");

$vEvent->setDtStart(new \DateTime('2013-12-23 10:00'));
$vEvent->setDtEnd(new \DateTime('2013-12-23 22:00'));
$vEvent->setSummary('Christmas2');

$vCalendar->addEvent($vEvent);

header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="cal.ics"');

echo $vCalendar->render();
?>