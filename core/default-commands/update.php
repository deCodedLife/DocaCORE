<?php

/**
 * @file Стандартная команда update.
 * Используется для редактирования записей в базе данных
 */


/**
 * Проверка обязательных св-в
 */
if ( !$requestData->id ) $API->returnResponse( "Отсутствует обязательное св-во `ID`", 400 );


/**
 * Значения для вставки
 */
$updateValues = [];

/**
 * Значения связанных таблиц для редактирования
 */
$join_updateValues = [];

/**
 * Значения умных списков
 */
$smartListProperties = [];


/**
 * Формирование значений для редактирования
 */

foreach ( $requestData as $propertyArticle => $propertyValue ) {

    if ( $propertyArticle === "id" ) continue;


    /**
     * Получение схемы объекта
     */
    foreach ( $objectScheme[ "properties" ] as $schemePropertyKey => $schemeProperty ) {

        if ( !$schemeProperty[ "is_autofill" ] ) continue;
        if ( $schemeProperty[ "article" ] !== $propertyArticle ) continue;


        /**
         * Загрузка файлов
         */
        switch ( $schemeProperty[ "data_type" ] ) {

            case "image":

                $requestData->{$schemeProperty[ "article" ]} = $API->uploadImagesFromForm( $requestData->id, $propertyValue );
                break;

            case "file":

                $requestData->{$schemeProperty[ "article" ]} = $API->uploadFilesFromForm( $requestData->id, $propertyValue );
                break;

        } // switch. $schemeProperty[ "data_type" ]


        /**
         * Получение информации о св-ве
         */
        $propertyName = $schemeProperty[ "article" ];
        $propertyValue = $requestData->{$schemeProperty[ "article" ]};


        /**
         * Обработка связанных таблиц
         */
        if ( $schemeProperty[ "join" ] ) $join_updateValues[ $propertyName ] = [
            "connection_table" => $schemeProperty[ "join" ][ "connection_table" ],
            "filter_property" => $schemeProperty[ "join" ][ "filter_property" ],
            "insert_property" => $schemeProperty[ "join" ][ "insert_property" ],
            "data" => []
        ];

        /**
         * Обработка умных списков
         */
        if ( $schemeProperty[ "field_type" ] === "smart_list" ) {

            $smartListProperties[ $schemeProperty[ "settings" ][ "connection_table" ] ] = $propertyValue;
            continue;

        } // if. $schemeProperty[ "field_type" ] === "smart_list"


        /**
         * Добавление св-ва в запрос
         */
        if ( $propertyValue !== null ) {

            if ( !$schemeProperty[ "join" ] ) $updateValues[ $propertyName ] = $propertyValue;
            else $join_updateValues[ $propertyName ][ "data" ] = $propertyValue;

        } // if. $propertyValue !== null


        /**
         * Проверка на уникальность
         */

        if ( $schemeProperty[ "is_unique" ] && $propertyValue ) {

            $repeatedProperty = $API->DB->from( $objectScheme[ "table" ] )
                ->where( $propertyName, $propertyValue )
                ->limit( 1 )
                ->fetch();

            if ( $repeatedProperty && ( $repeatedProperty[ "id" ] != $requestData->id ) )
                $API->returnResponse( "Запись с таким $propertyName уже существует", 500 );

        } // if. $schemeProperty[ "is_unique" ] && $propertyValue

    } // foreach. $objectScheme[ "properties" ]

} // foreach. $requestData


/**
 * Обработка пользовательских св-в
 */

if ( $userScheme ) {

    foreach ( $userScheme as $objectArticle => $object )
        if (
            ( $objectArticle == $objectScheme[ "table" ] ) ||
            ( "us__$objectArticle" == $objectScheme[ "table" ] )
        ) foreach ( $object->properties as $propertyArticle => $property )
            if ( $requestData->{$propertyArticle} ) $updateValues[ "us__$propertyArticle" ] = $requestData->{$propertyArticle};

} // if. $userScheme


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
     * Умные списки
     */
    foreach ( $smartListProperties as $table => $properties ) {

        /**
         * Очистка старых связей
         */
        $API->DB->deleteFrom( $table )
            ->where( "row_id", $requestData->id )
            ->execute();


        foreach ( $properties as $propertyValues ) {

            $propertyValues = (array) $propertyValues;
            $propertyValues[ "row_id" ] = $requestData->id;

            $API->DB->insertInto( $table )
                ->values( $propertyValues )
                ->execute();

        } // foreach. $join[ "data" ]

    } // foreach. $smartListProperties


    /**
     * Добавление лога
     */

    $logData = $requestData;
    $logDescription = "Обновлена запись ${objectScheme[ "title" ]} № $requestData->id";

    /**
     * @hook
     * Формирование описания логах
     */
    if ( file_exists( $public_customCommandDirPath . "/hooks/log.php" ) )
        require( $public_customCommandDirPath . "/hooks/log.php" );

    $API->addLog( [
        "table_name" => $objectScheme[ "table" ],
        "description" => $logDescription,
        "row_id" => $requestData->id
    ], $logData );

} catch ( PDOException $e ) {

    $API->returnResponse( $e->getMessage(), 500 );

} // try. update