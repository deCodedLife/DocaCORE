<?php

/**
 * @file
 * Обновление схем
 */


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
 * Обновление схемы
 */
if ( !file_put_contents( $schemePath, $requestData->scheme_body ) ) return false;


$API->returnResponse( true );