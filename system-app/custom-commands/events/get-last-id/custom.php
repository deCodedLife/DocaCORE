<?php

/**
 * @file
 * ID последнего события
 */


/**
 * Формирование фильтров
 */

$filters = [];

if ( $requestData->table_name ) $filters[ "table_name" ] = $requestData->table_name;
if ( $requestData->role_id ) $filters[ "role_id" ] = $requestData->role_id;


/**
 * Получение последнего события
 */

$lastEvent = $API->DB->from( "events" )
    ->where( $filters )
    ->orderBy( "last_update_at desc" )
    ->limit( 1 )
    ->fetch();

if ( !$lastEvent ) $API->returnResponse( 0 );


$API->returnResponse( (int) $lastEvent[ "id" ] );