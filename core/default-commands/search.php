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

$Sphinx->SetLimits( 0, 15 );
$searchIdList = $Sphinx->Query(
    $requestData->search,
    str_replace( "-", "_", $API::$configs[ "db" ][ "name" ] ) . "_" . $objectScheme[ "table" ]
);

if ( $searchIdList[ "matches" ] ) {

    $findRowsId = [];

    foreach ( $searchIdList[ "matches" ] as $searchId ) $findRowsId[] = $searchId[ "id" ];

    $rows = $API->DB->from( "users" )
        ->where( "id", $findRowsId )
        ->limit( 15 );

} // if. $searchIdList[ "matches" ]


/**
 * Обработка ответа
 */

$response[ "data" ] = $API->getResponseBuilder( $rows, $objectScheme, $requestData->is_list );