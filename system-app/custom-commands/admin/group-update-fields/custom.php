<?php

/**
 * @file
 * Поля для группового редактирования
 */


/**
 * Список св-в Объекта
 */
$objectProperties = [];


/**
 * Формирование пути к схеме объекта
 */

$publicSchemePath = $API::$configs[ "paths" ][ "public_object_schemes" ] . "/" . $requestData->scheme_name . ".json";
$systemSchemePath = $API::$configs[ "paths" ][ "system_object_schemes" ] . "/" . $requestData->scheme_name . ".json";


/**
 * Подключение схемы страницы
 */

$objectScheme = [];

if ( file_exists( $publicSchemePath ) ) $objectScheme = file_get_contents( $publicSchemePath );
elseif ( file_exists( $systemSchemePath ) ) $objectScheme = file_get_contents( $systemSchemePath );
else $API->returnResponse( "Отсутствует схема объекта", 500 );


/**
 * Декодирование схемы запроса
 */
try {

    $objectScheme = json_decode( $objectScheme, true );
    if ( $objectScheme === null ) $API->returnResponse( "Ошибка обработки схемы объекта", 500 );

} catch ( Exception $error ) {

    $API->returnResponse( "Несоответствие схеме объекта", 500 );

} // try. json_decode. $pageScheme


/**
 * Проверка доступов
 */
if ( !$API->validatePermissions( $objectScheme[ "required_permissions" ] ) )
    $API->returnResponse( "Нет доступа к объекту", 403 );


/**
 * Обработка св-в объекта
 */

foreach ( $objectScheme[ "properties" ] as $property ) {

    /**
     * Проверка участия поля в групповом редактировании
     */
    if ( $property[ "use_in_group_update" ] === false ) continue;

    /**
     * Проверка блокировки поля
     */
    if ( !in_array( "update", $property[ "use_in_commands" ] ) ) continue;


    $objectProperties[] = [
        "title" => $property[ "title" ],
        "article" => $property[ "article" ],
        "data_type" => $property[ "data_type" ],
        "field_type" => $property[ "field_type" ],
        "settings" => $property[ "settings" ],
        "search" => $property[ "search" ]
    ];

} // foreach. $objectScheme[ "properties" ]


$API->returnResponse( $objectProperties );