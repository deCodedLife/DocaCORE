<?php

/**
 * @file
 * Получение схем
 */


/**
 * Запрошенные схемы
 */
$result = [];

/**
 * Типы запрашиваемых схем
 */
$schemeTypes = [];


/**
 * Получение типов запрашиваемых схем
 */
if ( $requestData->scheme_type ) $schemeTypes[] = $requestData->scheme_type;
else $schemeTypes = [ "command", "db", "object", "page" ];


/**
 * Получение детальной информации о схеме.
 * Используется, когда запрошена конкретная схема
 */

if ( $requestData->scheme_type && $requestData->scheme_name ) {

    /**
     * Директория типа схем
     */
    $schemeDir = "";


    /**
     * Получение директории типа схем
     */
    switch ( $requestData->scheme_type ) {

        case "command":
            $schemeDir = $API::$configs[ "paths" ][ "public_command_schemes" ];
            break;

        case "db":
            $schemeDir = $API::$configs[ "paths" ][ "public_db_schemes" ];
            break;

        case "object":
            $schemeDir = $API::$configs[ "paths" ][ "public_object_schemes" ];
            break;

        case "page":
            $schemeDir = $API::$configs[ "paths" ][ "public_page_schemes" ];
            break;

    } // switch. $requestData->scheme_type

    if ( !$schemeDir ) $API->returnResponse( [] );


    /**
     * Получение пути к схеме
     */
    $schemePath = "$schemeDir/$requestData->scheme_name.json";


    /**
     * Подключение схемы
     */
    if ( file_exists( $schemePath ) ) $scheme = file_get_contents( $schemePath );
    else $this->returnResponse( "Отсутствует схема", 500 );


    /**
     * Декодирование схемы объекта
     */
    try {

        $scheme = json_decode( $scheme, true );

        if ( $scheme === null ) $API->returnResponse( "Ошибка обработки схемы объекта", 500 );

    } catch ( Exception $error ) {

        $API->returnResponse( "Несоответствие схеме объекта", 500 );

    } // try. json_decode. $scheme


    $API->returnResponse( $scheme );

} // if. $requestData->scheme_type && $requestData->scheme_name


/**
 * Обход типов схем.
 * Используется, когда запрошен общий список схем
 */

foreach ( $schemeTypes as $schemeType ) {

    /**
     * Директория типа схем
     */
    $schemeDir = "";


    /**
     * Получение директории типа схем
     */
    switch ( $schemeType ) {

        case "command":
            $schemeDir = $API::$configs[ "paths" ][ "public_command_schemes" ];
            break;

        case "db":
            $schemeDir = $API::$configs[ "paths" ][ "public_db_schemes" ];
            break;

        case "object":
            $schemeDir = $API::$configs[ "paths" ][ "public_object_schemes" ];
            break;

        case "page":
            $schemeDir = $API::$configs[ "paths" ][ "public_page_schemes" ];
            break;

    } // switch. $schemeType

    if ( !$schemeDir ) continue;


    /**
     * Обход директории типа схем
     */

    $schemeDir = dir( $schemeDir );

    while ( ( $schemeFile = $schemeDir->read() ) !== false ) {

        if ( ( $schemeFile === "." ) || ( $schemeFile === ".." ) ) continue;

        /**
         * Обновление списка запрошенных схем
         */
        $result[ $schemeType ][] = $schemeFile;

    } // while. $schemeDir->read()

    $schemeDir->close();

} // foreach. $schemeTypes


$response[ "data" ] = $result;