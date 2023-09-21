<?php

/**
 * @file
 * Привязка событий к расписанию
 */


/**
 * Добавление события в расписание
 *
 * @param $event        object   Событие
 * @param $performerId  integer  ID Исполнителя
 *
 * @return boolean
 */
function addEventIntoSchedule ( $event, $performerId ) {

    global $API;
    global $resultSchedule;
    global $performersDetail;
    global $public_customCommandDirPath;


    /**
     * Игнорирование записей, у сотрудников, которые не выводятся в расписании
     */
    if ( !$performersDetail[ $performerId ] ) return false;


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
    if ( $eventStartStep < $eventEndStep ) $eventEndStep--;

    /**
     * Описание события.
     * Выводится в ячейке события в Расписании
     */
    $eventDescription = [ $event[ "start_at" ] . "-" . $event[ "end_at" ] ];

    /**
     * Детальная информация о событии.
     * Выводится при наведении на событие в админке
     */
    $eventDetails = [];

    if ( !$event[ "icons" ] ) $event[ "icons" ] = [];


    /**
     * @hook
     * Заполнение детальной информации о событии
     */
    if ( file_exists( $public_customCommandDirPath . "/hooks/event-details.php" ) )
        require( $public_customCommandDirPath . "/hooks/event-details.php" );


    /**
     * Заполнение информации об Исполнителе
     */
    $resultSchedule[ $eventDate ][ $performerId ][ "performer_title" ] = $performersDetail[ $performerId ];


    /**
     * Добавление события в расписание
     */
    $resultSchedule[ $eventDate ][ $performerId ][ "schedule" ][ $eventStartStep ] = [
        "steps" => [ $eventStartStep, $eventEndStep ],
        "status" => "busy",
        "event" => [
            "id" => $event[ "id" ],
            "start_at" => $event[ "start_at" ],
            "end_at" => $event[ "end_at" ],
            "description" => $eventDescription,
            "color" => $event[ "color" ],
            "details" => $eventDetails,
            "icons" => $event[ "icons" ]
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
        case "string":

            /**
             * Добавление события в расписание
             */
            addEventIntoSchedule( $event, $event[ $requestData->performers_article ] );

            break;

        case "array":

            /**
             * Добавление события в расписание
             */
            addEventIntoSchedule( $event, $event[ $requestData->performers_article ][ "value" ] );

            break;

    } // switch. gettype( $event[ $requestData->joined_row_article ] )

} // foreach. $response[ "data" ]