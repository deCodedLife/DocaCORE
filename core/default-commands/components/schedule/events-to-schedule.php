<?php

/**
 * @file
 * Привязка событий к расписанию
 */


/**
 * Добавление события в расписание
 *
 * @param $event  object  Событие
 *
 * @return boolean
 */
function addEventIntoSchedule ( $event ) {

    global $requestData;
    global $resultSchedule;
    global $performersDetail;


    /**
     * Получение даты события
     */
    $eventDate = date( "Y-m-d", strtotime( $event[ "start_at" ] ) );

    /**
     * Получение шага начала события
     */
    $eventStartStep = getStepKey(
        date( "H:i", strtotime( $event[ "start_at" ] ) )
    );

    /**
     * Получение шага конца события
     */
    $eventEndStep = getStepKey(
        date( "H:i", strtotime( $event[ "end_at" ] ) )
    );


    /**
     * Заполнение информации об Исполнителе
     */
    $resultSchedule[ $eventDate ][ $event[ $requestData->performers_article ] ][ "performer_id" ] = $event[ $requestData->performers_article ];
    $resultSchedule[ $eventDate ][ $event[ $requestData->performers_article ] ][ "performer_title" ] = $performersDetail[ $event[ $requestData->performers_article ] ];


    /**
     * Добавление события в расписание
     */
    $resultSchedule[ $eventDate ][ $event[ $requestData->performers_article ] ][ "schedule" ][ $eventStartStep ] = [
        "steps" => [ $eventStartStep, $eventEndStep ],
        "status" => "busy",
        "event" => [
            "id" => $event[ "id" ],
            "start_at" => $event[ "start_at" ],
            "end_at" => $event[ "end_at" ],
            "color" => $event[ "color" ]
        ]
    ];


    return true;

} // function. addEventIntoSchedule


/**
 * Обработка событий
 */

foreach ( $response[ "data" ] as $event ) {

    /**
     * Получение цвета события
     */
    if ( !$event[ "status" ][ "color" ] ) $event[ "color" ] = "light";


    /**
     * Разделение обработки на одного или нескольких Исполнителей
     */

    switch ( gettype( $event[ $requestData->performers_article ] ) ) {

        case "integer":

            /**
             * Добавление события в расписание
             */
            addEventIntoSchedule( $event );

            break;

    } // switch. gettype( $event[ $requestData->joined_row_article ] )

} // foreach. $response[ "data" ]