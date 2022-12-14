<?php

/**
 * @file
 * Получение детальной информации об Исполнителях
 */

$performersRows = $API->DB->from( $requestData->performers_table )
    ->where( $performersFilter );

foreach ( $performersRows as $performersRow )
    $performersDetail[ $performersRow[ "id" ] ] = $performersRow[ $requestData->performers_title ];