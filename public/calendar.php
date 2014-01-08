<?php
require_once("../vendor/autoload.php");
require_once("../config.php");

try {
    require("db.php");

    date_default_timezone_set('Europe/Berlin');

    $vCalendar = new \Eluceo\iCal\Component\Calendar(CAL_ID);
    $vCalendar->setName(CAL_NAME);

    $tzDescription = array();
    $ruleDayLight = new \Eluceo\iCal\Property\Event\RecurrenceRule();
    $ruleDayLight->setFreq(\Eluceo\iCal\Property\Event\RecurrenceRule::FREQ_YEARLY);
    $ruleDayLight->setInterval(1);
    $ruleDayLight->setByDay("-1SU");
    $ruleDayLight->setByMonth(3);
    $ruleStandard = new \Eluceo\iCal\Property\Event\RecurrenceRule();
    $ruleStandard->setFreq(\Eluceo\iCal\Property\Event\RecurrenceRule::FREQ_YEARLY);
    $ruleStandard->setInterval(1);
    $ruleStandard->setByDay("-1SU");
    $ruleStandard->setByMonth(10);

    $tzDescription[] = new Eluceo\iCal\Component\TimezoneDescription("STANDARD", "+0200", "+0100", "CET", "19701025T030000", $ruleStandard);
    $tzDescription[] = new Eluceo\iCal\Component\TimezoneDescription("DAYLIGHT", "+0100", "+0200", "CEST", "19700329T020000", $ruleDayLight);

    $vCalendar->setTimezone("Europe/Berlin", $tzDescription );
    $vCalendar->setMethod("PUBLISH");

    foreach( $db->events() as $event )
    {
        $vEvent = new \Eluceo\iCal\Component\Event($event['Id']);

        $vEvent->setDtStamp(new \DateTime($event['Modified']));
        $vEvent->setDtStart(new \DateTime($event['StartDate']));
        $vEvent->setDtEnd(new \DateTime($event['EndDate']));
        $vEvent->setSummary($event['Name']);
        $vEvent->setDescription($event['Description']);
        $vEvent->setLocation($event['Location']);
        $vEvent->setUseTimezone(true);
        $vEvent->setUseUtc(false);

        $vCalendar->addEvent($vEvent);
    }

    // http://stackoverflow.com/questions/49547/making-sure-a-web-page-is-not-cached-across-all-browsers
    header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
    header('Pragma: no-cache'); // HTTP 1.0.
    header('Expires: 0'); // Proxies.

    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename="cal.ics"');

    echo $vCalendar->render();
} catch (Exception $ex) {
    echo"Error: ".$ex->getMessage();
}
