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
 * Получение детальной информации о записи
 */
$rowDetail = $API->DB->from( $objectScheme[ "table" ] )
    ->where( "id", $requestData->id )
    ->limit( 1 )
    ->fetch();


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

                if ( !$schemeProperty[ "settings" ][ "is_multiply" ] ) {

                    $requestData->{$schemeProperty[ "article" ]} = $API->uploadImagesFromForm( $requestData->id, $propertyValue[ 0 ] );

                } else {

                    $requestData->{$schemeProperty[ "article" ]} = $API->uploadMultiplyImages( $requestData->id, $propertyValue );

                } // if. !$schemeProperty[ "settings" ][ "is_multiply" ]

                break;

            case "file":

                if ( !$schemeProperty[ "settings" ][ "is_multiply" ] ) {

                    $requestData->{$schemeProperty[ "article" ]} = $API->uploadFilesFromForm( $requestData->id, $propertyValue );

                } else {

                    $requestData->{$schemeProperty[ "article" ]} = $API->uploadMultiplyFiles( $requestData->id, $propertyValue );

                } // if. !$schemeProperty[ "settings" ][ "is_multiply" ]

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

} catch ( PDOException $e ) {

    $API->returnResponse( $e->getMessage(), 500 );

} // try. update


/**
 * Формирование лога
 */

$isFieldsUpdate = false;
$logDescription = "Изменены поля: ";

foreach ( $objectScheme[ "properties" ] as $schemePropertyKey => $schemeProperty ) {

    if ( !$schemeProperty[ "is_autofill" ] ) continue;
    if ( !isset( $updateValues[ $schemeProperty[ "article" ] ] ) ) continue;


    /**
     * Игнорирование системных св-в
     */

    $isContinue = false;

    switch ( $schemeProperty[ "article" ] ) {

        case "id":
        case "password":
            $isContinue = true;
            break;

    } // switch. $schemePropertyKey

    if ( $isContinue ) continue;


    /**
     * Игнорирование типов св-в
     */

    switch ( $schemeProperty[ "field_type" ] ) {

        case "smart_list":
            $isContinue = true;
            break;

    } // switch. $schemePropertyKey

    if ( $isContinue ) continue;


    /**
     * Проверка наличия изменений
     */
    if ( $rowDetail[ $schemeProperty[ "article" ] ] == $updateValues[ $schemeProperty[ "article" ] ] ) continue;


    /**
     * Обработка списков
     */

    if ( $schemeProperty[ "field_type" ] == "list" ) {

        if ( $schemeProperty[ "data_type" ] == "integer" ) {

            /**
             * Получение изначального значения св-ва
             */
            $innerRowDetail_old = $API->DB->from( $schemeProperty[ "list_donor" ][ "table" ] )
                ->where( "id", $rowDetail[ $schemeProperty[ "article" ] ] )
                ->select( null )->select( $schemeProperty[ "list_donor" ][ "properties_title" ] )
                ->limit( 1 )
                ->fetch();

            /**
             * Получение нового значения св-ва
             */
            $innerRowDetail_new = $API->DB->from( $schemeProperty[ "list_donor" ][ "table" ] )
                ->where( "id", $updateValues[ $schemeProperty[ "article" ] ] )
                ->select( null )->select( $schemeProperty[ "list_donor" ][ "properties_title" ] )
                ->limit( 1 )
                ->fetch();


            /**
             * Обновление значений
             */
            $rowDetail[ $schemeProperty[ "article" ] ] = $innerRowDetail_old[ $schemeProperty[ "list_donor" ][ "properties_title" ] ];
            $updateValues[ $schemeProperty[ "article" ] ] = $innerRowDetail_new[ $schemeProperty[ "list_donor" ][ "properties_title" ] ];

        } // if. $schemeProperty[ "data_type" ] == "integer"

    } // if. $schemeProperty[ "field_type" ] == "list"


    $isFieldsUpdate = true;
    $logDescription .= $schemeProperty[ "title" ] . " с [" . $rowDetail[ $schemeProperty[ "article" ] ] . "] на [" . $updateValues[ $schemeProperty[ "article" ] ] . "], ";

} // foreach. $objectScheme[ "properties" ]

if ( !$isFieldsUpdate ) $logDescription = "Обновлена запись ${objectScheme[ "title" ]} № $requestData->id";
else $logDescription = substr( $logDescription, 0, -2 );


/**
 * Добавление лога
 */

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
], $requestData );