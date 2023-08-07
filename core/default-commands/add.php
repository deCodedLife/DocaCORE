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
 * Значения умных списков
 */
$smartListProperties = [];

/**
 * Загружаемые файлы
 */
$files = [];


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
     * Подготовка файлов к загрузке
     */
    switch ( $schemeProperty[ "data_type" ] ) {

        case "image":

            $files[] = [
                "property" => $propertyName,
                "type" => "image",
                "value" => $propertyValue
            ];
            break;

        case "file":

            $files[] = [
                "property" => $propertyName,
                "type" => "file",
                "value" => $propertyValue
            ];
            break;

    } // switch. $schemeProperty[ "data_type" ]


    /**
     * Обработка умных списков
     */
    if ( $schemeProperty[ "field_type" ] === "smart_list" ) {

        $smartListProperties[ $schemeProperty[ "settings" ][ "connection_table" ] ] = $propertyValue;
        continue;

    } // if. $schemeProperty[ "field_type" ] === "smart_list"


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

        $isDouble = false;

        $repeatedProperties = $API->DB->from( $objectScheme[ "table" ] )
            ->where( $propertyName, $propertyValue );

        foreach ( $repeatedProperties as $repeatedProperty )
            if ( $repeatedProperty[ "is_active" ] === "N" ) continue;
            else $isDouble = true;

        if ( $isDouble )
            $API->returnResponse( "Запись с таким $propertyName уже существует", 500 );

    } // if. $schemeProperty[ "is_unique" ] && $propertyValue

} // foreach. $objectScheme[ "properties" ] as $schemeProperty


/**
 * Обработка пользовательских св-в
 */

if ( $userScheme ) {

    foreach ( $userScheme as $objectArticle => $object )
        if ( "us__$objectArticle" == $objectScheme[ "table" ] )
            foreach ( $object->properties as $propertyArticle => $property )
                if ( $requestData->{$propertyArticle} ) $insertValues[ "us__$propertyArticle" ] = $requestData->{$propertyArticle};

} // if. $userScheme


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
     * Умные списки
     */
    foreach ( $smartListProperties as $table => $properties ) {

        /**
         * Очистка старых связей
         */
        $API->DB->deleteFrom( $table )
            ->where( "row_id", $insertId )
            ->execute();


        foreach ( $properties as $propertyValues ) {

            $propertyValues = (array) $propertyValues;
            $propertyValues[ "row_id" ] = $insertId;

            $API->DB->insertInto( $table )
                ->values( $propertyValues )
                ->execute();

        } // foreach. $join[ "data" ]

    } // foreach. $smartListProperties


    /**
     * Загрузка файлов
     */

    foreach ( $files as $file ) {

        switch ( $file[ "type" ] ) {

            case "image":

                switch ( gettype( $file[ "value" ] ) ) {

                    case "string":

                        $API->DB->update( $objectScheme[ "table" ] )
                            ->set( $file[ "property" ], $API->uploadImagesFromForm( $insertId, $file[ "value" ] ) )
                            ->where( [
                                "id" => $insertId,
                                "is_system" => "N"
                            ] )
                            ->execute();

                        break;

                    case "array":

                        $API->DB->update( $objectScheme[ "table" ] )
                            ->set( $file[ "property" ], $API->uploadMultiplyImages( $insertId, $file[ "value" ] ) )
                            ->where( [
                                "id" => $insertId,
                                "is_system" => "N"
                            ] )
                            ->execute();

                        break;

                } // switch. gettype( $file[ "value" ] )

                break;

            case "file":

                $API->DB->update( $objectScheme[ "table" ] )
                    ->set( $file[ "property" ], $API->uploadFilesFromForm( $insertId, $file[ "value" ] ) )
                    ->where( [
                        "id" => $insertId,
                        "is_system" => "N"
                    ] )
                    ->execute();

                break;

        } // switch. $file[ "type" ]

    } // foreach. $files


    /**
     * Вывод ID созданной записи
     */
    $response[ "data" ] = $insertId;


    /**
     * Добавление лога
     */
    $logData = $requestData;
    $logDescription = "Добавлена запись ${objectScheme[ "title" ]}";


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