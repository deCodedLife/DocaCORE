<?php

/**
 * @file
 * Обновление базы данных
 */


/**
 * Подключение вспомогательных функций
 */
require_once( "functions.php" );


/**
 * Токен.
 * Используется для проверки Пользователя
 */
$token = "0mv0H1UWorjo9I";

/**
 * Обработанные базы данных
 */
$updatedDatabases = [];


/**
 * Проверка токена Пользователя
 */
if ( $requestData->token !== "0mv0H1UWorjo9I" )
    $API->returnResponse( "Неверный токен", 403 );


/**
 * Обработка публичных схем Баз данных
 */
$result[ "public" ] = updateDatabaseSchemes(
    readDatabaseSchemesDir( $API::$configs[ "paths" ][ "public_db_schemes" ] )
);

/**
 * Обработка системных схем Баз данных
 */
$result[ "system" ] = updateDatabaseSchemes(
    readDatabaseSchemesDir( $API::$configs[ "paths" ][ "system_db_schemes" ] )
);


$response[ "data" ] = $result;