<?php

const HOURS_MINUTES = 60;
const DAY_HOURS = 24;
const MONTH_DAYS = 31;


if ( $requestData->run_configuration == "period" ) {

    $requestData->period = (
        ( $requestData->minutes ?? 0 ) +
        ( $requestData->hours ?? 0 ) * HOURS_MINUTES +
        ( $requestData->days ?? 0 ) * DAY_HOURS  * HOURS_MINUTES +
        ( $requestData->month ?? 0 ) * MONTH_DAYS * DAY_HOURS * HOURS_MINUTES
    );

}