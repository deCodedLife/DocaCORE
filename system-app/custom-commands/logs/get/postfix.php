<?php

/**
 * Сформированный список логов
 */
$resultLogs = [];

/**
 * Список названий таблиц
 */
$tableTitles = [];


foreach ( $response[ "data" ] as $log ) {

    /**
     * Получение названия таблицы
     */

    $tableTitle = $tableTitles[ $log[ "table_name" ] ];
    if ( !$tableTitle ) $tableTitle = $API->loadObjectScheme( $log[ "table_name" ] )[ "title" ];

    $tableTitles[ $log[ "table_name" ] ] = $tableTitle;
    $log[ "table_name" ] = $tableTitle;


    $resultLogs[] = $log;

} // foreach. $response[ "data" ]


$response[ "data" ] = $resultLogs;