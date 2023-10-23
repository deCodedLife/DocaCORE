<?php

/**
 * @file Стандартная команда search.
 * Используется для поиска записей в базе данных
 */


/**
 * Найденные записи
 */
$rows = [];


/**
 * Проверка наличия таблицы в схеме запроса
 */
if ( !$objectScheme[ "table" ] ) $API->returnResponse( "Отсутствует таблица в схеме запроса", 500 );


/**
 * Объявление объекта sphinx
 */

$Sphinx = new SphinxClient();

$Sphinx->SetSortMode( SPH_SORT_RELEVANCE  );
$Sphinx->SetArrayResult( true );


/**
 * Поиск совпадения
 */
$requestData->limit = $requestData->limit ?? 50;

$Sphinx->SetLimits( 0, $requestData->limit );
$searchIdList = $Sphinx->Query(
    $requestData->search,
    str_replace( "-", "_", $API::$configs[ "db" ][ "name" ] ) . "_" . $objectScheme[ "table" ]
);

if ( $searchIdList[ "matches" ] ) {

    $findRowsId = [];

    foreach ( $searchIdList[ "matches" ] as $searchId ) $findRowsId[] = $searchId[ "id" ];

    $rows = $API->DB->from( $objectScheme[ "table" ] )
        ->where( "id", $findRowsId )
        ->limit( $requestData->limit );

} // if. $searchIdList[ "matches" ]


/**
 * Обработка ответа
 */

$response[ "detail" ][ "rows_count" ] = count( $searchIdList[ "matches" ] );
$response[ "detail" ][ "pages_count" ] = ceil(
    $response[ "detail" ][ "rows_count" ] / $requestData->limit
);

$response[ "data" ] = $API->getResponseBuilder( $rows, $objectScheme, $requestData->context );