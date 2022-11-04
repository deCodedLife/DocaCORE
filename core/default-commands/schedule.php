<?php

/**
 * @file Стандартная команда schedule.
 * Используется для вывода расписания
 */


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
 * Кол-во минут в шаге
 */
$minutesPerStep = 60;
if ( $requestData->step ) $minutesPerStep = $requestData->step;

/**
 * Текущий временной отрезок
 * Используется, для формирования списка временных отрезков графика посещений
 */
$currentStep = $workdayStart;

/**
 * Детальная информация об Исполнителях
 */
$performersDetail = [];

/**
 * Дата начала и окончания графика по умолчанию
 */
if ( !$requestData->start_at ) $requestData->start_at = date( "Y-m-d" );
if ( !$requestData->end_at ) $requestData->end_at = date(
    "Y-m-d", strtotime( "+30 days", strtotime( $requestData->start_at ) )
);

/**
 * Принудительная сортировка
 */
$requestData->sort_by = "start_at";


/**
 * @hook
 * Объявление переменных
 */
if ( file_exists( $public_customCommandDirPath . "/hooks/after-variables-loading.php" ) )
    require( $public_customCommandDirPath . "/hooks/after-variables-loading.php" );


/**
 * Определение ключа временного отрезка
 *
 * @param $time  string  Время
 *
 * @return integer
 */
function getStepKey ( $time ) {

    global $stepsList;

    /**
     * Ключ указанного временного отрезка
     */
    $stepKeyResult = null;

    foreach ( $stepsList as $stepKey => $step ) {

        if ( $step === $time ) $stepKeyResult = $stepKey;

    } // foreach. $stepsList


    return $stepKeyResult;

} // function. getStepKey


/**
 * Отработка стандартного get запроса
 */
require( "get.php" );


/**
 * @hook
 * Отработка стандартного get запроса
 */
if ( file_exists( $public_customCommandDirPath . "/hooks/after-get-command.php" ) )
    require( $public_customCommandDirPath . "/hooks/after-get-command.php" );


/**
 * Получение детальной информации об Исполнителях
 */
require( "components/schedule/get-performers-detail.php" );

/**
 * Получение времени начала и окончания событий
 */
require( "components/schedule/get-event-times.php" );

/**
 * Формирование списка временных отрезков
 */
require( "components/schedule/get-steps.php" );

/**
 * Привязка дат к расписанию
 */
require( "components/schedule/dates-to-schedule.php" );

/**
 * Привязка событий к расписанию
 */
require( "components/schedule/events-to-schedule.php" );

/**
 * Привязка свободных ячеек к расписанию
 */
require( "components/schedule/empty-to-schedule.php" );


$response[ "data" ] = [
    "steps_list" => $stepsList,
    "schedule" => $resultSchedule
];