<?php

/**
 * @file Стандартная команда schedule.
 * Используется для вывода расписания
 */


/**
 * Отработка стандартного get запроса
 */
require( "get.php" );


/**
 * Сформированное расписание
 */
$resultSchedule = [];

/**
 * Начало рабочего дня
 */
$workdayStart = strtotime( "00:00" );

/**
 * Конец рабочего дня
 */
$workdayEnd = strtotime( "23:59" );

/**
 * Список временных отрезков
 */
$stepsList = [];

/**
 * Время начала/завершения событий
 * Используется, для нестандартных временных отрезков
 */
$eventTimes = [];

/**
 * Текущий день
 * Используется, для формирования списка дат в графике посещений
 */
$currentDay = strtotime( $requestData->start_at );

/**
 * Текущий временной отрезок
 * Используется, для формирования списка временных отрезков графика посещений
 */
$currentStep = $workdayStart;

/**
 * Шаг (кол-во минут) по умолчанию
 */
if ( !$requestData->step ) $requestData->step = 60;

/**
 * Связанные записи
 * https://tppr.me/H4RJA
 */
$joinedRowsDetail = [];

/**
 * События, привязанные к связанным записям
 */
$joinedRowsEvents = [];


/**
 * Получение детальной информации о связанных записях
 */

$joinedRows = $API->DB->from( $requestData->joined_table );

foreach ( $joinedRows as $joinedRow )
    $joinedRowsDetail[ $joinedRow[ "id" ] ] = $joinedRow[ $requestData->joined_row_title ];


/**
 * Получение времени начала и окончания событий
 */

foreach ( $response[ "data" ] as $event ) {

    $eventTimes[] = date(
        "H:i",
        strtotime( $event[ "start_at" ] )
    );
    $eventTimes[] = date(
        "H:i",
        strtotime( $event[ "end_at" ] )
    );

} // foreach. $response[ "data" ]

/**
 * Очистка дублей
 */
$eventTimes = array_unique( $eventTimes );

/**
 * Сортировка временных отрезков
 */
sort( $eventTimes );


/**
 * Формирование списка временных отрезков
 */

while ( $currentStep <= $workdayEnd ) {

    /**
     * Время текущего отрезка
     */
    $currentStepTime = date( "H:i", $currentStep );


    /**
     * Пополнение нестандартных временных отрезков
     */
    foreach ( $eventTimes as $eventTimeKey => $eventTime ) {

        if ( $eventTime >= $currentStepTime ) continue;
        if ( $stepsList && ( $stepsList[ count( $stepsList ) - 1 ] > $eventTime ) ) continue;

        $stepsList[] = $eventTime;

    } // foreach. $eventTimes


    /**
     * Пополнение списка временных отрезков
     */
    $stepsList[] = $currentStepTime;

    /**
     * Обновление текущего временного отрезка
     */
    $currentStep = strtotime( "+" . $requestData->step . " minutes", $currentStep );

} // while. $currentStep <= $workdayEnd

/**
 * Очистка дублей
 */
$stepsList = array_values(
    array_unique( $stepsList )
);


/**
 * Привязка событий к связанным записям
 */

foreach ( $response[ "data" ] as $event ) {

    /**
     * Получение цвета события
     */
    if ( !$event[ "status" ][ "color" ] ) $event[ "color" ] = "light";


    switch ( gettype( $event[ $requestData->joined_row_article ] ) ) {

        case "integer":
            $joinedRowsEvents[ $event[ $requestData->joined_row_article ] ][] = [
                "id" => $event[ "id" ],
                "start_at" => $event[ "start_at" ],
                "end_at" => $event[ "end_at" ],
                "color" => $event[ "color" ]
            ];
            break;

    } // switch. gettype( $event[ $requestData->joined_row_article ] )

} // foreach. $return[ "rows" ] as $visit

$API->returnResponse( $joinedRowsEvents );


$response[ "data" ] = [
    "steps_list" => $stepsList,
    "schedule" => $resultSchedule
];