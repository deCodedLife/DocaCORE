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


foreach ( $response[ "data" ] as $key => $row ) {

    $row[ "month" ] = round_down( ( ( $row[ "period" ] / HOURS_MINUTES ) / DAY_HOURS ) / MONTH_DAYS, 12 );
    $row[ "period" ] -= $row[ "month" ] * MONTH_DAYS * DAY_HOURS * HOURS_MINUTES;

    $row[ "days" ] = round_down( ( $row[ "period" ] / HOURS_MINUTES ) / DAY_HOURS, 31 );;

    $row[ "period" ] -= $row[ "days" ] * DAY_HOURS * HOURS_MINUTES;
    $row[ "hours" ] = round_down( ( $row[ "period" ] ) / HOURS_MINUTES, 23 );

    $row[ "period" ] -= $row[ "hours" ] * HOURS_MINUTES;
    $row[ "minutes" ] = min( $row[ "period" ], 59 );

    $response[ "data" ][ $key ] = $row;

}