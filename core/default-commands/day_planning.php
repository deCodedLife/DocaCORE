<?php

/**
 * @file Стандартная команда day_planning.
 * Используется блоком "Дневное планирование"
 */


/**
 * Проверка наличия таблицы в схеме запроса
 */
if ( !$objectScheme[ "table" ] ) $API->returnResponse( "Отсутствует таблица в схеме запроса", 500 );


/**
 * Тело ответа
 */
$response[ "data" ] = [];

/**
 * Фильтр записей
 */
$requestSettings[ "filter" ] = [
    "start_at >= ?" => $requestData->day . " 00:00:00",
    "start_at <= ?" => $requestData->day . " 23:59:59"
];


/**
 * @hook
 * Фильтрация записей
 */
if ( file_exists( $public_customCommandDirPath . "/hooks/events-filter.php" ) )
    require( $public_customCommandDirPath . "/hooks/events-filter.php" );


/**
 * Получение записей
 */

$events = $API->DB->from( $objectScheme[ "table" ] )
    ->orderBy( "start_at asc" );

if ( $objectScheme[ "is_trash" ] ) $requestSettings[ "filter" ][ "is_active" ] = "Y";;
$events->where( $requestSettings[ "filter" ] );


/**
 * Формирование списка записей
 */

foreach ( $events as $event ) {

    $isContinue = false;


    /**
     * Сформированная запись
     */
    $eventDetails = [
        "id" => $event[ "id" ],
        "body" => "",
        "color" => "primary",
        "links" => []
    ];

    /**
     * Получение времени записи
     */
    $eventDetails[ "time" ] = date( "H:i", strtotime( $event[ $requestData->time_from_property ] ) );
    $eventDetails[ "time" ] .= " - " . date( "H:i", strtotime( $event[ $requestData->time_to_property ] ) );
    

    /**
     * @hook
     * Формирование записи
     */
    if ( file_exists( $public_customCommandDirPath . "/hooks/event-details.php" ) )
        require( $public_customCommandDirPath . "/hooks/event-details.php" );


    if ( $isContinue ) continue;

    $response[ "data" ][] = $eventDetails;

} // foreach. $events


$API->returnResponse( $response[ "data" ] );