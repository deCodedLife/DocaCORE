<?php

/**
 * @file Стандартная команда update.
 * Используется для редактирования записей в базе данных
 */


/**
 * Значения для вставки
 */
$updateValues = [];

/**
 * Значения связанных таблиц для редактирования
 */
$join_updateValues = [];


/**
 * Формирование значений для редактирования
 */

foreach ( $requestData as $propertyArticle => $propertyValue ) {

    if ( $propertyArticle === "id" ) continue;


    /**
     * Получение схемы объекта
     */
    foreach ( $objectScheme[ "properties" ] as $schemeProperty ) {

        if ( !$schemeProperty[ "is_autofill" ] ) continue;
        if ( $schemeProperty[ "article" ] !== $propertyArticle ) continue;


        /**
         * Добавление св-ва в запрос
         */

        $propertyName = $schemeProperty[ "article" ];
        $propertyValue = $requestData->{$schemeProperty[ "article" ]};

        if ( $propertyValue === "" ) $propertyValue = null;


        /**
         * Обработка связанных таблиц
         */

        if ( $schemeProperty[ "join" ] ) $join_updateValues[ $propertyName ] = [
            "connection_table" => $schemeProperty[ "join" ][ "connection_table" ],
            "filter_property" => $schemeProperty[ "join" ][ "filter_property" ],
            "insert_property" => $schemeProperty[ "join" ][ "insert_property" ],
            "data" => []
        ];

        if ( $propertyValue ) {

            if ( !$schemeProperty[ "join" ] ) $updateValues[ $propertyName ] = $propertyValue;
            else $join_updateValues[ $propertyName ][ "data" ] = $propertyValue;

        } // if. $propertyValue


        /**
         * Проверка на уникальность
         */

        if ( $schemeProperty[ "is_unique" ] ) {

            $repeatedProperty = $API->DB->from( $objectScheme[ "table" ] )
                ->where( $propertyName, $propertyValue )
                ->limit( 1 )
                ->fetch();

            if ( $repeatedProperty && ( $repeatedProperty[ "id" ] != $requestData->id ) )
                $API->returnResponse( "Запись с таким $propertyName уже существует", 500 );

        } // if. $schemeProperty[ "is_unique" ]

    } // foreach. $objectScheme[ "properties" ]

} // foreach. $requestData


/**
 * Отправка запроса на редактирование записи
 */

try {

    if ( $updateValues ) $API->DB->update( $objectScheme[ "table" ] )
        ->set( $updateValues )
        ->where( [
            "id" => $requestData->id,
            "is_system" => "N"
        ] )
        ->execute();


    /**
     * Связанные таблицы
     */
    foreach ( $join_updateValues as $donor_table => $join ) {

        /**
         * Очистка старых связей
         */
        $API->DB->deleteFrom( $join[ "connection_table" ] )
            ->where( $join[ "insert_property" ], $requestData->id )
            ->execute();


        foreach ( $join[ "data" ] as $key => $connection_table_value ) {

            $API->DB->insertInto( $join[ "connection_table" ] )
                ->values( [
                    $join[ "insert_property" ] => $requestData->id,
                    $join[ "filter_property" ] => $connection_table_value
                ] )
                ->execute();

        } // foreach. $join[ "data" ]

    } // foreach. $join_updateValues


    /**
     * Добавление лога
     */
    $API->addLog( [
        "table_name" => $objectScheme[ "table" ],
        "description" => "Обновлена запись ${objectScheme[ "title" ]} № $requestData->id",
        "row_id" => $requestData->id
    ], $requestData );

} catch ( PDOException $e ) {

    $API->returnResponse( $e->getMessage(), 500 );

} // try. update