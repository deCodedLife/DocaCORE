<?php

/**
 * @file
 * Получение доступных переменных
 */


/**
 * Сформированный список переменных
 */
$resultVariablesList = [];

/**
 * Список доступных объектов
 */
$objectsList = [];


/**
 * Обход директории объектов
 */
function scanObjectDir ( $objectsDirPath ) {

    global $objectsList;


    $objectsDir = dir( $objectsDirPath );

    while ( ( $objectScheme = $objectsDir->read() ) !== false ) {

        if ( $objectScheme == "." || $objectScheme == ".." ) continue;

        $objectsList[] = substr( $objectScheme, 0, strpos( $objectScheme, "." ) );

    } // while. $objectsDir->read()

    $objectsDir->close();

} // function. scanObjectDir


/**
 * Формирование списка объектов
 */

scanObjectDir( $API::$configs[ "paths" ][ "public_object_schemes" ] );
scanObjectDir( $API::$configs[ "paths" ][ "system_object_schemes" ] );

$objectsList = array_unique( $objectsList );


/**
 * Формирование списка переменных
 */

foreach ( $objectsList as $objectArticle ) {

    /**
     * Получение схемы объекта
     */

    $objectScheme = $API->loadObjectScheme( $objectArticle );
    if ( !$objectScheme[ "properties" ] ) continue;

    $resultVariablesList[ $objectArticle ] = [
        "title" => $objectScheme[ "title" ],
        "variables" => []
    ];


    /**
     * Получение св-в объекта
     */

    foreach ( $objectScheme[ "properties" ] as $property ) {

        $resultVariablesList[ $objectArticle ][ "variables" ][ $property[ "article" ] ] = [
            "title" => $property[ "title" ],
            "field_type" => $property[ "field_type" ]
        ];

    } // foreach. $objectScheme[ "properties" ]

} // foreach. $objectsList


$response[ "data" ] = $resultVariablesList;