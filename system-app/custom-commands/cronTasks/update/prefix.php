<?php

const HOURS_MINUTES = 60;
const DAY_HOURS = 24;
const MONTH_DAYS = 31;

function round_down( float $num, int $max ): int
{
    return (int) max(
        round(
            min(
                $num,
                $max
            ),
            2,
            PHP_ROUND_HALF_DOWN
        ),
        0
    );
}

global $taskDetails;

$taskDetails = $API->DB->from( "cronTasks" )
    ->where( "id", $requestData->id )
    ->fetch();

$taskDetails[ "month" ] = round_down( ( ( $taskDetails[ "period" ] / HOURS_MINUTES ) / DAY_HOURS ) / MONTH_DAYS, 12 );
$taskDetails[ "period" ] -= $taskDetails[ "month" ] * MONTH_DAYS * DAY_HOURS * HOURS_MINUTES;

$taskDetails[ "days" ] = round_down( ( $taskDetails[ "period" ] / HOURS_MINUTES ) / DAY_HOURS, 31 );;

$taskDetails[ "period" ] -= $taskDetails[ "days" ] * DAY_HOURS * HOURS_MINUTES;
$taskDetails[ "hours" ] = round_down( ( $taskDetails[ "period" ] ) / HOURS_MINUTES, 23 );

$taskDetails[ "period" ] -= $taskDetails[ "hours" ] * HOURS_MINUTES;
$taskDetails[ "minutes" ] = min( $taskDetails[ "period" ], 59 );


if ( $requestData->run_configuration ?? $taskDetails[ "run_configuration" ] == "period" ) {

    $requestData->period = (
        ( $requestData->minutes ?? $taskDetails[ "minutes" ] ) +
        ( $requestData->hours ?? $taskDetails[ "hours" ] ) * HOURS_MINUTES +
        ( $requestData->days ?? $taskDetails[ "days" ] ) * DAY_HOURS  * HOURS_MINUTES +
        ( $requestData->month ?? $taskDetails[ "month" ] ) * MONTH_DAYS * DAY_HOURS * HOURS_MINUTES
    );

}