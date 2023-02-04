<?php

/**
 * @file Стандартная команда add.
 * Используется для добавления записей в базу данных
 */


/**
 * Значения для вставки
 */
$insertValues = [];

/**
 * Изображения для вставки
 */
$insertImages = [];

/**
 * Значения связанных таблиц для вставки
 */
$join_insertValues = [];


/**
 * Формирование значений для вставки
 */

foreach ( $objectScheme[ "properties" ] as $schemeProperty ) {

    if ( !$schemeProperty[ "is_autofill" ] ) continue;


    /**
     * Добавление св-ва в запрос
     */

    $propertyName = $schemeProperty[ "article" ];
    $propertyValue = $requestData->{$schemeProperty[ "article" ]};


    /**
     * Обработка связанных таблиц
     */

    if ( $propertyValue ) {

        if ( !$schemeProperty[ "join" ] ) $insertValues[ $propertyName ] = $propertyValue;
        else $join_insertValues[ $propertyName ] = [
            "connection_table" => $schemeProperty[ "join" ][ "connection_table" ],
            "filter_property" => $schemeProperty[ "join" ][ "filter_property" ],
            "insert_property" => $schemeProperty[ "join" ][ "insert_property" ],
            "data" => $propertyValue
        ];

    } // if. $propertyValue


    /**
     * Проверка на уникальность
     */

    if ( $schemeProperty[ "is_unique" ] && $propertyValue ) {

        $repeatedProperty = $API->DB->from( $objectScheme[ "table" ] )
            ->where( $propertyName, $propertyValue )
            ->limit( 1 )
            ->fetch();

        if ( $repeatedProperty && $propertyValue )
            $API->returnResponse( "Запись с таким $propertyName уже существует", 500 );

    } // if. $schemeProperty[ "is_unique" ] && $propertyValue

} // foreach. $objectScheme[ "properties" ] as $schemeProperty


try {

    $insertId = $API->DB->insertInto( $objectScheme[ "table" ] )
        ->values( $insertValues )
        ->execute();

    if ( !$insertId ) $API->returnResponse( "Ошибка добавления записи", 500 );


    /**
     * Связанные таблицы
     */
    foreach ( $join_insertValues as $donor_table => $join ) {

        foreach ( $join[ "data" ] as $connection_table_value ) {

            $API->DB->insertInto( $join[ "connection_table" ] )
                ->values( [
                    $join[ "insert_property" ] => $insertId,
                    $join[ "filter_property" ] => $connection_table_value
                ] )
                ->execute();

        } // foreach. $join[ "data" ]

    } // foreach. $join_insertValues


    /**
     * Вывод ID созданной записи
     */
    $response[ "data" ] = $insertId;


    /**
     * Добавление лога
     */
    $logData = $requestData;
    $logDescription = "Добавлена запись ${objectScheme[ "title" ]} № $insertId";

    /**
     * @hook
     * Формирование описания логах
     */
    if ( file_exists( $public_customCommandDirPath . "/hooks/log.php" ) )
        require( $public_customCommandDirPath . "/hooks/log.php" );

    $API->addLog( [
        "table_name" => $objectScheme[ "table" ],
        "description" => $logDescription,
        "row_id" => $insertId
    ], $logData );

} catch ( PDOException $e ) {

    $API->returnResponse( $e->getMessage(), 500 );

} // try. $API->DB->insertInto